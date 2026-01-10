<?php

class Team extends Model
{
    public function getAll()
    {
        return $this->selectAll('teams', ['is_deleted' => 0], 'nom', 'ASC');
    }
    
    public function getById($id)
    {
        return $this->selectById('teams', $id, 'id_team');
    }

    public function countAll()
    {
        return $this->count('teams', ['is_deleted' => 0]);
    }
    
    public function teamExists($id)
    {
        return $this->exists('teams', $id, 'id_team');
    }
    
    public function getMembers($teamId)
    {
        $query = "SELECT u.*, 
                         tm.role_dans_equipe, 
                         tm.date_adhesion, 
                         tm.id as member_id
                  FROM team_members tm
                  JOIN users u ON tm.usr_id = u.id_user
                  WHERE tm.team_id = :teamId 
                  AND tm.is_deleted = 0 
                  AND u.is_deleted = 0
                  ORDER BY u.nom ASC, u.prenom ASC";
        return $this->select($query, ['teamId' => $teamId]);
    }

    public function countMembers($teamId)
    {
        $query = "SELECT COUNT(*) as total 
                  FROM team_members tm
                  JOIN users u ON tm.usr_id = u.id_user
                  WHERE tm.team_id = :teamId 
                  AND tm.is_deleted = 0 
                  AND u.is_deleted = 0";
        $result = $this->select($query, ['teamId' => $teamId]);
        return (int)($result[0]['total'] ?? 0); 
    }

    public function addMember($teamId, $userId, $role = null)
    {
        $check = "SELECT COUNT(*) as total FROM team_members 
                  WHERE team_id = :team_id AND usr_id = :user_id AND is_deleted = 0";
        $exists = $this->select($check, ['team_id' => $teamId, 'user_id' => $userId]);
    
        if ((int)$exists[0]['total'] > 0) {
            return false;
        }
    
        return $this->insert('team_members', [
            'team_id' => $teamId, 
            'usr_id' => $userId, 
            'role_dans_equipe' => $role
        ]);
    }

    public function removeMember($teamId, $userId)
    {
        return $this->update('team_members', 
            ['is_deleted' => 1],
            ['team_id' => $teamId, 'usr_id' => $userId]
        );
    }

    public function updateMemberRole($teamId, $userId, $role)
    {
        return $this->update('team_members', 
            ['role_dans_equipe' => $role], 
            ['team_id' => $teamId, 'usr_id' => $userId, 'is_deleted' => 0]
        );
    }

    public function isMember($teamId, $userId)
    {
        $query = "SELECT COUNT(*) as total FROM team_members 
                  WHERE team_id = :team_id AND usr_id = :user_id 
                  AND is_deleted = 0";
        $result = $this->select($query, ['team_id' => $teamId, 'user_id' => $userId]);
        return (int)$result[0]['total'] > 0;
    }

    public function getTeamPublications($teamId)
    {
        $query = "SELECT DISTINCT pub.*, 
                         tp.libelle as type_libelle,
                         GROUP_CONCAT(DISTINCT CONCAT(u.prenom, ' ', u.nom) ORDER BY pa.ordre_auteur SEPARATOR ', ') as auteurs,
                         COUNT(DISTINCT pa.usr_id) as nb_auteurs_equipe
                  FROM publications pub
                  JOIN publication_auteurs pa ON pub.id_publication = pa.publication_id
                  JOIN users u ON pa.usr_id = u.id_user
                  JOIN team_members tm ON u.id_user = tm.usr_id
                  LEFT JOIN types_publications tp ON pub.type_publication_id = tp.id_type
                  WHERE tm.team_id = :teamId 
                  AND pub.is_deleted = 0 
                  AND pub.statut = 'publie'
                  AND pa.is_deleted = 0
                  AND tm.is_deleted = 0
                  GROUP BY pub.id_publication
                  ORDER BY pub.annee DESC, pub.date_publication DESC";
        return $this->select($query, ['teamId' => $teamId]);
    }
    
    public function getTeamProjets($teamId)
    {
        $membres = $this->getMembers($teamId);
        
        if (empty($membres)) {
            return [];
        }
        
        $membreIds = array_column($membres, 'id_user');
        $placeholders = implode(',', array_fill(0, count($membreIds), '?'));
        
        $query = "SELECT DISTINCT p.*, 
                         u.nom as responsable_nom, 
                         u.prenom as responsable_prenom,
                         COUNT(DISTINCT CASE WHEN pm.usr_id IN ($placeholders) THEN pm.usr_id END) as nb_membres_equipe
                  FROM projets p
                  LEFT JOIN users u ON p.responsable_id = u.id_user
                  LEFT JOIN projet_membres pm ON p.id_projet = pm.projet_id AND pm.is_deleted = 0
                  WHERE (p.responsable_id IN ($placeholders) OR pm.usr_id IN ($placeholders))
                  AND p.is_deleted = 0
                  GROUP BY p.id_projet
                  ORDER BY p.date_creation DESC";
        
        return $this->select($query, array_merge($membreIds, $membreIds, $membreIds));
    }

    public function getAllWithDetails()
    {
        $query = "SELECT t.*, 
                         u.nom as chef_nom, 
                         u.prenom as chef_prenom, 
                         u.grade as chef_grade,
                         COUNT(DISTINCT tm.usr_id) as nb_membres,
                         COUNT(DISTINCT pub_count.id_publication) as nb_publications
                  FROM teams t
                  LEFT JOIN users u ON t.chef_id = u.id_user
                  LEFT JOIN team_members tm ON t.id_team = tm.team_id AND tm.is_deleted = 0
                  LEFT JOIN (
                      SELECT p.id_publication, tm2.team_id
                      FROM publications p
                      JOIN publication_auteurs pa ON p.id_publication = pa.publication_id AND pa.is_deleted = 0
                      JOIN team_members tm2 ON pa.usr_id = tm2.usr_id AND tm2.is_deleted = 0
                      WHERE p.is_deleted = 0 AND p.statut = 'publie'
                      GROUP BY p.id_publication, tm2.team_id
                      HAVING COUNT(DISTINCT tm2.usr_id) >= 2
                  ) pub_count ON pub_count.team_id = t.id_team
                  WHERE t.is_deleted = 0
                  GROUP BY t.id_team
                  ORDER BY t.nom ASC";
        return $this->select($query);
    }
    
    public function getUserTeams($userId)
    {
        $query = "SELECT t.*, 
                         tm.role_dans_equipe, 
                         tm.date_adhesion,
                         u.nom as chef_nom, 
                         u.prenom as chef_prenom
                  FROM team_members tm
                  JOIN teams t ON tm.team_id = t.id_team
                  LEFT JOIN users u ON t.chef_id = u.id_user
                  WHERE tm.usr_id = :userId
                  AND t.is_deleted = 0 
                  AND tm.is_deleted = 0
                  ORDER BY t.nom ASC";
        return $this->select($query, ['userId' => $userId]);
    }
}