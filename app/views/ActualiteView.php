<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/DateHelper.php';
require_once __DIR__ . '/../helpers/ImageHelper.php';
require_once __DIR__ . '/components/Card.php';

class ActualiteView extends View
{
    protected $pageTitle = 'Actualités - LMCS';
    
    public function render()
    {
        $this->renderHeader();
        $actualites = $this->get('actualites', []);
        $types = $this->get('types', []);
        ?>

<div class="container mx-auto px-4 py-8">
    <nav class="mb-6 text-sm">
        <ol class="flex items-center gap-2 text-white/80">
            <li><a href="<?php echo BASE_URL; ?>" class="hover:text-white">Accueil</a></li>
            <li><?php echo HtmlHelper::icon('arrow-right', 'w-4 h-4'); ?></li>
            <li class="text-white font-semibold">Actualités</li>
        </ol>
    </nav>

    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Toutes les Actualités</h1>
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-10">
                <label for="search-input" class="block text-sm font-medium text-gray-700 mb-2">
                    Rechercher dans les actualités
                </label>
                <div class="relative">
                    <input type="text" id="search-input" placeholder="Rechercher par titre ou contenu..."
                        class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <?php echo HtmlHelper::icon('search', 'w-5 h-5'); ?>
                    </span>
                </div>
            </div>

            <div class="md:col-span-1 flex items-end">
                <?php echo HtmlHelper::button('Réinitialiser', null, 'secondary', 'close', ['id' => 'reset-btn-actualites']); ?>
            </div>
        </div>

        <div id="results-count" class="mt-4 text-sm text-gray-600">
            <span class="font-semibold"><?php echo count($actualites); ?></span> actualité(s) au total
        </div>
    </div>

    <div id="items-container-actualites" class="space-y-8">
        <?php if (!empty($actualites)): ?>
        <?php foreach ($actualites as $actu): ?>
        <?php Card::actualiteDetail($actu); ?>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="bg-white rounded-lg shadow-lg p-12">
            <?php echo HtmlHelper::emptyState('Aucune actualité disponible pour le moment.', 'document'); ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    window.filterSortSearch = new FilterSortSearch({
        searchInput: '#search-input',
        resetButton: '#reset-btn-actualites',
        itemsContainer: '#items-container-actualites',
        itemSelector: '.item-card',
        searchFields: ['data-title', 'data-content'],
        emptyMessage: 'Aucune actualité ne correspond à votre recherche.',
        onUpdate: function(filteredItems) {
            const count = filteredItems.length;
            const countElement = document.getElementById('results-count');
            if (countElement) {
                countElement.innerHTML =
                    `<span class="font-semibold">${count}</span> actualité(s) trouvée(s)`;
            }
        }
    });
});

function shareOnFacebook(title) {
    window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(window.location.href), '_blank',
        'width=600,height=400');
}

function shareOnTwitter(title) {
    window.open('https://twitter.com/intent/tweet?url=' + encodeURIComponent(window.location.href) + '&text=' +
        encodeURIComponent(title), '_blank', 'width=600,height=400');
}

function copyToClipboard(title) {
    navigator.clipboard.writeText(window.location.href).then(() => {
        alert('Lien copié dans le presse-papier !');
    }).catch(err => {
        console.error('Erreur lors de la copie:', err);
        alert('Impossible de copier le lien.');
    });
}
</script>

<?php
        $this->renderFooter();
    }
}
?>