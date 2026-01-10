<?php

require_once __DIR__ . '/../../vendor/tcpdf/tcpdf.php';

class UserProjetsPDFGenerator
{
    private $pdf;
    private $projets;
    private $user;
    private $stats;
    
    public function __construct($projets, $user, $stats)
    {
        $this->projets = $projets;
        $this->user = $user;
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
        $this->addStatistics();
        $this->addProjetsList();
        return $this->pdf;
    }
    
    private function addCoverPage()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', 'B', 28);
        $this->pdf->SetY(70);
        $this->pdf->Cell(0, 15, 'Bilan des Projets', 0, 1, 'C');
        
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
        $this->pdf->Cell(0, 10, $this->stats['total'] . ' projet' . ($this->stats['total'] > 1 ? 's' : ''), 0, 1, 'C');
    }
    
    private function addStatistics()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', 'B', 18);
        $this->pdf->Cell(0, 12, 'Statistiques Générales', 0, 1);
        $this->pdf->Ln(5);
        
        $this->pdf->SetFont('helvetica', '', 11);
        
        $stats = [
            ['Total projets', $this->stats['total']],
            ['En tant que responsable', $this->stats['as_responsable']],
            ['En tant que membre', $this->stats['as_membre']],
            ['Projets en cours', $this->stats['en_cours']],
            ['Projets terminés', $this->stats['termine']],
            ['Budget total', number_format($this->stats['budget_total'], 2, ',', ' ') . ' DA'],
            ['Collaborateurs uniques', $this->stats['nb_collaborateurs']],
            ['Publications liées', $this->stats['publications_totales']]
        ];
        
        foreach ($stats as $stat) {
            $this->pdf->Cell(120, 8, $stat[0], 1);
            $this->pdf->Cell(60, 8, $stat[1], 1, 1);
        }
        
        // Par thématique
        if (!empty($this->stats['par_thematique'])) {
            $this->pdf->Ln(10);
            $this->pdf->SetFont('helvetica', 'B', 14);
            $this->pdf->Cell(0, 10, 'Répartition par thématique', 0, 1);
            $this->pdf->SetFont('helvetica', '', 11);
            
            arsort($this->stats['par_thematique']);
            foreach ($this->stats['par_thematique'] as $them => $count) {
                $this->pdf->Cell(120, 7, $them, 1);
                $this->pdf->Cell(60, 7, $count, 1, 1);
            }
        }
    }
    
    private function addProjetsList()
    {
        foreach ($this->projets as $i => $projet) {
            $this->pdf->AddPage();
            $this->renderProjet($projet, $i + 1);
        }
    }
    
    private function renderProjet($projet, $num)
    {
        $this->pdf->SetFont('helvetica', 'B', 16);
        $this->pdf->Cell(0, 12, 'Projet ' . $num . ': ' . $projet['titre'], 0, 1);
        $this->pdf->Ln(3);
        
        // Informations générales
        $this->pdf->SetFont('helvetica', 'B', 12);
        $this->pdf->Cell(0, 8, 'Informations Générales', 0, 1);
        $this->pdf->SetFont('helvetica', '', 10);
        
        $infos = [
            ['Responsable', $projet['responsable_prenom'] . ' ' . $projet['responsable_nom']],
            ['Mon rôle', ($projet['responsable_id'] == $this->user['id_user']) ? 'Responsable' : ($projet['role_projet'] ?? 'Membre')],
            ['Thématique', $projet['thematique'] ?? '-'],
            ['Statut', ucfirst($projet['statut'])],
            ['Type de financement', $projet['type_financement'] ?? '-']
        ];
        
        if (!empty($projet['budget'])) {
            $infos[] = ['Budget', number_format($projet['budget'], 2, ',', ' ') . ' DA'];
        }
        
        if (!empty($projet['date_debut'])) {
            $infos[] = ['Date début', date('d/m/Y', strtotime($projet['date_debut']))];
        }
        
        if (!empty($projet['date_fin'])) {
            $infos[] = ['Date fin', date('d/m/Y', strtotime($projet['date_fin']))];
        }
        
        foreach ($infos as $info) {
            $this->pdf->Cell(60, 7, $info[0] . ':', 1);
            $this->pdf->Cell(120, 7, $info[1], 1, 1);
        }
        
        // Description
        if (!empty($projet['description'])) {
            $this->pdf->Ln(5);
            $this->pdf->SetFont('helvetica', 'B', 12);
            $this->pdf->Cell(0, 8, 'Description', 0, 1);
            $this->pdf->SetFont('helvetica', '', 10);
            $this->pdf->MultiCell(0, 6, $projet['description'], 0, 'L');
        }
        
        // Membres
        if (!empty($projet['membres'])) {
            $this->pdf->Ln(5);
            $this->pdf->SetFont('helvetica', 'B', 12);
            $this->pdf->Cell(0, 8, 'Membres (' . count($projet['membres']) . ')', 0, 1);
            
            $this->pdf->SetFont('helvetica', 'B', 9);
            $this->pdf->Cell(70, 7, 'Nom', 1);
            $this->pdf->Cell(60, 7, 'Grade', 1);
            $this->pdf->Cell(50, 7, 'Rôle', 1, 1);
            
            $this->pdf->SetFont('helvetica', '', 9);
            foreach ($projet['membres'] as $membre) {
                $this->pdf->Cell(70, 6, $membre['prenom'] . ' ' . $membre['nom'], 1);
                $this->pdf->Cell(60, 6, $membre['grade'], 1);
                $this->pdf->Cell(50, 6, $membre['role_projet'] ?? '-', 1, 1);
            }
        }
        
        // Publications
        if (!empty($projet['publications'])) {
            $this->pdf->Ln(5);
            $this->pdf->SetFont('helvetica', 'B', 12);
            $this->pdf->Cell(0, 8, 'Publications liées (' . count($projet['publications']) . ')', 0, 1);
            
            $this->pdf->SetFont('helvetica', '', 9);
            foreach ($projet['publications'] as $i => $pub) {
                if ($this->pdf->GetY() > 260) {
                    $this->pdf->AddPage();
                }
                
                $this->pdf->SetFont('helvetica', 'B', 9);
                $this->pdf->Cell(0, 6, ($i+1) . '. ' . $pub['titre'], 0, 1);
                $this->pdf->SetFont('helvetica', '', 8);
                $this->pdf->Cell(0, 5, '   ' . ($pub['type_libelle'] ?? '-') . ' - ' . $pub['annee'], 0, 1);
            }
        }
    }
    
    public function output($filename)
    {
        $this->pdf->Output($filename, 'D');
    }
}
?>