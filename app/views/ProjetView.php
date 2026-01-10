<?php

require_once __DIR__ . '/../helpers/DateHelper.php';
require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/components/Card.php';

class ProjetView extends View
{
    protected $pageTitle = 'Projets de Recherche - LMCS';
    
    public function render()
    {
        $this->renderHeader();
        echo '<div class="container mx-auto px-4 py-8 max-w-7xl">';
        $this->renderPageHeader();
        $this->renderFilters();
        $this->renderProjets();
        echo '</div>';
        $this->renderFooter();
        $this->renderScript();
    }
    
    private function renderPageHeader()
    {
        ?>
<div class="mb-8">
    <h1 class="text-4xl font-bold text-white mb-4">Projets de Recherche</h1>
    <p class="text-white text-lg">
        Découvrez l'ensemble des projets de recherche menés au sein du laboratoire LMCS
    </p>
</div>
<?php
    }
    
    private function renderFilters()
    {
        $thematiques = $this->get('thematiques', []);
        $responsables = $this->get('responsables', []);
        $currentFilters = $this->get('currentFilters', []);
        
        ?>
<div class="bg-white rounded-lg shadow-lg p-6 mb-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Recherche Avancée</h2>
        <button id="reset-btn"
            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition flex items-center justify-center gap-2">
            <?php echo HtmlHelper::icon('close') ?> Réinitialiser
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        <div class="col-span-full">
            <label class="block text-sm font-medium text-gray-700 mb-2">Recherche par mots-clés</label>
            <input type="text" id="search-input" placeholder="Titre, description, responsable..."
                value="<?php echo $this->escape($currentFilters['search'] ?? ''); ?>"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Thématique</label>
            <select id="filter-thematique" class="filter-select w-full px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Toutes les thématiques</option>
                <?php foreach ($thematiques as $thematique): ?>
                <option value="<?php echo $this->escape($thematique['thematique']); ?>"
                    <?php echo ($currentFilters['thematique'] ?? '') === $thematique['thematique'] ? 'selected' : ''; ?>>
                    <?php echo $this->escape($thematique['thematique']); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
            <select id="filter-statut" class="filter-select w-full px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Tous les statuts</option>
                <option value="en_cours"
                    <?php echo ($currentFilters['statut'] ?? '') === 'en_cours' ? 'selected' : ''; ?>>En cours</option>
                <option value="termine"
                    <?php echo ($currentFilters['statut'] ?? '') === 'termine' ? 'selected' : ''; ?>>Terminé</option>
                <option value="soumis" <?php echo ($currentFilters['statut'] ?? '') === 'soumis' ? 'selected' : ''; ?>>
                    Soumis</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Responsable</label>
            <select id="filter-responsable" class="filter-select w-full px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Tous les responsables</option>
                <?php foreach ($responsables as $responsable): ?>
                <option value="<?php echo $responsable['id_user']; ?>"
                    <?php echo ($currentFilters['responsable_id'] ?? '') == $responsable['id_user'] ? 'selected' : ''; ?>>
                    <?php echo $this->escape($responsable['prenom'] . ' ' . $responsable['nom']); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="flex items-center justify-between pt-4 border-t">
        <div class="flex items-center gap-2 text-gray-600">
            <span class="font-medium">Résultats:</span>
            <span id="result-count" class="text-blue-600 font-bold">0</span>
            <span>projet(s)</span>
        </div>
    </div>
</div>
<?php
    }
    
    private function renderProjets()
    {
        $projets = $this->get('projets', []);
  
        $projetsByThematique = [];
        foreach ($projets as $projet) {
            $thematique = $projet['thematique'] ?? 'Non classé';
            if (!isset($projetsByThematique[$thematique])) {
                $projetsByThematique[$thematique] = [];
            }
            $projetsByThematique[$thematique][] = $projet;
        }

        ksort($projetsByThematique);
        ?>
<div id="items-container">
    <?php if (empty($projets)): ?>
    <div class="text-center py-12 text-gray-500">Aucun projet trouvé</div>
    <?php else: ?>
    <?php foreach ($projetsByThematique as $thematique => $projetsList): ?>
    <div class="thematique-section mb-8">
        <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-3">
            <?php echo $this->escape($thematique); ?>
            <span class="thematique-count text-lg text-gray-300">(<?php echo count($projetsList); ?>
                projet<?php echo count($projetsList) > 1 ? 's' : ''; ?>)</span>
        </h2>
        <div class="space-y-6">
            <?php foreach ($projetsList as $projet): ?>
            <?php Card::project($projet); ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php
    }
    
    private function renderScript()
    {
        ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    window.filterSortSearch = new FilterSortSearch({
        searchInput: '#search-input',
        filterSelects: '.filter-select',
        resetButton: '#reset-btn',
        itemsContainer: '#items-container',
        itemSelector: '.item-card',
        searchFields: ['data-titre', 'data-description'],
        filterFields: {
            '#filter-thematique': 'data-thematique',
            '#filter-statut': 'data-statut',
            '#filter-responsable': 'data-responsable-id'
        },
        emptyMessage: 'Aucun projet ne correspond à vos critères.',
        onUpdate: function(filteredItems) {
            document.getElementById('result-count').textContent = filteredItems.length;

            const sections = document.querySelectorAll('.thematique-section');
            sections.forEach(function(section) {
                const visibleCards = Array.from(section.querySelectorAll('.item-card'))
                    .filter(card => card.style.display !== 'none');

                const countElement = section.querySelector('.thematique-count');
                if (countElement) {
                    const count = visibleCards.length;
                    countElement.textContent = `(${count} projet${count > 1 ? 's' : ''})`;
                }

                section.style.display = visibleCards.length > 0 ? '' : 'none';
            });
        }
    });

    document.getElementById('result-count').textContent = document.querySelectorAll('.item-card').length;
});
</script>
<?php
    }
}
?>