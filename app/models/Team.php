<?php

class Team
{
    private $db;
    
    public function __construct()
    {
        $this->db = new Database();
    }
    
    public function getAll()
    {
        $this->db->connect();
        $query = "SELECT t.*, 
                         u.nom as chef_nom, u.prenom as chef_prenom, u.photo as chef_photo
                  FROM teams t 
                  LEFT JOIN users u ON t.chef_id = u.id_user
                  WHERE t.is_deleted = 0 
                  ORDER BY t.nom ASC";
        $result = $this->db->query($query);
        $this->db->disconnect();
        return $result;
    }
    
    public function getById($id)
    {
        $this->db->connect();
        $query = "SELECT t.*, 
                         u.nom as chef_nom, u.prenom as chef_prenom, u.photo as chef_photo,
                         u.grade as chef_grade, u.email as chef_email
                  FROM teams t 
                  LEFT JOIN users u ON t.chef_id = u.id_user
                  WHERE t.id_team = :id AND t.is_deleted = 0";
        $result = $this->db->query($query, ['id' => $id]);
        $this->db->disconnect();
        return $result[0] ?? null;
    }
    
    public function getMembers($teamId)
    {
        $this->db->connect();
        $query = "SELECT u.*, tm.role_dans_equipe, tm.date_adhesion
                  FROM team_members tm
                  JOIN users u ON tm.usr_id = u.id_user
                  WHERE tm.team_id = :teamId AND tm.is_deleted = 0 AND u.is_deleted = 0
                  ORDER BY u.nom ASC, u.prenom ASC";
        $result = $this->db->query($query, ['teamId' => $teamId]);
        $this->db->disconnect();
        return $result;
    }
    
    public function searchTeams($search = '', $sortBy = 'nom', $sortOrder = 'ASC')
    {
        $this->db->connect();
        $allowedSort = ['nom', 'thematique', 'date_creation'];
        $sortBy = in_array($sortBy, $allowedSort) ? $sortBy : 'nom';
        $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';
        
        $query = "SELECT t.*, 
                         u.nom as chef_nom, u.prenom as chef_prenom
                  FROM teams t 
                  LEFT JOIN users u ON t.chef_id = u.id_user
                  WHERE t.is_deleted = 0 
                  AND (t.nom LIKE :search OR t.thematique LIKE :search OR t.description LIKE :search)
                  ORDER BY t.$sortBy $sortOrder";
        
        $result = $this->db->query($query, ['search' => "%$search%"]);
        $this->db->disconnect();
        return $result;
    }
}
?>