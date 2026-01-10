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

        if (empty($_POST['titre']) || empty($_POST['annee'])) {
            $_SESSION['error'] = 'Le titre et l\'année sont obligatoires';
            $this->redirect('adminPublication/create');
            return;
        }

        $publicationData = [
            'titre' => $_POST['titre'],
            'type_publication_id' => $_POST['type_publication_id'] ?? null,
            'resume' => $_POST['resume'] ?? null,
            'annee' => $_POST['annee'],
            'doi' => $_POST['doi'] ?? null,
            'lien_telechargement' => $_POST['fichier_pdf'] ?? null,
            'projet_id' => $_POST['projet_id'] ?? null,
            'domaine' => $_POST['domaine'] ?? null,
            'statut' => $_POST['statut'] ?? 'publie',
            'date_publication' => $_POST['date_publication'] ?? date('Y-m-d'),
            'date_soumission' => date('Y-m-d H:i:s')
        ];
        
        $publicationId = $this->publicationModel->insert('publications', $publicationData);
        
        if ($publicationId) {
            if (!empty($_POST['auteurs'])) {
                $auteurs = is_array($_POST['auteurs']) ? $_POST['auteurs'] : [$_POST['auteurs']];
                foreach ($auteurs as $index => $auteurId) {
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
        
        $publicationData = [
            'titre' => $_POST['titre'],
            'type_publication_id' => $_POST['type_publication_id'] ?? null,
            'resume' => $_POST['resume'] ?? null,
            'annee' => $_POST['annee'],
            'doi' => $_POST['doi'] ?? null,
            'lien_telechargement' => $_POST['lien_telechargement'] ?? null,
            'projet_id' => $_POST['projet_id'] ?? null,
            'domaine' => $_POST['domaine'] ?? null,
            'statut' => $_POST['statut'] ?? 'publie',
            'date_publication' => $_POST['date_publication'] ?? date('Y-m-d')
        ];
        
        $result = $this->publicationModel->updateById('publications', $id, $publicationData, 'id_publication');
        
        if ($result) {
            $this->publicationModel->update('publication_auteurs', ['is_deleted' => 1], ['publication_id' => $id]);
            
            if (!empty($_POST['auteurs'])) {
                $auteurs = is_array($_POST['auteurs']) ? $_POST['auteurs'] : [$_POST['auteurs']];
                foreach ($auteurs as $index => $auteurId) {
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
        
        $this->redirect('admin/publications');
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
        
        $this->redirect('admin/publications');
    }
    
    public function reject($id)
    {
        $result = $this->publicationModel->updateById('publications', $id, [
            'statut' => 'rejete'
        ], 'id_publication');
        
        $_SESSION[$result ? 'success' : 'error'] = $result 
            ? 'Publication rejetée' 
            : 'Erreur lors du rejet';
        
        $this->redirect('admin/publications');
    }
    
    public function delete($id)
    {
        $result = $this->publicationModel->softDelete('publications', $id, 'id_publication');
        
        $_SESSION[$result ? 'success' : 'error'] = $result 
            ? 'Publication supprimée' 
            : 'Erreur lors de la suppression';
        
        $this->redirect('admin/publications');
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
}
?>