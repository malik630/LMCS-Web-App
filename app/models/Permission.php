<?php

class Permission extends Model
{
    public function userHasPermission($userId, $permissionName)
    {
        $query = "SELECT COUNT(*) as total 
                  FROM user_permissions up
                  JOIN permissions p ON up.permission_id = p.id_permission
                  WHERE up.user_id = :user_id AND p.nom = :permission";
        
        $result = $this->select($query, [
            'user_id' => $userId,
            'permission' => $permissionName
        ]);
        
        return (int)($result[0]['total'] ?? 0) > 0;
    }
    
    public function getUserPermissions($userId)
    {
        $query = "SELECT p.* 
                  FROM permissions p
                  JOIN user_permissions up ON p.id_permission = up.permission_id
                  WHERE up.user_id = :user_id
                  ORDER BY p.categorie, p.nom";
        
        return $this->select($query, ['user_id' => $userId]);
    }

    public function getAllPermissionsGrouped()
    {
        $permissions = $this->selectAll('permissions', [], 'categorie, nom', 'ASC');
        
        $grouped = [];
        foreach ($permissions as $perm) {
            $grouped[$perm['categorie']][] = $perm;
        }
        
        return $grouped;
    }
    
    public function assignPermissionToUser($userId, $permissionId)
    {
        return $this->insert('user_permissions', [
            'user_id' => $userId,
            'permission_id' => $permissionId
        ]);
    }

    public function removePermissionFromUser($userId, $permissionId)
    {
        return $this->delete('user_permissions', [
            'user_id' => $userId,
            'permission_id' => $permissionId
        ]);
    }

    public function updateUserPermissions($userId, array $permissionIds)
    {
        $query = "DELETE FROM user_permissions WHERE user_id = :user_id";
        $this->select($query, ['user_id' => $userId]);

        foreach ($permissionIds as $permissionId) {
            $this->assignPermissionToUser($userId, $permissionId);
        }
        
        return true;
    }

    public function getRoleDefaultPermissions($role)
    {
        $query = "SELECT p.* 
                  FROM permissions p
                  JOIN role_permissions rp ON p.id_permission = rp.permission_id
                  WHERE rp.role = :role
                  ORDER BY p.categorie, p.nom";
        
        return $this->select($query, ['role' => $role]);
    }

    public function copyRolePermissionsToUser($userId, $role)
    {
        $defaultPermissions = $this->getRoleDefaultPermissions($role);
        $permissionIds = array_column($defaultPermissions, 'id_permission');
        
        return $this->updateUserPermissions($userId, $permissionIds);
    }
    
    // Gestion des templates de rôles
    public function roleHasPermission($role, $permissionName)
    {
        $query = "SELECT COUNT(*) as total 
                  FROM role_permissions rp
                  JOIN permissions p ON rp.permission_id = p.id_permission
                  WHERE rp.role = :role AND p.nom = :permission";
        
        $result = $this->select($query, [
            'role' => $role,
            'permission' => $permissionName
        ]);
        
        return (int)($result[0]['total'] ?? 0) > 0;
    }
    
    public function getRolePermissions($role)
    {
        $query = "SELECT p.* 
                  FROM permissions p
                  JOIN role_permissions rp ON p.id_permission = rp.permission_id
                  WHERE rp.role = :role
                  ORDER BY p.categorie, p.nom";
        
        return $this->select($query, ['role' => $role]);
    }
    
    public function updateRolePermissions($role, array $permissionIds)
    {
        $query = "DELETE FROM role_permissions WHERE role = :role";
        $this->select($query, ['role' => $role]);

        foreach ($permissionIds as $permissionId) {
            $this->insert('role_permissions', [
                'role' => $role,
                'permission_id' => $permissionId
            ]);
        }
        
        return true;
    }

    public function getUserPermissionIds($userId)
    {
        $query = "SELECT permission_id FROM user_permissions WHERE user_id = :userId";
        $result = $this->select($query, ['userId' => $userId]);
        
        return array_column($result, 'permission_id');
    }
}
?>