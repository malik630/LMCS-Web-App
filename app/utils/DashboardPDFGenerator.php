<?php

require_once __DIR__ . '/../../vendor/tcpdf/tcpdf.php';

class DashboardPDFGenerator
{
    private $pdf;
    private $user;
    private $stats;
    private $publications;
    private $projets;
    private $reservations;
    
    public function __construct($user, $stats, $publications, $projets, $reservations)
    {
        $this->user = $user;
        $this->stats = $stats;
        $this->publications = $publications;
        $this->projets = $projets;
        $this->reservations = $reservations;
        $this->pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);
        $this->pdf->SetMargins(15, 15, 15);
        $this->pdf->SetAutoPageBreak(true, 15);
    }
    
    public function generate()
    {
        $this->addCoverPage();
        $this->addSynthesis();
        $this->addPublicationsSection();
        $this->addProjetsSection();
        $this->addReservationsSection();
        return $this->pdf;
    }
    
    private function addCoverPage()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', 'B', 32);
        $this->pdf->SetY(60);
        $this->pdf->Cell(0, 18, 'Bilan Complet', 0, 1, 'C');
        
        $this->pdf->SetFont('helvetica', 'B', 20);
        $this->pdf->Cell(0, 12, $this->user['prenom'] . ' ' . $this->user['nom'], 0, 1, 'C');
        
        $this->pdf->SetFont('helvetica', '', 14);
        $this->pdf->Cell(0, 8, $this->user['grade'], 0, 1, 'C');
        
        if (!empty($this->user['poste'])) {
            $this->pdf->Cell(0, 8, $this->user['poste'], 0, 1, 'C');
        }
        
        $this->pdf->Ln(20);
        $this->pdf->SetFont('helvetica', '', 12);
        $this->pdf->Cell(0, 8, date('d/m/Y'), 0, 1, 'C');
    }
    
    private function addSynthesis()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', 'B', 20);
        $this->pdf->Cell(0, 12, 'Vue d\'Ensemble', 0, 1);
        $this->pdf->Ln(5);
        
        $this->pdf->SetFont('helvetica', 'B', 14);
        
        // Publications
        $this->pdf->Cell(0, 10, 'Publications', 0, 1);
        $this->pdf->SetFont('helvetica', '', 11);
        $pubStats = [
            ['Total publications', $this->stats['publications']['total']],
            ['Publiées', $this->stats['publications']['publie']],
            ['En attente', $this->stats['publications']['en_attente']],
            ['Productivité moyenne', $this->stats['publications']['productivite_moyenne'] . ' /an']
        ];
        
        foreach ($pubStats as $stat) {
            $this->pdf->Cell(100, 7, '  ' . $stat[0], 1);
            $this->pdf->Cell(80, 7, $stat[1], 1, 1);
        }
        
        $this->pdf->Ln(8);
        
        // Projets
        $this->pdf->SetFont('helvetica', 'B', 14);
        $this->pdf->Cell(0, 10, 'Projets', 0, 1);
        $this->pdf->SetFont('helvetica', '', 11);
        $projetStats = [
            ['Total projets', $this->stats['projets']['total']],
            ['Responsable', $this->stats['projets']['as_responsable']],
            ['Membre', $this->stats['projets']['as_membre']],
            ['En cours', $this->stats['projets']['en_cours']],
            ['Budget total', number_format($this->stats['projets']['budget_total'], 0, ',', ' ') . ' DA']
        ];
        
        foreach ($projetStats as $stat) {
            $this->pdf->Cell(100, 7, '  ' . $stat[0], 1);
            $this->pdf->Cell(80, 7, $stat[1], 1, 1);
        }
        
        $this->pdf->Ln(8);
        
        // Réservations
        $this->pdf->SetFont('helvetica', 'B', 14);
        $this->pdf->Cell(0, 10, 'Réservations', 0, 1);
        $this->pdf->SetFont('helvetica', '', 11);
        $resStats = [
            ['Total réservations', $this->stats['reservations']['total']],
            ['Heures totales', round($this->stats['reservations']['heures_totales']) . ' h'],
            ['Équipements utilisés', $this->stats['reservations']['nb_equipements']]
        ];
        
        foreach ($resStats as $stat) {
            $this->pdf->Cell(100, 7, '  ' . $stat[0], 1);
            $this->pdf->Cell(80, 7, $stat[1], 1, 1);
        }
        
        // Équipes
        if ($this->stats['teams']['total'] > 0) {
            $this->pdf->Ln(8);
            $this->pdf->SetFont('helvetica', 'B', 14);
            $this->pdf->Cell(0, 10, 'Équipes', 0, 1);
            $this->pdf->SetFont('helvetica', '', 11);
            $teamStats = [
                ['Total équipes', $this->stats['teams']['total']],
                ['Chef d\'équipe', $this->stats['teams']['as_chef']],
                ['Membre', $this->stats['teams']['as_membre']]
            ];
            
            foreach ($teamStats as $stat) {
                $this->pdf->Cell(100, 7, '  ' . $stat[0], 1);
                $this->pdf->Cell(80, 7, $stat[1], 1, 1);
            }
        }
    }
    
    private function addPublicationsSection()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', 'B', 18);
        $this->pdf->Cell(0, 12, 'Publications Récentes', 0, 1);
        $this->pdf->Ln(3);
        
        if (empty($this->publications)) {
            $this->pdf->SetFont('helvetica', 'I', 11);
            $this->pdf->Cell(0, 8, 'Aucune publication', 0, 1);
            return;
        }
        
        $recent = array_slice($this->publications, 0, 10);
        
        foreach ($recent as $i => $pub) {
            if ($this->pdf->GetY() > 250) {
                $this->pdf->AddPage();
            }
            
            $this->pdf->SetFont('helvetica', 'B', 10);
            $this->pdf->Cell(0, 6, ($i+1) . '. ' . $pub['titre'], 0, 1);
            
            $this->pdf->SetFont('helvetica', '', 8);
            $info = ($pub['type_libelle'] ?? 'Non spécifié') . ' - ' . $pub['annee'];
            $this->pdf->Cell(0, 5, '   ' . $info, 0, 1);
            $this->pdf->Ln(2);
        }
    }
    
    private function addProjetsSection()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', 'B', 18);
        $this->pdf->Cell(0, 12, 'Projets Actifs', 0, 1);
        $this->pdf->Ln(3);
        
        if (empty($this->projets)) {
            $this->pdf->SetFont('helvetica', 'I', 11);
            $this->pdf->Cell(0, 8, 'Aucun projet', 0, 1);
            return;
        }
        
        // Filtrer les projets actifs
        $actifs = array_filter($this->projets, function($p) {
            return $p['statut'] === 'en_cours';
        });
        
        if (empty($actifs)) {
            $actifs = array_slice($this->projets, 0, 5);
        }
        
        foreach ($actifs as $i => $projet) {
            if ($this->pdf->GetY() > 240) {
                $this->pdf->AddPage();
            }
            
            $this->pdf->SetFont('helvetica', 'B', 11);
            $this->pdf->Cell(0, 7, ($i+1) . '. ' . $projet['titre'], 0, 1);
            
            $this->pdf->SetFont('helvetica', '', 9);
            $role = ($projet['responsable_id'] == $this->user['id_user']) ? 'Responsable' : 'Membre';
            $this->pdf->Cell(0, 5, '   Rôle: ' . $role . ' | Statut: ' . ucfirst($projet['statut']), 0, 1);
            
            if (!empty($projet['thematique'])) {
                $this->pdf->Cell(0, 5, '   Thématique: ' . $projet['thematique'], 0, 1);
            }
            
            $this->pdf->Ln(3);
        }
    }
    
    private function addReservationsSection()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', 'B', 18);
        $this->pdf->Cell(0, 12, 'Réservations Récentes', 0, 1);
        $this->pdf->Ln(3);
        
        if (empty($this->reservations)) {
            $this->pdf->SetFont('helvetica', 'I', 11);
            $this->pdf->Cell(0, 8, 'Aucune réservation', 0, 1);
            return;
        }
        
        $recent = array_slice($this->reservations, 0, 15);
        
        $this->pdf->SetFont('helvetica', 'B', 8);
        $this->pdf->Cell(70, 7, 'Équipement', 1);
        $this->pdf->Cell(45, 7, 'Début', 1);
        $this->pdf->Cell(45, 7, 'Fin', 1);
        $this->pdf->Cell(20, 7, 'Statut', 1, 1);
        
        $this->pdf->SetFont('helvetica', '', 7);
        
        foreach ($recent as $res) {
            if ($this->pdf->GetY() > 260) {
                $this->pdf->AddPage();
                $this->pdf->SetFont('helvetica', 'B', 8);
                $this->pdf->Cell(70, 7, 'Équipement', 1);
                $this->pdf->Cell(45, 7, 'Début', 1);
                $this->pdf->Cell(45, 7, 'Fin', 1);
                $this->pdf->Cell(20, 7, 'Statut', 1, 1);
                $this->pdf->SetFont('helvetica', '', 7);
            }
            
            $equipement = substr($res['equipement_nom'], 0, 35);
            $debut = date('d/m/Y H:i', strtotime($res['date_debut']));
            $fin = date('d/m/Y H:i', strtotime($res['date_fin']));
            $statut = substr(ucfirst($res['statut']), 0, 10);
            
            $this->pdf->Cell(70, 6, $equipement, 1);
            $this->pdf->Cell(45, 6, $debut, 1);
            $this->pdf->Cell(45, 6, $fin, 1);
            $this->pdf->Cell(20, 6, $statut, 1, 1);
        }
    }
    
    public function output($filename)
    {
        $this->pdf->Output($filename, 'D');
    }
}
?>