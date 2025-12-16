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
            'confirmee' => ['text' => 'Confirmée', 'type' => 'success'],
            'annulee' => ['text' => 'Annulée', 'type' => 'danger'],
            'terminee' => ['text' => 'Terminée', 'type' => 'info']
        ]
    ];
    
    public function render()
    {
        $this->renderHeader();
        $this->renderDashboard();
        $this->renderFooter();
    }
    
    private function renderDashboard()
    {
        ?>
<div class="container mx-auto px-4 py-8">
    <?php $this->renderUserHeader(); ?>

    <div class="grid gap-4">
        <?php 
        $this->renderSection('Mes Projets', 'projets', 'Aucun projet en cours');
        $this->renderSection('Mes Publications', 'publications', 'Aucune publication');
        $this->renderSection('Mes Réservations', 'reservations', 'Aucune réservation');
        $this->renderSection('Mes Équipes', 'equipes', 'Aucune équipe');
        ?>
    </div>
</div>
<?php
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
            <?php echo HtmlHelper::button('Modifier mon profil', BASE_URL . 'dashboard/profile', 'primary', 'edit'); ?>
        </div>
    </div>
</div>
<?php
    }
    
    private function renderSection($title, $dataKey, $emptyMessage)
    {
        $items = $this->get($dataKey, []);
        
        Section::create($title, function() use ($items, $dataKey, $emptyMessage) {
            if (!empty($items)) {
                echo '<div class="space-y-4">';
                
                foreach (array_slice($items, 0, count($items)) as $item) {
                    $this->renderCard($dataKey, $item);
                }
                
                echo '</div>';
            } else {
                echo HtmlHelper::emptyState($emptyMessage);
            }
        }, 'bg-white');
    }
    
    private function renderCard($type, $data)
    {
        $cardData = match($type) {
            'projets' => $this->buildProjetCard($data),
            'publications' => $this->buildPublicationCard($data),
            'reservations' => $this->buildReservationCard($data),
            'equipes' => $this->buildEquipeCard($data),
            default => []
        };
        
        Card::dashboard($cardData);
    }
    
    private function buildProjetCard($projet)
    {
        $status = $this->statusConfig['projet'][$projet['statut']] ?? null;
        
        return [
            'title' => $projet['titre'],
            'badge' => $status ? $status['text'] : null,
            'badge_type' => $status ? $status['type'] : 'primary',
            'description' => $projet['description'] ?? null,
            'items' => array_filter([
                ['label' => 'Thématique', 'value' => $projet['thematique'] ?? null],
                ['label' => 'Rôle', 'value' => $projet['role_projet'] ?? null],
                ['label' => 'Date début', 'value' => $projet['date_debut'] ?? null],
                ['label' => 'Date fin', 'value' => $projet['date_fin'] ?? null],
                ['label' => 'Responsable', 
                 'value' => isset($projet['responsable_nom']) ? 
                             $projet['responsable_prenom'] . ' ' . $projet['responsable_nom'] : null],
                ['label' => 'Budget', 'value' => $projet['budget'] ?? null],
                ['label' => 'Financement', 'value' => $projet['type_financement'] ?? null]
            ], fn($item) => !empty($item['value']))
        ];
    }
    
    private function buildPublicationCard($publication)
    {
        $downloadUrl = null;
        if (!empty($publication['lien_telechargement'])) {
            $downloadUrl = $publication['lien_telechargement'];
        }
        
        return [
            'title' => $publication['titre'],
            'meta' => [
                ['type' => 'text', 'value' => $publication['annee']],
                [
                    'type' => 'badge', 
                    'value' => $publication['type_libelle'] ?? null, 
                    'badge_type' => 'success'
                ]
            ],
            'description' => $publication['resume'] ?? null,
            'items' => array_filter([
                ['label' => 'DOI', 'value' => $publication['doi'] ?? null],
                ['label' => 'Domaine', 'value' => $publication['domaine'] ?? null],
                ['label' => 'Date de publication', 'value' => $publication['date_publication'] ?? null],
                ['label' => 'Date de soumission', 'value' => $publication['date_soumission'] ?? null],
                ['label' => 'Statut', 'value' => ucfirst($publication['statut'] ?? '')],
            ], fn($item) => !empty($item['value'])),
            'footer_link' => $downloadUrl ? [
                'text' => 'Télécharger',
                'url' => $downloadUrl,
                'icon' => 'download',
                'target' => '_blank'
            ] : null
        ];
    }
    
    private function buildReservationCard($reservation)
    {
        $status = $this->statusConfig['reservation'][$reservation['statut']] ?? null;
        
        return [
            'title' => $reservation['equipement_nom'],
            'items' => [
                [
                    'label' => 'Du', 
                    'value' => DateHelper::format($reservation['date_debut'], 'd/m/Y H:i')
                ],
                [
                    'label' => 'Au', 
                    'value' => DateHelper::format($reservation['date_fin'], 'd/m/Y H:i')
                ]
            ],
            'badge' => $status ? $status['text'] : null,
            'badge_type' => $status ? $status['type'] : 'primary'
        ];
    }
    
    private function buildEquipeCard($equipe)
    {
        return [
            'title' => $equipe['nom'],
            'description' => $equipe['thematique'] ?? null,
            'badge' => $equipe['role_dans_equipe'] ?? null,
            'badge_type' => 'orange'
        ];
    }
}
?>