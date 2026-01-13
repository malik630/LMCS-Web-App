<?php

require_once __DIR__ . '/../../vendor/tcpdf/tcpdf.php';

class ProjetsPDFGenerator
{
    private $pdf;
    private $projets;
    private $stats;
    
    public function __construct($projets, $stats)
    {
        $this->projets = $projets;
        $this->stats = $stats;
        $this->pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);
        $this->pdf->SetMargins(15, 15, 15);
        $this->pdf->SetAutoPageBreak(true, 15);
    }
    
    public function generate()
    {
        $this->addCoverPage();
        $this->addGlobalStats();
        $this->addStatsByYear();
        $this->addProjectsByYear();
        return $this->pdf;
    }
    
    private function addCoverPage()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', 'B', 24);
        $this->pdf->SetY(80);
        $this->pdf->Cell(0, 10, 'Rapport Projets LMCS', 0, 1, 'C');
        $this->pdf->SetFont('helvetica', '', 12);
        $this->pdf->Cell(0, 10, date('d/m/Y'), 0, 1, 'C');
        $this->pdf->Cell(0, 10, count($this->projets) . ' projets', 0, 1, 'C');
    }
    
    private function addGlobalStats()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', 'B', 16);
        $this->pdf->Cell(0, 10, 'Statistiques Globales', 0, 1);
        $this->pdf->SetFont('helvetica', '', 11);
        
        $stats = [
            ['Total projets', $this->stats['total'] ?? 0],
            ['En cours', $this->stats['en_cours'] ?? 0],
            ['Terminés', $this->stats['termine'] ?? 0],
            ['Soumis', $this->stats['soumis'] ?? 0],
            ['Budget total', number_format($this->stats['budget_total'] ?? 0) . ' €']
        ];
        
        foreach ($stats as $s) {
            $this->pdf->Cell(100, 8, $s[0], 1);
            $this->pdf->Cell(80, 8, $s[1], 1, 1);
        }
    }
    
    private function addStatsByYear()
    {
        $this->pdf->Ln(10);
        $this->pdf->SetFont('helvetica', 'B', 14);
        $this->pdf->Cell(0, 10, 'Statistiques par Année', 0, 1);
        
        $byYear = [];
        foreach ($this->projets as $p) {
            $year = date('Y', strtotime($p['date_debut']));
            if (!isset($byYear[$year])) {
                $byYear[$year] = ['total' => 0, 'budget' => 0];
            }
            $byYear[$year]['total']++;
            $byYear[$year]['budget'] += floatval($p['budget'] ?? 0);
        }
        ksort($byYear);
        
        $this->pdf->SetFont('helvetica', 'B', 10);
        $this->pdf->Cell(50, 8, 'Année', 1);
        $this->pdf->Cell(50, 8, 'Nb projets', 1);
        $this->pdf->Cell(80, 8, 'Budget', 1, 1);
        
        $this->pdf->SetFont('helvetica', '', 10);
        foreach ($byYear as $year => $data) {
            $this->pdf->Cell(50, 7, $year, 1);
            $this->pdf->Cell(50, 7, $data['total'], 1);
            $this->pdf->Cell(80, 7, number_format($data['budget']) . ' €', 1, 1);
        }
    }
    
    private function addProjectsByYear()
    {
        // Grouper par année
        $byYear = [];
        foreach ($this->projets as $p) {
            $year = date('Y', strtotime($p['date_debut']));
            $byYear[$year][] = $p;
        }
        ksort($byYear);
        
        foreach ($byYear as $year => $projects) {
            $this->pdf->AddPage();
            $this->pdf->SetFont('helvetica', 'B', 16);
            $this->pdf->Cell(0, 10, 'Projets de ' . $year, 0, 1);
            $this->pdf->Ln(5);
            
            foreach ($projects as $i => $p) {
                if ($this->pdf->GetY() > 250) $this->pdf->AddPage();
                
                $this->pdf->SetFont('helvetica', 'B', 11);
                $this->pdf->Cell(0, 8, ($i+1) . '. ' . $p['titre'], 0, 1);
                
                $this->pdf->SetFont('helvetica', '', 9);
                $this->pdf->Cell(50, 6, 'Responsable:', 1);
                $this->pdf->Cell(130, 6, $p['responsable_prenom'] . ' ' . $p['responsable_nom'], 1, 1);
                $this->pdf->Cell(50, 6, 'Thématique:', 1);
                $this->pdf->Cell(130, 6, $p['thematique'] ?? '-', 1, 1);
                $this->pdf->Cell(50, 6, 'Statut:', 1);
                $this->pdf->Cell(130, 6, $p['statut'], 1, 1);
                $this->pdf->Cell(50, 6, 'Membres:', 1);
                $this->pdf->Cell(130, 6, $p['nb_membres'], 1, 1);
                
                if (!empty($p['budget'])) {
                    $this->pdf->Cell(50, 6, 'Budget:', 1);
                    $this->pdf->Cell(130, 6, number_format($p['budget']) . ' €', 1, 1);
                }
                
                $this->pdf->Ln(3);
            }
        }
    }
    
    public function output($filename)
    {
        $this->pdf->Output($filename, 'D');
    }
}
?>