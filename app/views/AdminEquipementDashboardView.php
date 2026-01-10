<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/DateHelper.php';
require_once 'components/Section.php';
require_once 'components/Table.php';
require_once 'components/StatsCard.php';

class AdminEquipementDashboardView extends View
{
    protected $pageTitle = 'Gestion Équipements - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $equipements = $this->get('equipements', []);
        $stats = $this->get('stats', []);
        $conflits = $this->get('conflits', []);
        
        echo '<div class="container mx-auto px-4 py-8">';
        $this->renderPageHeader();
        $this->renderStatsCards($stats);
        $this->renderEquipementsTable($equipements);
        echo '</div>';
        
        $this->renderFooter();
    }
    
    private function renderPageHeader()
    {
        ?>
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-4xl font-bold text-white mb-3">Gestion des Équipements</h1>
    </div>
    <div class="flex gap-4">
        <?php echo HtmlHelper::button('Réservations', BASE_URL . 'admin/reservations', 'secondary'); ?>
        <?php echo HtmlHelper::button('Rapports', BASE_URL . 'admin/rapportsEquipements', 'secondary'); ?>
        <?php echo HtmlHelper::button('+ Nouvel équipement', BASE_URL . 'admin/createEquipement', 'success'); ?>
        <?php echo HtmlHelper::button('← Retour', BASE_URL . 'admin', 'secondary'); ?>
    </div>
</div>
<?php
    }
    
    private function renderStatsCards($stats)
    {
        $cards = [
            [
                'label' => 'Total',
                'value' => $stats['total'],
                'icon' => 'briefcase',
                'color' => 'blue'
            ],
            [
                'label' => 'Libres',
                'value' => $stats['libres'],
                'icon' => 'check',
                'color' => 'green'
            ],
            [
                'label' => 'Maintenance',
                'value' => $stats['maintenance'],
                'icon' => 'warning',
                'color' => 'orange'
            ],
            [
                'label' => 'Conflits',
                'value' => $stats['conflits'],
                'icon' => 'error',
                'color' => 'red'
            ]
        ];
        
        StatsCard::renderGrid($cards, 4);
    }
    
    private function renderEquipementsTable($equipements)
    {
        Section::create('Liste des Équipements', function() use ($equipements) {
            Table::render([
                'id' => 'equipements-table',
                'headers' => [
                    ['label' => 'Équipement'],
                    ['label' => 'Type'],
                    ['label' => 'Localisation'],
                    ['label' => 'État'],
                    ['label' => 'Actions', 'class' => 'w-48']
                ],
                'data' => $this->generateTableData($equipements),
                'searchable' => true,
                'sortable' => true,
                'filterable' => false,
                'empty_message' => 'Aucun équipement trouvé'
            ]);
            
            $this->renderTableScript();
        }, 'bg-white');
    }
    
    private function generateTableData($equipements)
    {
        if (empty($equipements)) return '';
        
        return implode('', array_map([$this, 'generateRow'], $equipements));
    }
    
    private function generateRow($eq)
    {
        $etatColors = [
            'libre' => 'success',
            'reserve' => 'warning',
            'maintenance' => 'orange',
            'hors_service' => 'danger'
        ];
        
        $searchData = strtolower($eq['nom'] . ' ' . $eq['type_libelle'] . ' ' . $eq['localisation']);
        
        ob_start();
        ?>
<tr class="border-b hover:bg-gray-50" data-search="<?php echo $searchData; ?>">
    <td class="px-6 py-4" data-sort="<?php echo $this->escape($eq['nom']); ?>">
        <div class="font-bold"><?php echo $this->escape($eq['nom']); ?></div>
        <?php if (!empty($eq['description'])): ?>
        <div class="text-sm text-gray-600"><?php echo $this->escape($eq['description']); ?></div>
        <?php endif; ?>
    </td>
    <td class="px-6 py-4" data-sort="<?php echo $this->escape($eq['type_libelle']); ?>">
        <?php echo $this->escape($eq['type_libelle']); ?>
    </td>
    <td class="px-6 py-4" data-sort="<?php echo $this->escape($eq['localisation']); ?>">
        <?php echo $this->escape($eq['localisation']); ?>
    </td>
    <td class="px-6 py-4" data-sort="<?php echo $eq['etat']; ?>">
        <?php echo HtmlHelper::badge($eq['etat'], $etatColors[$eq['etat']] ?? 'primary'); ?>
    </td>
    <td class="px-6 py-4">
        <?php $this->renderRowActions($eq); ?>
    </td>
</tr>
<?php
        return ob_get_clean();
    }
    
    private function renderRowActions($eq)
    {
        $actions = [
            [
                'url' => BASE_URL . 'admin/editEquipement/' . $eq['id_equipement'],
                'icon' => 'edit',
                'title' => 'Modifier',
                'class' => 'text-gray-600 hover:text-gray-800'
            ],
            [
                'url' => BASE_URL . 'admin/historiqueEquipements?id=' . $eq['id_equipement'],
                'icon' => 'clock',
                'title' => 'Historique',
                'class' => 'text-blue-600 hover:text-blue-800'
            ],
            [
                'url' => BASE_URL . 'admin/deleteEquipement/' . $eq['id_equipement'],
                'icon' => 'trash',
                'title' => 'Supprimer',
                'class' => 'text-red-600 hover:text-red-800',
                'onclick' => "return confirm('Supprimer ?')"
            ]
        ];
        
        echo '<div class="flex gap-2">';
        foreach ($actions as $action) {
            echo '<a href="' . $action['url'] . '" ';
            echo 'title="' . $action['title'] . '" ';
            if (isset($action['onclick'])) {
                echo 'onclick="' . $action['onclick'] . '" ';
            }
            echo 'class="' . $action['class'] . '">';
            echo HtmlHelper::icon($action['icon'], 'w-5 h-5');
            echo '</a>';
        }
        echo '</div>';
    }
    
    private function renderTableScript()
    {
        ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    if (typeof TableManager !== "undefined") {
        new TableManager("equipements-table", null, {
            searchable: true,
            sortable: true,
            filterable: false
        });
    }
});
</script>
<?php
    }
}
?>