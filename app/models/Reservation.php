<?php

class Reservation extends Model
{
    public function create($data)
    {
        $requiredFields = ['equipement_id', 'usr_id', 'date_debut', 'date_fin'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                error_log("ERREUR: Champ requis manquant: $field");
                return false;
            }
        }
        error_log("Tous les champs requis sont présents");
    
        $dateDebut = str_replace('T', ' ', $data['date_debut']) . ':00';
        $dateFin = str_replace('T', ' ', $data['date_fin']) . ':00';
    
        error_log("Date début convertie: $dateDebut");
        error_log("Date fin convertie: $dateFin");

        if ($this->hasConflict($data['equipement_id'], $dateDebut, $dateFin)) {
            error_log("CONFLIT détecté pour équipement " . $data['equipement_id']);
            return false;
        }

        $insertData = [
            'equipement_id' => (int)$data['equipement_id'],
            'usr_id' => (int)$data['usr_id'],
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'motif' => !empty($data['motif']) ? $data['motif'] : null,
            'nb_instances' => isset($data['nb_instances']) ? (int)$data['nb_instances'] : 1,
            'statut' => 'en_attente',
            'date_reservation' => date('Y-m-d H:i:s')
        ];
    
        try {
            $result = $this->insert('reservations', $insertData);
        
            if ($result) {
                error_log("SUCCESS: Réservation créée, ID: $result");
                require_once __DIR__ . '/HistoriqueEquipement.php';
                $historique = new HistoriqueEquipement();
                $historique->log($data['equipement_id'], $data['usr_id'], 'reservation');
            
            } else {
                error_log("ECHEC: insert() a retourné FALSE");
            }
        
            return $result;
        
        } catch (Exception $e) {
            error_log("EXCEPTION lors de la création de la réservation: " . $e->getMessage());
            return false;
        }
    }
    
    public function hasConflict($equipementId, $dateDebut, $dateFin, $nbInstances = 1, $excludeReservationId = null, $resoudre = 0)
    {
        require_once __DIR__ . '/Equipement.php';
        $equipementModel = new Equipement();
        $equipement = $equipementModel->getById($equipementId);
        
        if (!$equipement || !$equipement['capacite']) {
            return $this->hasSimpleConflict($equipementId, $dateDebut, $dateFin, $excludeReservationId, $resoudre);
        }
        
        $capaciteMax = (int)$equipement['capacite'];

        $query = "SELECT nb_instances 
                  FROM reservations 
                  WHERE equipement_id = :equipement_id 
                  AND statut = 'confirmee'
                  AND (
                      (date_debut < :date_fin AND date_fin > :date_debut)
                  )";

        $queryAdmin = " AND statut IN ('confirmee', 'en_attente')";
        $queryUser = " AND statut = 'confirmee'";

        if(!$resoudre){
            $query .= $queryUser;
        } else {
            $query .= $queryAdmin;
        }
        
        $params = [
            'equipement_id' => $equipementId,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin
        ];

        if ($excludeReservationId) {
            $query .= " AND id_reservation != :exclude_id";
            $params['exclude_id'] = $excludeReservationId;
        }
        
        $reservations = $this->select($query, $params);

        $totalInstancesReservees = 0;
        foreach ($reservations as $reservation) {
            $totalInstancesReservees += (int)$reservation['nb_instances'];
        }

        $totalAvecNouvelleReservation = $totalInstancesReservees + $nbInstances;
        
        $hasConflict = $totalAvecNouvelleReservation > $capaciteMax;
        
        if ($hasConflict) {
            error_log("Conflit détecté: Le total ($totalAvecNouvelleReservation) dépasse la capacité ($capaciteMax)");
        } else {
            error_log("Pas de conflit: Capacité disponible (" . ($capaciteMax - $totalInstancesReservees) . " instances restantes)");
        }
        
        return $hasConflict;
    }

    private function hasSimpleConflict($equipementId, $dateDebut, $dateFin, $excludeReservationId = null, $resoudre = 0)
    {
        $query = "SELECT COUNT(*) as total 
                  FROM reservations 
                  WHERE equipement_id = :equipement_id 
                  AND (
                      (date_debut <= :date_debut AND date_fin > :date_debut)
                      OR (date_debut < :date_fin AND date_fin >= :date_fin)
                      OR (date_debut >= :date_debut AND date_fin <= :date_fin)
                  )";
                  
        $queryAdmin = " AND statut IN ('confirmee', 'en_attente')";
        $queryUser = " AND statut = 'confirmee'";

        if(!$resoudre){
            $query .= $queryUser;
        } else {
            $query .= $queryAdmin;
        }

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
        $count = (int)($result[0]['total'] ?? 0);
        
        error_log("Conflit simple - Nombre de réservations trouvées: $count");
        
        return $count > 0;
    }
    
    public function getById($id)
    {
        $query = "SELECT r.*, 
                         e.nom as equipement_nom, e.localisation, e.capacite,
                         te.libelle as type_equipement,
                         u.nom as user_nom, u.prenom as user_prenom, u.email as user_email
                  FROM reservations r
                  JOIN equipements e ON r.equipement_id = e.id_equipement
                  LEFT JOIN types_equipements te ON e.type_equipement_id = te.id_type
                  JOIN users u ON r.usr_id = u.id_user
                  WHERE r.id_reservation = :id";
        $result = $this->select($query, ['id' => $id]);
        return $result[0] ?? null;
    }
    
    public function updateStatus($reservationId, $newStatus)
    {
        $validStatuses = ['en_attente', 'confirmee', 'annulee', 'terminee', 'demande_annulation'];
        if (!in_array($newStatus, $validStatuses)) {
            error_log("Statut invalide: $newStatus");
            return false;
        }
 
        $reservation = $this->getById($reservationId);
        if (!$reservation) {
            error_log("Réservation $reservationId introuvable");
            return false;
        }
        
        // Mettre à jour le statut
        $result = $this->updateById('reservations', $reservationId, ['statut' => $newStatus], 'id_reservation');
        
        if ($result) {
            require_once __DIR__ . '/HistoriqueEquipement.php';
            $historique = new HistoriqueEquipement();
            
            $action = match($newStatus) {
                'confirmee' => 'debut_utilisation',
                'annulee', 'demande_annulation' => 'annulation',
                'terminee' => 'fin_utilisation',
                default => null
            };
            
            if ($action) {
                $historique->log(
                    $reservation['equipement_id'], 
                    $reservation['usr_id'], 
                    $action
                );
            }
        }
        
        return $result;
    }

    public function getConflitsDetails()
    {
  
        $query = "SELECT r.*, 
                         e.nom as equipement_nom, e.capacite, e.type_equipement_id,
                         te.libelle as type_equipement,
                         u.nom as user_nom, u.prenom as user_prenom, u.email as user_email
                  FROM reservations r
                  JOIN equipements e ON r.equipement_id = e.id_equipement
                  LEFT JOIN types_equipements te ON e.type_equipement_id = te.id_type
                  JOIN users u ON r.usr_id = u.id_user
                  WHERE r.statut IN ('confirmee', 'en_attente')
                  ORDER BY r.equipement_id, r.date_debut";
        
        $reservations = $this->select($query);
        
        $conflits = [];
        $reservationsParEquipement = [];
        
        foreach ($reservations as $r) {
            $reservationsParEquipement[$r['equipement_id']][] = $r;
        }

        foreach ($reservationsParEquipement as $equipementId => $resa) {
            $equipement = $resa[0];
            if ($equipement['type_equipement'] === 'salles') {
                $groupes = $this->analyserChevauchementsSalles($resa);
            } else {
                $groupes = $this->analyserChevauchements($resa, $equipement['capacite']);
            }
            foreach ($groupes as $groupe) {
                if ($groupe['a_conflit']) {
                    $conflits[] = $groupe;
                }
            }
        }
        
        return $conflits;
    }

    private function analyserChevauchements($reservations, $capacite)
    {
        $groupes = [];
        
        for ($i = 0; $i < count($reservations); $i++) {
            $r1 = $reservations[$i];
            $groupe = [
                'equipement_id' => $r1['equipement_id'],
                'equipement_nom' => $r1['equipement_nom'],
                'type_equipement' => $r1['type_equipement'],
                'capacite' => $capacite,
                'reservations' => [$r1],
                'total_instances' => (int)$r1['nb_instances'],
                'a_conflit' => false
            ];

            for ($j = $i + 1; $j < count($reservations); $j++) {
                $r2 = $reservations[$j];
                
                if ($this->seChevauche($r1['date_debut'], $r1['date_fin'], $r2['date_debut'], $r2['date_fin'])) {
                    $groupe['reservations'][] = $r2;
                    $groupe['total_instances'] += (int)$r2['nb_instances'];
                }
            }

            if ($groupe['total_instances'] > $capacite && count($groupe['reservations']) > 1) {
                $groupe['a_conflit'] = true;
                $groupe['depassement'] = $groupe['total_instances'] - $capacite;
                $groupes[] = $groupe;
            }
        }
        
        return $groupes;
    }
    
    private function analyserChevauchementsSalles($reservations)
    {
        $groupes = [];
        
        for ($i = 0; $i < count($reservations); $i++) {
            $r1 = $reservations[$i];
            $groupe = [
                'equipement_id' => $r1['equipement_id'],
                'equipement_nom' => $r1['equipement_nom'],
                'type_equipement' => $r1['type_equipement'],
                'capacite' => $r1['capacite'] ?? 1,
                'reservations' => [$r1],
                'total_instances' => 1,
                'a_conflit' => false
            ];
            
            for ($j = $i + 1; $j < count($reservations); $j++) {
                $r2 = $reservations[$j];
                
                if ($this->seChevauche($r1['date_debut'], $r1['date_fin'], $r2['date_debut'], $r2['date_fin'])) {
                    $groupe['reservations'][] = $r2;
                    $groupe['a_conflit'] = true;
                }
            }
            
            if ($groupe['a_conflit']) {
                $groupes[] = $groupe;
            }
        }
        
        return $groupes;
    }
    

    private function seChevauche($debut1, $fin1, $debut2, $fin2)
    {
        return (strtotime($debut1) < strtotime($fin2) && strtotime($fin1) > strtotime($debut2));
    }
    

    public function rejeterReservationsIncompatibles($reservationId)
    {
        $reservation = $this->getById($reservationId);
        if (!$reservation) {
            return 0;
        }

        require_once __DIR__ . '/Equipement.php';
        $equipementModel = new Equipement();
        $equipement = $equipementModel->getById($reservation['equipement_id']);
        
        if (!$equipement) {
            return 0;
        }
        
        $rejets = 0;

        // Pour les salles
        if ($equipement['type_libelle'] === 'salles') {
            $query = "SELECT id_reservation 
                      FROM reservations 
                      WHERE equipement_id = :equipement_id 
                      AND statut = 'en_attente'
                      AND id_reservation != :reservation_id
                      AND (date_debut < :date_fin AND date_fin > :date_debut)";
            
            $params = [
                'equipement_id' => $reservation['equipement_id'],
                'reservation_id' => $reservationId,
                'date_debut' => $reservation['date_debut'],
                'date_fin' => $reservation['date_fin']
            ];
            
            $incompatibles = $this->select($query, $params);
            
            foreach ($incompatibles as $inc) {
                if ($this->updateStatus($inc['id_reservation'], 'annulee')) {
                    $rejets++;
                }
            }
        } 

        // Pour les équipements autres que les salles
        else if ($equipement['capacite']) {
            $query = "SELECT SUM(nb_instances) as total 
                      FROM reservations 
                      WHERE equipement_id = :equipement_id 
                      AND statut = 'confirmee'
                      AND (date_debut < :date_fin AND date_fin > :date_debut)";
            
            $result = $this->select($query, [
                'equipement_id' => $reservation['equipement_id'],
                'date_debut' => $reservation['date_debut'],
                'date_fin' => $reservation['date_fin']
            ]);
            
            $totalConfirmees = (int)($result[0]['total'] ?? 0);
            $capaciteRestante = $equipement['capacite'] - $totalConfirmees;

            $query = "SELECT id_reservation, nb_instances 
                      FROM reservations 
                      WHERE equipement_id = :equipement_id 
                      AND statut = 'en_attente'
                      AND id_reservation != :reservation_id
                      AND (date_debut < :date_fin AND date_fin > :date_debut)
                      ORDER BY date_reservation ASC";
            
            $enAttente = $this->select($query, [
                'equipement_id' => $reservation['equipement_id'],
                'reservation_id' => $reservationId,
                'date_debut' => $reservation['date_debut'],
                'date_fin' => $reservation['date_fin']
            ]);
            
            // Rejeter celles qui dépassent la capacité restante
            foreach ($enAttente as $demande) {
                if ((int)$demande['nb_instances'] > $capaciteRestante) {
                    if ($this->updateStatus($demande['id_reservation'], 'annulee')) {
                        $rejets++;
                    }
                }
            }
        }
        
        return $rejets;
    }

    public function getByEquipement($equipementId)
    {
        $query = "SELECT r.*, 
                        u.nom as user_nom, u.prenom as user_prenom
                FROM reservations r
                JOIN users u ON r.usr_id = u.id_user
                WHERE r.equipement_id = :equipement_id 
                AND r.statut = 'confirmee'
                AND r.date_fin >= NOW()
                ORDER BY r.date_debut ASC";
    
        return $this->select($query, ['equipement_id' => $equipementId]);
    }
    
    public function getAllWithDetails()
    {
        $query = "SELECT r.*, 
                         e.nom as equipement_nom, e.localisation, e.capacite,
                         te.libelle as type_equipement,
                         u.nom as user_nom, u.prenom as user_prenom, u.email as user_email
                  FROM reservations r
                  JOIN equipements e ON r.equipement_id = e.id_equipement
                  LEFT JOIN types_equipements te ON e.type_equipement_id = te.id_type
                  JOIN users u ON r.usr_id = u.id_user
                  ORDER BY 
                    CASE r.statut
                        WHEN 'en_attente' THEN 1
                        WHEN 'demande_annulation' THEN 2
                        WHEN 'confirmee' THEN 3
                        ELSE 4
                    END,
                    r.date_debut DESC";
        return $this->select($query);
    }
    
    public function countActiveByEquipement($equipementId)
    {
        $query = "SELECT COUNT(*) as total
                  FROM reservations
                  WHERE equipement_id = :equipement_id
                  AND statut IN ('confirmee', 'en_attente')
                  AND date_fin >= NOW()";
        
        $result = $this->select($query, ['equipement_id' => $equipementId]);
        return (int)($result[0]['total'] ?? 0);
    }
}
?>