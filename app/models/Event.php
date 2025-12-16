<?php

class Event extends Model
{
    public function getAll()
    {
        return $this->selectAll('evenements', [], 'date_debut', 'DESC');
    }
    
    public function getById($id)
    {
        return $this->selectById('evenements', $id, 'id_evenement');
    }
    
    public function getUpcoming()
    {
        return $this->selectAll('evenements', ['statut' => 'a_venir'], 'date_debut', 'ASC');
    }
    
    public function getExterne($limit = null)
    {
        $query = "SELECT * FROM evenements
                  WHERE externe = 1 AND statut != 'annule'
                  ORDER BY date_debut DESC";
        
        if ($limit) {
            $query .= " LIMIT " . (int)$limit;
        }
        
        return $this->select($query);
    }
    
    public function getByType($typeId)
    {
        return $this->selectAll('evenements', [
            'type_evenement_id' => $typeId
        ], 'date_debut', 'DESC');
    }
    
    public function getByStatut($statut)
    {
        return $this->selectAll('evenements', ['statut' => $statut], 'date_debut', 'DESC');
    }
    
    public function getByOrganisateur($organisateurId)
    {
        return $this->selectAll('evenements', [
            'organisateur_id' => $organisateurId
        ], 'date_debut', 'DESC');
    }

    public function searchByTitle($search)
    {
        return $this->search('evenements', 'titre', $search);
    }
    
    public function updateStatut($id, $statut)
    {
        $validStatuts = ['a_venir', 'en_cours', 'termine', 'annule'];
        if (!in_array($statut, $validStatuts)) {
            return false;
        }
        return $this->updateById('evenements', $id, ['statut' => $statut], 'id_evenement');
    }

    public function countUpcoming()
    {
        return $this->count('evenements', ['statut' => 'a_venir']);
    }
    
    public function countByType($typeId)
    {
        return $this->count('evenements', ['type_evenement_id' => $typeId]);
    }
    
    public function eventExists($id)
    {
        return $this->exists('evenements', $id, 'id_evenement');
    }
 
    public function getInscriptions($eventId)
    {
        $query = "SELECT ie.*, u.nom, u.prenom, u.email as user_email
                  FROM inscriptions_evenements ie
                  LEFT JOIN users u ON ie.usr_id = u.id_user
                  WHERE ie.evenement_id = :event_id
                  ORDER BY ie.date_inscription DESC";
        return $this->select($query, ['event_id' => $eventId]);
    }

    public function countInscriptions($eventId)
    {
        $query = "SELECT COUNT(*) as total FROM inscriptions_evenements 
                  WHERE evenement_id = :event_id AND statut != 'annulee'";
        $result = $this->select($query, ['event_id' => $eventId]);
        return (int)($result[0]['total'] ?? 0);
    }
    
    public function inscrire($eventId, $userId = null, $nom = null, $email = null)
    {
        return $this->insert('inscriptions_evenements', [
            'evenement_id' => $eventId,
            'usr_id' => $userId,
            'nom' => $nom,
            'email' => $email
        ]);
    }
    
    public function isInscrit($eventId, $userId = null, $email = null)
    {
        if ($userId) {
            $query = "SELECT COUNT(*) as total FROM inscriptions_evenements 
                      WHERE evenement_id = :event_id AND usr_id = :user_id 
                      AND statut != 'annulee'";
            $params = ['event_id' => $eventId, 'user_id' => $userId];
        } else {
            $query = "SELECT COUNT(*) as total FROM inscriptions_evenements 
                      WHERE evenement_id = :event_id AND email = :email 
                      AND statut != 'annulee'";
            $params = ['event_id' => $eventId, 'email' => $email];
        }
        
        $result = $this->select($query, $params);
        return (int)$result[0]['total'] > 0;
    }
}
?>