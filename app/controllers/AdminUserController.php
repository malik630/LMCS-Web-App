<?php

class AdminUserController extends Controller
{
    private $userModel;
    private $permissionModel;
    
    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Accès refusé. Vous devez être administrateur.';
            header('Location: ' . BASE_URL);
            exit;
        }
        
        $this->userModel = $this->model('User');
        $this->permissionModel = $this->model('Permission');
    }
    
    public function index()
    {
        $users = $this->userModel->getAllWithDetails();
        $this->view('AdminUsersView', ['users' => $users]);
    }
    
    public function create()
    {
        $this->view('AdminCreateUserView', []);
    }
    
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/users');
            return;
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $email = $_POST['email'] ?? '';
        
        if (empty($username) || empty($password) || empty($email)) {
            $_SESSION['error'] = 'Tous les champs obligatoires doivent être remplis.';
            $this->redirect('admin/createUser');
            return;
        }

        if ($this->userModel->usernameExists($username)) {
            $_SESSION['error'] = 'Ce nom d\'utilisateur existe déjà.';
            $this->redirect('admin/createUser');
            return;
        }
        
        if ($this->userModel->emailExists($email)) {
            $_SESSION['error'] = 'Cet email existe déjà.';
            $this->redirect('admin/createUser');
            return;
        }
        
        $data = [
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'nom' => $_POST['nom'] ?? '',
            'prenom' => $_POST['prenom'] ?? '',
            'email' => $email,
            'grade' => $_POST['grade'] ?? '',
            'poste' => $_POST['poste'] ?? '',
            'role' => $_POST['role'] ?? 'enseignant-chercheur',
            'statut' => 'actif'
        ];
        
        $userId = $this->userModel->insert('users', $data);
        
        if ($userId) {
            $this->permissionModel->copyRolePermissionsToUser($userId, $data['role']);
            $_SESSION['success'] = 'Utilisateur créé avec succès.';
        } else {
            $_SESSION['error'] = 'Erreur lors de la création.';
        }
        
        $this->redirect('admin/users');
    }
    
    public function edit($userId)
    {
        $user = $this->userModel->getById($userId);
        
        if (!$user) {
            $_SESSION['error'] = 'Utilisateur introuvable.';
            $this->redirect('admin/users');
            return;
        }
        
        $this->view('AdminEditUserView', ['user' => $user]);
    }
    
    public function update($userId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/users');
            return;
        }
        
        $data = [
            'nom' => $_POST['nom'] ?? '',
            'prenom' => $_POST['prenom'] ?? '',
            'email' => $_POST['email'] ?? '',
            'grade' => $_POST['grade'] ?? '',
            'poste' => $_POST['poste'] ?? '',
            'role' => $_POST['role'] ?? 'enseignant-chercheur',
            'statut' => $_POST['statut'] ?? 'actif'
        ];
        
        $result = $this->userModel->updateById('users', $userId, $data, 'id_user');
        
        $_SESSION[$result ? 'success' : 'error'] = $result 
            ? 'Utilisateur modifié avec succès.' 
            : 'Erreur lors de la modification.';
        
        $this->redirect('admin/users');
    }
    
    public function suspend($userId)
    {
        $result = $this->userModel->updateStatut($userId, 'suspendu');
        
        $_SESSION[$result ? 'success' : 'error'] = $result 
            ? 'Utilisateur suspendu.' 
            : 'Erreur lors de la suspension.';
        
        $this->redirect('admin/users');
    }
    
    public function activate($userId)
    {
        $result = $this->userModel->updateStatut($userId, 'actif');
        
        $_SESSION[$result ? 'success' : 'error'] = $result 
            ? 'Utilisateur activé.' 
            : 'Erreur lors de l\'activation.';
        
        $this->redirect('admin/users');
    }
    
    public function delete($userId)
    {
        $result = $this->userModel->softDelete('users', $userId, 'id_user');
        
        $_SESSION[$result ? 'success' : 'error'] = $result 
            ? 'Utilisateur supprimé.' 
            : 'Erreur lors de la suppression.';
        
        $this->redirect('admin/users');
    }
    
    public function permissions($userId)
    {
        $user = $this->userModel->getById($userId);
        
        if (!$user) {
            $_SESSION['error'] = 'Utilisateur introuvable.';
            $this->redirect('admin/users');
            return;
        }
        
        $permissions = $this->permissionModel->getAllPermissionsGrouped();
        $userPermissions = $this->permissionModel->getUserPermissions($userId);
        
        $this->view('AdminUserPermissionsView', [
            'user' => $user,
            'permissions' => $permissions,
            'userPermissions' => $userPermissions
        ]);
    }
    
    public function updatePermissions($userId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/users');
            return;
        }
        
        $permissionIds = $_POST['permissions'] ?? [];
        
        $result = $this->permissionModel->updateUserPermissions($userId, $permissionIds);
        
        $_SESSION[$result ? 'success' : 'error'] = $result 
            ? 'Permissions utilisateur mises à jour avec succès.' 
            : 'Erreur lors de la mise à jour des permissions.';
        
        $this->redirect('admin/users');
    }
}
?>