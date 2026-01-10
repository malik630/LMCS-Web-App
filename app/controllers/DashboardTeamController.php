<?php

class DashboardTeamController extends Controller
{
    private function checkAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté.';
            $this->redirect('auth/login');
            return false;
        }
        return true;
    }
    
    public function index()
    {
        if (!$this->checkAuth()) return;
        
        $userModel = $this->model('User');
        $userId = $_SESSION['user_id'];
        
        $data = [
            'user' => $userModel->getById($userId),
            'teams' => $userModel->getUserTeams($userId)
        ];
        
        $this->view('DashboardTeamView', $data);
    }
    
    public function detail($teamId)
    {
        if (!$this->checkAuth()) return;
        
        $teamModel = $this->model('Team');
        $userId = $_SESSION['user_id'];
        
        $team = $teamModel->getById($teamId);
        
        if (!$team) {
            $_SESSION['error'] = 'Équipe introuvable.';
            $this->redirect('dashboardteam/index');
            return;
        }
        
        // Vérifier si membre
        if (!$teamModel->isMember($teamId, $userId) && $team['chef_id'] != $userId) {
            $_SESSION['error'] = 'Accès refusé.';
            $this->redirect('dashboardteam/index');
            return;
        }
        
        $isChef = ($team['chef_id'] == $userId);
        
        $data = [
            'team' => $team,
            'membres' => $teamModel->getMembers($teamId),
            'is_chef' => $isChef,
            'projets' => $teamModel->getTeamProjets($teamId),
            'publications' => $teamModel->getTeamPublications($teamId)
        ];
        
        $this->view('DashboardTeamDetailView', $data);
    }
    
    public function associateProjet($teamId)
    {
        if (!$this->checkAuth() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('dashboardteam/index');
            return;
        }
        
        $teamModel = $this->model('Team');
        $projetModel = $this->model('Projet');
        $userId = $_SESSION['user_id'];
        
        $team = $teamModel->getById($teamId);
        
        if (!$team || $team['chef_id'] != $userId) {
            $_SESSION['error'] = 'Seul le chef d\'équipe peut associer des projets.';
            $this->redirect('dashboardteam/index');
            return;
        }
        
        $projetId = (int)($_POST['projet_id'] ?? 0);
        
        if ($projetId <= 0) {
            $_SESSION['error'] = 'Projet invalide.';
            $this->redirect('dashboardteam/detail/' . $teamId);
            return;
        }
        
        $projet = $projetModel->getById($projetId);
        
        if (!$projet) {
            $_SESSION['error'] = 'Projet introuvable.';
            $this->redirect('dashboardteam/detail/' . $teamId);
            return;
        }
        
        // Vérifier si au moins un membre de l'équipe fait partie du projet
        $membres = $teamModel->getMembers($teamId);
        $membresProjets = $projetModel->getMembers($projetId);
        
        $hasCommonMember = false;
        foreach ($membres as $membre) {
            foreach ($membresProjets as $membreProjet) {
                if ($membre['id_user'] == $membreProjet['id_user']) {
                    $hasCommonMember = true;
                    break 2;
                }
            }
            // Vérifier aussi le responsable
            if ($membre['id_user'] == $projet['responsable_id']) {
                $hasCommonMember = true;
                break;
            }
        }
        
        if (!$hasCommonMember) {
            $_SESSION['warning'] = 'Aucun membre de l\'équipe ne fait partie de ce projet. Le projet sera quand même visible dans les statistiques de l\'équipe.';
        } else {
            $_SESSION['success'] = 'Le projet est maintenant associé à l\'équipe via ses membres.';
        }
        
        $this->redirect('dashboardteam/detail/' . $teamId);
    }
    
    public function associatePublication($teamId)
    {
        if (!$this->checkAuth() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('dashboardteam/index');
            return;
        }
        
        $teamModel = $this->model('Team');
        $publicationModel = $this->model('Publication');
        $userId = $_SESSION['user_id'];
        
        $team = $teamModel->getById($teamId);
        
        if (!$team || $team['chef_id'] != $userId) {
            $_SESSION['error'] = 'Seul le chef d\'équipe peut associer des publications.';
            $this->redirect('dashboardteam/index');
            return;
        }
        
        $publicationId = (int)($_POST['publication_id'] ?? 0);
        
        if ($publicationId <= 0) {
            $_SESSION['error'] = 'Publication invalide.';
            $this->redirect('dashboardteam/detail/' . $teamId);
            return;
        }
        
        $publication = $publicationModel->getByIdAdmin($publicationId);
        
        if (!$publication) {
            $_SESSION['error'] = 'Publication introuvable.';
            $this->redirect('dashboardteam/detail/' . $teamId);
            return;
        }
        
        // Vérifier si au moins un auteur est membre de l'équipe
        $membres = $teamModel->getMembers($teamId);
        $auteurs = $publicationModel->getAuteurs($publicationId);
        
        $hasCommonMember = false;
        foreach ($membres as $membre) {
            foreach ($auteurs as $auteur) {
                if ($membre['id_user'] == $auteur['id_user']) {
                    $hasCommonMember = true;
                    break 2;
                }
            }
        }
        
        if (!$hasCommonMember) {
            $_SESSION['warning'] = 'Aucun auteur de cette publication n\'est membre de l\'équipe. La publication sera quand même visible dans les statistiques.';
        } else {
            $_SESSION['success'] = 'La publication est maintenant associée à l\'équipe via ses auteurs.';
        }
        
        $this->redirect('dashboardteam/detail/' . $teamId);
    }
    
    public function rapportEquipe($teamId)
    {
        if (!$this->checkAuth()) return;
        
        $teamModel = $this->model('Team');
        $userId = $_SESSION['user_id'];
        
        $team = $teamModel->getById($teamId);
        
        if (!$team) {
            $_SESSION['error'] = 'Équipe introuvable.';
            $this->redirect('dashboardteam/index');
            return;
        }
        
        // Vérifier si membre ou chef
        if (!$teamModel->isMember($teamId, $userId) && $team['chef_id'] != $userId) {
            $_SESSION['error'] = 'Accès refusé.';
            $this->redirect('dashboardteam/index');
            return;
        }
        
        $membres = $teamModel->getMembers($teamId);
        $projets = $teamModel->getTeamProjets($teamId);
        $publications = $teamModel->getTeamPublications($teamId);
        
        require_once '../app/utils/TeamReportPDFGenerator.php';
        
        $generator = new TeamReportPDFGenerator($team, $membres, $projets, $publications);
        $generator->generate();
        $generator->output('equipe_' . preg_replace('/[^a-zA-Z0-9]/', '_', $team['nom']) . '_' . date('Y-m-d') . '.pdf');
    }
}