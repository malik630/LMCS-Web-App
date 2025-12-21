<?php

require_once __DIR__ . '/../helpers/DateHelper.php';
require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/ImageHelper.php';
require_once 'components/Section.php';
require_once 'components/Card.php';

class DashboardView extends View
{
    protected $pageTitle = 'Tableau de bord - LMCS';
    
    private $statusConfig = [
        'projet' => [
            'en_cours' => ['text' => 'En cours', 'type' => 'primary'],
            'termine' => ['text' => 'Terminé', 'type' => 'success'],
            'soumis' => ['text' => 'Soumis', 'type' => 'warning']
        ],
        'reservation' => [
            'en_attente' => ['text' => 'En attente', 'type' => 'warning'],
            'confirmee' => ['text' => 'Confirmée', 'type' => 'success'],
            'annulee' => ['text' => 'Annulée', 'type' => 'danger'],
            'terminee' => ['text' => 'Terminée', 'type' => 'info'],
            'demande_annulation' => ['text' => 'Demande d\'annulation', 'type' => 'orange']
        ]
    ];
    
    private $sections = [
        'projets' => ['title' => 'Mes Projets', 'empty' => 'Aucun projet en cours'],
        'publications' => ['title' => 'Mes Publications', 'empty' => 'Aucune publication'],
        'reservations' => ['title' => 'Mes Réservations', 'empty' => 'Aucune réservation'],
        'historique' => ['title' => 'Historique de mes réservations', 'empty' => 'Aucun historique'],
        'equipes' => ['title' => 'Mes Équipes', 'empty' => 'Aucune équipe']
    ];
    
    public function render()
    {
        $this->renderHeader();
        echo '<div class="container mx-auto px-4 py-8">';
        $this->renderUserHeader();
        $this->renderDashboardSections();
        echo '</div>';
        $this->renderFooter();
    }
    
    private function renderUserHeader()
    {
        $user = $this->get('user');
        
        ?>
<div class="bg-white rounded-lg shadow-lg p-6 mb-8">
    <div class="flex items-center gap-6">
        <div class="flex-shrink-0">
            <?php ImageHelper::renderUserPhoto($user); ?>
        </div>
        <div class="flex-grow">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                Bienvenue, <?php echo $this->escape($user['prenom'] . ' ' . $user['nom']); ?>
            </h1>
            <?php 
            echo HtmlHelper::infoList([
                ['label' => 'Grade', 'value' => $user['grade']],
                ['label' => 'Poste', 'value' => $user['poste'] ?? null],
                ['label' => 'Rôle', 'value' => ucfirst($user['role'])]
            ], 'space-y-1 text-gray-600');
            ?>
        </div>
        <div>
            <?php echo HtmlHelper::button(
                'Modifier mon profil',
                BASE_URL . 'dashboard/profile',
                'primary',
                'edit'
            ); ?>
        </div>
    </div>
</div>
<?php
    }
    
    private function renderDashboardSections()
    {
        echo '<div class="grid gap-4">';
        foreach ($this->sections as $key => $config) {
            $this->renderSection($key, $config['title'], $config['empty']);
        }
        echo '</div>';
    }
    
    private function renderSection($dataKey, $title, $emptyMessage)
    {
        $items = $this->get($dataKey, []);
        
        Section::create($title, function() use ($items, $dataKey, $emptyMessage) {
            if (empty($items)) {
                echo HtmlHelper::emptyState($emptyMessage);
                return;
            }
            
            echo '<div class="space-y-4">';
            foreach ($items as $item) {
                $this->renderCard($dataKey, $item);
            }
            echo '</div>';
        }, 'bg-white');
    }
    
    private function renderCard($type, $data)
    {
        $builders = [
            'projets' => 'buildProjetCard',
            'publications' => 'buildPublicationCard',
            'reservations' => 'buildReservationCard',
            'historique' => 'buildHistoriqueCard',
            'equipes' => 'buildEquipeCard'
        ];
        
        $builder = $builders[$type] ?? null;
        if ($builder && method_exists($this, $builder)) {
            Card::dashboard($this->$builder($data));
        }
    }
    
    private function buildProjetCard(array $projet)
    {
        $status = $this->statusConfig['projet'][$projet['statut']] ?? null;
        
        return [
            'title' => $projet['titre'],
            'badge' => $status['text'] ?? null,
            'badge_type' => $status['type'] ?? 'primary',
            'description' => $projet['description'] ?? null,
            'items' => $this->buildProjetItems($projet)
        ];
    }
    
    private function buildProjetItems(array $projet)
    {
        return array_filter([
            ['label' => 'Thématique', 'value' => $projet['thematique'] ?? null],
            ['label' => 'Rôle', 'value' => $projet['role_projet'] ?? null],
            ['label' => 'Date début', 'value' => $projet['date_debut'] ?? null],
            ['label' => 'Date fin', 'value' => $projet['date_fin'] ?? null],
            ['label' => 'Responsable', 'value' => $this->getResponsableName($projet)],
            ['label' => 'Budget', 'value' => $projet['budget'] ?? null],
            ['label' => 'Financement', 'value' => $projet['type_financement'] ?? null]
        ], fn($item) => !empty($item['value']));
    }
    
