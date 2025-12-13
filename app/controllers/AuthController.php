<?php

class AuthController extends Controller
{
    public function login()
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('dashboard');
            return;
        }
        
        $this->view('LoginView');
    }
 
    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('auth/login');
            return;
        }
        
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Veuillez remplir tous les champs.';
            $this->redirect('auth/login');
            return;
        }
        $userModel = $this->model('User');
        $user = $userModel->login($username, $password);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nom_complet'] = $user['prenom'] . ' ' . $user['nom'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['photo'] = $user['photo'];    
            $_SESSION['success'] = 'Connexion réussie ! Bienvenue ' . $_SESSION['nom_complet'];
            if ($user['role'] === 'admin') {
                $this->redirect('admin');
            } else {
                $this->redirect('dashboard');
            }
        } else {
            $_SESSION['error'] = 'Identifiants incorrects ou compte inactif.';
            $this->redirect('auth/login');
        }
    }

    public function logout()
    {
        session_destroy();
        session_start();
        $_SESSION['success'] = 'Vous avez été déconnecté avec succès.';
        $this->redirect('');
    }
}
?>