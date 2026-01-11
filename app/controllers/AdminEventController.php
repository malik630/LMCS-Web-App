<?php

class AdminEventController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Accès refusé. Vous devez être administrateur.';
            header('Location: ' . BASE_URL);
            exit;
        }
    }
    
    public function index()
    {
        $eventModel = $this->model('Event');
        $events = $eventModel->getAllWithType();
        
        $statistics = [
            'total' => count($events),
            'a_venir' => count(array_filter($events, fn($e) => $e['statut'] === 'a_venir')),
            'en_cours' => count(array_filter($events, fn($e) => $e['statut'] === 'en_cours')),
            'termine' => count(array_filter($events, fn($e) => $e['statut'] === 'termine')),
            'externe' => count(array_filter($events, fn($e) => $e['externe'] == 1)),
            'interne' => count(array_filter($events, fn($e) => $e['externe'] == 0))
        ];
        
        $this->view('AdminEventsView', [
            'events' => $events,
            'statistics' => $statistics
        ]);
    }
    
    public function create()
    {
        $eventModel = $this->model('Event');
        $types = $eventModel->getAllTypes();
        
        $userModel = $this->model('User');
        $organisateurs = $userModel->getActive();
        
        $this->view('AdminCreateEventView', [
            'types' => $types,
            'organisateurs' => $organisateurs
        ]);
    }
    
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/evenements');
            return;
        }
        
        $eventModel = $this->model('Event');
        
        $data = [
            'titre' => $_POST['titre'] ?? null,
            'description' => $_POST['description'] ?? null,
            'date_debut' => $_POST['date_debut'] ?? null,
            'date_fin' => $_POST['date_fin'] ?? null,
            'lieu' => $_POST['lieu'] ?? null,
            'type_evenement_id' => $_POST['type_evenement_id'] ?? null,
            'organisateur_id' => $_POST['organisateur_id'] ?? null,
            'capacite_max' => $_POST['capacite_max'] ?? null,
            'externe' => isset($_POST['externe']) ? 1 : 0,
            'statut' => $_POST['statut'] ?? 'a_venir',
            'date_creation' => date('Y-m-d H:i:s')
        ];
        
        if (empty($data['titre']) || empty($data['date_debut'])) {
            $_SESSION['error'] = 'Veuillez remplir tous les champs obligatoires.';
            $this->redirect('admin/createEvent');
            return;
        }
        
        if (strtotime($data['date_fin']) < strtotime($data['date_debut'])) {
            $_SESSION['error'] = 'La date de fin doit être postérieure à la date de début.';
            $this->redirect('admin/createEvent');
            return;
        }
        
        if ($eventModel->create($data)) {
            $_SESSION['success'] = 'Événement créé avec succès.';
            $this->redirect('admin/evenements');
        } else {
            $_SESSION['error'] = 'Erreur lors de la création de l\'événement.';
            $this->redirect('admin/createEvent');
        }
    }
    
    public function edit($eventId)
    {
        $eventModel = $this->model('Event');
        $event = $eventModel->getById($eventId);
        
        if (!$event) {
            $_SESSION['error'] = 'Événement introuvable.';
            $this->redirect('admin/evenements');
            return;
        }
        
        $types = $eventModel->getAllTypes();
        
        $userModel = $this->model('User');
        $organisateurs = $userModel->getActive();
        
        $this->view('AdminEditEventView', [
            'event' => $event,
            'types' => $types,
            'organisateurs' => $organisateurs
        ]);
    }
    
    public function update($eventId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/evenements');
            return;
        }
        
        $eventModel = $this->model('Event');
        $event = $eventModel->getById($eventId);
        
        if (!$event) {
            $_SESSION['error'] = 'Événement introuvable.';
            $this->redirect('admin/evenements');
            return;
        }
        
        $data = [
            'titre' => $_POST['titre'] ?? null,
            'description' => $_POST['description'] ?? null,
            'date_debut' => $_POST['date_debut'] ?? null,
            'date_fin' => $_POST['date_fin'] ?? null,
            'lieu' => $_POST['lieu'] ?? null,
            'type_evenement_id' => $_POST['type_evenement_id'] ?? null,
            'organisateur_id' => $_POST['organisateur_id'] ?? null,
            'capacite_max' => $_POST['capacite_max'] ?? null,
            'externe' => isset($_POST['externe']) ? 1 : 0,
            'statut' => $_POST['statut'] ?? 'a_venir'
        ];
        
        if (empty($data['titre']) || empty($data['date_debut'])) {
            $_SESSION['error'] = 'Veuillez remplir tous les champs obligatoires.';
            $this->redirect('admin/editEvent/' . $eventId);
            return;
        }
        
        if (strtotime($data['date_fin']) < strtotime($data['date_debut'])) {
            $_SESSION['error'] = 'La date de fin doit être postérieure à la date de début.';
            $this->redirect('admin/editEvent/' . $eventId);
            return;
        }
        
        if ($eventModel->updateEvent($eventId, $data)) {
            $_SESSION['success'] = 'Événement modifié avec succès.';
            $this->redirect('admin/evenements');
        } else {
            $_SESSION['error'] = 'Erreur lors de la modification.';
            $this->redirect('admin/editEvent/' . $eventId);
        }
    }
    
    public function delete($eventId)
    {
        $eventModel = $this->model('Event');
        
        if ($eventModel->deleteEvent($eventId)) {
            $_SESSION['success'] = 'Événement supprimé avec succès.';
        } else {
            $_SESSION['error'] = 'Erreur lors de la suppression.';
        }
        
        $this->redirect('admin/evenements');
    }
    
    public function manageInscriptions($eventId)
    {
        $eventModel = $this->model('Event');
        $event = $eventModel->getById($eventId);
        
        if (!$event) {
            $_SESSION['error'] = 'Événement introuvable.';
            $this->redirect('admin/evenements');
            return;
        }
        
        $inscriptions = $eventModel->getInscriptions($eventId);
        $nbConfirmees = count(array_filter($inscriptions, fn($i) => $i['statut'] === 'confirmee'));
        
        $this->view('AdminManageInscriptionsView', [
            'event' => $event,
            'inscriptions' => $inscriptions,
            'nb_confirmees' => $nbConfirmees
        ]);
    }
    
    public function confirmerInscription($inscriptionId)
    {
        $eventModel = $this->model('Event');
        $inscription = $eventModel->getInscriptionById($inscriptionId);
        
        if (!$inscription) {
            $_SESSION['error'] = 'Inscription introuvable.';
            $this->redirect('admin/evenements');
            return;
        }
        
        $event = $eventModel->getById($inscription['evenement_id']);
        
        if ($event['capacite_max']) {
            $nbConfirmees = $eventModel->countInscriptions($inscription['evenement_id']);
            
            if ($nbConfirmees >= $event['capacite_max']) {
                $_SESSION['error'] = 'Capacité maximale atteinte. Veuillez d\'abord rejeter une inscription.';
                $this->redirect('admin/manageInscriptions/' . $inscription['evenement_id']);
                return;
            }
        }
        
        if ($eventModel->updateInscriptionStatut($inscriptionId, 'confirmee')) {
            $_SESSION['success'] = 'Inscription confirmée avec succès.';
        } else {
            $_SESSION['error'] = 'Erreur lors de la confirmation.';
        }
        
        $this->redirect('admin/manageInscriptions/' . $inscription['evenement_id']);
    }
    
    public function rejeterInscription($inscriptionId)
    {
        $eventModel = $this->model('Event');
        $inscription = $eventModel->getInscriptionById($inscriptionId);
        
        if (!$inscription) {
            $_SESSION['error'] = 'Inscription introuvable.';
            $this->redirect('admin/evenements');
            return;
        }
        
        if ($eventModel->updateInscriptionStatut($inscriptionId, 'annulee')) {
            $_SESSION['success'] = 'Inscription rejetée avec succès.';
        } else {
            $_SESSION['error'] = 'Erreur lors du rejet.';
        }
        
        $this->redirect('admin/manageInscriptions/' . $inscription['evenement_id']);
    }
    
    public function annulerInscription($inscriptionId)
    {
        $eventModel = $this->model('Event');
        $inscription = $eventModel->getInscriptionById($inscriptionId);
        
        if (!$inscription) {
            $_SESSION['error'] = 'Inscription introuvable.';
            $this->redirect('admin/evenements');
            return;
        }
        
        if ($inscription['statut'] === 'demande_annulation') {
            if ($eventModel->updateInscriptionStatut($inscriptionId, 'annulee')) {
                $_SESSION['success'] = 'Demande d\'annulation traitée avec succès.';
            } else {
                $_SESSION['error'] = 'Erreur lors du traitement.';
            }
        } else {
            if ($eventModel->updateInscriptionStatut($inscriptionId, 'annulee')) {
                $_SESSION['success'] = 'Inscription annulée avec succès.';
            } else {
                $_SESSION['error'] = 'Erreur lors de l\'annulation.';
            }
        }
        
        $this->redirect('admin/manageInscriptions/' . $inscription['evenement_id']);
    }
}