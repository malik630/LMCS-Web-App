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

}
?>