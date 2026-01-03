<?php

class AdminTeamController extends Controller
{
    private $teamModel;
    private $userModel;
    
    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Accès refusé. Vous devez être administrateur.';
            header('Location: ' . BASE_URL);
            exit;
        }
        
        $this->teamModel = $this->model('Team');
        $this->userModel = $this->model('User');
    }
    
    public function index()
    {
        $teams = $this->teamModel->getAllWithDetails();
        $this->view('AdminTeamsView', ['teams' => $teams]);
    }
    
    public function create()
    {
        $users = $this->userModel->getActive();
        $this->view('AdminCreateTeamView', ['users' => $users]);
    }
    
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/equipes');
            return;
        }
        
        $nom = $_POST['nom'] ?? '';
        $chefId = $_POST['chef_id'] ?? '';
        
        if (empty($nom) || empty($chefId)) {
            $_SESSION['error'] = 'Le nom et le chef d\'équipe sont obligatoires.';
            $this->redirect('admin/createTeam');
            return;
        }
        
        $data = [
            'nom' => $nom,
            'thematique' => $_POST['thematique'] ?? null,
            'description' => $_POST['description'] ?? null,
            'chef_id' => $chefId,
            'date_creation' => date('Y-m-d')
        ];
        
        $teamId = $this->teamModel->insert('teams', $data);
        
        if ($teamId) {
            $this->teamModel->addMember($teamId, $chefId, 'Chef d\'équipe');
            $_SESSION['success'] = 'Équipe créée avec succès.';
            $this->redirect('admin/equipes');
        } else {
            $_SESSION['error'] = 'Erreur lors de la création de l\'équipe.';
            $this->redirect('admin/createTeam');
        }
    }
    
    public function edit($teamId)
    {
        $team = $this->teamModel->getById($teamId);
        
        if (!$team) {
            $_SESSION['error'] = 'Équipe introuvable.';
            $this->redirect('admin/equipes');
            return;
        }
        
        $users = $this->userModel->getActive();
        $this->view('AdminEditTeamView', [
            'team' => $team,
            'users' => $users
        ]);
    }
    
    public function update($teamId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/equipes');
            return;
        }
        
        $data = [
            'nom' => $_POST['nom'] ?? '',
            'thematique' => $_POST['thematique'] ?? null,
            'description' => $_POST['description'] ?? null,
            'chef_id' => $_POST['chef_id'] ?? null
        ];
        
        if (empty($data['nom']) || empty($data['chef_id'])) {
            $_SESSION['error'] = 'Le nom et le chef d\'équipe sont obligatoires.';
            $this->redirect('admin/editTeam/' . $teamId);
            return;
        }
        
        $result = $this->teamModel->updateById('teams', $teamId, $data, 'id_team');
        
        if ($result) {
            if (!$this->teamModel->isMember($teamId, $data['chef_id'])) {
                $this->teamModel->addMember($teamId, $data['chef_id'], 'Chef d\'équipe');
            }
            $_SESSION['success'] = 'Équipe mise à jour avec succès.';
        } else {
            $_SESSION['error'] = 'Erreur lors de la mise à jour.';
        }
        
        $this->redirect('admin/equipes');
    }
    
    public function delete($teamId)
    {
        $result = $this->teamModel->softDelete('teams', $teamId, 'id_team');
        
        if ($result) {
            $this->teamModel->update('team_members', ['is_deleted' => 1], ['team_id' => $teamId]);
            $_SESSION['success'] = 'Équipe supprimée avec succès.';
        } else {
            $_SESSION['error'] = 'Erreur lors de la suppression.';
        }
        
        $this->redirect('admin/equipes');
    }
    
    public function manageMembers($teamId)
    {
        $team = $this->teamModel->getById($teamId);
        
        if (!$team) {
            $_SESSION['error'] = 'Équipe introuvable.';
            $this->redirect('admin/equipes');
            return;
        }
        
        $members = $this->teamModel->getMembers($teamId);
        $allUsers = $this->userModel->getActive();
        $memberIds = array_column($members, 'id_user');
        $availableUsers = array_filter($allUsers, function($user) use ($memberIds) {
            return !in_array($user['id_user'], $memberIds);
        });
        
        $this->view('AdminTeamMembersView', [
            'team' => $team,
            'members' => $members,
            'availableUsers' => $availableUsers
        ]);
    }
    
    public function addMember()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/equipes');
            return;
        }
        
        $teamId = $_POST['team_id'] ?? null;
        $userId = $_POST['user_id'] ?? null;
        $role = $_POST['role'] ?? null;
        
        if (!$teamId || !$userId) {
            $_SESSION['error'] = 'Données manquantes.';
            $this->redirect('admin/equipes');
            return;
        }
        
        $result = $this->teamModel->addMember($teamId, $userId, $role);
        
        $_SESSION[$result ? 'success' : 'error'] = $result 
            ? 'Membre ajouté avec succès.' 
            : 'Erreur: le membre est peut-être déjà dans l\'équipe.';
        
        $this->redirect('admin/manageTeamMembers/' . $teamId);
    }
    
    public function removeMember($teamId, $userId)
    {
        $team = $this->teamModel->getById($teamId);
        if ($team['chef_id'] == $userId) {
            $_SESSION['error'] = 'Impossible de retirer le chef d\'équipe. Changez d\'abord le chef.';
            $this->redirect('admin/manageTeamMembers/' . $teamId);
            return;
        }
        
        $result = $this->teamModel->removeMember($teamId, $userId);
        
        $_SESSION[$result ? 'success' : 'error'] = $result 
            ? 'Membre retiré avec succès.' 
            : 'Erreur lors du retrait du membre.';
        
        $this->redirect('admin/manageTeamMembers/' . $teamId);
    }
    
    public function updateMemberRole()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/equipes');
            return;
        }
        
        $teamId = $_POST['team_id'] ?? null;
        $userId = $_POST['user_id'] ?? null;
        $role = $_POST['role'] ?? '';
        
        if (!$teamId || !$userId) {
            $_SESSION['error'] = 'Données manquantes.';
            $this->redirect('admin/equipes');
            return;
        }
        
        $result = $this->teamModel->updateMemberRole($teamId, $userId, $role);
        
        $_SESSION[$result ? 'success' : 'error'] = $result 
            ? 'Rôle mis à jour avec succès.' 
            : 'Erreur lors de la mise à jour du rôle.';
        
        $this->redirect('admin/manageTeamMembers/' . $teamId);
    }
}
?>