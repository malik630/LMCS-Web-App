<?php

class User
{
    private $db;
    
    public function __construct()
    {
        $this->db = new Database();
    }
    
    public function login($username, $password)
    {
        $this->db->connect();
        $query = "SELECT * FROM users WHERE username = :username AND statut = 'actif' AND is_deleted = 0";
        $result = $this->db->query($query, ['username' => $username]);
        $this->db->disconnect();        
        if (!empty($result)) {
            $user = $result[0];
            if (password_verify($password, $user['password'])) {
                $this->updateLastLogin($user['id_user']);
                return $user;
            }
        }
        return false;
    }

    private function updateLastLogin($userId)
    {
        $this->db->connect();
        $query = "UPDATE users SET derniere_connexion = NOW() WHERE id_user = :id";
        $this->db->query($query, ['id' => $userId]);
        $this->db->disconnect();
    }
    
    public function getById($id)
    {
        $this->db->connect();
        $query = "SELECT * FROM users WHERE id_user = :id AND is_deleted = 0";
        $result = $this->db->query($query, ['id' => $id]);
        $this->db->disconnect();
        return $result[0] ?? null;
    }
    
    public function updateProfile($userId, $data)
    {
        $this->db->connect();
        $fields = [];
        $params = ['id' => $userId];
        $allowedFields = ['nom', 'prenom', 'email', 'domaine_recherche', 'biographie', 'photo'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            $this->db->disconnect();
            return false;
        }
        
        $query = "UPDATE users SET " . implode(', ', $fields) . " WHERE id_user = :id";
        $result = $this->db->query($query, $params);
        $this->db->disconnect();
        
        return $result !== false;
    }
    
    public function changePassword($userId, $oldPassword, $newPassword)
    {
        $user = $this->getById($userId);
        if (!$user || !password_verify($oldPassword, $user['password'])) {
            return false;
        }
        
        $this->db->connect();
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $query = "UPDATE users SET password = :password WHERE id_user = :id";
        $result = $this->db->query($query, [
            'password' => $hashedPassword,
            'id' => $userId
        ]);
        $this->db->disconnect();
        
        return $result !== false;
    }
    
    public function getUserProjects($userId)
    {
        $this->db->connect();
        $query = "SELECT DISTINCT p.*, pm.role_projet,
                         u.nom as responsable_nom, u.prenom as responsable_prenom
                  FROM projets p
                  LEFT JOIN projet_membres pm ON p.id_projet = pm.projet_id
                  LEFT JOIN users u ON p.responsable_id = u.id_user
                  WHERE (p.responsable_id = :userId OR pm.usr_id = :userId2)
                  AND p.is_deleted = 0 AND (pm.is_deleted = 0 OR pm.is_deleted IS NULL)
                  ORDER BY p.date_creation DESC";
        $result = $this->db->query($query, ['userId' => $userId, 'userId2' => $userId]);
        $this->db->disconnect();
        return $result;
    }

    public function getUserPublications($userId)
    {
        $this->db->connect();
        $query = "SELECT DISTINCT pub.*, tp.libelle as type_libelle, pa.ordre_auteur
                  FROM publications pub
                  LEFT JOIN publication_auteurs pa ON pub.id_publication = pa.publication_id
                  LEFT JOIN types_publications tp ON pub.type_publication_id = tp.id_type
                  WHERE pa.usr_id = :userId AND pub.is_deleted = 0 AND pa.is_deleted = 0
                  ORDER BY pub.annee DESC, pub.date_publication DESC";
        $result = $this->db->query($query, ['userId' => $userId]);
        $this->db->disconnect();
        return $result;
    }
    
    /**
     * Récupérer les réservations d'équipements de l'utilisateur
     */
    public function getUserReservations($userId)
    {
        $this->db->connect();
        $query = "SELECT r.*, e.nom as equipement_nom, e.localisation, te.libelle as type_equipement
                  FROM reservations r
                  JOIN equipements e ON r.equipement_id = e.id_equipement
                  LEFT JOIN types_equipements te ON e.type_equipement_id = te.id_type
                  WHERE r.usr_id = :userId
                  ORDER BY r.date_debut DESC
                  LIMIT 10";
        $result = $this->db->query($query, ['userId' => $userId]);
        $this->db->disconnect();
        return $result;
    }

    public function getUserTeams($userId)
    {
        $this->db->connect();
        $query = "SELECT t.*, tm.role_dans_equipe, tm.date_adhesion,
                         u.nom as chef_nom, u.prenom as chef_prenom
                  FROM teams t
                  LEFT JOIN team_members tm ON t.id_team = tm.team_id
                  LEFT JOIN users u ON t.chef_id = u.id_user
                  WHERE (t.chef_id = :userId OR tm.usr_id = :userId2)
                  AND t.is_deleted = 0 AND (tm.is_deleted = 0 OR tm.is_deleted IS NULL)
                  ORDER BY t.nom ASC";
        $result = $this->db->query($query, ['userId' => $userId, 'userId2' => $userId]);
        $this->db->disconnect();
        return $result;
    }
    
    public function getUserDocuments($userId)
    {
        $this->db->connect();
        $query = "SELECT * FROM documents_personnels 
                  WHERE usr_id = :userId 
                  ORDER BY date_upload DESC";
        $result = $this->db->query($query, ['userId' => $userId]);
        $this->db->disconnect();
        return $result;
    }

    public function addDocument($userId, $titre, $type, $fichier, $tailleFichier)
    {
        $this->db->connect();
        $query = "INSERT INTO documents_personnels (usr_id, titre, type, fichier, taille_fichier)
                  VALUES (:userId, :titre, :type, :fichier, :taille)";
        $result = $this->db->query($query, [
            'userId' => $userId,
            'titre' => $titre,
            'type' => $type,
            'fichier' => $fichier,
            'taille' => $tailleFichier
        ]);
        $this->db->disconnect();
        return $result !== false;
    }
    
    /**
     * Supprimer un document personnel
     */
    public function deleteDocument($documentId, $userId)
    {
        $this->db->connect();
        $query = "DELETE FROM documents_personnels 
                  WHERE id_document = :docId AND usr_id = :userId";
        $result = $this->db->query($query, [
            'docId' => $documentId,
            'userId' => $userId
        ]);
        $this->db->disconnect();
        return $result !== false;
    }
    
    public function usernameExists($username, $excludeUserId = null)
    {
        $this->db->connect();
        $query = "SELECT id_user FROM users WHERE username = :username AND is_deleted = 0";
        $params = ['username' => $username];
        
        if ($excludeUserId) {
            $query .= " AND id_user != :excludeId";
            $params['excludeId'] = $excludeUserId;
        }
        
        $result = $this->db->query($query, $params);
        $this->db->disconnect();
        return !empty($result);
    }

    public function emailExists($email, $excludeUserId = null)
    {
        $this->db->connect();
        $query = "SELECT id_user FROM users WHERE email = :email AND is_deleted = 0";
        $params = ['email' => $email];
        
        if ($excludeUserId) {
            $query .= " AND id_user != :excludeId";
            $params['excludeId'] = $excludeUserId;
        }
        
        $result = $this->db->query($query, $params);
        $this->db->disconnect();
        return !empty($result);
    }
}
?>