<?php

class AdminPermissionController extends Controller
{
    private $permissionModel;
    
    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Accès refusé. Vous devez être administrateur.';
            header('Location: ' . BASE_URL);
            exit;
        }
        
        $this->permissionModel = $this->model('Permission');
    }
    
    public function index()
    {
        $roles = ['admin', 'enseignant-chercheur', 'doctorant', 'etudiant', 'invite'];
        $permissions = $this->permissionModel->getAllPermissionsGrouped();
        
        $rolePermissions = [];
        foreach ($roles as $role) {
            $rolePermissions[$role] = $this->permissionModel->getRolePermissions($role);
        }
        
        $this->view('AdminPermissionsView', [
            'roles' => $roles,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions
        ]);
    }
    
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/permissions');
            return;
        }
        
        $role = $_POST['role'] ?? '';
        $permissionIds = $_POST['permissions'] ?? [];
        
        $result = $this->permissionModel->updateRolePermissions($role, $permissionIds);
        
        $_SESSION[$result ? 'success' : 'error'] = $result 
            ? 'Permissions mises à jour pour le rôle ' . $role 
            : 'Erreur lors de la mise à jour.';
        
        $this->redirect('admin/permissions');
    }
}
?>