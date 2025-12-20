<?php

require_once __DIR__ . '/../helpers/DateHelper.php';
require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once 'components/Section.php';
require_once 'components/Card.php';

class OfferView extends View
{
    protected $pageTitle = 'Offres et Opportunités - LMCS';
    
    private $typeConfig = [
        'stage' => ['label' => 'Stages', 'icon' => 'user', 'color' => 'primary', 'description' => 'Offres de stages de recherche et développement'],
        'these' => ['label' => 'Thèses', 'icon' => 'edit', 'color' => 'success', 'description' => 'Sujets de thèses de doctorat disponibles'],
        'bourse' => ['label' => 'Bourses', 'icon' => 'check', 'color' => 'warning', 'description' => 'Opportunités de financement pour la recherche'],
        'collaboration' => ['label' => 'Collaborations', 'icon' => 'external-link', 'color' => 'info', 'description' => 'Opportunités de collaboration scientifique'],
        'emploi' => ['label' => 'Emplois', 'icon' => 'user', 'color' => 'danger', 'description' => 'Postes à pourvoir au laboratoire'],
        'autre' => ['label' => 'Autres', 'icon' => 'calendar', 'color' => 'orange', 'description' => 'Autres opportunités']
    ];
    
    public function render()
    {
        $this->renderHeader();
        echo '<div class="container mx-auto px-4 py-8">';
        $this->renderPageHeader();
        $this->renderContent();
        echo '</div>';
        $this->renderFooter();
        $this->renderScript();
    }
    
    private function renderPageHeader()
    {
        ?>
<div class="mb-8">
    <h1 class="text-4xl font-bold font-sans text-white mb-4">Offres et Opportunités</h1>
    <p class="text-blue-100 text-lg font-sans">
        Découvrez les opportunités de stages, thèses, bourses et collaborations au sein du laboratoire LMCS.
    </p>
</div>
<?php
    }
    
    private function renderContent()
    {
        $totalOffers = $this->get('totalOffers', 0);
        
        if ($totalOffers > 0) {
            $this->renderFilters();
            $this->renderOffers();
        } else {
            $this->renderEmptyState();
        }
    }
    
    private function renderFilters()
    {
        ?>
<div class="bg-white rounded-lg shadow-lg p-6 mb-8">
    <h2 class="text-xl font-bold text-gray-900 mb-4">Filtres</h2>
    <div class="flex flex-wrap gap-3">
        <button onclick="filterOffers('all')"
            class="filter-btn active bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold transition font-sans hover:bg-blue-700"
            data-type="all">
            Tous
        </button>
        <?php foreach ($this->typeConfig as $type => $config): ?>
        <button onclick="filterOffers('<?php echo $type; ?>')"
            class="filter-btn bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold transition font-sans hover:bg-blue-700 hover:text-white"
            data-type="<?php echo $type; ?>">
            <?php echo HtmlHelper::icon($config['icon'], 'w-4 h-4 inline mr-2'); ?>
            <?php echo $config['label']; ?>
        </button>
        <?php endforeach; ?>
    </div>
</div>
<?php
    }
    
    private function renderOffers()
    {
        $groupedOffers = $this->get('groupedOffers', []);
        
        echo '<div id="offers-container">';
        foreach ($this->typeConfig as $type => $config) {
            $offers = $groupedOffers[$type] ?? [];
            if (!empty($offers)) {
                $this->renderOfferTypeSection($type, $config, $offers);
            }
        }
        echo '</div>';
    }
    
    private function renderOfferTypeSection($type, array $config, array $offers)
    {
        ?>
<div class="offer-section category-section mb-8" data-type="<?php echo htmlspecialchars($type); ?>">
    <?php 
    Section::create($config['label'], function() use ($offers, $config) {
        echo '<p class="text-gray-600 font-sans mb-6">' . htmlspecialchars($config['description']) . '</p>';
        echo '<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">';
        foreach ($offers as $offer) {
            $this->renderOfferCard($offer, $config);
        }
        echo '</div>';
    });
    ?>
</div>
<?php
    }
    
    private function renderOfferCard(array $offer, array $typeConfig)
    {
        $isExpired = !empty($offer['date_limite']) && strtotime($offer['date_limite']) < time();
        
        echo '<div class="offer-card" data-type="' . htmlspecialchars($offer['type']) . '">';
        Card::render([
            'title' => $offer['titre'],
            'badge' => $typeConfig['label'],
            'badge_type' => $typeConfig['color'],
            'description' => $offer['description'],
            'description_max_height' => 'max-h-32',
            'items' => $this->buildOfferItems($offer),
            'meta' => $isExpired ? [['type' => 'badge', 'value' => 'Expirée', 'badge_type' => 'danger']] : [],
            'footer_button' => [
                'text' => 'Voir les détails',
                'url' => BASE_URL . 'offer/view/' . $offer['id_offre'],
                'type' => $isExpired ? 'secondary' : 'primary'
            ]
        ]);
        echo '</div>';
    }
    
    private function buildOfferItems(array $offer)
    {
        return array_filter([
            [
                'icon' => 'user',
                'label' => 'Responsable',
                'value' => $offer['responsable_nom'] ?? 'Non spécifié'
            ],
            [
                'icon' => 'email',
                'label' => 'Email',
                'value' => $offer['contact_email'] ?? 'Non spécifié'
            ],
            [
                'icon' => 'calendar',
                'label' => 'Date limite',
                'value' => !empty($offer['date_limite']) ? 
                           DateHelper::format($offer['date_limite'], 'd/m/Y') : 
                           'Non spécifiée'
            ],
            [
                'icon' => 'calendar',
                'label' => 'Publiée le',
                'value' => DateHelper::format($offer['date_creation'], 'd/m/Y')
            ]
        ], fn($item) => !empty($item['value']));
    }
    
    private function renderEmptyState()
    {
        Section::create(null, function() {
            echo HtmlHelper::emptyState(
                'Aucune offre disponible pour le moment',
                'calendar'
            );
            echo '<div class="text-center mt-6">';
            echo '<p class="text-gray-600 font-sans mb-4">Les nouvelles opportunités seront publiées ici dès qu\'elles seront disponibles.</p>';
            echo HtmlHelper::linkWithIcon(
                'Retour à l\'accueil',
                BASE_URL,
                'arrow-left',
                'text-blue-600 hover:text-blue-800 font-semibold'
            );
            echo '</div>';
        });
    }
    
    private function renderScript()
    {
        ?>
<script>
function filterOffers(type) {
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-600', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });

    const activeBtn = document.querySelector(`.filter-btn[data-type="${type}"]`);
    if (activeBtn) {
        activeBtn.classList.add('active', 'bg-blue-600', 'text-white');
        activeBtn.classList.remove('bg-gray-200', 'text-gray-700');
    }

    document.querySelectorAll('.offer-section').forEach(section => {
        section.style.display = (type === 'all' || section.dataset.type === type) ? 'block' : 'none';
    });
}
</script>
<?php
    }
}
?>