    private function getResponsableName(array $projet)
    {
        if (!isset($projet['responsable_nom'])) return null;
        return $projet['responsable_prenom'] . ' ' . $projet['responsable_nom'];
    }
    
    private function buildPublicationCard(array $pub)
    {
        return [
            'title' => $pub['titre'],
            'meta' => [
                ['type' => 'text', 'value' => $pub['annee']],
                ['type' => 'badge', 'value' => $pub['type_libelle'] ?? null, 'badge_type' => 'success']
            ],
            'description' => $pub['resume'] ?? null,
            'items' => $this->buildPublicationItems($pub),
            'footer_link' => $this->buildPublicationLink($pub)
        ];
    }
    
    private function buildPublicationItems(array $pub)
    {
        return array_filter([
            ['label' => 'DOI', 'value' => $pub['doi'] ?? null],
            ['label' => 'Domaine', 'value' => $pub['domaine'] ?? null],
            ['label' => 'Date de publication', 'value' => $pub['date_publication'] ?? null],
            ['label' => 'Date de soumission', 'value' => $pub['date_soumission'] ?? null],
            ['label' => 'Statut', 'value' => !empty($pub['statut']) ? ucfirst($pub['statut']) : null]
        ], fn($item) => !empty($item['value']));
    }
    
    private function buildPublicationLink(array $pub)
    {
        $downloadUrl = $pub['lien_telechargement'] ?? null;
        
        if (!$downloadUrl) return null;
        
        return [
            'text' => 'Télécharger',
            'url' => $downloadUrl,
            'icon' => 'download',
            'target' => '_blank'
        ];
    }
    
    private function buildReservationCard(array $reservation)
    {
        $status = $this->statusConfig['reservation'][$reservation['statut']] ?? 
                  ['text' => ucfirst($reservation['statut']), 'type' => 'info'];
        
        $items = [
            ['label' => 'Du', 'value' => DateHelper::format($reservation['date_debut'], 'd/m/Y H:i')],
            ['label' => 'Au', 'value' => DateHelper::format($reservation['date_fin'], 'd/m/Y H:i')]
        ];
        
        if (!empty($reservation['motif'])) {
            $items[] = ['label' => 'Motif', 'value' => $reservation['motif']];
        }
        
        if (!empty($reservation['nb_instances'])) {
            $items[] = ['label' => 'Quantité', 'value' => $reservation['nb_instances']];
        }
        
        // ✅ CORRECTION: Ne pas afficher le bouton si demande d'annulation déjà envoyée
        $footerButton = null;
        if (in_array($reservation['statut'], ['en_attente', 'confirmee'])) {
            $footerButton = [
                'text' => 'Demander l\'annulation',
                'url' => BASE_URL . 'reservation/cancel/' . $reservation['id_reservation'],
                'type' => 'danger',
                'onclick_confirm' => 'Êtes-vous sûr de vouloir demander l\'annulation de cette réservation ?',
                // Ajout d'une classe personnalisée pour limiter la largeur
                'class' => 'inline-flex'
            ];
        }
        
        return [
            'title' => $reservation['equipement_nom'],
            'items' => $items,
            'badge' => $status['text'],
            'badge_type' => $status['type'],
            'footer_button' => $footerButton
        ];
    }
    
    private function buildEquipeCard(array $equipe)
    {
        $nbMembres = $equipe['nb_membres'] ?? null;
        
        $items = [];
        
        if (!empty($equipe['role_dans_equipe'])) {
            $items[] = ['label' => 'Mon rôle', 'value' => $equipe['role_dans_equipe']];
        }
        
        if ($nbMembres !== null) {
            $items[] = ['label' => 'Membres', 'value' => $nbMembres . ' membre' . ($nbMembres > 1 ? 's' : '')];
        }

        $footerLink = [
            'text' => 'Voir l\'équipe',
            'url' => BASE_URL . 'team/detail/' . $equipe['id_team'],
            'icon' => 'arrow-right'
        ];
        
        return [
            'title' => $equipe['nom'],
            'description' => $equipe['thematique'] ?? null,
            'badge' => $equipe['role_dans_equipe'] ?? 'Membre',
            'badge_type' => 'orange',
            'items' => $items,
            'footer_link' => $footerLink
        ];
    }
    
    // ✅ Simple : afficher l'historique
    private function buildHistoriqueCard(array $h)
    {
        $actions = [
            'reservation' => ['text' => 'Réservation', 'type' => 'primary'],
            'annulation' => ['text' => 'Annulation', 'type' => 'danger'],
            'fin_utilisation' => ['text' => 'Fin d\'utilisation', 'type' => 'success']
        ];
        
        $action = $actions[$h['action']] ?? ['text' => $h['action'], 'type' => 'info'];
        
        return [
            'title' => $h['equipement_nom'],
            'badge' => $action['text'],
            'badge_type' => $action['type'],
            'items' => [
                ['label' => 'Date', 'value' => DateHelper::format($h['date_action'], 'd/m/Y H:i')]
            ]
        ];
    }
}
?>