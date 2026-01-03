<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/DateHelper.php';
require_once 'components/Section.php';
require_once 'components/Table.php';

class AdminEquipementDashboardView extends View
{
    protected $pageTitle = 'Gestion Équipements - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $equipements = $this->get('equipements', []);
        $stats = $this->get('stats', []);
        $conflits = $this->get('conflits', []);
        ?>

<div class="container mx-auto px-4 py-8">
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

    <div class="grid md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Total</span>
                <?php echo HtmlHelper::icon('briefcase', 'w-5 h-5 text-gray-400'); ?>
            </div>
            <div class="text-3xl font-bold text-gray-900"><?php echo $stats['total']; ?></div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Libres</span>
                <?php echo HtmlHelper::icon('check', 'w-5 h-5 text-gray-400'); ?>
            </div>
            <div class="text-3xl font-bold text-green-600"><?php echo $stats['libres']; ?></div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Maintenance</span>
                <?php echo HtmlHelper::icon('warning', 'w-5 h-5 text-gray-400'); ?>
            </div>
            <div class="text-3xl font-bold text-orange-600"><?php echo $stats['maintenance']; ?></div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Conflits</span>
                <?php echo HtmlHelper::icon('error', 'w-5 h-5 text-gray-400'); ?>
            </div>
            <div class="text-3xl font-bold text-red-600"><?php echo $stats['conflits']; ?></div>
        </div>
    </div>

    <?php 
    Section::create('Liste des Équipements', function() use ($equipements) {
        $tableData = $this->generateTableData($equipements);
        
        Table::render([
            'id' => 'equipements-table',
            'headers' => [
                ['label' => 'Équipement'],
                ['label' => 'Type'],
                ['label' => 'Localisation'],
                ['label' => 'État'],
                ['label' => 'Actions', 'class' => 'w-48']
            ],
            'data' => $tableData,
            'searchable' => true,
            'sortable' => true,
            'filterable' => false,
            'empty_message' => 'Aucun équipement trouvé'
        ]);
        
        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof TableManager !== "undefined") {
                new TableManager("equipements-table", null, {
                    searchable: true,
                    sortable: true,
                    filterable: false
                });
            }
        });
        </script>';
    }, 'bg-white');
    ?>
</div>

<?php
        $this->renderFooter();
    }
    
    private function generateTableData($equipements)
    {
        if (empty($equipements)) return '';
        
        $html = '';
        foreach ($equipements as $eq) {
            $html .= $this->generateRow($eq);
        }
        return $html;
    }
    
    private function generateRow($eq)
    {
        $etatColors = [
            'libre' => 'success',
            'reserve' => 'warning',
            'maintenance' => 'orange',
            'hors_service' => 'danger'
        ];
        
        ob_start();
        ?>
<tr class="border-b hover:bg-gray-50"
    data-search="<?php echo strtolower($eq['nom'] . ' ' . $eq['type_libelle'] . ' ' . $eq['localisation']); ?>">
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
        <div class="flex gap-2">
            <a href="<?php echo BASE_URL . 'admin/editEquipement/' . $eq['id_equipement']; ?>" title="Modifier"
                class="text-gray-600 hover:text-gray-800">
                <?php echo HtmlHelper::icon('edit', 'w-5 h-5'); ?>
            </a>
            <a href="<?php echo BASE_URL . 'admin/historiqueEquipements?id=' . $eq['id_equipement']; ?>"
                title="Historique" class="text-blue-600 hover:text-blue-800">
                <?php echo HtmlHelper::icon('clock', 'w-5 h-5'); ?>
            </a>
            <a href="<?php echo BASE_URL . 'admin/deleteEquipement/' . $eq['id_equipement']; ?>"
                onclick="return confirm('Supprimer ?')" title="Supprimer" class="text-red-600 hover:text-red-800">
                <?php echo HtmlHelper::icon('trash', 'w-5 h-5'); ?>
            </a>
        </div>
    </td>
</tr>
<?php
        return ob_get_clean();
    }
}