<?php

class AdminSettingsController extends Controller
{
    private $settingsModel;
    
    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Accès refusé. Vous devez être administrateur.';
            header('Location: ' . BASE_URL);
            exit;
        }
        
        $this->settingsModel = $this->model('Settings');
    }
    
    public function index()
    {
        $settings = $this->settingsModel->getAllSettings();
        $backups = $this->settingsModel->getAllBackups();
        
        // Récupérer les logos actuels
        $logoLabo = $this->getLogoFiles('../public/assets/images/logo_labo');
        $logoEsi = $this->getLogoFiles('../public/assets/images/logo_esi');
        
        $this->view('AdminSettingsView', [
            'settings' => $settings,
            'backups' => $backups,
            'logo_labo' => $logoLabo,
            'logo_esi' => $logoEsi
        ]);
    }
    
    private function getLogoFiles($directory)
    {
        if (!is_dir($directory)) {
            return null;
        }
        
        $files = scandir($directory);
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
        
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, $imageExtensions)) {
                return $file;
            }
        }
        
        return null;
    }
    
    public function updateTheme()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/parametres');
        }
        
        // Mettre à jour le thème
        $this->redirect('admin/parametres');
    }
    
    public function uploadLogo()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/parametres');
        }
        // Modifier le logo
        
        $this->redirect('admin/parametres');
    }
    
    public function backupDatabase()
    {
        try {
            $result = $this->settingsModel->createBackup();
            
            if ($result['success']) {
                $_SESSION['success'] = 'Sauvegarde créée avec succès.';
            } else {
                $_SESSION['error'] = $result['error'] ?? 'Erreur lors de la création de la sauvegarde.';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erreur : ' . $e->getMessage();
        }
        
        $this->redirect('admin/parametres');
    }
    
    public function downloadBackup($filename = null)
    {
        if (!$filename) {
            $_SESSION['error'] = 'Nom de fichier manquant.';
            $this->redirect('admin/parametres');
        }
        
        $filepath = '../backups/' . basename($filename);
        
        if (!file_exists($filepath)) {
            $_SESSION['error'] = 'Fichier de sauvegarde introuvable.';
            $this->redirect('admin/parametres');
        }
        
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }
    
    public function restoreDatabase()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/parametres');
        }
        
        if (!isset($_FILES['backup_file']) || $_FILES['backup_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Erreur lors du téléchargement du fichier.';
            $this->redirect('admin/parametres');
        }
        
        $file = $_FILES['backup_file'];
        if (pathinfo($file['name'], PATHINFO_EXTENSION) !== 'sql') {
            $_SESSION['error'] = 'Le fichier doit être au format .sql';
            $this->redirect('admin/parametres');
        }
        
        try {
            $result = $this->settingsModel->restoreBackup($file['tmp_name']);
            
            if ($result['success']) {
                $_SESSION['success'] = 'Base de données restaurée avec succès.';
            } else {
                $_SESSION['error'] = $result['error'] ?? 'Erreur lors de la restauration.';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la restauration : ' . $e->getMessage();
        }
        
        $this->redirect('admin/parametres');
    }
    
    public function deleteBackup($filename = null)
    {
        if (!$filename) {
            $_SESSION['error'] = 'Nom de fichier manquant.';
            $this->redirect('admin/parametres');
        }
        
        $filepath = '../backups/' . basename($filename);
        
        if (file_exists($filepath)) {
            unlink($filepath);
            $this->settingsModel->deleteBackup($filename);
            $_SESSION['success'] = 'Sauvegarde supprimée avec succès.';
        } else {
            $_SESSION['error'] = 'Fichier introuvable.';
        }
        
        $this->redirect('admin/parametres');
    }
    
    public function listBackups()
    {
        $backups = $this->settingsModel->getAllBackups();
        $this->json(['backups' => $backups]);
    }
}
?>