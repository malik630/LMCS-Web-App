<?php

class Equipement extends Model
{
    public function getAll()
    {
        $query = "SELECT e.*, te.libelle as type_libelle
                  FROM equipements e
                  LEFT JOIN types_equipements te ON e.type_equipement_id = te.id_type
                  WHERE e.is_deleted = 0 
                  ORDER BY e.nom ASC";
        return $this->select($query);
    }
    
    public function getById($id)
    {
        $query = "SELECT e.*, te.libelle as type_libelle
                  FROM equipements e
                  LEFT JOIN types_equipements te ON e.type_equipement_id = te.id_type
                  WHERE e.id_equipement = :id AND e.is_deleted = 0";
        $result = $this->select($query, ['id' => $id]);
        return $result[0] ?? null;
    }

    public function getAvailable()
    {
        $query = "SELECT e.*, te.libelle as type_libelle
                  FROM equipements e
                  LEFT JOIN types_equipements te ON e.type_equipement_id = te.id_type
                  WHERE e.etat = 'libre' AND e.is_deleted = 0
                  ORDER BY e.nom ASC";
        return $this->select($query);
    }
    
    public function getByType($typeId)
    {
        $query = "SELECT e.*, te.libelle as type_libelle
                  FROM equipements e
                  LEFT JOIN types_equipements te ON e.type_equipement_id = te.id_type
                  WHERE e.type_equipement_id = :typeId AND e.is_deleted = 0
                  ORDER BY e.nom ASC";
        return $this->select($query, ['typeId' => $typeId]);
    }
    
    public function getByEtat($etat)
    {
        $query = "SELECT e.*, te.libelle as type_libelle
                  FROM equipements e
                  LEFT JOIN types_equipements te ON e.type_equipement_id = te.id_type
                  WHERE e.etat = :etat AND e.is_deleted = 0
                  ORDER BY e.nom ASC";
        return $this->select($query, ['etat' => $etat]);
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
                  AND statut IN ('confirmee', 'en_attente')
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
        return (int)($result[0]['total'] ?? 0) === 0;
    }
    
    public function getWithReservations($limit = 10)
    {
        $query = "SELECT e.*, te.libelle as type_libelle,
                         COUNT(r.id_reservation) as nb_reservations,
                         MAX(r.date_debut) as derniere_reservation
                  FROM equipements e
                  LEFT JOIN types_equipements te ON e.type_equipement_id = te.id_type
                  LEFT JOIN reservations r ON e.id_equipement = r.equipement_id
                  WHERE e.is_deleted = 0
                  GROUP BY e.id_equipement
                  ORDER BY nb_reservations DESC
                  LIMIT " . (int)$limit;
        return $this->select($query);
    }
    
    public function getTypes()
    {
        return $this->selectAll('types_equipements', [], 'libelle', 'ASC');
    }

    public function getRapportUtilisation($dateDebut, $dateFin)
    {
        $query = "SELECT e.nom as equipement, e.localisation,
                         COUNT(r.id_reservation) as nb_reservations,
                         SUM(TIMESTAMPDIFF(HOUR, r.date_debut, r.date_fin)) as heures_total,
                         COUNT(DISTINCT r.usr_id) as nb_utilisateurs
                  FROM equipements e
                  LEFT JOIN reservations r ON e.id_equipement = r.equipement_id
                      AND r.date_debut BETWEEN :debut AND :fin
                      AND r.statut IN ('confirmee', 'terminee')
                  WHERE e.is_deleted = 0
                  GROUP BY e.id_equipement
                  ORDER BY nb_reservations DESC";
        
        return $this->select($query, ['debut' => $dateDebut, 'fin' => $dateFin]);
    }
    
    public function getRapportParUtilisateur($dateDebut, $dateFin)
    {
        $query = "SELECT u.nom, u.prenom, u.email,
                         COUNT(r.id_reservation) as nb_demandes,
                         SUM(TIMESTAMPDIFF(HOUR, r.date_debut, r.date_fin)) as heures_total
                  FROM users u
                  JOIN reservations r ON u.id_user = r.usr_id
                  WHERE r.date_debut BETWEEN :debut AND :fin
                  GROUP BY u.id_user
                  ORDER BY nb_demandes DESC";
        
        return $this->select($query, ['debut' => $dateDebut, 'fin' => $dateFin]);
    }
    
    public function getHistorique($limit = 100)
    {
        $query = "SELECT h.*, e.nom as equipement_nom, te.libelle as type_equipement,
                         u.nom as user_nom, u.prenom as user_prenom
                  FROM historique_equipements h
                  JOIN equipements e ON h.equipement_id = e.id_equipement
                  LEFT JOIN types_equipements te ON e.type_equipement_id = te.id_type
                  LEFT JOIN users u ON h.usr_id = u.id_user
                  ORDER BY h.date_action DESC
                  LIMIT " . (int)$limit;
        
        return $this->select($query);
    }
    
    public function getHistoriqueByEquipement($equipementId, $limit = 100)
    {
        $query = "SELECT h.*, e.nom as equipement_nom, te.libelle as type_equipement,
                         u.nom as user_nom, u.prenom as user_prenom
                  FROM historique_equipements h
                  JOIN equipements e ON h.equipement_id = e.id_equipement
                  LEFT JOIN types_equipements te ON e.type_equipement_id = te.id_type
                  LEFT JOIN users u ON h.usr_id = u.id_user
                  WHERE h.equipement_id = :equipement_id
                  ORDER BY h.date_action DESC
                  LIMIT " . (int)$limit;
        
        return $this->select($query, ['equipement_id' => $equipementId]);
    }
}
?>