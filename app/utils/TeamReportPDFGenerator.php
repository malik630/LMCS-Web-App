<?php

require_once __DIR__ . '/../../vendor/tcpdf/tcpdf.php';

class TeamReportPDFGenerator
{
    private $pdf;
    private $team;
    private $membres;
    private $projets;
    private $publications;
    
    public function __construct($team, $membres, $projets, $publications)
    {
        $this->team = $team;
        $this->membres = $membres;
        $this->projets = $projets;
        $this->publications = $publications;
        $this->pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);
        $this->pdf->SetMargins(15, 15, 15);
        $this->pdf->SetAutoPageBreak(true, 15);
    }
    
    public function generate()
    {
        $this->addCoverPage();
        $this->addTeamInfo();
        $this->addMembers();
        $this->addProjects();
        $this->addPublications();
        $this->addStatistics();
        return $this->pdf;
    }
    
    private function addCoverPage()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', 'B', 28);
        $this->pdf->SetY(70);
        $this->pdf->Cell(0, 15, 'Rapport d\'Équipe', 0, 1, 'C');
        
        $this->pdf->SetFont('helvetica', 'B', 20);
        $this->pdf->Ln(10);
        $this->pdf->Cell(0, 12, $this->team['nom'], 0, 1, 'C');
        
        $this->pdf->SetFont('helvetica', '', 14);
        $this->pdf->Ln(15);
        $this->pdf->Cell(0, 10, date('d/m/Y'), 0, 1, 'C');
        
        $this->pdf->Ln(10);
        $this->pdf->SetFont('helvetica', 'I', 12);
        $stats = [
            count($this->membres) . ' membre' . (count($this->membres) > 1 ? 's' : ''),
            count($this->projets) . ' projet' . (count($this->projets) > 1 ? 's' : ''),
            count($this->publications) . ' publication' . (count($this->publications) > 1 ? 's' : '')
        ];
        $this->pdf->Cell(0, 8, implode(' • ', $stats), 0, 1, 'C');
    }
    
    private function addTeamInfo()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', 'B', 18);
        $this->pdf->Cell(0, 12, 'Informations de l\'Équipe', 0, 1);
        $this->pdf->Ln(5);
        
        $this->pdf->SetFont('helvetica', '', 11);
        
        $infos = [
            ['Nom', $this->team['nom']],
            ['Thématique', $this->team['thematique'] ?? '-'],
            ['Date de création', date('d/m/Y', strtotime($this->team['date_creation']))]
        ];
        
        // Chef d'équipe depuis les données de team
        if (!empty($this->team['chef_nom'])) {
            $infos[] = ['Chef d\'équipe', $this->team['chef_prenom'] . ' ' . $this->team['chef_nom']];
        }
        
        foreach ($infos as $info) {
            $this->pdf->Cell(60, 8, $info[0] . ':', 1);
            $this->pdf->Cell(120, 8, $info[1], 1, 1);
        }
        
        // Description
        if (!empty($this->team['description'])) {
            $this->pdf->Ln(10);
            $this->pdf->SetFont('helvetica', 'B', 12);
            $this->pdf->Cell(0, 8, 'Description', 0, 1);
            $this->pdf->SetFont('helvetica', '', 10);
            $this->pdf->MultiCell(0, 6, $this->team['description'], 0, 'L');
        }
    }
    
    private function addMembers()
    {
        $this->pdf->Ln(10);
        $this->pdf->SetFont('helvetica', 'B', 16);
        $this->pdf->Cell(0, 12, 'Membres (' . count($this->membres) . ')', 0, 1);
        
        $this->pdf->SetFont('helvetica', 'B', 9);
        $this->pdf->Cell(50, 7, 'Nom', 1);
        $this->pdf->Cell(45, 7, 'Grade', 1);
        $this->pdf->Cell(45, 7, 'Rôle dans l\'équipe', 1);
        $this->pdf->Cell(40, 7, 'Adhésion', 1, 1);
        
        $this->pdf->SetFont('helvetica', '', 8);
        foreach ($this->membres as $membre) {
            if ($this->pdf->GetY() > 260) {
                $this->pdf->AddPage();
                $this->pdf->SetFont('helvetica', 'B', 9);
                $this->pdf->Cell(50, 7, 'Nom', 1);
                $this->pdf->Cell(45, 7, 'Grade', 1);
                $this->pdf->Cell(45, 7, 'Rôle dans l\'équipe', 1);
                $this->pdf->Cell(40, 7, 'Adhésion', 1, 1);
                $this->pdf->SetFont('helvetica', '', 8);
            }
            
            $nom = $membre['prenom'] . ' ' . $membre['nom'];
            $date = date('d/m/Y', strtotime($membre['date_adhesion']));
            
            $this->pdf->Cell(50, 6, $nom, 1);
            $this->pdf->Cell(45, 6, $membre['grade'], 1);
            $this->pdf->Cell(45, 6, $membre['role_dans_equipe'] ?? '-', 1);
            $this->pdf->Cell(40, 6, $date, 1, 1);
        }
    }
    
    private function addProjects()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', 'B', 16);
        $this->pdf->Cell(0, 12, 'Projets de l\'Équipe (' . count($this->projets) . ')', 0, 1);
        $this->pdf->Ln(3);
        
        if (empty($this->projets)) {
            $this->pdf->SetFont('helvetica', 'I', 11);
            $this->pdf->Cell(0, 8, 'Aucun projet associé', 0, 1);
            return;
        }
        
        foreach ($this->projets as $i => $projet) {
            if ($this->pdf->GetY() > 240) {
                $this->pdf->AddPage();
            }
            
            $this->pdf->SetFont('helvetica', 'B', 11);
            $this->pdf->Cell(0, 8, ($i+1) . '. ' . $projet['titre'], 0, 1);
            
            $this->pdf->SetFont('helvetica', '', 9);
            $infos = [
                'Responsable: ' . $projet['responsable_prenom'] . ' ' . $projet['responsable_nom'],
                'Statut: ' . ucfirst($projet['statut'])
            ];
            
            if (!empty($projet['thematique'])) {
                $infos[] = 'Thématique: ' . $projet['thematique'];
            }
            
            if (!empty($projet['budget'])) {
                $infos[] = 'Budget: ' . number_format($projet['budget'], 2, ',', ' ') . ' DA';
            }
            
            foreach ($infos as $info) {
                $this->pdf->Cell(0, 5, '   ' . $info, 0, 1);
            }
            
            $this->pdf->Ln(3);
        }
    }
    
    private function addPublications()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', 'B', 16);
        $this->pdf->Cell(0, 12, 'Publications de l\'Équipe (' . count($this->publications) . ')', 0, 1);
        $this->pdf->Ln(3);
        
        if (empty($this->publications)) {
            $this->pdf->SetFont('helvetica', 'I', 11);
            $this->pdf->Cell(0, 8, 'Aucune publication associée', 0, 1);
            return;
        }
        
        // Grouper par année
        $parAnnee = [];
        foreach ($this->publications as $pub) {
            $annee = $pub['annee'];
            $parAnnee[$annee][] = $pub;
        }
        
        krsort($parAnnee);
        
        foreach ($parAnnee as $annee => $pubs) {
            $this->pdf->SetFont('helvetica', 'B', 12);
            $this->pdf->Cell(0, 8, $annee . ' (' . count($pubs) . ')', 0, 1);
            
            $this->pdf->SetFont('helvetica', '', 9);
            foreach ($pubs as $i => $pub) {
                if ($this->pdf->GetY() > 260) {
                    $this->pdf->AddPage();
                }
                
                $this->pdf->SetFont('helvetica', 'B', 9);
                $this->pdf->Cell(0, 6, '• ' . $pub['titre'], 0, 1);
                
                $this->pdf->SetFont('helvetica', '', 8);
                $type = $pub['type_libelle'] ?? 'Non spécifié';
                $this->pdf->Cell(0, 5, '   ' . $type, 0, 1);
                
                if (!empty($pub['doi'])) {
                    $this->pdf->Cell(0, 5, '   DOI: ' . $pub['doi'], 0, 1);
                }
            }
            
            $this->pdf->Ln(3);
        }
    }
    
    private function addStatistics()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', 'B', 18);
        $this->pdf->Cell(0, 12, 'Statistiques', 0, 1);
        $this->pdf->Ln(5);
        
        // Stats générales
        $this->pdf->SetFont('helvetica', '', 11);
        
        $stats = [
            ['Membres', count($this->membres)],
            ['Projets', count($this->projets)],
            ['Publications', count($this->publications)]
        ];
        
        // Calcul du budget total
        $budgetTotal = 0;
        foreach ($this->projets as $p) {
            $budgetTotal += floatval($p['budget'] ?? 0);
        }
        if ($budgetTotal > 0) {
            $stats[] = ['Budget total projets', number_format($budgetTotal, 2, ',', ' ') . ' DA'];
        }
        
        // Projets par statut
        $parStatut = [];
        foreach ($this->projets as $p) {
            $statut = ucfirst($p['statut']);
            $parStatut[$statut] = ($parStatut[$statut] ?? 0) + 1;
        }
        
        foreach ($stats as $stat) {
            $this->pdf->Cell(120, 8, $stat[0], 1);
            $this->pdf->Cell(60, 8, $stat[1], 1, 1);
        }
        
        if (!empty($parStatut)) {
            $this->pdf->Ln(10);
            $this->pdf->SetFont('helvetica', 'B', 14);
            $this->pdf->Cell(0, 10, 'Projets par statut', 0, 1);
            $this->pdf->SetFont('helvetica', '', 11);
            
            foreach ($parStatut as $statut => $count) {
                $this->pdf->Cell(120, 7, $statut, 1);
                $this->pdf->Cell(60, 7, $count, 1, 1);
            }
        }
        
        // Publications par type
        $parType = [];
        foreach ($this->publications as $pub) {
            $type = $pub['type_libelle'] ?? 'Non spécifié';
            $parType[$type] = ($parType[$type] ?? 0) + 1;
        }
        
        if (!empty($parType)) {
            $this->pdf->Ln(10);
            $this->pdf->SetFont('helvetica', 'B', 14);
            $this->pdf->Cell(0, 10, 'Publications par type', 0, 1);
            $this->pdf->SetFont('helvetica', '', 11);
            
            arsort($parType);
            foreach ($parType as $type => $count) {
                $this->pdf->Cell(120, 7, $type, 1);
                $this->pdf->Cell(60, 7, $count, 1, 1);
            }
        }
    }
    
    public function output($filename)
    {
        $this->pdf->Output($filename, 'D');
    }
}