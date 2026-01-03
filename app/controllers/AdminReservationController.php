<?php

class AdminReservationController extends Controller
{
    private $reservationModel;
    
    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Accès refusé';
            header('Location: ' . BASE_URL);
            exit;
        }
        
        $this->reservationModel = $this->model('Reservation');
    }
    
    public function index()
    {
        $reservations = $this->reservationModel->getAllWithDetails();
        $conflits = $this->reservationModel->getConflitsDetails();
        
        $stats = [
            'en_attente' => count(array_filter($reservations, fn($r) => $r['statut'] == 'en_attente')),
            'demande_annulation' => count(array_filter($reservations, fn($r) => $r['statut'] == 'demande_annulation')),
            'confirmee' => count(array_filter($reservations, fn($r) => $r['statut'] == 'confirmee')),
            'total' => count($reservations),
            'conflits' => count($conflits)
        ];
        
        $this->view('AdminReservationsView', [
            'reservations' => $reservations,
            'stats' => $stats,
            'conflits' => $conflits
        ]);
    }
    
    public function confirmer($id)
    {
        $reservation = $this->reservationModel->getById($id);
        
        if (!$reservation) {
            $_SESSION['error'] = 'Réservation introuvable';
            $this->redirect('admin/reservations');
            return;
        }

        $nbInstances = (int)($reservation['nb_instances'] ?? 1);
        $disponible = !$this->reservationModel->hasConflict(
            $reservation['equipement_id'],
            $reservation['date_debut'],
            $reservation['date_fin'],
            $nbInstances,
            $id,
            1
        );
        
        if (!$disponible) {
            $_SESSION['error'] = 'Capacité insuffisante pour cette période. Réduisez le nombre d\'instances ou rejetez d\'autres demandes en attente.';
            $this->redirect('admin/reservations');
            return;
        }
        
        $result = $this->reservationModel->updateStatus($id, 'confirmee');
        
        if ($result) {
            $rejets = $this->reservationModel->rejeterReservationsIncompatibles($id);
            
            if ($rejets > 0) {
                $_SESSION['success'] = "Réservation confirmée avec succès. $rejets demande(s) incompatible(s) automatiquement rejetée(s).";
            } else {
                $_SESSION['success'] = 'Réservation confirmée avec succès';
            }
        } else {
            $_SESSION['error'] = 'Erreur lors de la confirmation';
        }
        
        $this->redirect('admin/reservations');
    }
    
    public function rejeter($id)
    {
        $result = $this->reservationModel->updateStatus($id, 'annulee');
        
        $_SESSION[$result ? 'success' : 'error'] = $result 
            ? 'Réservation rejetée' 
            : 'Erreur lors du rejet';
        
        $this->redirect('admin/reservations');
    }
    
    public function annuler($id)
    {
        $reservation = $this->reservationModel->getById($id);
        
        if (!$reservation) {
            $_SESSION['error'] = 'Réservation introuvable';
            $this->redirect('admin/reservations');
            return;
        }
        
        $result = $this->reservationModel->updateStatus($id, 'annulee');
        
        $_SESSION[$result ? 'success' : 'error'] = $result 
            ? 'Réservation annulée avec succès' 
            : 'Erreur lors de l\'annulation';
        
        $this->redirect('admin/reservations');
    }
    
    public function details($id)
    {
        $reservation = $this->reservationModel->getById($id);
        
        if (!$reservation) {
            $_SESSION['error'] = 'Réservation introuvable';
            $this->redirect('admin/reservations');
            return;
        }
        
        $this->view('AdminReservationDetailsView', [
            'reservation' => $reservation
        ]);
    }
}