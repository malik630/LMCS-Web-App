<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/components/PageHeader.php';
require_once __DIR__ . '/components/StatsCard.php';
require_once __DIR__ . '/components/Section.php';

class DashboardProjetView extends View
{
    protected $pageTitle = 'Mes Projets - LMCS';
    
    public function render()
    {
        $this->renderHeader();
        $user = $this->get('user');
        $projets = $this->get('projets');
        $canCreate = $this->get('can_create');
        
        // Grouper par statut
        $parStatut = [
            'en_cours' => [],
            'termine' => [],
            'soumis' => []
        ];
        
        foreach ($projets as $projet) {
            if (isset($parStatut[$projet['statut']])) {
                $parStatut[$projet['statut']][] = $projet;
            }
        }
        
        // Page header
        $actions = [];
        
        if ($canCreate) {
            $actions[] = [
                'url' => BASE_URL . 'dashboardprojet/create',
                'text' => 'Nouveau Projet',
                'type' => 'primary',
                'icon' => 'plus'
            ];
        }
        
        $actions[] = [
            'url' => BASE_URL . 'dashboardstats/rapportProjets',
            'text' => 'Générer Rapport PDF',
            'type' => 'secondary',
            'icon' => 'download'
        ];
        
        PageHeader::render([
            'title' => 'Mes Projets',
            'subtitle' => count($projets) . ' projet' . (count($projets) > 1 ? 's' : '') . ' au total',
            'actions' => $actions
        ]);
        
        // Statistiques
        StatsCard::renderGrid([
            [
                'label' => 'En cours',
                'value' => count($parStatut['en_cours']),
                'icon' => 'briefcase',
                'color' => 'blue'
            ],
            [
                'label' => 'Terminés',
                'value' => count($parStatut['termine']),
                'icon' => 'check',
                'color' => 'green'
            ],
            [
                'label' => 'Soumis',
                'value' => count($parStatut['soumis']),
                'icon' => 'clock',
                'color' => 'yellow'
            ]
        ]);
        
        // Projets par statut
        if (!empty($parStatut['en_cours'])) {
            $this->renderProjetsByStatus('Projets En Cours', $parStatut['en_cours']);
        }
        
        if (!empty($parStatut['termine'])) {
            $this->renderProjetsByStatus('Projets Terminés', $parStatut['termine']);
        }
        
        if (!empty($parStatut['soumis'])) {
            $this->renderProjetsByStatus('Projets Soumis', $parStatut['soumis']);
        }
        
        // Empty state
        if (empty($projets)) {
            $this->renderEmptyState($canCreate);
        }
        
        PageHeader::close();
        $this->renderFooter();
    }
    
    private function renderProjetsByStatus($title, $projets)
    {
        Section::create($title, function() use ($projets) {
            echo '<div class="grid md:grid-cols-2 gap-6">';
            foreach ($projets as $projet) {
                $this->renderProjetCard($projet);
            }
            echo '</div>';
        }, 'bg-white');
    }
    
    private function renderProjetCard($projet)
    {
        $badgeType = [
            'en_cours' => 'primary',
            'termine' => 'success',
            'soumis' => 'warning'
        ][$projet['statut']] ?? 'info';
        
        $badgeText = [
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'soumis' => 'Soumis'
        ][$projet['statut']] ?? $projet['statut'];
        
        $isResponsable = $projet['is_responsable'];
        $nbMembres = count($projet['membres'] ?? []);
        $nbPublications = count($projet['publications'] ?? []);
        ?>
<div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
    <div class="flex justify-between items-start mb-3">
        <div class="flex-grow">
            <div class="flex items-center gap-2 mb-2">
                <?php echo HtmlHelper::badge($badgeText, $badgeType); ?>
                <?php if ($isResponsable): ?>
                <?php echo HtmlHelper::badge('Responsable', 'success'); ?>
                <?php else: ?>
                <?php echo HtmlHelper::badge($projet['role_projet'] ?? 'Membre', 'info'); ?>
                <?php endif; ?>
            </div>
            <h3 class="font-bold text-gray-900 text-xl"><?php echo $this->escape($projet['titre']); ?></h3>
        </div>
    </div>

    <?php if (!empty($projet['description'])): ?>
    <p class="text-gray-600 text-sm mb-4 line-clamp-3">
        <?php echo $this->escape(substr($projet['description'], 0, 150)); ?><?php echo strlen($projet['description']) > 150 ? '...' : ''; ?>
    </p>
    <?php endif; ?>

    <div class="grid grid-cols-3 gap-4 mb-4 p-3 bg-gray-50 rounded">
        <div class="text-center">
            <p class="text-2xl font-bold text-blue-600"><?php echo $nbMembres; ?></p>
            <p class="text-xs text-gray-600">Membres</p>
        </div>
        <div class="text-center">
            <p class="text-2xl font-bold text-green-600"><?php echo $nbPublications; ?></p>
            <p class="text-xs text-gray-600">Publications</p>
        </div>
        <div class="text-center">
            <p class="text-2xl font-bold text-purple-600">
                <?php echo !empty($projet['budget']) ? number_format($projet['budget']/1000, 0) . 'K' : '-'; ?>
            </p>
            <p class="text-xs text-gray-600">Budget (DA)</p>
        </div>
    </div>

    <div class="space-y-2 text-sm text-gray-600 mb-4">
        <?php if (!empty($projet['thematique'])): ?>
        <p><span class="font-semibold">Thématique:</span> <?php echo $this->escape($projet['thematique']); ?></p>
        <?php endif; ?>
        <?php if (!empty($projet['responsable_nom'])): ?>
        <p><span class="font-semibold">Responsable:</span>
            <?php echo $this->escape($projet['responsable_prenom'] . ' ' . $projet['responsable_nom']); ?></p>
        <?php endif; ?>
    </div>

    <div class="pt-4 border-t border-gray-200 flex gap-2">
        <a href="<?php echo BASE_URL; ?>dashboardprojet/detail/<?php echo $projet['id_projet']; ?>"
            class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition inline-flex items-center justify-center gap-2">
            <?php echo HtmlHelper::icon('eye', 'w-4 h-4'); ?>
            Voir détails
        </a>

        <?php if ($isResponsable): ?>
        <a href="<?php echo BASE_URL; ?>dashboardprojet/edit/<?php echo $projet['id_projet']; ?>"
            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition inline-flex items-center gap-2">
            <?php echo HtmlHelper::icon('edit', 'w-4 h-4'); ?>
            Modifier
        </a>
        <?php endif; ?>
    </div>
</div>
<?php
    }
    
    private function renderEmptyState($canCreate)
    {
        ?>
<div class="bg-white rounded-lg shadow-lg p-12 text-center">
    <div class="mb-4 flex justify-center">
        <?php echo HtmlHelper::icon('briefcase', 'w-16 h-16 text-gray-400'); ?>
    </div>
    <h3 class="text-xl font-semibold text-gray-700 mb-2">Aucun projet</h3>
    <p class="text-gray-500 mb-6">Commencez par créer votre premier projet</p>
    <?php if ($canCreate): ?>
    <a href="<?php echo BASE_URL; ?>dashboardprojet/create"
        class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
        <?php echo HtmlHelper::icon('plus', 'w-5 h-5'); ?>
        Créer un projet
    </a>
    <?php endif; ?>
</div>
<?php
    }
}
?>