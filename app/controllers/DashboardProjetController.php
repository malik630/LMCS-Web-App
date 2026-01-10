<?php

class DashboardProjetController extends Controller
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
    
    private function hasPermission($permission)
    {
        $permissionModel = $this->model('Permission');
        return $permissionModel->userHasPermission($_SESSION['user_id'], $permission);
    }
    
    private function getAvailableMembers($currentMembers = [])
    {
        $userModel = $this->model('User');
        $allUsers = $userModel->getActive();
        
        $membreIds = array_column($currentMembers, 'id_user');
        $availableMembers = [];
        
        foreach ($allUsers as $user) {
            if (!in_array($user['id_user'], $membreIds) && $user['role'] !== 'admin') {
                $availableMembers[] = $user;
            }
        }
        
        return $availableMembers;
    }
    
    public function index()
    {
        if (!$this->checkAuth()) return;
        
        $userModel = $this->model('User');
        $projetModel = $this->model('Projet');
        $userId = $_SESSION['user_id'];
        
        $projets = $userModel->getUserProjects($userId);

        foreach ($projets as &$projet) {
            $projet['membres'] = $projetModel->getMembers($projet['id_projet']);
            $projet['publications'] = $projetModel->getPublications($projet['id_projet']);
            $projet['is_responsable'] = ($projet['responsable_id'] == $userId);
        }
        
        $data = [
            'user' => $userModel->getById($userId),
            'projets' => $projets,
            'can_create' => $this->hasPermission('projets.create')
        ];
        
        $this->view('DashboardProjetView', $data);
    }
    
    public function detail($id)
    {
        if (!$this->checkAuth()) return;
        
        $projetModel = $this->model('Projet');
        $userId = $_SESSION['user_id'];
        
        $projet = $projetModel->getById($id);
        
        if (!$projet) {
            $_SESSION['error'] = 'Projet introuvable.';
            $this->redirect('dashboardprojet/index');
            return;
        }

        $membres = $projetModel->getMembers($id);
        $hasAccess = $projet['responsable_id'] == $userId;
        
        if (!$hasAccess) {
            foreach ($membres as $membre) {
                if ($membre['id_user'] == $userId) {
                    $hasAccess = true;
                    break;
                }
            }
        }
        
        if (!$hasAccess) {
            $_SESSION['error'] = 'Accès refusé.';
            $this->redirect('dashboardprojet/index');
            return;
        }
        
        $isResponsable = ($projet['responsable_id'] == $userId);

        $availableMembers = [];
        if ($isResponsable) {
            $availableMembers = $this->getAvailableMembers($membres);
        }
        
        $data = [
            'projet' => $projet,
            'membres' => $membres,
            'publications' => $projetModel->getPublications($id),
            'partenaires' => $projetModel->getPartenaires($id),
            'is_responsable' => $isResponsable,
            'available_members' => $availableMembers
        ];
        
        $this->view('DashboardProjetDetailView', $data);
    }
    
    public function create()
    {
        if (!$this->checkAuth()) return;
        
        if (!$this->hasPermission('projets.create')) {
            $_SESSION['error'] = 'Vous n\'avez pas la permission de créer des projets.';
            $this->redirect('dashboardprojet/index');
            return;
        }
        
        $userModel = $this->model('User');
        
        $data = [
            'users' => $userModel->getActive()
        ];
        
        $this->view('DashboardProjetCreateView', $data);
    }
    
    public function store()
    {
        if (!$this->checkAuth() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('dashboardprojet/index');
            return;
        }
        
        if (!$this->hasPermission('projets.create')) {
            $_SESSION['error'] = 'Permission refusée.';
            $this->redirect('dashboardprojet/index');
            return;
        }
        
        $projetModel = $this->model('Projet');
        $userId = $_SESSION['user_id'];
        
        $titre = trim($_POST['titre'] ?? '');
        
        if (empty($titre)) {
            $_SESSION['error'] = 'Le titre est obligatoire.';
            $this->redirect('dashboardprojet/create');
            return;
        }
        
        $projetData = [
            'titre' => $titre,
            'description' => $_POST['description'] ?? null,
            'thematique' => $_POST['thematique'] ?? null,
            'type_financement' => $_POST['type_financement'] ?? null,
            'statut' => 'en_cours',
            'responsable_id' => $userId,
            'date_debut' => $_POST['date_debut'] ?? null,
            'date_fin' => $_POST['date_fin'] ?? null,
            'budget' => !empty($_POST['budget']) ? (float)$_POST['budget'] : null,
            'date_creation' => date('Y-m-d H:i:s')
        ];
        
        $projetId = $projetModel->insert('projets', $projetData);
        
        if ($projetId) {
            // Ajouter le créateur comme membre
            $projetModel->insert('projet_membres', [
                'projet_id' => $projetId,
                'usr_id' => $userId,
                'role_projet' => 'Chef de projet'
            ]);
            
            $_SESSION['success'] = 'Projet créé avec succès.';
            $this->redirect('dashboardprojet/detail/' . $projetId);
        } else {
            $_SESSION['error'] = 'Erreur lors de la création du projet.';
            $this->redirect('dashboardprojet/create');
        }
    }
    
    public function edit($id)
    {
        if (!$this->checkAuth()) return;
        
        $projetModel = $this->model('Projet');
        $userId = $_SESSION['user_id'];
        
        $projet = $projetModel->getById($id);
        
        if (!$projet) {
            $_SESSION['error'] = 'Projet introuvable.';
            $this->redirect('dashboardprojet/index');
            return;
        }
        
        $canEdit = $projet['responsable_id'] == $userId || $this->hasPermission('projets.edit');
        
        if (!$canEdit && $this->hasPermission('projets.edit_own')) {
            $membres = $projetModel->getMembers($id);
            foreach ($membres as $membre) {
                if ($membre['id_user'] == $userId) {
                    $canEdit = true;
                    break;
                }
            }
        }
        
        if (!$canEdit) {
            $_SESSION['error'] = 'Vous n\'avez pas la permission de modifier ce projet.';
            $this->redirect('dashboardprojet/index');
            return;
        }
        
        $data = [
            'projet' => $projet
        ];
        
        $this->view('DashboardProjetEditView', $data);
    }
    
    public function update($id)
    {
        if (!$this->checkAuth() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('dashboardprojet/index');
            return;
        }
        
        $projetModel = $this->model('Projet');
        $userId = $_SESSION['user_id'];
        
        $projet = $projetModel->getById($id);
        
        if (!$projet) {
            $_SESSION['error'] = 'Projet introuvable.';
            $this->redirect('dashboardprojet/index');
            return;
        }
 
        $canEdit = $projet['responsable_id'] == $userId || $this->hasPermission('projets.edit');
        
        if (!$canEdit) {
            $_SESSION['error'] = 'Accès refusé.';
            $this->redirect('dashboardprojet/index');
            return;
        }
        
        $updateData = [
            'titre' => $_POST['titre'] ?? $projet['titre'],
            'description' => $_POST['description'] ?? $projet['description'],
            'thematique' => $_POST['thematique'] ?? $projet['thematique'],
            'type_financement' => $_POST['type_financement'] ?? $projet['type_financement'],
            'date_debut' => $_POST['date_debut'] ?? $projet['date_debut'],
            'date_fin' => $_POST['date_fin'] ?? $projet['date_fin'],
            'budget' => !empty($_POST['budget']) ? (float)$_POST['budget'] : $projet['budget']
        ];

        if ($projet['responsable_id'] == $userId && isset($_POST['statut'])) {
            $updateData['statut'] = $_POST['statut'];
        }
        
        if ($projetModel->updateById('projets', $id, $updateData, 'id_projet')) {
            $_SESSION['success'] = 'Projet mis à jour avec succès.';
        } else {
            $_SESSION['error'] = 'Erreur lors de la mise à jour.';
        }
        
        $this->redirect('dashboardprojet/detail/' . $id);
    }
    
    public function close($id)
    {
        if (!$this->checkAuth()) return;
        
        $projetModel = $this->model('Projet');
        $userId = $_SESSION['user_id'];
        
        $projet = $projetModel->getById($id);
        
        if (!$projet || $projet['responsable_id'] != $userId) {
            $_SESSION['error'] = 'Seul le responsable peut clôturer le projet.';
            $this->redirect('dashboardprojet/index');
            return;
        }
        
        if ($projetModel->updateById('projets', $id, ['statut' => 'termine'], 'id_projet')) {
            $_SESSION['success'] = 'Projet clôturé avec succès.';
        } else {
            $_SESSION['error'] = 'Erreur lors de la clôture.';
        }
        
        $this->redirect('dashboardprojet/detail/' . $id);
    }
    
    public function addMember($projetId)
    {
        if (!$this->checkAuth() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('dashboardprojet/index');
            return;
        }
        
        $projetModel = $this->model('Projet');
        $userModel = $this->model('User');
        $userId = $_SESSION['user_id'];
        
        $projet = $projetModel->getById($projetId);
        
        if (!$projet || $projet['responsable_id'] != $userId) {
            $_SESSION['error'] = 'Seul le responsable peut ajouter des membres.';
            $this->redirect('dashboardprojet/index');
            return;
        }
        
        $membreId = (int)($_POST['membre_id'] ?? 0);
        $role = $_POST['role_projet'] ?? 'Membre';
        
        if ($membreId <= 0) {
            $_SESSION['error'] = 'Membre invalide.';
            $this->redirect('dashboardprojet/detail/' . $projetId);
            return;
        }

        $newMember = $userModel->getById($membreId);
        if (!$newMember || $newMember['role'] !== 'admin') {
            $_SESSION['error'] = 'Vous ne pouvez pas ajouter cet utilisateur.';
            $this->redirect('dashboardprojet/detail/' . $projetId);
            return;
        }

        $membres = $projetModel->getMembers($projetId);
        foreach ($membres as $m) {
            if ($m['id_user'] == $membreId) {
                $_SESSION['error'] = 'Cette personne est déjà membre du projet.';
                $this->redirect('dashboardprojet/detail/' . $projetId);
                return;
            }
        }
        
        if ($projetModel->insert('projet_membres', [
            'projet_id' => $projetId,
            'usr_id' => $membreId,
            'role_projet' => $role
        ])) {
            $_SESSION['success'] = 'Membre ajouté avec succès.';
        } else {
            $_SESSION['error'] = 'Erreur lors de l\'ajout du membre.';
        }
        
        $this->redirect('dashboardprojet/detail/' . $projetId);
    }
    
    public function removeMember($projetId, $membreId)
    {
        if (!$this->checkAuth()) return;
        
        $projetModel = $this->model('Projet');
        $userId = $_SESSION['user_id'];
        
        $projet = $projetModel->getById($projetId);
        
        if (!$projet || $projet['responsable_id'] != $userId) {
            $_SESSION['error'] = 'Seul le responsable peut retirer des membres.';
            $this->redirect('dashboardprojet/index');
            return;
        }

        if ($membreId == $projet['responsable_id']) {
            $_SESSION['error'] = 'Vous ne pouvez pas vous retirer du projet en tant que responsable.';
            $this->redirect('dashboardprojet/detail/' . $projetId);
            return;
        }
        
        if ($projetModel->update('projet_membres', 
            ['is_deleted' => 1],
            ['projet_id' => $projetId, 'usr_id' => $membreId]
        )) {
            $_SESSION['success'] = 'Membre retiré avec succès.';
        } else {
            $_SESSION['error'] = 'Erreur lors du retrait.';
        }
        
        $this->redirect('dashboardprojet/detail/' . $projetId);
    }
}
?>