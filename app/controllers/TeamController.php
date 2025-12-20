<?php

class TeamController extends Controller
{
    private $teamModel;
    private $userModel;
    
    public function __construct()
    {
        $this->teamModel = $this->model('Team');
        $this->userModel = $this->model('User');
    }
    
    public function index()
    {
        $teams = $this->teamModel->getAllWithDetails();
        $organigramme = $this->userModel->getOrganigramme();
        
        $data = [
            'teams' => $teams,
            'director' => $this->userModel->getDirector(),
            'organigramme' => $organigramme,
            'teamsForOrganigramme' => $this->prepareTeamsForOrganigramme($organigramme),
            'teamsWithMembers' => $this->prepareTeamsWithMembers($teams)
        ];
        
        $this->view('TeamView', $data);
    }
    
    public function detail($id)
    {
        $team = $this->teamModel->getById($id);
        
        if (!$team) {
            $_SESSION['error'] = 'Équipe introuvable';
            $this->redirect('team');
            return;
        }
        
        $data = [
            'team' => $team,
            'members' => $this->teamModel->getMembers($id),
            'publications' => $this->teamModel->getTeamPublications($id)
        ];
        
        $this->view('TeamDetailView', $data);
    }
    
    public function member($id)
    {
        $user = $this->userModel->getById($id);
        
        if (!$user || $user['is_deleted'] == 1) {
            $_SESSION['error'] = 'Membre introuvable';
            $this->redirect('team');
            return;
        }
        
        $data = [
            'user' => $user,
            'teams' => $this->teamModel->getUserTeams($id),
            'projects' => $this->userModel->getUserProjects($id),
            'publications' => $this->userModel->getUserPublications($id)
        ];
        
        $this->view('MemberView', $data);
    }
    
    public function getTeamsData()
    {
        header('Content-Type: application/json');
        
        $teams = $this->teamModel->getAllWithDetails();
        $tableData = $this->prepareJsonTableData($teams);
        
        echo json_encode(['data' => $tableData]);
        exit;
    }
    
    private function prepareTeamsForOrganigramme(array $organigramme)
    {
        $teamsForOrganigramme = [];
        
        foreach ($organigramme as $member) {
            $teams = $this->teamModel->getUserTeams($member['id_user']);
            $teamsForOrganigramme[$member['id_user']] = $teams;
        }
        
        return $teamsForOrganigramme;
    }
    
    private function prepareTeamsWithMembers(array $teams)
    {
        $result = [];
        
        foreach ($teams as $team) {
            $members = $this->teamModel->getMembers($team['id_team']);
            if (!empty($members)) {
                $result[] = [
                    'team' => $team,
                    'members' => $members
                ];
            }
        }
        
        return $result;
    }
    
    private function prepareJsonTableData(array $teams)
    {
        $tableData = [];
        
        foreach ($teams as $team) {
            $members = $this->teamModel->getMembers($team['id_team']);
            
            foreach ($members as $index => $member) {
                $isChef = ($team['chef_id'] == $member['id_user']);
                $poste = $isChef ? 'Chef d\'équipe' : ($member['role_dans_equipe'] ?? 'Membre');
                
                $tableData[] = [
                    'team_id' => $team['id_team'],
                    'team_name' => $team['nom'],
                    'team_thematique' => $team['thematique'] ?? '',
                    'member_id' => $member['id_user'],
                    'member_name' => $member['prenom'] . ' ' . $member['nom'],
                    'member_grade' => $member['grade'],
                    'member_poste' => $poste,
                    'is_chef' => $isChef,
                    'member_photo' => $member['photo'] ?? '',
                    'is_first_row' => ($index === 0),
                    'rowspan' => count($members)
                ];
            }
        }
        
        return $tableData;
    }
}
?>