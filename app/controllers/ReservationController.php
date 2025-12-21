<?php

class ReservationController extends Controller
{
    public function create()
    {   
        try {
            if (!isset($_SESSION['user_id'])) {
                $this->json(['success' => false, 'message' => 'Vous devez être connecté.'], 401);
                return;
            }
 
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->json(['success' => false, 'message' => 'Méthode non autorisée.'], 405);
                return;
            }

            $equipementId = $_POST['equipement_id'] ?? '';
            $dateDebut = $_POST['date_debut'] ?? '';
            $dateFin = $_POST['date_fin'] ?? '';
            $motif = $_POST['motif'] ?? '';
            $nbInstances = intval($_POST['nb_instances'] ?? 1);
            
            if (empty($equipementId) || empty($dateDebut) || empty($dateFin)) {
                $this->json(['success' => false, 'message' => 'Veuillez remplir tous les champs obligatoires.'], 400);
                return;
            }

            $equipementModel = $this->model('Equipement');
            $equipement = $equipementModel->getById($equipementId);
            
            if (!$equipement) {
                error_log("ERREUR: Equipement $equipementId introuvable");
                $this->json(['success' => false, 'message' => 'Équipement introuvable.'], 404);
                return;
            }

            $debut = strtotime($dateDebut);
            $fin = strtotime($dateFin);
            $now = time();
            
            if ($debut < $now) {
                $this->json(['success' => false, 'message' => 'La date de début doit être dans le futur.'], 400);
                return;
            }
            
            if ($fin <= $debut) {
                $this->json(['success' => false, 'message' => 'La date de fin doit être après la date de début.'], 400);
                return;
            }

            if ($equipement['type_libelle'] !== 'salles' && $equipement['capacite']) {
                if ($nbInstances < 1 || $nbInstances > $equipement['capacite']) {
                    $this->json([
                        'success' => false, 
                        'message' => "Le nombre d'instances doit être entre 1 et " . $equipement['capacite'] . "."
                    ], 400);
                    return;
                }
            }
            
            $reservationModel = $this->model('Reservation');
            if ($reservationModel->hasConflict($equipementId, $dateDebut, $dateFin)) {
                $this->json([
                    'success' => false, 
                    'message' => 'Cet équipement est déjà réservé pour cette période.'
                ], 409);
                return;
            }

            $data = [
                'equipement_id' => $equipementId,
                'usr_id' => $_SESSION['user_id'],
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
                'motif' => $motif,
                'nb_instances' => $nbInstances
            ];

            $reservationId = $reservationModel->create($data);
            
            if ($reservationId) {
                $_SESSION['success'] = 'La demande de réservation a été prise en compte.';
                $this->json([
                    'success' => true, 
                    'message' => 'Votre demande de réservation a été enregistrée avec succès. Elle sera traitée par un administrateur.',
                    'reservation_id' => $reservationId
                ]);
            } else {
                $_SESSION['error'] = 'Erreur lors de la création de la réservation.';
                $this->json([
                    'success' => false, 
                    'message' => 'Erreur lors de l\'enregistrement de la réservation. Consultez les logs serveur.'
                ], 500);
            }
            
        } catch (Exception $e) {
            $this->json([
                'success' => false, 
                'message' => 'Erreur: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function checkAvailability()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Méthode non autorisée.'], 405);
            return;
        }
        
        $equipementId = $_POST['equipement_id'] ?? '';
        $dateDebut = $_POST['date_debut'] ?? '';
        $dateFin = $_POST['date_fin'] ?? '';
        
        if (empty($equipementId) || empty($dateDebut) || empty($dateFin)) {
            $this->json(['error' => 'Paramètres manquants.'], 400);
            return;
        }
        
        $reservationModel = $this->model('Reservation');
        $hasConflict = $reservationModel->hasConflict($equipementId, $dateDebut, $dateFin);
        
        $reservations = $reservationModel->getByEquipement($equipementId);
        
        $slots = array_map(function($r) {
            return [
                'start' => $r['date_debut'],
                'end' => $r['date_fin'],
                'status' => $r['statut'],
                'user' => $r['user_prenom'] . ' ' . $r['user_nom']
            ];
        }, $reservations);
        
        $this->json([
            'available' => !$hasConflict,
            'reserved_slots' => $slots
        ]);
    }

    public function cancel($reservationId)
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login');
            return;
        }
        
        $reservationModel = $this->model('Reservation');
        $reservation = $reservationModel->getById($reservationId);
        
        if (!$reservation || $reservation['usr_id'] != $_SESSION['user_id']) {
            $this->redirect('dashboard');
            return;
        }
        
        if (in_array($reservation['statut'], ['annulee', 'terminee'])) {
            $this->redirect('dashboard');
            return;
        }
        
        $updated = $reservationModel->updateStatus($reservationId, 'demande_annulation');
        
        if ($updated) {
            $_SESSION['success'] = 'La demande d\'annulation a été prise en compte.';
        } else {
            $_SESSION['error'] = 'Erreur lors de la demande d\'annulation.';
        }
        
        $this->redirect('dashboard');
    }
}