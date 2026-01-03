<?php

class AdminEquipementController extends Controller
{
    private $equipementModel;
    private $reservationModel;
    private $historiqueModel;
    
    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Accès refusé';
            header('Location: ' . BASE_URL);
            exit;
        }
        
        $this->equipementModel = $this->model('Equipement');
        $this->reservationModel = $this->model('Reservation');
        $this->historiqueModel = $this->model('HistoriqueEquipement');
    }
    
    public function index()
    {
        $equipements = $this->equipementModel->getAll();
        $conflits = $this->reservationModel->getConflitsDetails();
        
        $stats = [
            'total' => count($equipements),
            'libres' => $this->equipementModel->countAvailable(),
            'maintenance' => $this->equipementModel->countInMaintenance(),
            'conflits' => count($conflits)
        ];
        
        $this->view('AdminEquipementDashboardView', [
            'equipements' => $equipements,
            'stats' => $stats,
            'conflits' => $conflits
        ]);
    }
    
    public function create()
    {
        $types = $this->equipementModel->getTypes();
        $this->view('AdminCreateEquipementView', ['types' => $types]);
    }
    
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/equipements');
            return;
        }
        
        $equipementId = $this->equipementModel->insert('equipements', [
            'nom' => $_POST['nom'] ?? '',
            'type_equipement_id' => $_POST['type_equipement_id'] ?? null,
            'etat' => $_POST['etat'] ?? 'libre',
            'localisation' => $_POST['localisation'] ?? null,
            'capacite' => $_POST['capacite'] ?? null,
            'description' => $_POST['description'] ?? null
        ]);
        
        $_SESSION[$equipementId ? 'success' : 'error'] = $equipementId 
            ? 'Équipement créé avec succès' 
            : 'Erreur lors de la création';
        
        $this->redirect('admin/equipements');
    }
    
    public function edit($id)
    {
        $equipement = $this->equipementModel->getById($id);
        
        if (!$equipement) {
            $_SESSION['error'] = 'Équipement introuvable';
            $this->redirect('admin/equipements');
            return;
        }
        
        $types = $this->equipementModel->getTypes();
        $this->view('AdminEditEquipementView', [
            'equipement' => $equipement,
            'types' => $types
        ]);
    }
    
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/equipements');
            return;
        }
        
        $result = $this->equipementModel->updateById('equipements', $id, [
            'nom' => $_POST['nom'] ?? '',
            'type_equipement_id' => $_POST['type_equipement_id'] ?? null,
            'etat' => $_POST['etat'] ?? 'libre',
            'localisation' => $_POST['localisation'] ?? null,
            'capacite' => $_POST['capacite'] ?? null,
            'description' => $_POST['description'] ?? null
        ], 'id_equipement');
        
        if ($result) {
            $this->historiqueModel->log($id, $_SESSION['user_id'], 'etat_change');
        }
        
        $_SESSION[$result ? 'success' : 'error'] = $result 
            ? 'Équipement modifié avec succès' 
            : 'Erreur lors de la modification';
        
        $this->redirect('admin/equipements');
    }
    
    public function delete($id)
    {
        $result = $this->equipementModel->softDelete('equipements', $id, 'id_equipement');
        
        $_SESSION[$result ? 'success' : 'error'] = $result 
            ? 'Équipement supprimé avec succès' 
            : 'Erreur lors de la suppression';
        
        $this->redirect('admin/equipements');
    }
    
    public function rapports()
    {
        $dateDebut = $_GET['date_debut'] ?? date('Y-m-01');
        $dateFin = $_GET['date_fin'] ?? date('Y-m-t');
        
        $rapport = $this->equipementModel->getRapportUtilisation($dateDebut, $dateFin);
        $parUtilisateur = $this->equipementModel->getRapportParUtilisateur($dateDebut, $dateFin);
        
        $this->view('AdminEquipementRapportsView', [
            'rapport' => $rapport,
            'parUtilisateur' => $parUtilisateur,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin
        ]);
    }
    
    public function historique($equipementId = null)
    {
        if ($equipementId === null && isset($_GET['id'])) {
            $equipementId = $_GET['id'];
        }
        
        $historique = $equipementId 
            ? $this->equipementModel->getHistoriqueByEquipement($equipementId)
            : $this->equipementModel->getHistorique();
        
        $equipement = $equipementId ? $this->equipementModel->getById($equipementId) : null;
        
        $this->view('AdminEquipementHistoriqueView', [
            'historique' => $historique,
            'equipementId' => $equipementId,
            'equipement' => $equipement
        ]);
    }
    
    public function exportPDF()
    {
        $dateDebut = $_GET['date_debut'] ?? date('Y-m-01');
        $dateFin = $_GET['date_fin'] ?? date('Y-m-t');
        
        require_once __DIR__ . '/../utils/EquipementsPDFGenerator.php';
        
        $data = $this->equipementModel->getRapportUtilisation($dateDebut, $dateFin);
        
        $pdf = new EquipementsPDFGenerator($data, $dateDebut, $dateFin);
        $pdf->generate();
        $pdf->output('rapport_equipements_' . date('Ymd') . '.pdf');
    }
}