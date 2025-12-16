<?php

class Offer extends Model
{
    public function getAllOpen()
    {
        return $this->selectAll('offres', [
            'statut' => 'ouverte',
            'is_deleted' => 0
        ], 'date_creation', 'DESC');
    }
    
    public function getAll()
    {
        return $this->selectAll('offres', ['is_deleted' => 0], 'date_creation', 'DESC');
    }

    public function getById($id)
    {
        return $this->selectById('offres', $id, 'id_offre');
    }
    
    public function getByType($type)
    {
        return $this->selectAll('offres', [
            'type' => $type,
            'statut' => 'ouverte',
            'is_deleted' => 0
        ], 'date_creation', 'DESC');
    }
    
    public function getByStatut($statut)
    {
        return $this->selectAll('offres', [
            'statut' => $statut,
            'is_deleted' => 0
        ], 'date_creation', 'DESC');
    }
    
    public function getByResponsable($responsableId)
    {
        return $this->selectAll('offres', [
            'responsable_id' => $responsableId,
            'is_deleted' => 0
        ], 'date_creation', 'DESC');
    }
    
    public function updateStatut($id, $statut)
    {
        $validStatuts = ['ouverte', 'fermee', 'pourvue'];
        if (!in_array($statut, $validStatuts)) {
            return false;
        }
        return $this->updateById('offres', $id, ['statut' => $statut], 'id_offre');
    }

    public function searchByTitle($search)
    {
        return $this->search('offres', 'titre', $search, ['is_deleted' => 0]);
    }
    
    public function countByType($type)
    {
        return $this->count('offres', [
            'type' => $type,
            'is_deleted' => 0
        ]);
    }

    public function countOpen()
    {
        return $this->count('offres', [
            'statut' => 'ouverte',
            'is_deleted' => 0
        ]);
    }
}
?>