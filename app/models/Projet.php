<?php

class Projet extends Model
{
    public function getAllWithDetails()
    {
        $query = "SELECT p.*, 
                         u.nom as responsable_nom, 
                         u.prenom as responsable_prenom,
                         u.id_user as responsable_id,
                         (SELECT COUNT(*) FROM projet_membres pm WHERE pm.projet_id = p.id_projet AND pm.is_deleted = 0) as nb_membres,
                         (SELECT COUNT(*) FROM publications pub WHERE pub.projet_id = p.id_projet AND pub.is_deleted = 0) as nb_publications
                  FROM projets p
                  LEFT JOIN users u ON p.responsable_id = u.id_user
                  WHERE p.is_deleted = 0
                  ORDER BY p.date_creation DESC";
        return $this->select($query);
    }
    
    public function filterProjets(array $filters)
    {
        $query = "SELECT p.*, 
                         u.nom as responsable_nom, 
                         u.prenom as responsable_prenom,
                         u.id_user as responsable_id,
                         (SELECT COUNT(*) FROM projet_membres pm WHERE pm.projet_id = p.id_projet AND pm.is_deleted = 0) as nb_membres,
                         (SELECT COUNT(*) FROM publications pub WHERE pub.projet_id = p.id_projet AND pub.is_deleted = 0) as nb_publications
                  FROM projets p
                  LEFT JOIN users u ON p.responsable_id = u.id_user
                  WHERE p.is_deleted = 0";
        
        $params = [];
        
        if (!empty($filters['thematique'])) {
            $query .= " AND p.thematique = :thematique";
            $params['thematique'] = $filters['thematique'];
        }
        
        if (!empty($filters['statut'])) {
            $query .= " AND p.statut = :statut";
            $params['statut'] = $filters['statut'];
        }
        
        if (!empty($filters['responsable_id'])) {
            $query .= " AND p.responsable_id = :responsable_id";
            $params['responsable_id'] = $filters['responsable_id'];
        }
        
        if (!empty($filters['search'])) {
            $query .= " AND (p.titre LIKE :search OR p.description LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $query .= " ORDER BY p.date_creation DESC";
        
        return $this->select($query, $params);
    }
    
    public function getById($id)
    {
        return $this->selectById('projets', $id, 'id_projet');
    }
    
    public function getMembers($projetId)
    {
        $query = "SELECT u.id_user, u.nom, u.prenom, u.email, u.photo, u.grade, pm.role_projet
                  FROM projet_membres pm
                  JOIN users u ON pm.usr_id = u.id_user
                  WHERE pm.projet_id = :projet_id 
                  AND pm.is_deleted = 0 
                  AND u.is_deleted = 0
                  ORDER BY u.nom ASC, u.prenom ASC";
        return $this->select($query, ['projet_id' => $projetId]);
    }
    
    public function getPublications($projetId)
    {
        $query = "SELECT pub.*, 
                         tp.libelle as type_libelle,
                         GROUP_CONCAT(CONCAT(u.prenom, ' ', u.nom) ORDER BY pa.ordre_auteur SEPARATOR ', ') as auteurs
                  FROM publications pub
                  LEFT JOIN types_publications tp ON pub.type_publication_id = tp.id_type
                  LEFT JOIN publication_auteurs pa ON pub.id_publication = pa.publication_id AND pa.is_deleted = 0
                  LEFT JOIN users u ON pa.usr_id = u.id_user AND u.is_deleted = 0
                  WHERE pub.projet_id = :projet_id 
                  AND pub.is_deleted = 0
                  GROUP BY pub.id_publication
                  ORDER BY pub.annee DESC, pub.date_publication DESC";
        return $this->select($query, ['projet_id' => $projetId]);
    }
    
    public function getPartenaires($projetId)
    {
        $query = "SELECT part.*
                  FROM projet_partenaires pp
                  JOIN partenaires part ON pp.partenaire_id = part.id_partenaire
                  WHERE pp.projet_id = :projet_id 
                  AND pp.is_deleted = 0 
                  AND part.is_deleted = 0
                  ORDER BY part.nom ASC";
        return $this->select($query, ['projet_id' => $projetId]);
    }
    
    public function getAllThematiques()
    {
        $query = "SELECT DISTINCT thematique 
                  FROM projets 
                  WHERE is_deleted = 0 AND thematique IS NOT NULL AND thematique != ''
                  ORDER BY thematique ASC";
        return $this->select($query);
    }
    
    public function getAllResponsables()
    {
        $query = "SELECT DISTINCT u.id_user, u.nom, u.prenom
                  FROM projets p
                  JOIN users u ON p.responsable_id = u.id_user
                  WHERE p.is_deleted = 0 AND u.is_deleted = 0
                  ORDER BY u.nom ASC, u.prenom ASC";
        return $this->select($query);
    }
}
?>