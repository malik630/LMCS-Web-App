<?php

require_once __DIR__ . '/../../vendor/tcpdf/tcpdf.php';

class UserPublicationsPDFGenerator
{
    private $pdf;
    private $publications;
    private $user;
    
    public function __construct($publications, $user)
    {
        $this->publications = $publications;
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
        $this->addPublicationsList();
        return $this->pdf;
    }
    
    private function addCoverPage()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', 'B', 28);
        $this->pdf->SetY(70);
        $this->pdf->Cell(0, 15, 'Bilan des Publications', 0, 1, 'C');
        
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
        $nbPubs = count($this->publications);
        $this->pdf->Cell(0, 10, $nbPubs . ' publication' . ($nbPubs > 1 ? 's' : ''), 0, 1, 'C');
    }
    
    private function addStatistics()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', 'B', 18);
        $this->pdf->Cell(0, 12, 'Statistiques', 0, 1);
        $this->pdf->Ln(5);
        
        $parStatut = ['publie' => 0, 'en_attente' => 0, 'rejete' => 0];
        $parAnnee = [];
        $parType = [];
        
        foreach ($this->publications as $pub) {
            if (isset($parStatut[$pub['statut']])) {
                $parStatut[$pub['statut']]++;
            }
            
            $annee = $pub['annee'];
            $parAnnee[$annee] = ($parAnnee[$annee] ?? 0) + 1;
            
            $type = $pub['type_libelle'] ?? 'Non spécifié';
            $parType[$type] = ($parType[$type] ?? 0) + 1;
        }
        
        // Par statut
        $this->pdf->SetFont('helvetica', 'B', 14);
        $this->pdf->Cell(0, 10, 'Répartition par statut', 0, 1);
        $this->pdf->SetFont('helvetica', '', 11);
        
        $labels = [
            'publie' => 'Publiées',
            'en_attente' => 'En attente',
            'rejete' => 'Rejetées'
        ];
        
        foreach ($parStatut as $statut => $count) {
            $this->pdf->Cell(120, 7, $labels[$statut], 1);
            $this->pdf->Cell(60, 7, $count, 1, 1);
        }
        
        $this->pdf->Ln(8);
        
        // Par année
        $this->pdf->SetFont('helvetica', 'B', 14);
        $this->pdf->Cell(0, 10, 'Répartition par année', 0, 1);
        $this->pdf->SetFont('helvetica', '', 11);
        
        krsort($parAnnee);
        foreach ($parAnnee as $annee => $count) {
            $this->pdf->Cell(120, 7, $annee, 1);
            $this->pdf->Cell(60, 7, $count, 1, 1);
        }
        
        // Par type
        if (!empty($parType)) {
            $this->pdf->Ln(8);
            $this->pdf->SetFont('helvetica', 'B', 14);
            $this->pdf->Cell(0, 10, 'Répartition par type', 0, 1);
            $this->pdf->SetFont('helvetica', '', 11);
            
            arsort($parType);
            foreach ($parType as $type => $count) {
                $this->pdf->Cell(120, 7, $type, 1);
                $this->pdf->Cell(60, 7, $count, 1, 1);
            }
        }
    }
    
    private function addPublicationsList()
    {
        // Grouper par année
        $parAnnee = [];
        foreach ($this->publications as $pub) {
            $annee = $pub['annee'];
            $parAnnee[$annee][] = $pub;
        }
        
        krsort($parAnnee);
        
        foreach ($parAnnee as $annee => $pubs) {
            $this->pdf->AddPage();
            $this->pdf->SetFont('helvetica', 'B', 16);
            $this->pdf->Cell(0, 12, 'Publications ' . $annee, 0, 1);
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
        
        // Type et année
        $info = [];
        if (!empty($pub['type_libelle'])) {
            $info[] = $pub['type_libelle'];
        }
        $info[] = $pub['annee'];
        
        $this->pdf->MultiCell(165, 5, implode(' | ', $info), 0, 'L');
        
        // Domaine
        if (!empty($pub['domaine'])) {
            $this->pdf->SetX(25);
            $this->pdf->MultiCell(165, 5, 'Domaine: ' . $pub['domaine'], 0, 'L');
        }
        
        // DOI
        if (!empty($pub['doi'])) {
            $this->pdf->SetX(25);
            $this->pdf->MultiCell(165, 5, 'DOI: ' . $pub['doi'], 0, 'L');
        }
        
        // Statut
        $labels = ['publie' => 'Publiée', 'en_attente' => 'En attente', 'rejete' => 'Rejetée'];
        $this->pdf->SetX(25);
        $this->pdf->MultiCell(165, 5, 'Statut: ' . ($labels[$pub['statut']] ?? $pub['statut']), 0, 'L');
        
        // Résumé
        if (!empty($pub['resume'])) {
            $this->pdf->SetX(25);
            $this->pdf->SetFont('helvetica', 'I', 9);
            $resumeShort = strlen($pub['resume']) > 250 ? substr($pub['resume'], 0, 250) . '...' : $pub['resume'];
            $this->pdf->MultiCell(165, 5, $resumeShort, 0, 'L');
        }
        
        $this->pdf->Ln(4);
    }
    
    public function output($filename)
    {
        $this->pdf->Output($filename, 'D');
    }
}
?>