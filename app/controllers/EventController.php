<?php

class EventController extends Controller
{
    public function index()
    {
        $eventModel = $this->model('Event');
        $events = $eventModel->getAllWithType();
        
        $this->view('EventView', ['events' => $events]);
    }
    
    public function register($eventId)
    {
        $eventModel = $this->model('Event');
        $event = $eventModel->getById($eventId);
        
        if (!$event) {
            $_SESSION['error'] = 'Événement introuvable.';
            $this->redirect('event');
            return;
        }
        
        // Si l'événement est interne, on vérifie l'authentification
        if (!$event['externe'] && !isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté pour vous inscrire à cet événement.';
            $this->redirect('auth/login');
            return;
        }
        
        $this->view('EventRegisterView', ['event' => $event]);
    }
    
    public function submitRegistration($eventId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('event');
            return;
        }
        
        $eventModel = $this->model('Event');
        $event = $eventModel->getById($eventId);
        
        if (!$event) {
            $_SESSION['error'] = 'Événement introuvable.';
            $this->redirect('event');
            return;
        }
 
        if (!$event['externe'] && !isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté.';
            $this->redirect('auth/login');
            return;
        }

        $userId = $_SESSION['user_id'] ?? null;
        $prenom = $_POST['prenom'] ?? null;
        $nom = $_POST['nom'] ?? null;
        $email = $_POST['email'] ?? null;

        $nomComplet = null;
        if ($prenom && $nom) {
            $nomComplet = trim($prenom . ' ' . $nom);
        }

        if (!$userId && (empty($prenom) || empty($nom) || empty($email))) {
            $_SESSION['error'] = 'Veuillez remplir tous les champs.';
            $this->redirect('event/register/' . $eventId);
            return;
        }
    
        if ($eventModel->isInscrit($eventId, $userId, $email)) {
            $_SESSION['error'] = 'Vous êtes déjà inscrit à cet événement.';
            $this->redirect('event/register/' . $eventId);
            return;
        }

        if ($event['capacite_max']) {
            $nbInscrits = $eventModel->countInscriptions($eventId);
            if ($nbInscrits >= $event['capacite_max']) {
                $_SESSION['error'] = 'Cet événement est complet.';
                $this->redirect('event/register/' . $eventId);
                return;
            }
        }

        if ($eventModel->inscrire($eventId, $userId, $nomComplet, $email)) {
            $_SESSION['success'] = 'Votre inscription a été enregistrée. Elle sera confirmée par un administrateur.';
            $this->redirect('event');
        } else {
            $_SESSION['error'] = 'Erreur lors de l\'inscription.';
            $this->redirect('event/register/' . $eventId);
        }
    }
    
    public function cancelRegistration($inscriptionId)
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté.';
            $this->redirect('auth/login');
            return;
        }
        
        $eventModel = $this->model('Event');
        $inscription = $eventModel->getInscriptionById($inscriptionId);
        
        if (!$inscription || $inscription['usr_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Inscription introuvable.';
            $this->redirect('dashboard');
            return;
        }
        
        if (!in_array($inscription['statut'], ['en_attente', 'confirmee'])) {
            $_SESSION['error'] = 'Cette inscription ne peut plus être annulée.';
            $this->redirect('dashboard');
            return;
        }
        
        if ($eventModel->updateInscriptionStatut($inscriptionId, 'demande_annulation')) {
            $_SESSION['success'] = 'Votre inscription a été annulée.';
        } else {
            $_SESSION['error'] = 'Erreur lors de l\'annulation.';
        }
        
        $this->redirect('dashboard');
    }
}
?>