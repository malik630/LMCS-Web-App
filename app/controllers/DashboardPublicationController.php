<?php

class DashboardPublicationController extends Controller
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
    
    public function index()
    {
        if (!$this->checkAuth()) return;
        
        $userModel = $this->model('User');
        $publicationModel = $this->model('Publication');
        $userId = $_SESSION['user_id'];
        
        $data = [
            'user' => $userModel->getById($userId),
            'publications' => $userModel->getUserPublications($userId),
            'types' => $publicationModel->getTypes(),
            'can_create' => $this->hasPermission('publications.create')
        ];
        
        $this->view('DashboardPublicationView', $data);
    }
    
    public function create()
    {
        if (!$this->checkAuth()) return;
        
        if (!$this->hasPermission('publications.create')) {
            $_SESSION['error'] = 'Vous n\'avez pas la permission de créer des publications.';
            $this->redirect('dashboardpublication/index');
            return;
        }
        
        $publicationModel = $this->model('Publication');
        $userModel = $this->model('User');
        $userId = $_SESSION['user_id'];
        
        $data = [
            'types' => $publicationModel->getTypes(),
            'projets' => $userModel->getUserProjects($userId),
            'users' => $userModel->getActive()
        ];
        
        $this->view('DashboardPublicationCreateView', $data);
    }
    
    public function store()
    {
        if (!$this->checkAuth() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('dashboardpublication/index');
            return;
        }
        
        if (!$this->hasPermission('publications.create')) {
            $_SESSION['error'] = 'Permission refusée.';
            $this->redirect('dashboardpublication/index');
            return;
        }
        
        $publicationModel = $this->model('Publication');
        $userId = $_SESSION['user_id'];

        $titre = trim($_POST['titre'] ?? '');
        $annee = (int)($_POST['annee'] ?? date('Y'));
        $typeId = (int)($_POST['type_publication_id'] ?? 0);
        
        if (empty($titre) || $annee < 1900 || $annee > date('Y') + 5 || $typeId <= 0) {
            $_SESSION['error'] = 'Veuillez remplir tous les champs obligatoires correctement.';
            $this->redirect('dashboardpublication/create');
            return;
        }
        
        $fichierPdf = null;
        if (isset($_FILES['fichier_pdf']) && $_FILES['fichier_pdf']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadPDF($_FILES['fichier_pdf']);
            if ($uploadResult['success']) {
                $fichierPdf = $uploadResult['filename'];
            } else {
                $_SESSION['error'] = $uploadResult['message'];
                $this->redirect('dashboardpublication/create');
                return;
            }
        }
  
        $publicationData = [
            'titre' => $titre,
            'type_publication_id' => $typeId,
            'resume' => $_POST['resume'] ?? null,
            'annee' => $annee,
            'doi' => $_POST['doi'] ?? null,
            'lien_telechargement' => $_POST['lien_telechargement'] ?? null,
            'fichier_pdf' => $fichierPdf,
            'projet_id' => !empty($_POST['projet_id']) ? (int)$_POST['projet_id'] : null,
            'domaine' => $_POST['domaine'] ?? null,
            'date_publication' => $_POST['date_publication'] ?? null,
            'statut' => 'en_attente',
            'date_soumission' => date('Y-m-d H:i:s')
        ];
        
        $publicationId = $publicationModel->insert('publications', $publicationData);
        
        if ($publicationId) {
            $publicationModel->insert('publication_auteurs', [
                'publication_id' => $publicationId,
                'usr_id' => $userId,
                'ordre_auteur' => 1
            ]);

            if (!empty($_POST['coauteurs']) && is_array($_POST['coauteurs'])) {
                $ordre = 2;
                foreach ($_POST['coauteurs'] as $coauteurId) {
                    $coauteurId = (int)$coauteurId;
                    if ($coauteurId > 0 && $coauteurId != $userId) {
                        $publicationModel->insert('publication_auteurs', [
                            'publication_id' => $publicationId,
                            'usr_id' => $coauteurId,
                            'ordre_auteur' => $ordre++
                        ]);
                    }
                }
            }
            
            $_SESSION['success'] = 'Publication créée avec succès. Elle est en attente de validation.';
        } else {
            $_SESSION['error'] = 'Erreur lors de la création de la publication.';
        }
        
        $this->redirect('dashboardpublication/index');
    }
    
    public function edit($id)
    {
        if (!$this->checkAuth()) return;
        
        $publicationModel = $this->model('Publication');
        $userModel = $this->model('User');
        $userId = $_SESSION['user_id'];
        
        $publication = $publicationModel->getByIdAdmin($id);
        
        if (!$publication) {
            $_SESSION['error'] = 'Publication introuvable.';
            $this->redirect('dashboardpublication/index');
            return;
        }

        $auteurs = $publicationModel->getAuteurs($id);
        $isAuthor = false;
        foreach ($auteurs as $auteur) {
            if ($auteur['id_user'] == $userId) {
                $isAuthor = true;
                break;
            }
        }
        
        if (!$isAuthor && !$this->hasPermission('publications.edit')) {
            $_SESSION['error'] = 'Vous n\'avez pas la permission de modifier cette publication.';
            $this->redirect('dashboardpublication/index');
            return;
        }
        
        $data = [
            'publication' => $publication,
            'types' => $publicationModel->getTypes(),
            'projets' => $userModel->getUserProjects($userId),
            'auteurs' => $auteurs,
            'users' => $userModel->getActive()
        ];
        
        $this->view('DashboardPublicationEditView', $data);
    }
    
    public function update($id)
    {
        if (!$this->checkAuth() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('dashboardpublication/index');
            return;
        }
        
        $publicationModel = $this->model('Publication');
        $userId = $_SESSION['user_id'];
        
        $publication = $publicationModel->getByIdAdmin($id);
        
        if (!$publication) {
            $_SESSION['error'] = 'Publication introuvable.';
            $this->redirect('dashboardpublication/index');
            return;
        }

        $auteurs = $publicationModel->getAuteurs($id);
        $isAuthor = false;
        foreach ($auteurs as $auteur) {
            if ($auteur['id_user'] == $userId) {
                $isAuthor = true;
                break;
            }
        }
        
        if (!$isAuthor && !$this->hasPermission('publications.edit')) {
            $_SESSION['error'] = 'Accès refusé.';
            $this->redirect('dashboardpublication/index');
            return;
        }

        $fichierPdf = $publication['fichier_pdf'];
        if (isset($_FILES['fichier_pdf']) && $_FILES['fichier_pdf']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadPDF($_FILES['fichier_pdf']);
            if ($uploadResult['success']) {
                if (!empty($publication['fichier_pdf'])) {
                    $oldFile = __DIR__ . '/../../public/assets/documents' . $publication['fichier_pdf'];
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
                $fichierPdf = $uploadResult['filename'];
            }
        }
        
        $updateData = [
            'titre' => $_POST['titre'] ?? $publication['titre'],
            'type_publication_id' => $_POST['type_publication_id'] ?? $publication['type_publication_id'],
            'resume' => $_POST['resume'] ?? $publication['resume'],
            'annee' => $_POST['annee'] ?? $publication['annee'],
            'doi' => $_POST['doi'] ?? $publication['doi'],
            'lien_telechargement' => ASSETS_URL . 'documents/' . $fichierPdf,
            'fichier_pdf' => $fichierPdf,
            'projet_id' => !empty($_POST['projet_id']) ? (int)$_POST['projet_id'] : null,
            'domaine' => $_POST['domaine'] ?? $publication['domaine'],
            'date_publication' => $_POST['date_publication'] ?? $publication['date_publication']
        ];
        
        if ($publicationModel->updateById('publications', $id, $updateData, 'id_publication')) {
            if (isset($_POST['coauteurs'])) {
                // Garder uniquement l'utilisateur actuel comme auteur principal
                $publicationModel->delete('publication_auteurs', ['publication_id' => $id]);
                
                $publicationModel->insert('publication_auteurs', [
                    'publication_id' => $id,
                    'usr_id' => $userId,
                    'ordre_auteur' => 1
                ]);
                
                if (!empty($_POST['coauteurs']) && is_array($_POST['coauteurs'])) {
                    $ordre = 2;
                    foreach ($_POST['coauteurs'] as $coauteurId) {
                        $coauteurId = (int)$coauteurId;
                        if ($coauteurId > 0 && $coauteurId != $userId) {
                            $publicationModel->insert('publication_auteurs', [
                                'publication_id' => $id,
                                'usr_id' => $coauteurId,
                                'ordre_auteur' => $ordre++
                            ]);
                        }
                    }
                }
            }
            
            $_SESSION['success'] = 'Publication mise à jour avec succès.';
        } else {
            $_SESSION['error'] = 'Erreur lors de la mise à jour.';
        }
        
        $this->redirect('dashboardpublication/index');
    }
    
    public function delete($id)
    {
        if (!$this->checkAuth()) return;
        
        $publicationModel = $this->model('Publication');
        $userId = $_SESSION['user_id'];
        
        $publication = $publicationModel->getByIdAdmin($id);
        
        if (!$publication) {
            $_SESSION['error'] = 'Publication introuvable.';
            $this->redirect('dashboardpublication/index');
            return;
        }

        $auteurs = $publicationModel->getAuteurs($id);
        $isFirstAuthor = !empty($auteurs) && $auteurs[0]['id_user'] == $userId;
        
        if (!$isFirstAuthor && !$this->hasPermission('publications.delete')) {
            $_SESSION['error'] = 'Seul le premier auteur peut supprimer cette publication.';
            $this->redirect('dashboardpublication/index');
            return;
        }
        
        if ($publicationModel->softDelete('publications', $id, 'id_publication')) {
            $_SESSION['success'] = 'Publication supprimée avec succès.';
        } else {
            $_SESSION['error'] = 'Erreur lors de la suppression.';
        }
        
        $this->redirect('dashboardpublication/index');
    }
    
    public function submitForApproval($id)
    {
        if (!$this->checkAuth()) return;
        
        $publicationModel = $this->model('Publication');
        $userId = $_SESSION['user_id'];
        
        $publication = $publicationModel->getByIdAdmin($id);
        
        if (!$publication) {
            $_SESSION['error'] = 'Publication introuvable.';
            $this->redirect('dashboardpublication/index');
            return;
        }

        $auteurs = $publicationModel->getAuteurs($id);
        $isAuthor = false;
        foreach ($auteurs as $auteur) {
            if ($auteur['id_user'] == $userId) {
                $isAuthor = true;
                break;
            }
        }
        
        if (!$isAuthor) {
            $_SESSION['error'] = 'Accès refusé.';
            $this->redirect('dashboardpublication/index');
            return;
        }
        
        if ($publication['statut'] !== 'en_attente' && $publication['statut'] !== 'rejete') {
            $_SESSION['error'] = 'Cette publication ne peut pas être soumise.';
            $this->redirect('dashboardpublication/index');
            return;
        }
        
        if ($publicationModel->updateById('publications', $id, ['statut' => 'en_attente'], 'id_publication')) {
            $_SESSION['success'] = 'Publication soumise pour validation par l\'administrateur.';
        } else {
            $_SESSION['error'] = 'Erreur lors de la soumission.';
        }
        
        $this->redirect('dashboardpublication/index');
    }
    
    private function uploadPDF($file)
    {
        $allowedTypes = ['application/pdf'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Seuls les fichiers PDF sont autorisés.'];
        }
        
        if ($file['size'] > 10 * 1024 * 1024) {
            return ['success' => false, 'message' => 'Le fichier est trop volumineux (max 10 MB).'];
        }
        
        $uploadDir = __DIR__ . '/../../public/assets/documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $filename = uniqid() . '_' . time() . '.pdf';
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => true, 'filename' => $filename];
        }
        
        return ['success' => false, 'message' => 'Erreur lors de l\'upload.'];
    }
}
?>