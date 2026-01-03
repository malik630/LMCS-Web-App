<?php

class AdminController extends Controller
{
    public function __construct()
    {
        // Vérifier que l'utilisateur a le rôle admin
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Accès refusé. Vous devez être administrateur.';
            header('Location: ' . BASE_URL);
            exit;
        }
    }
    
    public function index()
    {
        $this->view('AdminDashboardView', []);
    }
}
?>