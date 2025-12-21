<?php

class HistoriqueEquipement extends Model
{
    public function log($equipementId, $userId, $action)
    {
        $validActions = [
            'reservation', 
            'annulation', 
            'debut_utilisation', 
            'fin_utilisation', 
            'maintenance', 
            'etat_change'
        ];
        
        if (!in_array($action, $validActions)) {
            error_log("Action invalide: $action");
            return false;
        }
        
        $data = [
            'equipement_id' => (int)$equipementId,
            'usr_id' => $userId ? (int)$userId : null,
            'action' => $action,
            'date_action' => date('Y-m-d H:i:s')
        ];
        
        return $this->insert('historique_equipements', $data);
    }
}
?>