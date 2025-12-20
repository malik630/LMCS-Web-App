<?php

require_once __DIR__ . '/../helpers/DateHelper.php';
require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once 'components/Section.php';
require_once 'components/Card.php';

class EquipementView extends View
{
    protected $pageTitle = 'Équipements - LMCS';
    
    public function render()
    {
        $this->renderHeader();
        echo '<div class="container mx-auto px-4 py-8">';
        
        $this->renderPageHeader();
        $this->renderFilters();
        $this->renderEquipments();
        
        echo '</div>';
        $this->renderScript();
        $this->renderFooter();
    }
    
    private function renderPageHeader()
    {
        ?>
<div class="mb-8">
    <h1 class="text-4xl font-bold text-white mb-3">Équipements du laboratoire</h1>
    <p class="text-blue-100 text-lg">Découvrez nos ressources techniques et infrastructures</p>
</div>
<?php
    }
    
    private function renderFilters()
    {
        ?>
<div class="bg-white rounded-lg shadow-lg p-6 mb-8">
    <div class="grid md:grid-cols-4 gap-4">
        <input type="text" id="search-input" placeholder="Rechercher..."
            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

        <select id="filter-type" class="filter-select px-4 py-2 border border-gray-300 rounded-lg">
            <option value="">Tous les types</option>
            <option value="1">Salles</option>
            <option value="2">Serveurs</option>
            <option value="3">PC</option>
            <option value="4">Robots</option>
            <option value="5">Imprimantes</option>
            <option value="6">Capteurs</option>
        </select>

        <select id="filter-etat" class="filter-select px-4 py-2 border border-gray-300 rounded-lg">
            <option value="">Tous les états</option>
            <option value="libre">Libre</option>
            <option value="reserve">Réservé</option>
            <option value="maintenance">Maintenance</option>
            <option value="hors_service">Hors service</option>
        </select>

        <button id="reset-btn" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
            Réinitialiser
        </button>
    </div>
</div>
<?php
    }
    
    private function renderEquipments()
    {
        $equipements = $this->data;
        
        // Grouper les équipements par type
        $equipementsByType = [];
        foreach ($equipements as $eq) {
            $typeId = $eq['type_equipement_id'] ?? 'other';
            $typeLibelle = $eq['type_libelle'] ?? 'Autre';
            
            if (!isset($equipementsByType[$typeId])) {
                $equipementsByType[$typeId] = [
                    'libelle' => ucfirst($typeLibelle),
                    'items' => []
                ];
            }
            
            $equipementsByType[$typeId]['items'][] = $eq;
        }
        
        if (empty($equipements)) {
            Section::create('Liste des équipements', function() {
                echo HtmlHelper::emptyState('Aucun équipement disponible');
            });
            return;
        }
        
        ?>
<div class="mb-12">
    <h2 class="text-3xl font-serif font-bold leading-relaxed tracking-tight text-white mb-6">
        Liste des équipements
    </h2>

    <div id="items-container">
        <?php foreach ($equipementsByType as $typeId => $typeData): ?>
        <div class="thematique-section mb-8" data-type="<?php echo $typeId; ?>">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-t-lg p-4 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">
                    <?php echo htmlspecialchars($typeData['libelle']); ?>
                </h3>
                <span class="thematique-count text-blue-100 text-sm">
                    (<?php echo count($typeData['items']); ?>
                    <?php echo count($typeData['items']) > 1 ? 'éléments' : 'élément'; ?>)
                </span>
            </div>

            <div class="bg-white rounded-b-lg shadow-lg p-6">
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($typeData['items'] as $eq): ?>
                    <?php $this->renderEquipmentCard($eq); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php
    }
    
