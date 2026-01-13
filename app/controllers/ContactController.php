<?php

class ContactController extends Controller
{
    private $contactModel;
    
    public function __construct()
    {
        $this->contactModel = $this->model('Contact');
    }
    
    public function index()
    {
        $this->view('ContactView');
    }
    
    public function send()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('contact');
            return;
        }
 
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $sujet = trim($_POST['sujet'] ?? '');
        $message = trim($_POST['message'] ?? '');
        
        if (empty($nom) || empty($email) || empty($sujet) || empty($message)) {
            $_SESSION['error'] = 'Tous les champs sont obligatoires.';
            $this->redirect('contact');
            return;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Adresse email invalide.';
            $this->redirect('contact');
            return;
        }
        
        if (strlen($message) < 10) {
            $_SESSION['error'] = 'Le message doit contenir au moins 10 caractères.';
            $this->redirect('contact');
            return;
        }

        $data = [
            'nom' => $nom,
            'email' => $email,
            'sujet' => $sujet,
            'message' => $message
        ];
        
        $result = $this->contactModel->create($data);
        
        if ($result) {
            $_SESSION['success'] = 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.';
        } else {
            $_SESSION['error'] = 'Une erreur est survenue lors de l\'envoi du message. Veuillez réessayer.';
        }
        
        $this->redirect('contact');
    }
}
?>