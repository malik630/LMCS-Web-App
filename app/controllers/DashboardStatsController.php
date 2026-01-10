<?php

class DashboardStatsController extends Controller
{
    private function checkAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté.';
            $this->redirect('auth/login');
            return false;
        }
        return true;
    }
    
    public function index()
    {
        if (!$this->checkAuth()) return;
        
        $userId = $_SESSION['user_id'];
        $userModel = $this->model('User');
        
        $data = [
            'user' => $userModel->getById($userId),
            'publication_stats' => $this->getPublicationStats($userId),
            'projet_stats' => $this->getProjetStats($userId),
            'reservation_stats' => $this->getReservationStats($userId),
            'team_stats' => $this->getTeamStats($userId)
        ];
        
        $this->view('DashboardStatsView', $data);
    }
    
    private function getPublicationStats($userId)
    {
        $userModel = $this->model('User');
        $publications = $userModel->getUserPublications($userId);
        
        $stats = [
            'total' => count($publications),
            'publie' => 0,
            'en_attente' => 0,
            'rejete' => 0,
            'par_annee' => [],
            'par_type' => [],
            'derniere_annee' => null,
            'annees_actives' => 0,
            'productivite_moyenne' => 0
        ];
        
        $anneesUniques = [];
        
        foreach ($publications as $pub) {
            // Par statut
            $statut = $pub['statut'];
            if (isset($stats[$statut])) {
                $stats[$statut]++;
            }
            
            // Par année
            $annee = $pub['annee'];
            if (!isset($stats['par_annee'][$annee])) {
                $stats['par_annee'][$annee] = 0;
            }
            $stats['par_annee'][$annee]++;
            $anneesUniques[$annee] = true;
            
            // Par type
            $type = $pub['type_libelle'] ?? 'Non spécifié';
            if (!isset($stats['par_type'][$type])) {
                $stats['par_type'][$type] = 0;
            }
            $stats['par_type'][$type]++;
        }
        
        ksort($stats['par_annee']);
        $stats['annees_actives'] = count($anneesUniques);
        $stats['derniere_annee'] = !empty($anneesUniques) ? max(array_keys($anneesUniques)) : null;
        $stats['productivite_moyenne'] = $stats['annees_actives'] > 0 
            ? round($stats['total'] / $stats['annees_actives'], 2) 
            : 0;
        
        return $stats;
    }
    
    private function getProjetStats($userId)
    {
        $userModel = $this->model('User');
        $projetModel = $this->model('Projet');
        $projets = $userModel->getUserProjects($userId);
        
        $stats = [
            'total' => count($projets),
            'as_responsable' => 0,
            'as_membre' => 0,
            'en_cours' => 0,
            'termine' => 0,
            'soumis' => 0,
            'budget_total' => 0,
            'par_thematique' => [],
            'nb_collaborateurs' => 0,
            'publications_totales' => 0
        ];
        
        $collaborateurs = [];
        
        foreach ($projets as $projet) {
            // Par rôle
            if ($projet['responsable_id'] == $userId) {
                $stats['as_responsable']++;
            } else {
                $stats['as_membre']++;
            }
            
            // Par statut
            $statut = $projet['statut'];
            if (isset($stats[$statut])) {
                $stats[$statut]++;
            }
            
            // Budget
            $stats['budget_total'] += floatval($projet['budget'] ?? 0);
            
            // Par thématique
            $thematique = $projet['thematique'] ?? 'Non spécifié';
            if (!isset($stats['par_thematique'][$thematique])) {
                $stats['par_thematique'][$thematique] = 0;
            }
            $stats['par_thematique'][$thematique]++;
            
            // Collaborateurs
            $membres = $projetModel->getMembers($projet['id_projet']);
            foreach ($membres as $membre) {
                if ($membre['id_user'] != $userId) {
                    $collaborateurs[$membre['id_user']] = true;
                }
            }
            
            // Publications
            $publications = $projetModel->getPublications($projet['id_projet']);
            $stats['publications_totales'] += count($publications);
        }
        
        $stats['nb_collaborateurs'] = count($collaborateurs);
        
        return $stats;
    }
    
    private function getReservationStats($userId)
    {
        $userModel = $this->model('User');
        $reservations = $userModel->getUserReservations($userId, 1000);
        
        $stats = [
            'total' => count($reservations),
            'confirmee' => 0,
            'en_attente' => 0,
            'annulee' => 0,
            'terminee' => 0,
            'par_equipement' => [],
            'heures_totales' => 0,
            'nb_equipements' => 0
        ];
        
        $equipements = [];
        
        foreach ($reservations as $res) {
            // Par statut
            $statut = $res['statut'];
            if (isset($stats[$statut])) {
                $stats[$statut]++;
            }
            
            // Par équipement
            $equipement = $res['equipement_nom'];
            if (!isset($stats['par_equipement'][$equipement])) {
                $stats['par_equipement'][$equipement] = 0;
            }
            $stats['par_equipement'][$equipement]++;
            $equipements[$res['equipement_id']] = true;
            
            // Heures totales
            if (!empty($res['date_debut']) && !empty($res['date_fin'])) {
                $debut = strtotime($res['date_debut']);
                $fin = strtotime($res['date_fin']);
                $heures = ($fin - $debut) / 3600;
                $stats['heures_totales'] += $heures;
            }
        }
        
        $stats['nb_equipements'] = count($equipements);
        $stats['heures_totales'] = round($stats['heures_totales'], 1);
        
        return $stats;
    }
    
    private function getTeamStats($userId)
    {
        $userModel = $this->model('User');
        $teams = $userModel->getUserTeams($userId);
        
        $stats = [
            'total' => count($teams),
            'as_chef' => 0,
            'as_membre' => 0,
            'membres_totaux' => 0
        ];
        
        foreach ($teams as $team) {
            if ($team['chef_id'] == $userId) {
                $stats['as_chef']++;
            } else {
                $stats['as_membre']++;
            }
            
            $stats['membres_totaux'] += intval($team['nb_membres'] ?? 0);
        }
        
        return $stats;
    }
    
    public function rapportPublications()
    {
        if (!$this->checkAuth()) return;
        
        $userId = $_SESSION['user_id'];
        $userModel = $this->model('User');
        
        $user = $userModel->getById($userId);
        $publications = $userModel->getUserPublications($userId);
        
        require_once '../app/utils/UserPublicationsPDFGenerator.php';
        
        $generator = new UserPublicationsPDFGenerator($publications, $user);
        $generator->generate();
        $generator->output('publications_' . $user['nom'] . '_' . date('Y-m-d') . '.pdf');
    }
    
    public function rapportProjets()
    {
        if (!$this->checkAuth()) return;
        
        $userId = $_SESSION['user_id'];
        $userModel = $this->model('User');
        $projetModel = $this->model('Projet');
        
        $user = $userModel->getById($userId);
        $projets = $userModel->getUserProjects($userId);
        
        // Enrichir les projets
        foreach ($projets as &$projet) {
            $projet['membres'] = $projetModel->getMembers($projet['id_projet']);
            $projet['publications'] = $projetModel->getPublications($projet['id_projet']);
        }
        
        $stats = $this->getProjetStats($userId);
        
        require_once '../app/utils/UserProjetsPDFGenerator.php';
        
        $generator = new UserProjetsPDFGenerator($projets, $user, $stats);
        $generator->generate();
        $generator->output('projets_' . $user['nom'] . '_' . date('Y-m-d') . '.pdf');
    }
    
    public function rapportReservations()
    {
        if (!$this->checkAuth()) return;
        
        $userId = $_SESSION['user_id'];
        $userModel = $this->model('User');
        
        $user = $userModel->getById($userId);
        $reservations = $userModel->getUserReservations($userId, 1000);
        
        // Préparer les données pour le PDF
        $data = [];
        foreach ($reservations as $res) {
            $data[] = [
                'equipement' => $res['equipement_nom'],
                'localisation' => $res['localisation'] ?? '-',
                'date_debut' => date('d/m/Y H:i', strtotime($res['date_debut'])),
                'date_fin' => date('d/m/Y H:i', strtotime($res['date_fin'])),
                'statut' => ucfirst($res['statut']),
                'nb_instances' => $res['nb_instances'] ?? 1
            ];
        }
        
        require_once '../app/utils/UserReservationsPDFGenerator.php';
        
        $generator = new UserReservationsPDFGenerator($data, $user);
        $generator->generate();
        $generator->output('reservations_' . $user['nom'] . '_' . date('Y-m-d') . '.pdf');
    }
    
    public function rapportComplet()
    {
        if (!$this->checkAuth()) return;
        
        $userId = $_SESSION['user_id'];
        $userModel = $this->model('User');
        $projetModel = $this->model('Projet');
        
        $user = $userModel->getById($userId);
        $publications = $userModel->getUserPublications($userId);
        $projets = $userModel->getUserProjects($userId);
        $reservations = $userModel->getUserReservations($userId, 1000);
        
        foreach ($projets as &$projet) {
            $projet['membres'] = $projetModel->getMembers($projet['id_projet']);
            $projet['publications'] = $projetModel->getPublications($projet['id_projet']);
        }
        
        $stats = [
            'publications' => $this->getPublicationStats($userId),
            'projets' => $this->getProjetStats($userId),
            'reservations' => $this->getReservationStats($userId),
            'teams' => $this->getTeamStats($userId)
        ];
        
        require_once '../app/utils/DashboardPDFGenerator.php';
        
        $generator = new DashboardPDFGenerator($user, $stats, $publications, $projets, $reservations);
        $generator->generate();
        $generator->output('bilan_complet_' . $user['nom'] . '_' . date('Y-m-d') . '.pdf');
    }
}
?>