<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/DateHelper.php';
require_once __DIR__ . '/../helpers/ImageHelper.php';
require_once 'components/Section.php';

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
                <label for="search-input-actualite" class="block text-sm font-medium text-gray-700 mb-2">
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
        <?php $this->renderActualiteDetail($actu); ?>
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
        searchInput: '#search-input-actualite',
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
    
    private function renderActualiteDetail($actu)
    {
        ?>
<article id="actualite-<?php echo $actu['id_actualite']; ?>"
    class="item-card bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition scroll-mt-20"
    data-title="<?php echo $this->escape($actu['titre'] ?? ''); ?>"
    data-content="<?php echo $this->escape($actu['contenu'] ?? ''); ?>">

    <div class="grid md:grid-cols-3 gap-0">
        <div class="md:col-span-1">
            <?php if (!empty($actu['image'])): ?>
            <?php 
                $imageSrc = ASSETS_URL . 'images/' . $actu['image'];
                $fallback = ImageHelper::placeholder(400, 300, '#667eea');
                ?>
            <img src="<?php echo $imageSrc; ?>" alt="<?php echo $this->escape($actu['titre']); ?>"
                class="w-full h-full object-cover min-h-[300px]" onerror="this.src='<?php echo $fallback; ?>'">
            <?php else: ?>
            <div
                class="w-full h-full min-h-[300px] bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center text-white">
                <?php echo HtmlHelper::icon('document', 'w-16 h-16'); ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="md:col-span-2 p-6">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <?php if (!empty($actu['type_libelle'])): ?>
                <?php echo HtmlHelper::badge($actu['type_libelle'], 'primary'); ?>
                <?php endif; ?>

                <?php if (!empty($actu['date_publication'])): ?>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <?php echo HtmlHelper::icon('clock', 'w-4 h-4'); ?>
                    <span><?php echo DateHelper::relative($actu['date_publication']); ?></span>
                </div>
                <?php endif; ?>
            </div>

            <h2 class="text-2xl font-bold text-gray-900 mb-3">
                <?php echo $this->escape($actu['titre'] ?? ''); ?>
            </h2>

            <div class="text-gray-700 mb-4 leading-relaxed">
                <?php echo nl2br($this->escape($actu['contenu'] ?? '')); ?>
            </div>

            <?php if (!empty($actu['detail'])): ?>
            <div class="mt-4 p-4 bg-blue-50 border-l-4 border-blue-600 rounded">
                <h4 class="font-semibold text-blue-900 mb-2 flex items-center gap-2">
                    <?php echo HtmlHelper::icon('info', 'w-4 h-4'); ?>
                    Informations complémentaires
                </h4>
                <p class="text-sm text-gray-700"><?php echo nl2br($this->escape($actu['detail'])); ?></p>
            </div>
            <?php endif; ?>

            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex gap-2">
                        <button onclick="shareOnFacebook('<?php echo addslashes($this->escape($actu['titre'])); ?>')"
                            class="p-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                            title="Partager sur Facebook">
                            <?php echo HtmlHelper::icon('external-link', 'w-4 h-4'); ?>
                        </button>
                        <button onclick="shareOnTwitter('<?php echo addslashes($this->escape($actu['titre'])); ?>')"
                            class="p-2 bg-sky-500 text-white rounded hover:bg-sky-600 transition"
                            title="Partager sur Twitter">
                            <?php echo HtmlHelper::icon('external-link', 'w-4 h-4'); ?>
                        </button>
                        <button onclick="copyToClipboard('<?php echo addslashes($this->escape($actu['titre'])); ?>')"
                            class="p-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition"
                            title="Copier le lien">
                            <?php echo HtmlHelper::icon('clipboard', 'w-4 h-4'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>
<?php
    }
}
?>