<?php

class Publication extends Model
{
    public function getAll()
    {
        $query = "SELECT p.*, 
                         tp.libelle as type_libelle,
                         GROUP_CONCAT(
                             CONCAT(u.prenom, ' ', u.nom) 
                             ORDER BY pa.ordre_auteur 
                             SEPARATOR ', '
                         ) as auteurs
                  FROM publications p
                  LEFT JOIN types_publications tp ON p.type_publication_id = tp.id_type
                  LEFT JOIN publication_auteurs pa ON p.id_publication = pa.publication_id AND pa.is_deleted = 0
                  LEFT JOIN users u ON pa.usr_id = u.id_user
                  WHERE p.is_deleted = 0 AND p.statut = 'publie'
                  GROUP BY p.id_publication
                  ORDER BY p.annee DESC, p.date_publication DESC";
        
        return $this->select($query);
    }
    
    public function getTypes()
    {
        return $this->selectAll('types_publications', [], 'libelle', 'ASC');
    }
    
    public function getAllAuthors()
    {
        $query = "SELECT DISTINCT u.id_user, u.nom, u.prenom
                  FROM users u
                  JOIN publication_auteurs pa ON u.id_user = pa.usr_id
                  JOIN publications p ON pa.publication_id = p.id_publication
                  WHERE p.is_deleted = 0 AND p.statut = 'publie' AND pa.is_deleted = 0
                  ORDER BY u.nom, u.prenom";
        
        return $this->select($query);
    }
    
    public function getYears()
    {
        $query = "SELECT DISTINCT annee 
                  FROM publications 
                  WHERE is_deleted = 0 AND statut = 'publie'
                  ORDER BY annee DESC";
        
        return $this->select($query);
    }
    
    public function getDomains()
    {
        $query = "SELECT DISTINCT domaine 
                  FROM publications 
                  WHERE is_deleted = 0 AND statut = 'publie' AND domaine IS NOT NULL
                  ORDER BY domaine";
        
        return $this->select($query);
    }
}
?>