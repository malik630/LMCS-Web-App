<?php

require_once __DIR__ . '/../helpers/DateHelper.php';
require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/ImageHelper.php';
require_once 'components/Section.php';
require_once 'components/Card.php';

class ProjetView extends View
{
    protected $pageTitle = 'Catalogue des Projets de Recherche - LMCS';
    
    private $statutConfig = [
        'en_cours' => ['label' => 'En cours', 'color' => 'primary'],
        'termine' => ['label' => 'Terminé', 'color' => 'success'],
        'soumis' => ['label' => 'Soumis', 'color' => 'warning']
    ];
    
    public function render()
    {
        $this->renderHeader();
        echo '<div class="container mx-auto px-4 py-8">';
        $this->renderPageHeader();
        $this->renderFiltersAndSort();
        $this->renderProjets();
        echo '</div>';
        $this->renderFooter();
    }
    
    private function renderPageHeader()
    {
        ?>
<div class="mb-8">
    <h1 class="text-4xl font-bold text-white mb-4">Catalogue des Projets de Recherche</h1>
    <p class="text-blue-100 text-lg">
        Découvrez les projets de recherche du laboratoire LMCS classés par thématique et statut.
    </p>
</div>
<?php
    }
    
    private function renderFiltersAndSort()
    {
        $thematiques = $this->get('thematiques', []);
        $responsables = $this->get('responsables', []);
        
        ?>
<div class="bg-white rounded-lg shadow-lg p-6 mb-8">
    <h2 class="text-xl font-bold text-gray-900 mb-4">Filtres, Recherche et Tri</h2>

    <div class="grid md:grid-cols-4 gap-4 mb-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
            <input type="text" id="search-input" placeholder="Titre ou description..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Thématique</label>
            <select id="filter-thematique"
                class="filter-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Toutes</option>
                <?php foreach ($thematiques as $them): ?>
                <option value="<?php echo $this->escape($them['thematique']); ?>">
                    <?php echo $this->escape($them['thematique']); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Responsable</label>
            <select id="filter-responsable"
                class="filter-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Tous</option>
                <?php foreach ($responsables as $resp): ?>
                <option value="<?php echo $resp['id_user']; ?>">
                    <?php echo $this->escape($resp['prenom'] . ' ' . $resp['nom']); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
            <select id="filter-statut"
                class="filter-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Tous</option>
                <?php foreach ($this->statutConfig as $key => $config): ?>
                <option value="<?php echo $key; ?>"><?php echo $config['label']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="flex items-center gap-4">
        <?php echo HtmlHelper::button('Réinitialiser', null, 'secondary', 'close', ['id' => 'reset-btn']); ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    window.filterSortSearch = new FilterSortSearch({
        searchInput: '#search-input',
        filterSelects: '.filter-select',
        sortSelect: '#sort-select',
        resetButton: '#reset-btn',
        itemsContainer: '#projets-container',
        itemSelector: '.projet-card',
        searchFields: ['data-titre', 'data-description'],
        filterFields: {
            '#filter-thematique': 'data-thematique',
            '#filter-responsable': 'data-responsable',
            '#filter-statut': 'data-statut'
        },
        sortFunction: function(items, sortValue) {
            return items.sort((a, b) => {
                switch (sortValue) {
                    case 'recent':
                        const dateA = a.getAttribute('data-date-creation');
                        const dateB = b.getAttribute('data-date-creation');
                        if (!dateA || !dateB) return 0;
                        return new Date(dateB).getTime() - new Date(dateA).getTime();

                    case 'ancien':
                        const dateA2 = a.getAttribute('data-date-creation');
                        const dateB2 = b.getAttribute('data-date-creation');
                        if (!dateA2 || !dateB2) return 0;
                        return new Date(dateA2).getTime() - new Date(dateB2).getTime();

                    case 'titre':
                        const titreA = (a.getAttribute('data-titre') || '').toLowerCase();
                        const titreB = (b.getAttribute('data-titre') || '').toLowerCase();
                        return titreA.localeCompare(titreB, 'fr');

                    default:
                        return 0;
                }
            });
        }
    });
});
</script>
<?php
    }
    
    private function renderProjets()
    {
        $projets = $this->get('projets', []);
        
        if (empty($projets)) {
            echo HtmlHelper::emptyState('Aucun projet trouvé', 'calendar');
            return;
        }
        
        $grouped = $this->groupByThematique($projets);
        
        echo '<div id="projets-container">';
        foreach ($grouped as $thematique => $projetsList) {
            $this->renderThematiqueSection($thematique, $projetsList);
        }
        echo '</div>';
    }
    
    private function groupByThematique(array $projets)
    {
        $grouped = [];
        foreach ($projets as $projet) {
            $them = $projet['thematique'] ?? 'Autre';
            $grouped[$them][] = $projet;
        }
        ksort($grouped);
        return $grouped;
    }
    
    private function renderThematiqueSection(string $thematique, array $projets)
    {
        ?>
<div class="mb-12 thematique-section" data-thematique="<?php echo $this->escape($thematique); ?>">
    <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
        <span class="bg-blue-600 text-white px-4 py-2 rounded-lg">
            <?php echo $this->escape($thematique); ?>
        </span>
        <span class="text-blue-200 text-lg thematique-count">
            (<?php echo count($projets); ?> projet<?php echo count($projets) > 1 ? 's' : ''; ?>)
        </span>
    </h2>

    <div class="space-y-6">
        <?php foreach ($projets as $projet): ?>
        <?php $this->renderProjetCard($projet); ?>
        <?php endforeach; ?>
    </div>
</div>
<?php
    }
    
    private function renderProjetCard(array $projet)
    {
        $statutConfig = $this->statutConfig[$projet['statut']] ?? ['label' => $projet['statut'], 'color' => 'info'];
        $projetModel = new Projet();
        $membres = $projetModel->getMembers($projet['id_projet']);
        $publications = $projetModel->getPublications($projet['id_projet']);
        $partenaires = $projetModel->getPartenaires($projet['id_projet']);
        
        // Préparer les données pour le JavaScript
        $projetData = [
            'projet' => $projet,
            'membres' => $membres,
            'publications' => $publications,
            'partenaires' => $partenaires
        ];
        
        ?>
<div class="projet-card bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow"
    data-titre="<?php echo $this->escape(strtolower($projet['titre'])); ?>"
    data-description="<?php echo $this->escape(strtolower($projet['description'] ?? '')); ?>"
    data-thematique="<?php echo $this->escape($projet['thematique'] ?? 'Autre'); ?>"
    data-responsable="<?php echo $projet['responsable_id'] ?? ''; ?>" data-statut="<?php echo $projet['statut']; ?>"
    data-date-creation="<?php echo $projet['date_creation']; ?>"
    data-projet='<?php echo htmlspecialchars(json_encode($projetData), ENT_QUOTES, 'UTF-8'); ?>'>

    <div class="p-6">
        <!-- En-tête avec badge et titre -->
        <div class="flex items-start justify-between mb-4">
            <div class="flex-grow">
                <?php echo HtmlHelper::badge($statutConfig['label'], $statutConfig['color']); ?>
                <h3 class="text-2xl font-bold text-gray-900 mt-2"><?php echo $this->escape($projet['titre']); ?></h3>
            </div>
        </div>

        <!-- Contenu principal en deux colonnes -->
        <div class="grid md:grid-cols-3 gap-6 mb-4">
            <!-- Description -->
            <div class="md:col-span-2">
                <div class="text-gray-600 mb-4 max-h-20 overflow-y-auto pr-2 custom-scrollbar">
                    <?php echo nl2br($this->escape($projet['description'] ?? '')); ?>
                </div>
            </div>

            <!-- Informations clés -->
            <div class="space-y-2 text-sm">
                <?php if (!empty($projet['responsable_nom'])): ?>
                <div class="flex items-center gap-2 text-gray-700">
                    <?php echo HtmlHelper::icon('user', 'w-5 h-5 text-blue-600'); ?>
                    <span class="font-semibold">Responsable:</span>
                    <span><?php echo $this->escape($projet['responsable_prenom'] . ' ' . $projet['responsable_nom']); ?></span>
                </div>
                <?php endif; ?>

                <?php if (!empty($projet['type_financement'])): ?>
                <div class="flex items-center gap-2 text-gray-700">
                    <?php echo HtmlHelper::icon('check', 'w-5 h-5 text-green-600'); ?>
                    <span class="font-semibold">Financement:</span>
                    <span><?php echo $this->escape($projet['type_financement']); ?></span>
                </div>
                <?php endif; ?>

                <div class="flex items-center gap-2 text-gray-700">
                    <?php echo HtmlHelper::icon('user', 'w-5 h-5 text-purple-600'); ?>
                    <span><strong><?php echo count($membres); ?></strong> membre(s)</span>
                </div>

                <div class="flex items-center gap-2 text-gray-700">
                    <?php echo HtmlHelper::icon('edit', 'w-5 h-5 text-orange-600'); ?>
                    <span><strong><?php echo count($publications); ?></strong> publication(s)</span>
                </div>
            </div>
        </div>

        <!-- Bouton voir détails -->
        <div class="flex justify-end pt-4 border-t border-gray-200">
            <a href="#"
                onclick="event.preventDefault(); toggleProjetDetails(<?php echo $projet['id_projet']; ?>); return false;"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
                <?php echo HtmlHelper::icon('arrow-right', 'w-5 h-5'); ?>
                <span>Voir plus de détails</span>
            </a>
        </div>
    </div>

    <!-- Section détails (cachée par défaut) -->
    <div id="details-<?php echo $projet['id_projet']; ?>" class="hidden"></div>
</div>
<?php
    }
}
?>