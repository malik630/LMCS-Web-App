<?php

class Reservation extends Model
{
    public function create($data)
    {
        error_log("=== DEBUT Reservation->create() ===");
        
        // Valider les champs requis
        $requiredFields = ['equipement_id', 'usr_id', 'date_debut', 'date_fin'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                error_log("ERREUR: Champ requis manquant: $field");
                return false;
            }
        }
        error_log("Tous les champs requis sont présents");
        
        // Convertir le format datetime-local en format MySQL
        $dateDebut = str_replace('T', ' ', $data['date_debut']) . ':00';
        $dateFin = str_replace('T', ' ', $data['date_fin']) . ':00';
        
        error_log("Date début convertie: $dateDebut");
        error_log("Date fin convertie: $dateFin");
        
        // Vérifier les conflits
        if ($this->hasConflict($data['equipement_id'], $dateDebut, $dateFin)) {
            error_log("CONFLIT détecté pour équipement " . $data['equipement_id']);
            return false;
        }
        error_log("Aucun conflit détecté");
        
        // Préparer les données pour insertion
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
        
        error_log("Données préparées pour insertion:");
        error_log(print_r($insertData, true));
        
        // Insérer
        try {
            error_log("Appel de Model->insert()");
            $result = $this->insert('reservations', $insertData);
            
            if ($result) {
                error_log("=== SUCCESS: Réservation créée, ID: $result ===");
                
                // ✅ NOUVEAU: Logger dans l'historique
                require_once __DIR__ . '/HistoriqueEquipement.php';
                $historique = new HistoriqueEquipement();
                $historique->log($data['equipement_id'], $data['usr_id'], 'reservation');
                
            } else {
                error_log("=== ECHEC: insert() a retourné FALSE ===");
            }
            
            return $result;
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function hasConflict($equipementId, $dateDebut, $dateFin, $excludeReservationId = null)
    {
        error_log("Vérification conflit pour équipement $equipementId");
        
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
        
        error_log("Query: $query");
        error_log("Params: " . print_r($params, true));
        
        $result = $this->select($query, $params);
        $count = (int)($result[0]['total'] ?? 0);
        
        error_log("Nombre de conflits trouvés: $count");
        
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
                'confirmee' => 'reservation',
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
}
?>