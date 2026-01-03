<?php

require_once __DIR__ . '/../../vendor/tcpdf/tcpdf.php';

class EquipementsPDFGenerator
{
    private $pdf;
    private $data;
    private $dateDebut;
    private $dateFin;
    
    public function __construct($data, $dateDebut, $dateFin)
    {
        $this->data = $data;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
        $this->pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);
        $this->pdf->SetMargins(15, 15, 15);
        $this->pdf->SetAutoPageBreak(true, 15);
    }
    
    public function generate()
    {
        $this->addCoverPage();
        $this->addTablePage();
        $this->addStatsPage();
        return $this->pdf;
    }
    
    private function addCoverPage()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', 'B', 24);
        $this->pdf->SetY(80);
        $this->pdf->Cell(0, 10, 'Rapport d\'Utilisation', 0, 1, 'C');
        $this->pdf->Cell(0, 10, 'Équipements', 0, 1, 'C');
        
        $this->pdf->SetFont('helvetica', '', 12);
        $this->pdf->Ln(10);
        $this->pdf->Cell(0, 8, 'Période : ' . date('d/m/Y', strtotime($this->dateDebut)), 0, 1, 'C');
        $this->pdf->Cell(0, 8, 'au ' . date('d/m/Y', strtotime($this->dateFin)), 0, 1, 'C');
    }
    
    private function addTablePage()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', 'B', 14);
        $this->pdf->Cell(0, 10, 'Taux d\'Occupation', 0, 1);
        
        $this->pdf->SetFont('helvetica', 'B', 9);
        $this->pdf->Cell(70, 8, 'Équipement', 1);
        $this->pdf->Cell(40, 8, 'Localisation', 1);
        $this->pdf->Cell(30, 8, 'Réservations', 1, 0, 'C');
        $this->pdf->Cell(30, 8, 'Heures', 1, 1, 'C');
        
        $this->pdf->SetFont('helvetica', '', 8);
        foreach ($this->data as $row) {
            $this->pdf->Cell(70, 7, $row['equipement'], 1);
            $this->pdf->Cell(40, 7, $row['localisation'], 1);
            $this->pdf->Cell(30, 7, $row['nb_reservations'], 1, 0, 'C');
            $this->pdf->Cell(30, 7, $row['heures_total'] . 'h', 1, 1, 'C');
        }
    }
    
    private function addStatsPage()
    {
        $totalRes = array_sum(array_column($this->data, 'nb_reservations'));
        $totalHeures = array_sum(array_column($this->data, 'heures_total'));
        
        $this->pdf->Ln(10);
        $this->pdf->SetFont('helvetica', 'B', 14);
        $this->pdf->Cell(0, 10, 'Statistiques Globales', 0, 1);
        
        $this->pdf->SetFont('helvetica', '', 11);
        $this->pdf->Cell(100, 8, 'Total Réservations', 1);
        $this->pdf->Cell(70, 8, $totalRes, 1, 1);
        
        $this->pdf->Cell(100, 8, 'Total Heures', 1);
        $this->pdf->Cell(70, 8, $totalHeures . 'h', 1, 1);
        
        $this->pdf->Cell(100, 8, 'Équipements Suivis', 1);
        $this->pdf->Cell(70, 8, count($this->data), 1, 1);
    }
    
    public function output($filename)
    {
        $this->pdf->Output($filename, 'D');
    }
}