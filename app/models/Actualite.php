<?php

class Actualite extends Model
{
    public function getAllForSlider()
    {
        $query = "SELECT a.*, t.libelle as type_libelle 
                  FROM actualites a 
                  LEFT JOIN types_actualites t ON a.type_actualite_id = t.id_type
                  WHERE a.afficher_diaporama = 1 
                  ORDER BY a.ordre_diaporama ASC, a.date_publication DESC";
        return $this->select($query);
    }
    
    public function getRecent($limit = 6)
    {
        return $this->selectAll('actualites', [], 'date_publication', 'DESC', $limit);
    }
    
    public function getById($id)
    {
        return $this->selectById('actualites', $id, 'id_actualite');
    }

    public function getAll()
    {
        return $this->selectAll('actualites', [], 'date_publication', 'DESC');
    }
    
    public function getByType($typeId, $limit = null)
    {
        return $this->selectAll('actualites', [
            'type_actualite_id' => $typeId
        ], 'date_publication', 'DESC', $limit);
    }

    public function searchByTitle($search)
    {
        return $this->search('actualites', 'titre', $search);
    }
    
    public function countByType($typeId)
    {
        return $this->count('actualites', ['type_actualite_id' => $typeId]);
    }
}
?>