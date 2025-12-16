<?php

class User extends Model
{
    public function login($username, $password)
    {
        $query = "SELECT * FROM users 
                  WHERE username = :username 
                  AND statut = 'actif' 
                  AND is_deleted = 0";
        $result = $this->select($query, ['username' => $username]);
        
        if (!empty($result)) {
            $user = $result[0];
            if (password_verify($password, $user['password'])) {
                $this->updateById('users', $user['id_user'], [
                    'derniere_connexion' => date('Y-m-d H:i:s')
                ], 'id_user');
                return $user;
            }
        }
        return false;
    }

    public function getById($id)
    {
        return $this->selectById('users', $id, 'id_user');
    }

    public function getAll()
    {
        return $this->selectAll('users', ['is_deleted' => 0], 'nom', 'ASC');
    }
    
    public function updateProfile($userId, $data)
    {
        $allowedFields = ['nom', 'prenom', 'email', 'domaine_recherche', 'biographie', 'photo'];
        $updateData = [];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }
        
        if (empty($updateData)) {
            return false;
        }
        
        return $this->updateById('users', $userId, $updateData, 'id_user');
    }
    
    public function changePassword($userId, $oldPassword, $newPassword)
    {
        $user = $this->selectById('users', $userId, 'id_user');
        if (!$user || !password_verify($oldPassword, $user['password'])) {
            return false;
        }
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->updateById('users', $userId, ['password' => $hashedPassword], 'id_user');
    }
    
    public function resetPassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->updateById('users', $userId, ['password' => $hashedPassword], 'id_user');
    }
    
    public function updateStatut($userId, $statut)
    {
        $validStatuts = ['actif', 'suspendu', 'inactif'];
        if (!in_array($statut, $validStatuts)) {
            return false;
        }
        return $this->updateById('users', $userId, ['statut' => $statut], 'id_user');
    }
    
    public function getByRole($role)
    {
        return $this->selectAll('users', [
            'role' => $role, 
            'statut' => 'actif',
            'is_deleted' => 0
        ], 'nom', 'ASC');
    }
    
    public function getActive()
    {
        return $this->selectAll('users', [
            'statut' => 'actif',
            'is_deleted' => 0
        ], 'nom', 'ASC');
    }
    
    public function searchByName($search)
    {
        return $this->search('users', 'nom', $search, ['is_deleted' => 0]);
    }
    
    public function countByRole($role)
    {
        return $this->count('users', ['role' => $role, 'is_deleted' => 0]);
    }
    
    public function countActive()
    {
        return $this->count('users', ['statut' => 'actif', 'is_deleted' => 0]);
    }
    
    public function userExists($id)
    {
        return $this->exists('users', $id, 'id_user');
    }
    
    public function usernameExists($username, $excludeUserId = null)
    {
        $query = "SELECT id_user FROM users 
                  WHERE username = :username AND is_deleted = 0";
        $params = ['username' => $username];
        
        if ($excludeUserId) {
            $query .= " AND id_user != :excludeId";
            $params['excludeId'] = $excludeUserId;
        }
        
        $result = $this->select($query, $params);
        return !empty($result);
    }

    public function emailExists($email, $excludeUserId = null)
    {
        $query = "SELECT id_user FROM users 
                  WHERE email = :email AND is_deleted = 0";
        $params = ['email' => $email];
        
        if ($excludeUserId) {
            $query .= " AND id_user != :excludeId";
            $params['excludeId'] = $excludeUserId;
        }
        
        $result = $this->select($query, $params);
        return !empty($result);
    }
    
    public function getUserProjects($userId)
    {
        $query = "SELECT DISTINCT p.*, pm.role_projet,
                         u.nom as responsable_nom, u.prenom as responsable_prenom
                  FROM projets p
                  LEFT JOIN projet_membres pm ON p.id_projet = pm.projet_id
                  LEFT JOIN users u ON p.responsable_id = u.id_user
                  WHERE (p.responsable_id = :userId OR pm.usr_id = :userId2)
                  AND p.is_deleted = 0 
                  AND (pm.is_deleted = 0 OR pm.is_deleted IS NULL)
                  ORDER BY p.date_creation DESC";
        return $this->select($query, ['userId' => $userId, 'userId2' => $userId]);
    }

    public function getUserPublications($userId)
    {
        $query = "SELECT DISTINCT pub.*, tp.libelle as type_libelle, pa.ordre_auteur
                  FROM publications pub
                  LEFT JOIN publication_auteurs pa ON pub.id_publication = pa.publication_id
                  LEFT JOIN types_publications tp ON pub.type_publication_id = tp.id_type
                  WHERE pa.usr_id = :userId 
                  AND pub.is_deleted = 0 
                  AND pa.is_deleted = 0
                  ORDER BY pub.annee DESC, pub.date_publication DESC";
        return $this->select($query, ['userId' => $userId]);
    }
    
    public function getUserReservations($userId, $limit = 10)
    {
        $query = "SELECT r.*, e.nom as equipement_nom, e.localisation, te.libelle as type_equipement
                  FROM reservations r
                  JOIN equipements e ON r.equipement_id = e.id_equipement
                  LEFT JOIN types_equipements te ON e.type_equipement_id = te.id_type
                  WHERE r.usr_id = :userId
                  ORDER BY r.date_debut DESC
                  LIMIT " . (int)$limit;
        return $this->select($query, ['userId' => $userId]);
    }

    public function getUserTeams($userId)
    {
        $query = "SELECT t.*, tm.role_dans_equipe, tm.date_adhesion,
                         u.nom as chef_nom, u.prenom as chef_prenom
                  FROM teams t
                  LEFT JOIN team_members tm ON t.id_team = tm.team_id
                  LEFT JOIN users u ON t.chef_id = u.id_user
                  WHERE (t.chef_id = :userId OR tm.usr_id = :userId2)
                  AND t.is_deleted = 0 
                  AND (tm.is_deleted = 0 OR tm.is_deleted IS NULL)
                  ORDER BY t.nom ASC";
        return $this->select($query, ['userId' => $userId, 'userId2' => $userId]);
    }

    public function getUserDocuments($userId)
    {
        $query = "SELECT * FROM documents_personnels 
                  WHERE usr_id = :userId 
                  ORDER BY date_upload DESC";
        return $this->select($query, ['userId' => $userId]);
    }

    public function addDocument($userId, $titre, $type, $fichier, $tailleFichier)
    {
        return $this->insert('documents_personnels', [
            'usr_id' => $userId,
            'titre' => $titre,
            'type' => $type,
            'fichier' => $fichier,
            'taille_fichier' => $tailleFichier,
            'date_upload' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function deleteDocument($documentId, $userId)
    {
        return $this->delete('documents_personnels', [
            'id_document' => $documentId,
            'usr_id' => $userId
        ]);
    }
}
?>