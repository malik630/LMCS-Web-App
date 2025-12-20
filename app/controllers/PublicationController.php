<?php

class PublicationController extends Controller
{
    public function index()
    {
        $publicationModel = $this->model('Publication');
        
        $data = [
            'publications' => $publicationModel->getAll(),
            'types' => $publicationModel->getTypes(),
            'authors' => $publicationModel->getAllAuthors(),
            'years' => $publicationModel->getYears(),
            'domains' => $publicationModel->getDomains()
        ];
        
        $this->view('PublicationView', $data);
    }
    
    public function download($id)
    {
        if (empty($id)) {
            $_SESSION['error'] = 'Publication non trouvée.';
            $this->redirect('publication');
            return;
        }
        
        $publicationModel = $this->model('Publication');
        $publication = $publicationModel->selectById('publications', $id, 'id_publication');
        
        if (!$publication) {
            $_SESSION['error'] = 'Publication non trouvée.';
            $this->redirect('publication');
            return;
        }
        
        // Vérifier lien externe d'abord
        if (!empty($publication['lien_telechargement'])) {
            header('Location: ' . $publication['lien_telechargement']);
            exit;
        }
        
        // Vérifier fichier PDF local
        if (empty($publication['fichier_pdf'])) {
            $_SESSION['error'] = 'Aucun fichier disponible pour cette publication.';
            $this->redirect('publication');
            return;
        }
        
        // Chemin du fichier
        $filePath = '../public/assets/documents/publications/' . $publication['fichier_pdf'];
        
        if (!file_exists($filePath)) {
            // Essayer sans le dossier publications
            $filePath = '../public/assets/documents/' . $publication['fichier_pdf'];
            if (!file_exists($filePath)) {
                $_SESSION['error'] = 'Fichier introuvable: ' . $publication['fichier_pdf'];
                $this->redirect('publication');
                return;
            }
        }
        
        // Nettoyer les buffers de sortie
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Headers pour afficher le PDF dans le navigateur
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($publication['fichier_pdf']) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($filePath));
        header('Accept-Ranges: bytes');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        // Lire et envoyer le fichier
        readfile($filePath);
        exit;
    }
}
?>