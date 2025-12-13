<?php

class Event
{
    private $db;
    
    public function __construct()
    {
        $this->db = new Database();
    }
    
    public function getUpcoming()
    {
        $this->db->connect();
        $query = "SELECT e.*, te.libelle as type_libelle,
                         u.nom as organisateur_nom, u.prenom as organisateur_prenom
                  FROM evenements e
                  LEFT JOIN types_evenements te ON e.type_evenement_id = te.id_type
                  LEFT JOIN users u ON e.organisateur_id = u.id_user
                  WHERE e.statut = 'a_venir'
                  ORDER BY e.date_debut ASC";
        $result = $this->db->query($query);
        $this->db->disconnect();
        return $result;
    }
    
    public function getExterne($limit = null)
    {
        $this->db->connect();
        $query = "SELECT e.*, te.libelle as type_libelle,
                         u.nom as organisateur_nom, u.prenom as organisateur_prenom
                  FROM evenements e
                  LEFT JOIN types_evenements te ON e.type_evenement_id = te.id_type
                  LEFT JOIN users u ON e.organisateur_id = u.id_user
                  WHERE e.externe = 1 AND e.statut != 'annule'
                  ORDER BY e.date_debut DESC";
        
        if ($limit) {
            $limit = (int)$limit;
            $query .= " LIMIT $limit";
        }
        
        $result = $this->db->query($query);
        $this->db->disconnect();
        return $result;
    }
    
    public function getAll($filters = [])
    {
        $this->db->connect();
        
        $where = "WHERE 1=1";
        $params = [];
        
        if (!empty($filters['type'])) {
            $where .= " AND e.type_evenement_id = :type";
            $params['type'] = $filters['type'];
        }
        
        if (!empty($filters['statut'])) {
            $where .= " AND e.statut = :statut";
            $params['statut'] = $filters['statut'];
        }
        
        if (!empty($filters['search'])) {
            $where .= " AND (e.titre LIKE :search OR e.description LIKE :search)";
            $params['search'] = "%{$filters['search']}%";
        }
        
        $sortBy = $filters['sortBy'] ?? 'date_debut';
        $sortOrder = strtoupper($filters['sortOrder'] ?? 'DESC');
        $allowedSort = ['date_debut', 'titre', 'type_evenement_id'];
        $sortBy = in_array($sortBy, $allowedSort) ? $sortBy : 'date_debut';
        $sortOrder = $sortOrder === 'ASC' ? 'ASC' : 'DESC';
        
        $query = "SELECT e.*, te.libelle as type_libelle,
                         u.nom as organisateur_nom, u.prenom as organisateur_prenom
                  FROM evenements e
                  LEFT JOIN types_evenements te ON e.type_evenement_id = te.id_type
                  LEFT JOIN users u ON e.organisateur_id = u.id_user
                  $where
                  ORDER BY e.$sortBy $sortOrder";
        
        $result = $this->db->query($query, $params);
        $this->db->disconnect();
        return $result;
    }
    
    public function getTypes()
    {
        $this->db->connect();
        $query = "SELECT * FROM types_evenements ORDER BY libelle ASC";
        $result = $this->db->query($query);
        $this->db->disconnect();
        return $result;
    }
}
?>