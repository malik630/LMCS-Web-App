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

    public function getByChef($chefId)
    {
        return $this->selectAll('teams', [
            'chef_id' => $chefId,
            'is_deleted' => 0
        ], 'nom', 'ASC');
    }

    public function searchTeams($search = '', $sortBy = 'nom', $sortOrder = 'ASC')
    {
        $allowedSort = ['nom', 'thematique', 'date_creation'];
        $sortBy = in_array($sortBy, $allowedSort) ? $sortBy : 'nom';
        $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';
        
        if (empty($search)) {
            return $this->selectAll('teams', ['is_deleted' => 0], $sortBy, $sortOrder);
        }
        
        return $this->search('teams', 'nom', $search, ['is_deleted' => 0]);
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
        $query = "SELECT u.*, tm.role_dans_equipe, tm.date_adhesion, tm.id as member_id
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
    
        return $this->insert('team_members', ['team_id' => $teamId, 'usr_id' => $userId, 'role_dans_equipe' => $role]);
    }

    public function removeMember($teamId, $userId)
    {
        return $this->update('team_members',['is_deleted' => 1],['team_id' => $teamId, 'usr_id' => $userId]);
    }

    public function updateMemberRole($teamId, $userId, $role)
    {
        return $this->update('team_members', ['role_dans_equipe' => $role], ['team_id' => $teamId, 'usr_id' => $userId, 'is_deleted' => 0]);
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
        $query = "SELECT DISTINCT pub.*, tp.libelle as type_libelle,
                        GROUP_CONCAT(CONCAT(u.prenom, ' ', u.nom) ORDER BY pa.ordre_auteur SEPARATOR ', ') as auteurs
                  FROM publications pub
                  JOIN publication_auteurs pa ON pub.id_publication = pa.publication_id
                  JOIN users u ON pa.usr_id = u.id_user
                  JOIN team_members tm ON u.id_user = tm.usr_id
                  LEFT JOIN types_publications tp ON pub.type_publication_id = tp.id_type
                  WHERE tm.team_id = :teamId 
                  AND pub.is_deleted = 0 
                  AND pa.is_deleted = 0
                  AND tm.is_deleted = 0
                  GROUP BY pub.id_publication
                  ORDER BY pub.annee DESC, pub.date_publication DESC";
        return $this->select($query, ['teamId' => $teamId]);
    }
}
?>