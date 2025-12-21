<?php

class DashboardController extends Controller
{
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté pour accéder à cette page.';
            $this->redirect('auth/login');
            return;
        }
        $userModel = $this->model('User');
        $userId = $_SESSION['user_id'];
        $data = [
            'user' => $userModel->getById($userId),
            'projets' => $userModel->getUserProjects($userId),
            'publications' => $userModel->getUserPublications($userId),
            'reservations' => $userModel->getUserReservations($userId),
            'equipes' => $userModel->getUserTeams($userId),
            'historique' => $userModel->getUserHistory($userId)
        ];
        
        $this->view('DashboardView', $data);
    }
    
    public function profile()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté pour accéder à cette page.';
            $this->redirect('auth/login');
            return;
        }
        
        $userModel = $this->model('User');
        $userId = $_SESSION['user_id'];     
        $data = [
            'user' => $userModel->getById($userId),
            'documents' => $userModel->getUserDocuments($userId)
        ];
        
        $this->view('ProfileView', $data);
    }
    
    public function updateProfile()
    {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('dashboard/profile');
            return;
        }
        
        $userModel = $this->model('User');
        $userId = $_SESSION['user_id'];
        $data = [
            'nom' => $_POST['nom'] ?? '',
            'prenom' => $_POST['prenom'] ?? '',
            'email' => $_POST['email'] ?? '',
            'domaine_recherche' => $_POST['domaine_recherche'] ?? '',
            'biographie' => $_POST['biographie'] ?? ''
        ];

        if (empty($data['nom']) || empty($data['prenom']) || empty($data['email'])) {
            $_SESSION['error'] = 'Les champs nom, prénom et email sont obligatoires.';
            $this->redirect('dashboard/profile');
            return;
        }

        if ($userModel->emailExists($data['email'], $userId)) {
            $_SESSION['error'] = 'Cet email est déjà utilisé par un autre compte.';
            $this->redirect('dashboard/profile');
            return;
        }

        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadPhoto($_FILES['photo']);
            if ($uploadResult['success']) {
                $data['photo'] = $uploadResult['filename'];
                $user = $userModel->getById($userId);
                if (!empty($user['photo']) && file_exists('../public/assets/images/users/' . $user['photo'])) {
                    unlink('../public/assets/images/users/' . $user['photo']);
                }
            } else {
                $_SESSION['error'] = $uploadResult['message'];
                $this->redirect('dashboard/profile');
                return;
            }
        }

        if ($userModel->updateProfile($userId, $data)) {
            $_SESSION['nom_complet'] = $data['prenom'] . ' ' . $data['nom'];
            $_SESSION['email'] = $data['email'];
            if (isset($data['photo'])) {
                $_SESSION['photo'] = $data['photo'];
            }
            $_SESSION['success'] = 'Profil mis à jour avec succès.';
        } else {
            $_SESSION['error'] = 'Erreur lors de la mise à jour du profil.';
        }
        
        $this->redirect('dashboard/profile');
    }
    
    public function changePassword()
    {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('dashboard/profile');
            return;
        }
        
        $userModel = $this->model('User');
        $userId = $_SESSION['user_id'];
        
        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
            $_SESSION['error'] = 'Veuillez remplir tous les champs.';
            $this->redirect('dashboard/profile');
            return;
        }
        
        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'Les nouveaux mots de passe ne correspondent pas.';
            $this->redirect('dashboard/profile');
            return;
        }
        
        if (strlen($newPassword) < 6) {
            $_SESSION['error'] = 'Le mot de passe doit contenir au moins 6 caractères.';
            $this->redirect('dashboard/profile');
            return;
        }

        if ($userModel->changePassword($userId, $oldPassword, $newPassword)) {
            $_SESSION['success'] = 'Mot de passe modifié avec succès.';
        } else {
            $_SESSION['error'] = 'Ancien mot de passe incorrect.';
        }
        
        $this->redirect('dashboard/profile');
    }
    
    public function uploadDocument()
    {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('dashboard/profile');
            return;
        }
        
        $userModel = $this->model('User');
        $userId = $_SESSION['user_id'];
        $titre = $_POST['titre'] ?? '';
        $type = $_POST['type'] ?? '';
        
        if (empty($titre) || !isset($_FILES['document'])) {
            $_SESSION['error'] = 'Veuillez remplir tous les champs.';
            $this->redirect('dashboard/profile');
            return;
        }
        
        $file = $_FILES['document'];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Erreur lors de l\'upload du fichier.';
            $this->redirect('dashboard/profile');
            return;
        }

        $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            $_SESSION['error'] = 'Type de fichier non autorisé.';
            $this->redirect('dashboard/profile');
            return;
        }

        if ($file['size'] > 10 * 1024 * 1024) {
            $_SESSION['error'] = 'Le fichier est trop volumineux (max 10 MB).';
            $this->redirect('dashboard/profile');
            return;
        }

        $uploadDir = '../public/assets/documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = uniqid() . '_' . time() . '.' . $fileExtension;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            if ($userModel->addDocument($userId, $titre, $type, $filename, $file['size'])) {
                $_SESSION['success'] = 'Document ajouté avec succès.';
            } else {
                $_SESSION['error'] = 'Erreur lors de l\'enregistrement du document.';
                unlink($destination);
            }
        } else {
            $_SESSION['error'] = 'Erreur lors de l\'upload du fichier.';
        }
        
        $this->redirect('dashboard/profile');
    }
    
    public function deleteDocument($documentId)
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('dashboard/profile');
            return;
        }
        
        $userModel = $this->model('User');
        $userId = $_SESSION['user_id'];
        $documents = $userModel->getUserDocuments($userId);
        $document = null;
        foreach ($documents as $doc) {
            if ($doc['id_document'] == $documentId) {
                $document = $doc;
                break;
            }
        }
        
        if ($document && $userModel->deleteDocument($documentId, $userId)) {
            $filePath = '../public/assets/documents/' . $document['fichier'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $_SESSION['success'] = 'Document supprimé avec succès.';
        } else {
            $_SESSION['error'] = 'Erreur lors de la suppression du document.';
        }
        
        $this->redirect('dashboard/profile');
    }
    
    private function uploadPhoto($file)
    {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Type de fichier non autorisé (JPG, PNG, GIF uniquement).'];
        }
        
        if ($file['size'] > 2 * 1024 * 1024) {
            return ['success' => false, 'message' => 'La photo est trop volumineuse (max 2 MB).'];
        }
        
        $uploadDir = '../public/assets/images/users/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => true, 'filename' => $filename];
        }
        
        return ['success' => false, 'message' => 'Erreur lors de l\'upload de la photo.'];
    }
}
?>