<?php

class Equipement extends Model
{
    public function getAll()
    {
        return $this->selectAll('equipements', ['is_deleted' => 0], 'nom', 'ASC');
    }
    
    public function getById($id)
    {
        return $this->selectById('equipements', $id, 'id_equipement');
    }
    
    public function getAvailable()
    {
        return $this->selectAll('equipements', [
            'etat' => 'libre',
            'is_deleted' => 0
        ], 'nom', 'ASC');
    }
    
    public function getByType($typeId)
    {
        return $this->selectAll('equipements', [
            'type_equipement_id' => $typeId,
            'is_deleted' => 0
        ], 'nom', 'ASC');
    }
    
    public function getByEtat($etat)
    {
        return $this->selectAll('equipements', [
            'etat' => $etat,
            'is_deleted' => 0
        ], 'nom', 'ASC');
    }
    
    public function getByLocalisation($localisation)
    {
        return $this->selectAll('equipements', [
            'localisation' => $localisation,
            'is_deleted' => 0
        ], 'nom', 'ASC');
    }
    
    public function searchByName($search)
    {
        return $this->search('equipements', 'nom', $search, ['is_deleted' => 0]);
    }
    
    public function updateEtat($id, $etat)
    {
        $validEtats = ['libre', 'reserve', 'maintenance', 'hors_service'];
        if (!in_array($etat, $validEtats)) {
            return false;
        }
        return $this->updateById('equipements', $id, ['etat' => $etat], 'id_equipement');
    }
    
    public function countAvailable()
    {
        return $this->count('equipements', [
            'etat' => 'libre',
            'is_deleted' => 0
        ]);
    }
    
    public function countByType($typeId)
    {
        return $this->count('equipements', [
            'type_equipement_id' => $typeId,
            'is_deleted' => 0
        ]);
    }
    
    public function countInMaintenance()
    {
        return $this->count('equipements', [
            'etat' => 'maintenance',
            'is_deleted' => 0
        ]);
    }
    
    public function equipementExists($id)
    {
        return $this->exists('equipements', $id, 'id_equipement');
    }
    
    public function isAvailable($equipementId, $dateDebut, $dateFin, $excludeReservationId = null)
    {
        $query = "SELECT COUNT(*) as total 
                  FROM reservations 
                  WHERE equipement_id = :equipement_id 
                  AND statut = 'confirmee'
                  AND (
                      (date_debut <= :date_debut AND date_fin > :date_debut)
                      OR (date_debut < :date_fin AND date_fin >= :date_fin)
                      OR (date_debut >= :date_debut AND date_fin <= :date_fin)
                  )";
        
        $params = [
            'equipement_id' => $equipementId,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin
        ];
        
        if ($excludeReservationId) {
            $query .= " AND id_reservation != :exclude_id";
            $params['exclude_id'] = $excludeReservationId;
        }
        
        $result = $this->select($query, $params);
        return (int)$result[0]['total'] === 0;
    }
}
?>