    private function renderEquipmentCard($eq)
    {
        $etatConfig = [
            'libre' => ['text' => 'Libre', 'type' => 'success'],
            'reserve' => ['text' => 'Réservé', 'type' => 'warning'],
            'maintenance' => ['text' => 'Maintenance', 'type' => 'orange'],
            'hors_service' => ['text' => 'Hors service', 'type' => 'danger']
        ];
        
        $etat = $etatConfig[$eq['etat']] ?? ['text' => $eq['etat'], 'type' => 'info'];
        $isAvailable = $eq['etat'] === 'libre';
        
        // Tronquer la localisation si elle est trop longue
        $localisation = $eq['localisation'] ?? null;
        if ($localisation && strlen($localisation) > 40) {
            $localisation = substr($localisation, 0, 37) . '...';
        }
        
        $items = array_filter([
            ['label' => 'Type', 'value' => $eq['type_libelle'] ?? null],
            ['label' => 'Localisation', 'value' => $localisation],
            ['label' => 'Capacité', 'value' => !empty($eq['capacite']) ? $eq['capacite'] . ' personnes' : null],
            ['label' => 'N° série', 'value' => $eq['numero_serie'] ?? null]
        ], fn($item) => !empty($item['value']));
        
        $footerButton = null;
        if ($isAvailable) {
            if (isset($_SESSION['user_id'])) {
                $footerButton = [
                    'text' => 'Réserver',
                    'url' => BASE_URL . 'reservation?equipement=' . $eq['id_equipement'],
                    'type' => 'primary'
                ];
            } else {
                $footerButton = [
                    'text' => 'Se connecter pour réserver',
                    'url' => BASE_URL . 'auth/login',
                    'type' => 'primary'
                ];
            }
        }
        
        // Ajouter les attributs data directement dans le HTML
        echo '<div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition transform hover:-translate-y-1 flex flex-col h-full" 
                   data-title="' . htmlspecialchars(strtolower($eq['nom'])) . '" 
                   data-type="' . htmlspecialchars($eq['type_equipement_id']) . '" 
                   data-etat="' . htmlspecialchars($eq['etat']) . '">';
        
        echo '<div class="p-6 flex flex-col flex-grow">';
        
        // Badge état
        echo '<div class="mb-2">' . HtmlHelper::badge($etat['text'], $etat['type']) . '</div>';
        
        // Titre
        echo '<h3 class="text-xl font-bold mt-2 mb-3">' . htmlspecialchars($eq['nom']) . '</h3>';
        
        // Description avec hauteur max et scroll
        if (!empty($eq['description'])) {
            echo '<div class="text-gray-600 mb-4 max-h-20 overflow-y-auto pr-2 custom-scrollbar flex-grow">';
            echo '<p>' . nl2br(htmlspecialchars($eq['description'])) . '</p>';
            echo '</div>';
        }
        
        // Items avec word-break pour éviter le débordement
        if (!empty($items)) {
            echo '<div class="space-y-2 mb-4">';
            foreach ($items as $item) {
                echo '<div class="flex items-start gap-2 text-sm text-gray-600">';
                if (!empty($item['icon'])) {
                    echo '<span class="flex-shrink-0 mt-0.5">' . HtmlHelper::icon($item['icon']) . '</span>';
                }
                echo '<span class="break-words">';
                if (!empty($item['label'])) {
                    echo '<span class="font-semibold">' . htmlspecialchars($item['label']) . ':</span> ';
                }
                echo htmlspecialchars($item['value']);
                echo '</span>';
                echo '</div>';
            }
            echo '</div>';
        }
        
        // Footer button
        if ($footerButton) {
            echo '<div class="mt-auto pt-4 border-t border-gray-200">';
            echo HtmlHelper::button(
                $footerButton['text'],
                $footerButton['url'],
                $footerButton['type'],
                null,
                ['class' => 'w-full justify-center']
            );
            echo '</div>';
        }
        
        echo '</div>'; // Close p-6
        echo '</div>'; // Close card
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
        itemSelector: '.bg-white.rounded-lg.shadow-lg[data-title]',
        searchFields: ['data-title'],
        filterFields: {
            '#filter-type': 'data-type',
            '#filter-etat': 'data-etat'
        },
        emptyMessage: 'Aucun équipement ne correspond à vos critères.'
    });
});
</script>
<?php
    }
}
?>