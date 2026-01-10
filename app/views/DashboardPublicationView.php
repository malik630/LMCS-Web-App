<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/components/PageHeader.php';
require_once __DIR__ . '/components/StatsCard.php';
require_once __DIR__ . '/components/Section.php';

class DashboardPublicationView extends View
{
    protected $pageTitle = 'Mes Publications - LMCS';
    
    public function render()
    {
        $this->renderHeader();
        $user = $this->get('user');
        $publications = $this->get('publications');
        $types = $this->get('types');
        $canCreate = $this->get('can_create');

        $parStatut = [
            'publie' => [],
            'en_attente' => [],
            'rejete' => []
        ];
        
        foreach ($publications as $pub) {
            if (isset($parStatut[$pub['statut']])) {
                $parStatut[$pub['statut']][] = $pub;
            }
        }
        
        // Page header
        $actions = [];
        
        if ($canCreate) {
            $actions[] = [
                'url' => BASE_URL . 'dashboardpublication/create',
                'text' => 'Nouvelle Publication',
                'type' => 'primary',
                'icon' => 'plus'
            ];
        }
        
        $actions[] = [
            'url' => BASE_URL . 'dashboardstats/rapportPublications',
            'text' => 'Générer Rapport PDF',
            'type' => 'secondary',
            'icon' => 'download'
        ];
        
        PageHeader::render([
            'title' => 'Mes Publications',
            'subtitle' => count($publications) . ' publication' . (count($publications) > 1 ? 's' : '') . ' au total',
            'actions' => $actions
        ]);

        StatsCard::renderGrid([
            [
                'label' => 'Publiées',
                'value' => count($parStatut['publie']),
                'icon' => 'check',
                'color' => 'green'
            ],
            [
                'label' => 'En attente',
                'value' => count($parStatut['en_attente']),
                'icon' => 'clock',
                'color' => 'yellow'
            ],
            [
                'label' => 'Rejetées',
                'value' => count($parStatut['rejete']),
                'icon' => 'close',
                'color' => 'red'
            ]
        ]);
        
        if (!empty($parStatut['publie'])) {
            $this->renderPublicationsByStatus('Publications Publiées', $parStatut['publie'], 'publie');
        }
        
        if (!empty($parStatut['en_attente'])) {
            $this->renderPublicationsByStatus('Publications En Attente de Validation', $parStatut['en_attente'], 'en_attente');
        }
        
        if (!empty($parStatut['rejete'])) {
            $this->renderPublicationsByStatus('Publications Rejetées', $parStatut['rejete'], 'rejete');
        }

        if (empty($publications)) {
            $this->renderEmptyState($canCreate);
        }
        
        PageHeader::close();
        $this->renderFooter();
    }
    
    private function renderPublicationsByStatus($title, $publications, $statut)
    {
        Section::create($title, function() use ($publications, $statut) {
            echo '<div class="space-y-4">';
            foreach ($publications as $pub) {
                $this->renderPublicationCard($pub, $statut);
            }
            echo '</div>';
        }, 'bg-white');
    }
    
    private function renderPublicationCard($pub, $statut)
    {
        $badgeType = [
            'publie' => 'success',
            'en_attente' => 'warning',
            'rejete' => 'danger'
        ][$statut] ?? 'info';
        
        $badgeText = [
            'publie' => 'Publiée',
            'en_attente' => 'En attente',
            'rejete' => 'Rejetée'
        ][$statut] ?? $statut;
        
        ?>
<div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
    <div class="flex justify-between items-start mb-3">
        <div class="flex-grow">
            <?php echo HtmlHelper::badge($badgeText, $badgeType); ?>
            <h3 class="font-bold text-gray-900 mt-2 text-lg"><?php echo $this->escape($pub['titre']); ?></h3>
        </div>
    </div>

    <div class="space-y-2 text-sm text-gray-600 mb-4">
        <p><span class="font-semibold">Type:</span> <?php echo $this->escape($pub['type_libelle'] ?? 'Non spécifié'); ?>
        </p>
        <p><span class="font-semibold">Année:</span> <?php echo $pub['annee']; ?></p>
        <?php if (!empty($pub['domaine'])): ?>
        <p><span class="font-semibold">Domaine:</span> <?php echo $this->escape($pub['domaine']); ?></p>
        <?php endif; ?>
        <?php if (!empty($pub['doi'])): ?>
        <p><span class="font-semibold">DOI:</span> <?php echo $this->escape($pub['doi']); ?></p>
        <?php endif; ?>
    </div>

    <?php if (!empty($pub['resume'])): ?>
    <div class="text-gray-600 text-sm mb-4 max-h-24 overflow-y-auto">
        <p><?php echo nl2br($this->escape(substr($pub['resume'], 0, 200))); ?><?php echo strlen($pub['resume']) > 200 ? '...' : ''; ?>
        </p>
    </div>
    <?php endif; ?>

    <div class="pt-4 border-t border-gray-200 flex gap-2">
        <a href="<?php echo BASE_URL; ?>dashboardpublication/edit/<?php echo $pub['id_publication']; ?>"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition inline-flex items-center gap-2">
            <?php echo HtmlHelper::icon('edit', 'w-4 h-4'); ?>
            Modifier
        </a>

        <?php if ($statut === 'en_attente' || $statut === 'rejete'): ?>
        <a href="<?php echo BASE_URL; ?>dashboardpublication/submitForApproval/<?php echo $pub['id_publication']; ?>"
            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition inline-flex items-center gap-2">
            <?php echo HtmlHelper::icon('upload', 'w-4 h-4'); ?>
            Soumettre
        </a>
        <?php endif; ?>

        <a href="<?php echo BASE_URL; ?>dashboardpublication/delete/<?php echo $pub['id_publication']; ?>"
            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette publication ?')"
            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition inline-flex items-center gap-2">
            <?php echo HtmlHelper::icon('trash', 'w-4 h-4'); ?>
            Supprimer
        </a>
    </div>
</div>
<?php
    }
    
    private function renderEmptyState($canCreate)
    {
        ?>
<div class="bg-white rounded-lg shadow-lg p-12 text-center">
    <div class="mb-4 flex justify-center">
        <?php echo HtmlHelper::icon('document', 'w-16 h-16 text-gray-400'); ?>
    </div>
    <h3 class="text-xl font-semibold text-gray-700 mb-2">Aucune publication</h3>
    <p class="text-gray-500 mb-6">Commencez par créer votre première publication</p>
    <?php if ($canCreate): ?>
    <a href="<?php echo BASE_URL; ?>dashboardpublication/create"
        class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
        <?php echo HtmlHelper::icon('plus', 'w-5 h-5'); ?>
        Créer une publication
    </a>
    <?php endif; ?>
</div>
<?php
    }
}
?>