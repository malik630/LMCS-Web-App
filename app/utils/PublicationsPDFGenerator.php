<?php

require_once __DIR__ . '/../../vendor/tcpdf/tcpdf.php';

class PublicationsPDFGenerator
{
    private $pdf;
    private $publications;
    private $title;
    
    public function __construct($publications, $title)
    {
        $this->publications = $publications;
        $this->title = $title;
        $this->pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);
        $this->pdf->SetMargins(15, 15, 15);
        $this->pdf->SetAutoPageBreak(true, 15);
    }
    
    public function generate()
    {
        $this->addCoverPage();
        $this->addSummary();
        $this->addPublicationsByType();
        $this->addStatistics();
        return $this->pdf;
    }
    
    private function addCoverPage()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', 'B', 28);
        $this->pdf->SetY(80);
        $this->pdf->Cell(0, 15, 'LMCS', 0, 1, 'C');
        
        $this->pdf->SetFont('helvetica', 'B', 20);
        $this->pdf->Cell(0, 12, $this->title, 0, 1, 'C');
        
        $this->pdf->SetFont('helvetica', '', 14);
        $this->pdf->Cell(0, 10, date('d/m/Y'), 0, 1, 'C');
        
        $this->pdf->SetY(140);
        $this->pdf->SetFont('helvetica', 'I', 12);
        $this->pdf->MultiCell(0, 8, count($this->publications) . ' publication' . (count($this->publications) > 1 ? 's' : '') . ' répertoriée' . (count($this->publications) > 1 ? 's' : ''), 0, 'C');
    }
    
    private function addSummary()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', 'B', 18);
        $this->pdf->Cell(0, 12, 'Résumé', 0, 1);
        $this->pdf->Ln(5);
        
        // Grouper par type
        $byType = [];
        $byYear = [];
        $allAuthors = [];
        
        foreach ($this->publications as $pub) {
            $type = $pub['type_libelle'] ?? 'Non spécifié';
            $byType[$type] = ($byType[$type] ?? 0) + 1;
            
            $year = $pub['annee'];
            $byYear[$year] = ($byYear[$year] ?? 0) + 1;
            
            if (!empty($pub['auteurs'])) {
                $authors = explode(', ', $pub['auteurs']);
                foreach ($authors as $author) {
                    $allAuthors[$author] = ($allAuthors[$author] ?? 0) + 1;
                }
            }
        }
        
        // Tableau par type
        $this->pdf->SetFont('helvetica', 'B', 14);
        $this->pdf->Cell(0, 10, 'Répartition par type', 0, 1);
        $this->pdf->SetFont('helvetica', '', 11);
        
        arsort($byType);
        foreach ($byType as $type => $count) {
            $this->pdf->Cell(120, 7, $type, 1);
            $this->pdf->Cell(60, 7, $count . ' publication' . ($count > 1 ? 's' : ''), 1, 1);
        }
        
        $this->pdf->Ln(8);
        
        // Tableau par année
        $this->pdf->SetFont('helvetica', 'B', 14);
        $this->pdf->Cell(0, 10, 'Répartition par année', 0, 1);
        $this->pdf->SetFont('helvetica', '', 11);
        
        ksort($byYear);
        foreach ($byYear as $year => $count) {
            $this->pdf->Cell(120, 7, $year, 1);
            $this->pdf->Cell(60, 7, $count . ' publication' . ($count > 1 ? 's' : ''), 1, 1);
        }
        
        $this->pdf->Ln(8);
        
        // Top auteurs
        $this->pdf->SetFont('helvetica', 'B', 14);
        $this->pdf->Cell(0, 10, 'Top 10 auteurs', 0, 1);
        $this->pdf->SetFont('helvetica', '', 11);
        
        arsort($allAuthors);
        $topAuthors = array_slice($allAuthors, 0, 10, true);
        foreach ($topAuthors as $author => $count) {
            $this->pdf->Cell(120, 7, $author, 1);
            $this->pdf->Cell(60, 7, $count . ' publication' . ($count > 1 ? 's' : ''), 1, 1);
        }
    }
    
    private function addPublicationsByType()
    {
        // Grouper par type
        $byType = [];
        foreach ($this->publications as $pub) {
            $type = $pub['type_libelle'] ?? 'Non spécifié';
            $byType[$type][] = $pub;
        }
        
        ksort($byType);
        
        foreach ($byType as $type => $pubs) {
            $this->pdf->AddPage();
            $this->pdf->SetFont('helvetica', 'B', 16);
            $this->pdf->Cell(0, 12, $type . ' (' . count($pubs) . ')', 0, 1);
            $this->pdf->Ln(3);
            
            foreach ($pubs as $i => $pub) {
                if ($this->pdf->GetY() > 250) {
                    $this->pdf->AddPage();
                }
                
                $this->renderPublication($pub, $i + 1);
            }
        }
    }
    
    private function renderPublication($pub, $num)
    {
        $this->pdf->SetFont('helvetica', 'B', 11);
        $this->pdf->Cell(10, 6, $num . '.', 0, 0);
        $this->pdf->MultiCell(170, 6, $pub['titre'], 0, 'L');
        
        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->SetX(25);
        
        // Auteurs
        if (!empty($pub['auteurs'])) {
            $this->pdf->MultiCell(165, 5, 'Auteurs: ' . $pub['auteurs'], 0, 'L');
        }
        
        // Année et type
        $info = [];
        $info[] = 'Année: ' . $pub['annee'];
        if (!empty($pub['type_libelle'])) {
            $info[] = 'Type: ' . $pub['type_libelle'];
        }
        if (!empty($pub['domaine'])) {
            $info[] = 'Domaine: ' . $pub['domaine'];
        }
        
        $this->pdf->SetX(25);
        $this->pdf->MultiCell(165, 5, implode(' | ', $info), 0, 'L');
        
        // DOI
        if (!empty($pub['doi'])) {
            $this->pdf->SetX(25);
            $this->pdf->MultiCell(165, 5, 'DOI: ' . $pub['doi'], 0, 'L');
        }
        
        // Résumé
        if (!empty($pub['resume'])) {
            $this->pdf->SetX(25);
            $this->pdf->SetFont('helvetica', 'I', 9);
            $resumeShort = strlen($pub['resume']) > 300 ? substr($pub['resume'], 0, 300) . '...' : $pub['resume'];
            $this->pdf->MultiCell(165, 5, $resumeShort, 0, 'L');
        }
        
        $this->pdf->Ln(4);
    }
    
    private function addStatistics()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', 'B', 18);
        $this->pdf->Cell(0, 12, 'Statistiques détaillées', 0, 1);
        $this->pdf->Ln(5);
        
        // Calculs statistiques
        $totalPubs = count($this->publications);
        $years = array_unique(array_column($this->publications, 'annee'));
        sort($years);
        $yearSpan = count($years) > 1 ? min($years) . ' - ' . max($years) : (min($years) ?? 'N/A');
        
        $avgPerYear = count($years) > 0 ? round($totalPubs / count($years), 1) : 0;
        
        $this->pdf->SetFont('helvetica', '', 11);
        
        $stats = [
            ['Nombre total de publications', $totalPubs],
            ['Période couverte', $yearSpan],
            ['Nombre d\'années', count($years)],
            ['Moyenne par an', $avgPerYear]
        ];
        
        foreach ($stats as $stat) {
            $this->pdf->Cell(120, 8, $stat[0], 1);
            $this->pdf->Cell(60, 8, $stat[1], 1, 1);
        }
    }
    
    public function output($filename)
    {
        $this->pdf->Output($filename, 'D');
    }
}
?>