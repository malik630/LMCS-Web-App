<?php

class AdminPublicationController extends Controller
{
    private $publicationModel;
    private $userModel;
    private $projetModel;
    
    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Accès refusé. Vous devez être administrateur.';
            header('Location: ' . BASE_URL);
            exit;
        }
        
        $this->publicationModel = $this->model('Publication');
        $this->userModel = $this->model('User');
        $this->projetModel = $this->model('Projet');
    }
    
    public function publications()
    {
        $filter = $_GET['filter'] ?? 'all';
        
        $publications = match($filter) {
            'pending' => $this->publicationModel->getPending(),
            'published' => $this->publicationModel->getPublished(),
            'rejected' => $this->publicationModel->getRejected(),
            default => $this->publicationModel->getAllAdmin()
        };
        
        $stats = [
            'total' => count($this->publicationModel->getAllAdmin()),
            'pending' => count($this->publicationModel->getPending()),
            'published' => count($this->publicationModel->getPublished()),
            'rejected' => count($this->publicationModel->getRejected())
        ];
        
        $this->view('AdminPublicationsView', [
            'publications' => $publications,
            'stats' => $stats,
            'currentFilter' => $filter
        ]);
    }
    
    public function create()
    {
        $types = $this->publicationModel->getTypes();
        $users = $this->userModel->getActive();
        $projets = $this->projetModel->selectAll('projets', ['is_deleted' => 0]);
        
        $this->view('AdminCreatePublicationView', [
            'types' => $types,
            'users' => $users,
            'projets' => $projets
        ]);
    }
    
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('adminPublication/publications');
            return;
        }

        $titre = trim($_POST['titre'] ?? '');
        $annee = (int)($_POST['annee'] ?? date('Y'));
        $typeId = (int)($_POST['type_publication_id'] ?? 0);
        
        if (empty($titre) || $annee < 1900 || $annee > date('Y') + 5 || $typeId <= 0) {
            $_SESSION['error'] = 'Veuillez remplir tous les champs obligatoires correctement.';
            $this->redirect('adminPublication/create');
            return;
        }

        if (empty($_POST['auteurs']) || !is_array($_POST['auteurs'])) {
            $_SESSION['error'] = 'Veuillez sélectionner au moins un auteur.';
            $this->redirect('adminPublication/create');
            return;
        }

        $fichierPdf = null;
        $lienTelechargement = null;
        if (isset($_FILES['fichier_pdf']) && $_FILES['fichier_pdf']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadPDF($_FILES['fichier_pdf']);
            if ($uploadResult['success']) {
                $fichierPdf = $uploadResult['filename'];
                $lienTelechargement = ASSETS_URL . 'documents/' . $fichierPdf;
            } else {
                $_SESSION['error'] = $uploadResult['message'];
                $this->redirect('adminPublication/create');
                return;
            }
        }
        
        $publicationData = [
            'titre' => $titre,
            'type_publication_id' => $typeId,
            'resume' => $_POST['resume'] ?? null,
            'annee' => $annee,
            'doi' => $_POST['doi'] ?? null,
            'lien_telechargement' => $lienTelechargement,
            'fichier_pdf' => $fichierPdf,
            'projet_id' => !empty($_POST['projet_id']) ? (int)$_POST['projet_id'] : null,
            'domaine' => $_POST['domaine'] ?? null,
            'statut' => 'publie',
            'date_publication' => $_POST['date_publication'] ?? date('Y-m-d'),
            'date_soumission' => date('Y-m-d H:i:s')
        ];
        
        $publicationId = $this->publicationModel->insert('publications', $publicationData);
        
        if ($publicationId) {
            $auteurs = $_POST['auteurs'];
            foreach ($auteurs as $index => $auteurId) {
                $auteurId = (int)$auteurId;
                if ($auteurId > 0) {
                    $this->publicationModel->insert('publication_auteurs', [
                        'publication_id' => $publicationId,
                        'usr_id' => $auteurId, 
                        'ordre_auteur' => $index + 1
                    ]);
                }
            }
            
            $_SESSION['success'] = 'Publication créée avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors de la création';
        }
        
        $this->redirect('adminPublication/publications');
    }
    
    public function edit($id)
    {
        $publication = $this->publicationModel->getByIdAdmin($id);
        
        if (!$publication) {
            $_SESSION['error'] = 'Publication introuvable';
            $this->redirect('adminPublication/publications');
            return;
        }
        
        $types = $this->publicationModel->getTypes();
        $users = $this->userModel->getActive();
        $projets = $this->projetModel->selectAll('projets', ['is_deleted' => 0]);
        $auteurs = $this->publicationModel->getAuteurs($id);
        
        $this->view('AdminEditPublicationView', [
            'publication' => $publication,
            'types' => $types,
            'users' => $users,
            'projets' => $projets,
            'auteurs' => $auteurs
        ]);
    }
    
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('adminPublication/publications');
            return;
        }
        
        $publication = $this->publicationModel->getByIdAdmin($id);
        
        if (!$publication) {
            $_SESSION['error'] = 'Publication introuvable.';
            $this->redirect('adminPublication/publications');
            return;
        }

        if (empty($_POST['auteurs']) || !is_array($_POST['auteurs'])) {
            $_SESSION['error'] = 'Veuillez sélectionner au moins un auteur.';
            $this->redirect('adminPublication/edit/' . $id);
            return;
        }

        $fichierPdf = $publication['fichier_pdf'];
        $lienTelechargement = $publication['lien_telechargement'];
        
        if (isset($_FILES['fichier_pdf']) && $_FILES['fichier_pdf']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadPDF($_FILES['fichier_pdf']);
            if ($uploadResult['success']) {
                if (!empty($publication['fichier_pdf'])) {
                    $oldFile = __DIR__ . '/../../public/assets/documents/' . $publication['fichier_pdf'];
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
                $fichierPdf = $uploadResult['filename'];
                $lienTelechargement = ASSETS_URL . 'documents/' . $fichierPdf;
            } else {
                $_SESSION['error'] = $uploadResult['message'];
                $this->redirect('adminPublication/edit/' . $id);
                return;
            }
        }
        
        $publicationData = [
            'titre' => $_POST['titre'],
            'type_publication_id' => $_POST['type_publication_id'] ?? null,
            'resume' => $_POST['resume'] ?? null,
            'annee' => $_POST['annee'],
            'doi' => $_POST['doi'] ?? null,
            'lien_telechargement' => $lienTelechargement,
            'fichier_pdf' => $fichierPdf,
            'projet_id' => !empty($_POST['projet_id']) ? (int)$_POST['projet_id'] : null,
            'domaine' => $_POST['domaine'] ?? null,
            'statut' => $_POST['statut'] ?? $publication['statut'],
            'date_publication' => $_POST['date_publication'] ?? date('Y-m-d')
        ];
        
        $result = $this->publicationModel->updateById('publications', $id, $publicationData, 'id_publication');
        
        if ($result) {
            $this->publicationModel->delete('publication_auteurs', ['publication_id' => $id]);
            
            $auteurs = $_POST['auteurs'];
            foreach ($auteurs as $index => $auteurId) {
                $auteurId = (int)$auteurId;
                if ($auteurId > 0) {
                    $this->publicationModel->insert('publication_auteurs', [
                        'publication_id' => $id,
                        'usr_id' => $auteurId, 
                        'ordre_auteur' => $index + 1
                    ]);
                }
            }
            
            $_SESSION['success'] = 'Publication modifiée avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors de la modification';
        }
        
        $this->redirect('adminPublication/publications');
    }
    
    public function publish($id)
    {
        $result = $this->publicationModel->updateById('publications', $id, [
            'statut' => 'publie',
            'date_publication' => date('Y-m-d')
        ], 'id_publication');
        
        $_SESSION[$result ? 'success' : 'error'] = $result 
            ? 'Publication publiée avec succès' 
            : 'Erreur lors de la publication';
        
        $this->redirect('adminPublication/publications');
    }
    
    public function reject($id)
    {
        $result = $this->publicationModel->updateById('publications', $id, [
            'statut' => 'rejete'
        ], 'id_publication');
        
        $_SESSION[$result ? 'success' : 'error'] = $result 
            ? 'Publication rejetée' 
            : 'Erreur lors du rejet';
        
        $this->redirect('adminPublication/publications');
    }
    
    public function delete($id)
    {
        $publication = $this->publicationModel->getByIdAdmin($id);
        
        if ($publication && !empty($publication['fichier_pdf'])) {
            $fichierPath = __DIR__ . '/../../public/assets/documents/' . $publication['fichier_pdf'];
            if (file_exists($fichierPath)) {
                unlink($fichierPath);
            }
        }
        
        $result = $this->publicationModel->softDelete('publications', $id, 'id_publication');
        
        $_SESSION[$result ? 'success' : 'error'] = $result 
            ? 'Publication supprimée' 
            : 'Erreur lors de la suppression';
        
        $this->redirect('adminPublication/publications');
    }
    
    public function rapports()
    {
        $years = $this->publicationModel->getYears();
        $authors = $this->publicationModel->getAllAuthors();
        
        $this->view('AdminPublicationRapportsView', [
            'years' => $years,
            'authors' => $authors
        ]);
    }
    
    public function generateRapport()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('adminPublication/rapports');
            return;
        }
        
        $type = $_POST['type'] ?? 'year';
        $year = $_POST['year'] ?? null;
        $authorId = $_POST['author_id'] ?? null;
        $format = $_POST['format'] ?? 'pdf';
        
        if ($type === 'year' && $year) {
            $publications = $this->publicationModel->getByYear($year);
            $title = "Rapport Bibliographique {$year}";
        } elseif ($type === 'author' && $authorId) {
            $publications = $this->publicationModel->getByAuthor($authorId);
            $author = $this->userModel->getById($authorId);
            $title = "Publications de {$author['prenom']} {$author['nom']}";
        } else {
            $_SESSION['error'] = 'Paramètres invalides';
            $this->redirect('adminPublication/rapports');
            return;
        }
        
        if ($format === 'pdf') {
            $this->generatePDFRapport($publications, $title);
        } else {
            $this->generateExcelRapport($publications, $title);
        }
    }
    
    private function generatePDFRapport($publications, $title)
    {
        require_once __DIR__ . '/../utils/PublicationsPDFGenerator.php';
        
        $pdf = new PublicationsPDFGenerator($publications, $title);
        $pdf->generate();
        $pdf->output('rapport_publications_' . date('Ymd') . '.pdf');
    }
    
    private function generateExcelRapport($publications, $title)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="rapport_publications_' . date('Ymd') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($output, ['Titre', 'Type', 'Auteurs', 'Année', 'DOI', 'Domaine', 'Statut']);
        
        foreach ($publications as $pub) {
            fputcsv($output, [
                $pub['titre'],
                $pub['type_libelle'] ?? '',
                $pub['auteurs'] ?? '',
                $pub['annee'],
                $pub['doi'] ?? '',
                $pub['domaine'] ?? '',
                $pub['statut']
            ]);
        }
        
        fclose($output);
        exit;
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