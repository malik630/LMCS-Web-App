<?php

class AdminProjetController extends Controller
{
    private $projetModel;
    private $userModel;
    private $partenaireModel;
    
    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Accès refusé. Vous devez être administrateur.';
            header('Location: ' . BASE_URL);
            exit;
        }
        
        $this->projetModel = $this->model('Projet');
        $this->userModel = $this->model('User');
        $this->partenaireModel = $this->model('Partner');
    }
    
    public function index()
    {
        $projets = $this->projetModel->getAllWithDetails();
        
        $stats = [
            'total' => count($projets),
            'en_cours' => count(array_filter($projets, fn($p) => $p['statut'] == 'en_cours')),
            'termine' => count(array_filter($projets, fn($p) => $p['statut'] == 'termine')),
            'soumis' => count(array_filter($projets, fn($p) => $p['statut'] == 'soumis')),
            'budget_total' => array_sum(array_column($projets, 'budget'))
        ];
        
        $this->view('AdminProjetsView', ['projets' => $projets, 'statistics' => $stats]);
    }
    
    public function create()
    {
        $users = $this->userModel->getActive();
        $this->view('AdminCreateProjetView', ['users' => $users]);
    }
    
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/projets');
            return;
        }
        
        $projetId = $this->projetModel->insert('projets', [
            'titre' => $_POST['titre'] ?? '',
            'responsable_id' => $_POST['responsable_id'] ?? null,
            'statut' => $_POST['statut'] ?? 'en_cours',
            'thematique' => $_POST['thematique'] ?? null,
            'budget' => $_POST['budget'] ?? null,
            'description' => $_POST['description'] ?? null,
            'date_creation' => date('Y-m-d')
        ]);
        
        if ($projetId) {
            $this->projetModel->insert('projet_membres', [
                'projet_id' => $projetId,
                'usr_id' => $_POST['responsable_id'],
                'role_projet' => 'Responsable'
            ]);
            $_SESSION['success'] = 'Projet créé avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors de la création';
        }
        
        $this->redirect('admin/projets');
    }
    
    public function edit($id)
    {
        $projet = $this->projetModel->getById($id);
        
        if (!$projet) {
            $_SESSION['error'] = 'Projet introuvable';
            $this->redirect('admin/projets');
            return;
        }
        
        $users = $this->userModel->getActive();
        $this->view('AdminEditProjetView', [
            'projet' => $projet,
            'users' => $users
        ]);
    }
    
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/projets');
            return;
        }
        
        $result = $this->projetModel->updateById('projets', $id, [
            'titre' => $_POST['titre'] ?? '',
            'responsable_id' => $_POST['responsable_id'] ?? null,
            'statut' => $_POST['statut'] ?? 'en_cours',
            'thematique' => $_POST['thematique'] ?? null,
            'budget' => $_POST['budget'] ?? null,
            'description' => $_POST['description'] ?? null
        ], 'id_projet');
        
        $_SESSION[$result ? 'success' : 'error'] = $result 
            ? 'Projet modifié avec succès' 
            : 'Erreur lors de la modification';
        
        $this->redirect('admin/projets');
    }
    
    public function delete($id)
    {
        $result = $this->projetModel->softDelete('projets', $id, 'id_projet');
        
        $_SESSION[$result ? 'success' : 'error'] = $result 
            ? 'Projet supprimé avec succès' 
            : 'Erreur lors de la suppression';
        
        $this->redirect('admin/projets');
    }
    
    public function manageMembers($id)
    {
        $projet = $this->projetModel->getById($id);
        
        if (!$projet) {
            $_SESSION['error'] = 'Projet introuvable';
            $this->redirect('admin/projets');
            return;
        }
        
        $membres = $this->projetModel->getMembers($id);
        $partenaires = $this->projetModel->getPartenaires($id);
        
        $allUsers = $this->userModel->getActive();
        $memberIds = array_column($membres, 'id_user');
        $availableUsers = array_filter($allUsers, fn($u) => !in_array($u['id_user'], $memberIds));
        
        $allPartenaires = $this->partenaireModel->selectAll('partenaires', ['is_deleted' => 0]);
        $partIds = array_column($partenaires, 'id_partenaire');
        $availablePartenaires = array_filter($allPartenaires, fn($p) => !in_array($p['id_partenaire'], $partIds));
        
        $this->view('AdminProjetMembersView', [
            'projet' => $projet,
            'membres' => $membres,
            'partenaires' => $partenaires,
            'availableUsers' => $availableUsers,
            'availablePartenaires' => $availablePartenaires
        ]);
    }
    
    public function addMember()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/projets');
            return;
        }
        
        $result = $this->projetModel->insert('projet_membres', [
            'projet_id' => $_POST['projet_id'],
            'usr_id' => $_POST['user_id'],
            'role_projet' => $_POST['role_projet'] ?? null
        ]);
        
        $_SESSION[$result ? 'success' : 'error'] = $result 
            ? 'Membre ajouté avec succès' 
            : 'Erreur lors de l\'ajout';
        
        $this->redirect('admin/manageProjetMembers/' . $_POST['projet_id']);
    }
    
    public function removeMember($projetId, $userId)
    {
        $result = $this->projetModel->update('projet_membres', ['is_deleted' => 1], [
            'projet_id' => $projetId,
            'usr_id' => $userId
        ]);
        
        $_SESSION[$result ? 'success' : 'error'] = $result 
            ? 'Membre retiré avec succès' 
            : 'Erreur lors du retrait';
        
        $this->redirect('admin/manageProjetMembers/' . $projetId);
    }
    
    public function addPartenaire()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/projets');
            return;
        }
        
        $result = $this->projetModel->insert('projet_partenaires', [
            'projet_id' => $_POST['projet_id'],
            'partenaire_id' => $_POST['partenaire_id']
        ]);
        
        $_SESSION[$result ? 'success' : 'error'] = $result 
            ? 'Partenaire ajouté avec succès' 
            : 'Erreur lors de l\'ajout';
        
        $this->redirect('admin/manageProjetMembers/' . $_POST['projet_id']);
    }
    
    public function removePartenaire($projetId, $partId)
    {
        $result = $this->projetModel->update('projet_partenaires', ['is_deleted' => 1], [
            'projet_id' => $projetId,
            'partenaire_id' => $partId
        ]);
        
        $_SESSION[$result ? 'success' : 'error'] = $result 
            ? 'Partenaire retiré avec succès' 
            : 'Erreur lors du retrait';
        
        $this->redirect('admin/manageProjetMembers/' . $projetId);
    }
    
    public function rapportPDF()
    {
        $projets = $this->projetModel->getAllWithDetails();
        
        $stats = [
            'total' => count($projets),
            'en_cours' => count(array_filter($projets, fn($p) => $p['statut'] == 'en_cours')),
            'termine' => count(array_filter($projets, fn($p) => $p['statut'] == 'termine')),
            'budget_total' => array_sum(array_column($projets, 'budget'))
        ];
        
        require_once __DIR__ . '/../utils/ProjetsPDFGenerator.php';
        
        $pdf = new ProjetsPDFGenerator($projets, $stats);
        $pdf->generate();
        $pdf->output('rapport_projets_' . date('Ymd') . '.pdf');
    }
}
?>