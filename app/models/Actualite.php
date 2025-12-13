<?php

class Actualite
{
    private $db;
    
    public function __construct()
    {
        $this->db = new Database();
    }
    
    public function getAllForSlider()
    {
        $this->db->connect();
        $query = "SELECT a.*, t.libelle as type_libelle 
                  FROM actualites a 
                  LEFT JOIN types_actualites t ON a.type_actualite_id = t.id_type
                  WHERE a.afficher_diaporama = 1 
                  ORDER BY a.ordre_diaporama ASC, a.date_publication DESC";
        $result = $this->db->query($query);
        $this->db->disconnect();
        return $result;
    }
    
    public function getRecent($limit)
    {
        $this->db->connect();
        $limit = (int)$limit;
        $query = "SELECT a.*, t.libelle as type_libelle 
                  FROM actualites a 
                  LEFT JOIN types_actualites t ON a.type_actualite_id = t.id_type
                  ORDER BY a.date_publication DESC 
                  LIMIT $limit";
        $result = $this->db->query($query);
        $this->db->disconnect();
        return $result;
    }
    
    public function getById($id)
    {
        $this->db->connect();
        $query = "SELECT a.*, t.libelle as type_libelle 
                  FROM actualites a 
                  LEFT JOIN types_actualites t ON a.type_actualite_id = t.id_type
                  WHERE a.id_actualite = :id";
        $result = $this->db->query($query, ['id' => $id]);
        $this->db->disconnect();
        return $result[0] ?? null;
    }
}
?>