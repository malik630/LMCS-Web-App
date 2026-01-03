<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/DateHelper.php';
require_once 'components/Section.php';
require_once 'components/Table.php';

class AdminProjetsView extends View
{
    protected $pageTitle = 'Gestion des Projets - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $projets = $this->get('projets', []);
        $stats = $this->get('statistics', []);
        ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-white mb-3">Gestion des Projets</h1>
        </div>
        <div class="flex gap-4">
            <?php echo HtmlHelper::button('Rapport PDF', BASE_URL . 'admin/rapportProjetsPDF', 'secondary'); ?>
            <?php echo HtmlHelper::button('+ Nouveau projet', BASE_URL . 'admin/createProjet', 'success'); ?>
            <?php echo HtmlHelper::button('← Retour', BASE_URL . 'admin', 'secondary'); ?>
        </div>
    </div>

    <div class="grid md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Total</span>
                <?php echo HtmlHelper::icon('briefcase', 'w-5 h-5 text-gray-400'); ?>
            </div>
            <div class="text-3xl font-bold text-gray-900"><?php echo $stats['total'] ?? 0; ?></div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">En cours</span>
                <?php echo HtmlHelper::icon('clock', 'w-5 h-5 text-gray-400'); ?>
            </div>
            <div class="text-3xl font-bold text-blue-600"><?php echo $stats['en_cours'] ?? 0; ?></div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Terminés</span>
                <?php echo HtmlHelper::icon('check', 'w-5 h-5 text-gray-400'); ?>
            </div>
            <div class="text-3xl font-bold text-green-600"><?php echo $stats['termine'] ?? 0; ?></div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Soumis</span>
                <?php echo HtmlHelper::icon('document', 'w-5 h-5 text-gray-400'); ?>
            </div>
            <div class="text-3xl font-bold text-gray-600"><?php echo $stats['soumis'] ?? 0; ?></div>
        </div>
    </div>

    <?php 
    Section::create('Liste des Projets', function() use ($projets) {
        $tableData = $this->generateTableData($projets);
        
        Table::render([
            'id' => 'projets-table',
            'headers' => [
                ['label' => 'Projet'],
                ['label' => 'Responsable'],
                ['label' => 'Statut'],
                ['label' => 'Membres'],
                ['label' => 'Actions', 'class' => 'w-48']
            ],
            'data' => $tableData,
            'searchable' => true,
            'sortable' => true,
            'filterable' => false,
            'empty_message' => 'Aucun projet trouvé'
        ]);
        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof TableManager !== "undefined") {
                new TableManager("projets-table", null, {
                    searchable: true,
                    sortable: true,
                    filterable: false
                });
            }
        });
        </script>';
    }, 'bg-white');
    ?>

    <?php $this->renderDetailedStats(); ?>
</div>

<?php
        $this->renderFooter();
    }
    
    private function generateTableData($projets)
    {
        if (empty($projets)) return '';
        
        $html = '';
        foreach ($projets as $p) {
            $html .= $this->generateRow($p);
        }
        return $html;
    }
    
    private function generateRow($p)
    {
        ob_start();
        ?>
<tr class="border-b hover:bg-gray-50"
    data-search="<?php echo strtolower($p['titre'] . ' ' . $p['responsable_nom']); ?>">
    <td class="px-6 py-4" data-sort="<?php echo $this->escape($p['titre']); ?>">
        <div class="font-bold"><?php echo $this->escape($p['titre']); ?></div>
        <div class="text-sm text-gray-600"><?php echo $this->escape($p['thematique'] ?? ''); ?></div>
    </td>
    <td class="px-6 py-4" data-sort="<?php echo $this->escape($p['responsable_nom']); ?>">
        <?php echo $this->escape($p['responsable_prenom'] . ' ' . $p['responsable_nom']); ?>
    </td>
    <td class="px-6 py-4" data-sort="<?php echo $p['statut']; ?>">
        <?php echo HtmlHelper::badge($p['statut'], 'primary'); ?>
    </td>
    <td class="px-6 py-4 text-center" data-sort="<?php echo $p['nb_membres']; ?>">
        <span class="inline-flex items-center gap-1 text-gray-700">
            <?php echo HtmlHelper::icon('user', 'w-4 h-4'); ?>
            <?php echo $p['nb_membres']; ?>
        </span>
    </td>
    <td class="px-6 py-4">
        <div class="flex gap-2">
            <a href="<?php echo BASE_URL . 'admin/manageProjetMembers/' . $p['id_projet']; ?>" title="Membres"
                class="text-blue-600 hover:text-blue-800">
                <?php echo HtmlHelper::icon('users', 'w-5 h-5'); ?>
            </a>
            <a href="<?php echo BASE_URL . 'admin/editProjet/' . $p['id_projet']; ?>" title="Modifier"
                class="text-gray-600 hover:text-gray-800">
                <?php echo HtmlHelper::icon('edit', 'w-5 h-5'); ?>
            </a>
            <a href="<?php echo BASE_URL . 'admin/deleteProjet/' . $p['id_projet']; ?>"
                onclick="return confirm('Supprimer ?')" title="Supprimer" class="text-red-600 hover:text-red-800">
                <?php echo HtmlHelper::icon('trash', 'w-5 h-5'); ?>
            </a>
        </div>
    </td>
</tr>
<?php
        return ob_get_clean();
    }
    
    private function renderDetailedStats()
    {
        $projets = $this->get('projets', []);
    
        $byThematique = [];
        $byAnnee = [];
        
        foreach ($projets as $p) {
            $them = $p['thematique'] ?? 'Non définie';
            $byThematique[$them] = ($byThematique[$them] ?? 0) + 1;
            
            $annee = date('Y', strtotime($p['date_debut']));
            $byAnnee[$annee] = ($byAnnee[$annee] ?? 0) + 1;
        }
        
        arsort($byThematique);
        ksort($byAnnee);
        ?>

<div class="grid md:grid-cols-2 gap-8 mt-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-bold mb-4 text-gray-900">Par Thématique</h2>
        <div class="space-y-3">
            <?php foreach ($byThematique as $them => $count): ?>
            <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                <span class="text-gray-700"><?php echo $this->escape($them); ?></span>
                <span class="font-bold text-gray-900 px-3 py-1 bg-gray-100 rounded"><?php echo $count; ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-bold mb-4 text-gray-900">Par Année</h2>
        <div class="space-y-3">
            <?php foreach ($byAnnee as $annee => $count): ?>
            <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                <span class="text-gray-700"><?php echo $annee; ?></span>
                <span class="font-bold text-gray-900 px-3 py-1 bg-gray-100 rounded"><?php echo $count; ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php
    }
}
?>