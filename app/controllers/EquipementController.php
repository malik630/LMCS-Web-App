<?php

class EquipementController extends Controller
{
    public function index()
    {
        $equipementModel = $this->model('Equipement');
        $equipements = $equipementModel->getAll();
        
        $this->view('EquipementView', $equipements);
    }
    
    public function statistics()
    {
        $equipementModel = $this->model('Equipement');
        
        $data = [
            'globalStats' => $equipementModel->getStatistics(),
            'statsByType' => $equipementModel->getStatisticsByType(),
            'mostUsed' => $equipementModel->getWithReservations(10)
        ];
        
        $this->view('EquipementStatsView', $data);
    }
    
    public function availability()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $equipementId = $_POST['equipement_id'] ?? '';
        $dateDebut = $_POST['date_debut'] ?? '';
        $dateFin = $_POST['date_fin'] ?? '';
        
        if (empty($equipementId) || empty($dateDebut) || empty($dateFin)) {
            $this->json(['error' => 'Missing parameters'], 400);
            return;
        }
        
        $equipementModel = $this->model('Equipement');
        $available = $equipementModel->isAvailable($equipementId, $dateDebut, $dateFin);
        
        // Obtenir aussi les créneaux réservés
        $reservationModel = $this->model('Reservation');
        $reservations = $reservationModel->getByEquipement($equipementId);
        
        $slots = array_map(function($r) {
            return [
                'start' => $r['date_debut'],
                'end' => $r['date_fin'],
                'status' => $r['statut']
            ];
        }, $reservations);
        
        $this->json([
            'available' => $available,
            'reserved_slots' => $slots
        ]);
    }
}
?>