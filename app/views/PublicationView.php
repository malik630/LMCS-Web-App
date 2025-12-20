<?php

require_once __DIR__ . '/../helpers/DateHelper.php';
require_once __DIR__ . '/../helpers/HtmlHelper.php';

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
    <h1 class="text-4xl font-bold text-white mb-4">Base Documentaire</h1>
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
        <button id="reset-btn" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
            Réinitialiser
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        <div class="col-span-full">
            <label class="block text-sm font-medium text-gray-700 mb-2">Recherche par mots-clés</label>
            <input type="text" id="search-input" placeholder="Titre, auteurs, résumé, DOI..."
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
            <select id="filter-type" class="filter-select w-full px-4 py-2 border border-gray-300 rounded-lg">
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
            <p class="text-xs text-gray-500 mt-1">Maintenez Ctrl (Cmd sur Mac) pour sélectionner plusieurs auteurs</p>
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
<div id="items-container">
    <?php if (empty($publications)): ?>
    <div class="text-center py-12 text-gray-500">Aucune publication trouvée</div>
    <?php else: ?>
    <div class="space-y-6">
        <?php foreach ($publications as $pub): ?>
        <?php $this->renderCard($pub); ?>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?php
    }
    
    private function renderCard($pub)
    {
        ?>
<div class="item-card bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition"
    data-title="<?php echo $this->escape($pub['titre']); ?>" data-year="<?php echo $pub['annee']; ?>"
    data-type="<?php echo $this->escape($pub['type_libelle'] ?? ''); ?>"
    data-domain="<?php echo $this->escape($pub['domaine'] ?? ''); ?>"
    data-authors="<?php echo $this->escape($pub['auteurs'] ?? ''); ?>"
    data-resume="<?php echo $this->escape($pub['resume'] ?? ''); ?>"
    data-doi="<?php echo $this->escape($pub['doi'] ?? ''); ?>">

    <div class="flex items-center gap-2 mb-3">
        <?php echo HtmlHelper::badge($pub['type_libelle'] ?? 'Publication', 'primary'); ?>
        <span class="text-sm text-gray-500 font-semibold"><?php echo $pub['annee']; ?></span>
        <?php if (!empty($pub['domaine'])): ?>
        <?php echo HtmlHelper::badge($pub['domaine'], 'info'); ?>
        <?php endif; ?>
    </div>

    <h3 class="text-xl font-bold text-gray-900 mb-2">
        <?php echo $this->escape($pub['titre']); ?>
    </h3>

    <?php if (!empty($pub['auteurs'])): ?>
    <p class="text-gray-600 text-sm mb-3">
        <span class="font-semibold">Auteurs:</span> <?php echo $this->escape($pub['auteurs']); ?>
    </p>
    <?php endif; ?>

    <?php if (!empty($pub['resume'])): ?>
    <p class="text-gray-700 mb-4 line-clamp-3">
        <?php echo $this->escape(substr($pub['resume'], 0, 250)); ?>
        <?php if (strlen($pub['resume']) > 250): ?>...<?php endif; ?>
    </p>
    <?php endif; ?>

    <div class="flex flex-wrap items-center gap-4 text-sm mb-4 pb-4 border-b">
        <?php if (!empty($pub['doi'])): ?>
        <div class="flex items-center gap-2 text-gray-600">
            <span class="font-semibold">DOI:</span>
            <a href="https://doi.org/<?php echo $this->escape($pub['doi']); ?>" target="_blank"
                class="text-blue-600 hover:underline">
                <?php echo $this->escape($pub['doi']); ?>
            </a>
        </div>
        <?php endif; ?>

        <?php if (!empty($pub['date_publication'])): ?>
        <div class="flex items-center gap-2 text-gray-500">
            <?php echo HtmlHelper::icon('calendar', 'w-4 h-4'); ?>
            <span><?php echo DateHelper::format($pub['date_publication']); ?></span>
        </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($pub['lien_telechargement']) || !empty($pub['fichier_pdf'])): ?>
    <div>
        <?php if (!empty($pub['lien_telechargement'])): ?>
        <a href="<?php echo $pub['lien_telechargement']; ?>" target="_blank"
            class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <?php echo HtmlHelper::icon('download', 'w-4 h-4'); ?>
            <span>Télécharger</span>
        </a>
        <?php endif; ?>
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
        searchInput: '#search-input',
        filterSelects: '.filter-select',
        sortSelect: '#sort-select',
        resetButton: '#reset-btn',
        itemsContainer: '#items-container',
        itemSelector: '.item-card',
        searchFields: ['data-title', 'data-authors', 'data-resume', 'data-doi'],
        filterFields: {
            '#filter-year': 'data-year',
            '#filter-type': 'data-type',
            '#filter-domain': 'data-domain'
        },
        sortFunction: function(items, sortValue) {
            items.sort(function(a, b) {
                var titleA, titleB, yearA, yearB;

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
                    // date_desc par défaut
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
    var authorSelect = document.getElementById('filter-author');
    authorSelect.addEventListener('change', function() {
        var selectedAuthors = Array.from(this.selectedOptions).map(function(opt) {
            return opt.value.toLowerCase();
        });

        if (selectedAuthors.length === 0) {
            window.filterSortSearch.applyFilters();
            return;
        }

        var allCards = document.querySelectorAll('.item-card');
        allCards.forEach(function(card) {
            var cardAuthors = card.getAttribute('data-authors').toLowerCase();
            var hasAuthor = selectedAuthors.some(function(author) {
                return cardAuthors.includes(author);
            });
            card.style.display = hasAuthor ? '' : 'none';
        });

        var visibleCards = Array.from(allCards).filter(function(c) {
            return c.style.display !== 'none';
        });
        document.getElementById('result-count').textContent = visibleCards.length;

        if (visibleCards.length === 0) {
            window.filterSortSearch.showEmptyState();
        } else {
            window.filterSortSearch.hideEmptyState();
        }
    });

    // Initialiser le compteur
    document.getElementById('result-count').textContent = document.querySelectorAll('.item-card').length;
});
</script>
<?php
    }
}
?>