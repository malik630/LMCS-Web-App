<?php

require_once __DIR__ . '/../../vendor/tcpdf/tcpdf.php';

class UserReservationsPDFGenerator
{
    private $pdf;
    private $data;
    private $user;
    
    public function __construct($data, $user)
    {
        $this->data = $data;
        $this->user = $user;
        $this->pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);
        $this->pdf->SetMargins(15, 15, 15);
        $this->pdf->SetAutoPageBreak(true, 15);
    }
    
    public function generate()
    {
        $this->addCoverPage();
        $this->addStatistics();
        $this->addReservationsList();
        return $this->pdf;
    }
    
    private function addCoverPage()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', 'B', 28);
        $this->pdf->SetY(70);
        $this->pdf->Cell(0, 15, 'Bilan des Réservations', 0, 1, 'C');
        
        $this->pdf->SetFont('helvetica', '', 16);
        $this->pdf->Ln(10);
        $this->pdf->Cell(0, 10, $this->user['prenom'] . ' ' . $this->user['nom'], 0, 1, 'C');
        
        $this->pdf->SetFont('helvetica', 'I', 12);
        $this->pdf->Cell(0, 8, $this->user['grade'], 0, 1, 'C');
        
        $this->pdf->Ln(15);
        $this->pdf->SetFont('helvetica', '', 14);
        $this->pdf->Cell(0, 10, date('d/m/Y'), 0, 1, 'C');
        
        $this->pdf->Ln(10);
        $this->pdf->SetFont('helvetica', 'B', 14);
        $this->pdf->Cell(0, 10, count($this->data) . ' réservation' . (count($this->data) > 1 ? 's' : ''), 0, 1, 'C');
    }
    
    private function addStatistics()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', 'B', 18);
        $this->pdf->Cell(0, 12, 'Statistiques', 0, 1);
        $this->pdf->Ln(5);
        
        $parStatut = [];
        $parEquipement = [];
        $totalHeures = 0;
        
        foreach ($this->data as $res) {
            $statut = $res['statut'];
            $parStatut[$statut] = ($parStatut[$statut] ?? 0) + 1;
            
            $equipement = $res['equipement'];
            $parEquipement[$equipement] = ($parEquipement[$equipement] ?? 0) + 1;

            if (!empty($res['date_debut']) && !empty($res['date_fin'])) {
                $debut = strtotime($res['date_debut']);
                $fin = strtotime($res['date_fin']);
                $totalHeures += ($fin - $debut) / 3600;
            }
        }
        
        $this->pdf->SetFont('helvetica', '', 11);
        
        $stats = [
            ['Total réservations', count($this->data)],
            ['Heures totales', round($totalHeures, 1) . ' h'],
            ['Équipements différents', count($parEquipement)]
        ];
        
        foreach ($stats as $stat) {
            $this->pdf->Cell(120, 8, $stat[0], 1);
            $this->pdf->Cell(60, 8, $stat[1], 1, 1);
        }
        
        // Par statut
        if (!empty($parStatut)) {
            $this->pdf->Ln(10);
            $this->pdf->SetFont('helvetica', 'B', 14);
            $this->pdf->Cell(0, 10, 'Répartition par statut', 0, 1);
            $this->pdf->SetFont('helvetica', '', 11);
            
            foreach ($parStatut as $statut => $count) {
                $this->pdf->Cell(120, 7, ucfirst($statut), 1);
                $this->pdf->Cell(60, 7, $count, 1, 1);
            }
        }
        
        // Top équipements
        if (!empty($parEquipement)) {
            $this->pdf->Ln(8);
            $this->pdf->SetFont('helvetica', 'B', 14);
            $this->pdf->Cell(0, 10, 'Équipements les plus réservés', 0, 1);
            $this->pdf->SetFont('helvetica', '', 11);
            
            arsort($parEquipement);
            $top = array_slice($parEquipement, 0, 10, true);
            foreach ($top as $eq => $count) {
                $this->pdf->Cell(120, 7, $eq, 1);
                $this->pdf->Cell(60, 7, $count, 1, 1);
            }
        }
    }
    
    private function addReservationsList()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', 'B', 16);
        $this->pdf->Cell(0, 12, 'Liste des Réservations', 0, 1);
        $this->pdf->Ln(3);
        
        $this->pdf->SetFont('helvetica', 'B', 8);
        $this->pdf->Cell(50, 7, 'Équipement', 1);
        $this->pdf->Cell(40, 7, 'Début', 1);
        $this->pdf->Cell(40, 7, 'Fin', 1);
        $this->pdf->Cell(30, 7, 'Statut', 1);
        $this->pdf->Cell(20, 7, 'Qté', 1, 1);
        
        $this->pdf->SetFont('helvetica', '', 7);
        
        foreach ($this->data as $res) {
            if ($this->pdf->GetY() > 260) {
                $this->pdf->AddPage();
                $this->pdf->SetFont('helvetica', 'B', 8);
                $this->pdf->Cell(50, 7, 'Équipement', 1);
                $this->pdf->Cell(40, 7, 'Début', 1);
                $this->pdf->Cell(40, 7, 'Fin', 1);
                $this->pdf->Cell(30, 7, 'Statut', 1);
                $this->pdf->Cell(20, 7, 'Qté', 1, 1);
                $this->pdf->SetFont('helvetica', '', 7);
            }
            
            $this->pdf->Cell(50, 6, substr($res['equipement'], 0, 25), 1);
            $this->pdf->Cell(40, 6, $res['date_debut'], 1);
            $this->pdf->Cell(40, 6, $res['date_fin'], 1);
            $this->pdf->Cell(30, 6, $res['statut'], 1);
            $this->pdf->Cell(20, 6, $res['nb_instances'], 1, 1);
        }
    }
    
    public function output($filename)
    {
        $this->pdf->Output($filename, 'D');
    }
}
?>