<?php

require_once __DIR__ . '/../helpers/DateHelper.php';
require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/components/Card.php';

class PublicationView extends View
{
    protected $pageTitle = 'Publications - LMCS';
    
    public function render()
    {
        $this->renderHeader();
        echo '<div class="container mx-auto px-4 py-8 max-w-7xl">';
        $this->renderPageHeader();
        $this->renderFilters();
        $this->renderPublications();
        echo '</div>';
        $this->renderFooter();
        $this->renderScript();
    }
    
    private function renderPageHeader()
    {
        ?>
<div class="mb-8">
    <h1 class="text-4xl font-bold text-white mb-4">Publications et Base Documentaire</h1>
    <p class="text-white text-lg">
        Consultez l'ensemble des publications scientifiques du laboratoire LMCS
    </p>
</div>
<?php
    }
    
    private function renderFilters()
    {
        $types = $this->get('types', []);
        $authors = $this->get('authors', []);
        $years = $this->get('years', []);
        $domains = $this->get('domains', []);
        
        ?>
<div class="bg-white rounded-lg shadow-lg p-6 mb-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Recherche Avancée</h2>
        <button id="reset-btn-publications"
            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition flex items-center justify-center gap-2">
            <?php echo HtmlHelper::icon('close') ?> Réinitialiser
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        <div class="col-span-full">
            <label class="block text-sm font-medium text-gray-700 mb-2">Recherche par mots-clés</label>
            <input type="text" id="search-input-publications" placeholder="Titre, auteurs, résumé, DOI..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Année</label>
            <select id="filter-year" class="filter-select w-full px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Toutes les années</option>
                <?php foreach ($years as $year): ?>
                <option value="<?php echo $year['annee']; ?>"><?php echo $year['annee']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
            <select id="filter-type-publication"
                class="filter-select w-full px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Tous les types</option>
                <?php foreach ($types as $type): ?>
                <option value="<?php echo $this->escape($type['libelle']); ?>">
                    <?php echo $this->escape($type['libelle']); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Domaine</label>
            <select id="filter-domain" class="filter-select w-full px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Tous les domaines</option>
                <?php foreach ($domains as $domain): ?>
                <option value="<?php echo $this->escape($domain['domaine']); ?>">
                    <?php echo $this->escape($domain['domaine']); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-span-full">
            <label class="block text-sm font-medium text-gray-700 mb-2">Auteurs (sélection multiple)</label>
            <select id="filter-author" multiple
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                style="min-height: 120px;">
                <?php foreach ($authors as $author): ?>
                <option value="<?php echo $this->escape($author['prenom'] . ' ' . $author['nom']); ?>">
                    <?php echo $this->escape($author['prenom'] . ' ' . $author['nom']); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="flex items-center justify-between pt-4 border-t">
        <div class="flex items-center gap-2 text-gray-600">
            <span class="font-medium">Résultats:</span>
            <span id="result-count" class="text-blue-600 font-bold">0</span>
            <span>publication(s)</span>
        </div>
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-700">Trier par:</label>
            <select id="sort-select" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="date_desc">Date (récent → ancien)</option>
                <option value="date_asc">Date (ancien → récent)</option>
                <option value="titre_asc">Titre (A → Z)</option>
                <option value="titre_desc">Titre (Z → A)</option>
            </select>
        </div>
    </div>
</div>
<?php
    }
    
    private function renderPublications()
    {
        $publications = $this->get('publications', []);
        ?>
<div id="items-container-publications">
    <?php if (empty($publications)): ?>
    <div class="text-center py-12 text-gray-500">Aucune publication trouvée</div>
    <?php else: ?>
    <div class="space-y-6">
        <?php foreach ($publications as $pub): ?>
        <?php Card::publication($pub); ?>
        <?php endforeach; ?>
    </div>
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
        searchInput: '#search-input-publications',
        filterSelects: '.filter-select',
        sortSelect: '#sort-select',
        resetButton: '#reset-btn-publications',
        itemsContainer: '#items-container-publications',
        itemSelector: '.item-card',
        searchFields: ['data-title', 'data-authors', 'data-resume', 'data-doi'],
        filterFields: {
            '#filter-year': 'data-year',
            '#filter-type-publication': 'data-type',
            '#filter-domain': 'data-domain'
        },
        sortFunction: function(items, sortValue) {
            items.sort(function(a, b) {
                let titleA, titleB, yearA, yearB;

                if (sortValue === 'titre_asc') {
                    titleA = a.getAttribute('data-title').toLowerCase();
                    titleB = b.getAttribute('data-title').toLowerCase();
                    return titleA.localeCompare(titleB);
                } else if (sortValue === 'titre_desc') {
                    titleA = a.getAttribute('data-title').toLowerCase();
                    titleB = b.getAttribute('data-title').toLowerCase();
                    return titleB.localeCompare(titleA);
                } else if (sortValue === 'date_asc') {
                    yearA = parseInt(a.getAttribute('data-year'));
                    yearB = parseInt(b.getAttribute('data-year'));
                    return yearA - yearB;
                } else {
                    yearA = parseInt(a.getAttribute('data-year'));
                    yearB = parseInt(b.getAttribute('data-year'));
                    return yearB - yearA;
                }
            });
            return items;
        },
        emptyMessage: 'Aucune publication ne correspond à vos critères.',
        onUpdate: function(filteredItems) {
            document.getElementById('result-count').textContent = filteredItems.length;
        }
    });

    // Filtre multiple par auteurs
    let authorSelect = document.getElementById('filter-author');
    authorSelect.addEventListener('change', function() {
        let selectedAuthors = Array.from(this.selectedOptions).map(function(opt) {
            return opt.value.toLowerCase();
        });

        if (selectedAuthors.length === 0) {
            window.filterSortSearch.applyFilters();
            return;
        }

        let allCards = document.querySelectorAll('.item-card');
        allCards.forEach(function(card) {
            let cardAuthors = card.getAttribute('data-authors').toLowerCase();
            let hasAuthor = selectedAuthors.some(function(author) {
                return cardAuthors.includes(author);
            });
            card.style.display = hasAuthor ? '' : 'none';
        });

        let visibleCards = Array.from(allCards).filter(function(c) {
            return c.style.display !== 'none';
        });
        document.getElementById('result-count').textContent = visibleCards.length;

        if (visibleCards.length === 0) {
            window.filterSortSearch.showEmptyState();
        } else {
            window.filterSortSearch.hideEmptyState();
        }
    });

    document.getElementById('result-count').textContent = document.querySelectorAll('.item-card').length;
});
</script>
<?php
    }
}
?>