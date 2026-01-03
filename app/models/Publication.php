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
    
    public function getAllAdmin()
    {
        $query = "SELECT p.*, 
                         tp.libelle as type_libelle,
                         pr.titre as projet_titre,
                         GROUP_CONCAT(
                             CONCAT(u.prenom, ' ', u.nom) 
                             ORDER BY pa.ordre_auteur 
                             SEPARATOR ', '
                         ) as auteurs,
                         COUNT(DISTINCT pa.usr_id) as nb_auteurs
                  FROM publications p
                  LEFT JOIN types_publications tp ON p.type_publication_id = tp.id_type
                  LEFT JOIN projets pr ON p.projet_id = pr.id_projet
                  LEFT JOIN publication_auteurs pa ON p.id_publication = pa.publication_id AND pa.is_deleted = 0
                  LEFT JOIN users u ON pa.usr_id = u.id_user
                  WHERE p.is_deleted = 0
                  GROUP BY p.id_publication
                  ORDER BY p.date_soumission DESC";
        
        return $this->select($query);
    }
    
    public function getPending()
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
                  WHERE p.is_deleted = 0 AND p.statut = 'en_attente'
                  GROUP BY p.id_publication
                  ORDER BY p.date_soumission ASC";
        
        return $this->select($query);
    }
    
    public function getPublished()
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
                  ORDER BY p.date_publication DESC";
        
        return $this->select($query);
    }
    
    public function getRejected()
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
                  WHERE p.is_deleted = 0 AND p.statut = 'rejete'
                  GROUP BY p.id_publication
                  ORDER BY p.date_soumission DESC";
        
        return $this->select($query);
    }
    
    public function getByIdAdmin($id)
    {
        $query = "SELECT p.*, 
                         tp.libelle as type_libelle,
                         pr.titre as projet_titre
                  FROM publications p
                  LEFT JOIN types_publications tp ON p.type_publication_id = tp.id_type
                  LEFT JOIN projets pr ON p.projet_id = pr.id_projet
                  WHERE p.id_publication = :id AND p.is_deleted = 0";
        
        $result = $this->select($query, ['id' => $id]);
        return $result[0] ?? null;
    }
    
    public function getAuteurs($publicationId)
    {
        $query = "SELECT u.id_user, u.nom, u.prenom, pa.ordre_auteur
                  FROM publication_auteurs pa
                  JOIN users u ON pa.usr_id = u.id_user
                  WHERE pa.publication_id = :id AND pa.is_deleted = 0
                  ORDER BY pa.ordre_auteur ASC";
        
        return $this->select($query, ['id' => $publicationId]);
    }
    
    public function getByYear($year)
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
                  WHERE p.is_deleted = 0 AND p.statut = 'publie' AND p.annee = :year
                  GROUP BY p.id_publication
                  ORDER BY p.date_publication DESC";
        
        return $this->select($query, ['year' => $year]);
    }
    
    public function getByAuthor($authorId)
    {
        $query = "SELECT p.*, 
                         tp.libelle as type_libelle,
                         GROUP_CONCAT(
                             CONCAT(u.prenom, ' ', u.nom) 
                             ORDER BY pa.ordre_auteur 
                             SEPARATOR ', '
                         ) as auteurs,
                         pa2.ordre_auteur
                  FROM publications p
                  LEFT JOIN types_publications tp ON p.type_publication_id = tp.id_type
                  JOIN publication_auteurs pa2 ON p.id_publication = pa2.publication_id 
                      AND pa2.usr_id = :authorId AND pa2.is_deleted = 0
                  LEFT JOIN publication_auteurs pa ON p.id_publication = pa.publication_id AND pa.is_deleted = 0
                  LEFT JOIN users u ON pa.usr_id = u.id_user
                  WHERE p.is_deleted = 0 AND p.statut = 'publie'
                  GROUP BY p.id_publication
                  ORDER BY p.annee DESC, p.date_publication DESC";
        
        return $this->select($query, ['authorId' => $authorId]);
    }
}
?>