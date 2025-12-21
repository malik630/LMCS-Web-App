<?php

class Offer extends Model
{
    public function getAll()
    {
        return $this->selectAll('offres', ['is_deleted' => 0], 'date_creation', 'DESC');
    }

    public function getById($id)
    {
        return $this->selectById('offres', $id, 'id_offre');
    }
}
?>