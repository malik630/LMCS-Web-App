<?php

class Partner extends Model
{
    public function getAll()
    {
        return $this->selectAll('partenaires', ['is_deleted' => 0], 'nom', 'ASC');
    }
    
    public function getById($id)
    {
        return $this->selectById('partenaires', $id, 'id_partenaire');
    }
    
    public function getByType($type)
    {
        return $this->selectAll('partenaires', [
            'type' => $type,
            'is_deleted' => 0
        ], 'nom', 'ASC');
    }
    
    public function getByPays($pays)
    {
        return $this->selectAll('partenaires', [
            'pays' => $pays,
            'is_deleted' => 0
        ], 'nom', 'ASC');
    }
    
    public function searchByName($search)
    {
        return $this->search('partenaires', 'nom', $search, ['is_deleted' => 0]);
    }

    public function countByType($type)
    {
        return $this->count('partenaires', [
            'type' => $type,
            'is_deleted' => 0
        ]);
    }

    public function partnerExists($id)
    {
        return $this->exists('partenaires', $id, 'id_partenaire');
    }
}
?>