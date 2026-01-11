<?php

class Event extends Model
{
    public function getAllTypes()
    {
        return $this->selectAll('types_evenements', [], 'libelle', 'ASC');
    }
    
    public function create(array $data)
    {
        return $this->insert('evenements', $data);
    }
    
    public function updateEvent($eventId, array $data)
    {
        return $this->updateById('evenements', $eventId, $data, 'id_evenement');
    }
    
    public function deleteEvent($eventId)
    {
        return $this->softDelete('evenements', $eventId, 'id_evenement');
    }
    
    public function getAllWithType()
    {
        $query = "SELECT e.*, te.libelle as type_libelle,
                         u.nom as organisateur_nom, u.prenom as organisateur_prenom
                  FROM evenements e
                  LEFT JOIN types_evenements te ON e.type_evenement_id = te.id_type
                  LEFT JOIN users u ON e.organisateur_id = u.id_user
                  WHERE e.is_deleted = 0
                  ORDER BY e.date_debut DESC";
        return $this->select($query);
    }

    public function getAll()
    {
        return $this->selectAll('evenements', ['is_deleted' => 0], 'date_debut', 'DESC');
    }
    
    public function getById($id)
    {
        $query = "SELECT e.*, te.libelle as type_libelle,
                         u.nom as organisateur_nom, u.prenom as organisateur_prenom
                  FROM evenements e
                  LEFT JOIN types_evenements te ON e.type_evenement_id = te.id_type
                  LEFT JOIN users u ON e.organisateur_id = u.id_user
                  WHERE e.id_evenement = :id AND e.is_deleted = 0";
        $result = $this->select($query, ['id' => $id]);
        return $result[0] ?? null;
    }
    
    public function getUpcoming()
    {
        return $this->selectAll('evenements', [
            'statut' => 'a_venir',
            'is_deleted' => 0
        ], 'date_debut', 'ASC');
    }
    
    public function getExterne($limit = null)
    {
        $query = "SELECT e.*, te.libelle as type_libelle
                  FROM evenements e
                  LEFT JOIN types_evenements te ON e.type_evenement_id = te.id_type
                  WHERE e.externe = 1 
                  AND e.statut != 'annule'
                  AND e.is_deleted = 0
                  ORDER BY e.date_debut DESC";
        
        if ($limit) {
            $query .= " LIMIT " . (int)$limit;
        }
        
        return $this->select($query);
    }
    
    public function getByType($typeId)
    {
        return $this->selectAll('evenements', [
            'type_evenement_id' => $typeId,
            'is_deleted' => 0
        ], 'date_debut', 'DESC');
    }
    
    public function getByStatut($statut)
    {
        return $this->selectAll('evenements', [
            'statut' => $statut,
            'is_deleted' => 0
        ], 'date_debut', 'DESC');
    }
    
    public function getByOrganisateur($organisateurId)
    {
        return $this->selectAll('evenements', [
            'organisateur_id' => $organisateurId,
            'is_deleted' => 0
        ], 'date_debut', 'DESC');
    }

    public function searchByTitle($search)
    {
        return $this->search('evenements', 'titre', $search, ['is_deleted' => 0]);
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
        return $this->count('evenements', [
            'statut' => 'a_venir',
            'is_deleted' => 0
        ]);
    }
    
    public function countByType($typeId)
    {
        return $this->count('evenements', [
            'type_evenement_id' => $typeId,
            'is_deleted' => 0
        ]);
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
                  WHERE evenement_id = :event_id 
                  AND statut = 'confirmee'";
        $result = $this->select($query, ['event_id' => $eventId]);
        return (int)($result[0]['total'] ?? 0);
    }
    
    public function inscrire($eventId, $userId = null, $nom = null, $email = null)
    {
        return $this->insert('inscriptions_evenements', [
            'evenement_id' => $eventId,
            'usr_id' => $userId,
            'nom' => $nom,
            'email' => $email,
            'statut' => 'en_attente',
            'date_inscription' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function isInscrit($eventId, $userId = null, $email = null)
    {
        if ($userId) {
            $query = "SELECT COUNT(*) as total FROM inscriptions_evenements 
                      WHERE evenement_id = :event_id AND usr_id = :user_id 
                      AND statut IN ('en_attente', 'confirmee')";
            $params = ['event_id' => $eventId, 'user_id' => $userId];
        } else {
            $query = "SELECT COUNT(*) as total FROM inscriptions_evenements 
                      WHERE evenement_id = :event_id AND email = :email 
                      AND statut IN ('en_attente', 'confirmee')";
            $params = ['event_id' => $eventId, 'email' => $email];
        }
        
        $result = $this->select($query, $params);
        return (int)$result[0]['total'] > 0;
    }

    public function getInscriptionById($id)
    {
        $query = "SELECT ie.*, e.titre as evenement_titre, e.date_debut, e.capacite_max
                  FROM inscriptions_evenements ie
                  JOIN evenements e ON ie.evenement_id = e.id_evenement
                  WHERE ie.id = :id";
        $result = $this->select($query, ['id' => $id]);
        return $result[0] ?? null;
    }

    public function updateInscriptionStatut($id, $statut)
    {
        $validStatuts = ['en_attente', 'confirmee', 'annulee', 'demande_annulation'];
        if (!in_array($statut, $validStatuts)) {
            return false;
        }
        return $this->updateById('inscriptions_evenements', $id, ['statut' => $statut]);
    }
}
?>