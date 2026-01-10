<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/DateHelper.php';
require_once 'components/Section.php';
require_once 'components/Table.php';
require_once 'components/StatsCard.php';

class AdminProjetsView extends View
{
    protected $pageTitle = 'Gestion des Projets - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $projets = $this->get('projets', []);
        $stats = $this->get('statistics', []);
        
        echo '<div class="container mx-auto px-4 py-8">';
        $this->renderPageHeader();
        $this->renderStatsCards($stats);
        $this->renderProjetsTable($projets);
        $this->renderDetailedStats($projets);
        echo '</div>';
        
        $this->renderFooter();
    }
    
    private function renderPageHeader()
    {
        ?>
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
<?php
    }
    
    private function renderStatsCards($stats)
    {
        $cards = [
            [
                'label' => 'Total',
                'value' => $stats['total'] ?? 0,
                'icon' => 'briefcase',
                'color' => 'blue'
            ],
            [
                'label' => 'En cours',
                'value' => $stats['en_cours'] ?? 0,
                'icon' => 'clock',
                'color' => 'blue'
            ],
            [
                'label' => 'Terminés',
                'value' => $stats['termine'] ?? 0,
                'icon' => 'check',
                'color' => 'green'
            ],
            [
                'label' => 'Soumis',
                'value' => $stats['soumis'] ?? 0,
                'icon' => 'document',
                'color' => 'purple'
            ]
        ];
        
        StatsCard::renderGrid($cards, 4);
    }
    
    private function renderProjetsTable($projets)
    {
        Section::create('Liste des Projets', function() use ($projets) {
            Table::render([
                'id' => 'projets-table',
                'headers' => [
                    ['label' => 'Projet'],
                    ['label' => 'Responsable'],
                    ['label' => 'Statut'],
                    ['label' => 'Membres'],
                    ['label' => 'Actions', 'class' => 'w-48']
                ],
                'data' => $this->generateTableData($projets),
                'searchable' => true,
                'sortable' => true,
                'filterable' => false,
                'empty_message' => 'Aucun projet trouvé'
            ]);
            
            $this->renderTableScript();
        }, 'bg-white');
    }
    
    private function generateTableData($projets)
    {
        if (empty($projets)) return '';
        return implode('', array_map([$this, 'generateRow'], $projets));
    }
    
    private function generateRow($p)
    {
        $searchData = strtolower($p['titre'] . ' ' . $p['responsable_nom']);
        
        ob_start();
        ?>
<tr class="border-b hover:bg-gray-50" data-search="<?php echo $searchData; ?>">
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
        <?php $this->renderRowActions($p); ?>
    </td>
</tr>
<?php
        return ob_get_clean();
    }
    
    private function renderRowActions($p)
    {
        $actions = [
            [
                'url' => BASE_URL . 'admin/manageProjetMembers/' . $p['id_projet'],
                'icon' => 'users',
                'title' => 'Membres',
                'class' => 'text-blue-600 hover:text-blue-800'
            ],
            [
                'url' => BASE_URL . 'admin/editProjet/' . $p['id_projet'],
                'icon' => 'edit',
                'title' => 'Modifier',
                'class' => 'text-gray-600 hover:text-gray-800'
            ],
            [
                'url' => BASE_URL . 'admin/deleteProjet/' . $p['id_projet'],
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
        new TableManager("projets-table", null, {
            searchable: true,
            sortable: true,
            filterable: false
        });
    }
});
</script>
<?php
    }
    
    private function renderDetailedStats($projets)
    {
        $byThematique = $this->groupByThematique($projets);
        $byAnnee = $this->groupByAnnee($projets);
        
        ?>
<div class="grid md:grid-cols-2 gap-8 mt-8">
    <?php $this->renderThematiqueStats($byThematique); ?>
    <?php $this->renderAnneeStats($byAnnee); ?>
</div>
<?php
    }
    
    private function groupByThematique($projets)
    {
        $grouped = [];
        foreach ($projets as $p) {
            $them = $p['thematique'] ?? 'Non définie';
            $grouped[$them] = ($grouped[$them] ?? 0) + 1;
        }
        arsort($grouped);
        return $grouped;
    }
    
    private function groupByAnnee($projets)
    {
        $grouped = [];
        foreach ($projets as $p) {
            $annee = date('Y', strtotime($p['date_debut']));
            $grouped[$annee] = ($grouped[$annee] ?? 0) + 1;
        }
        ksort($grouped);
        return $grouped;
    }
    
    private function renderThematiqueStats($byThematique)
    {
        ?>
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h2 class="text-xl font-bold mb-4 text-gray-900">Par Thématique</h2>
    <div class="space-y-3">
        <?php foreach ($byThematique as $them => $count): ?>
        <?php $this->renderStatRow($them, $count); ?>
        <?php endforeach; ?>
    </div>
</div>
<?php
    }
    
    private function renderAnneeStats($byAnnee)
    {
        ?>
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h2 class="text-xl font-bold mb-4 text-gray-900">Par Année</h2>
    <div class="space-y-3">
        <?php foreach ($byAnnee as $annee => $count): ?>
        <?php $this->renderStatRow($annee, $count); ?>
        <?php endforeach; ?>
    </div>
</div>
<?php
    }
    
    private function renderStatRow($label, $count)
    {
        ?>
<div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
    <span class="text-gray-700"><?php echo $this->escape($label); ?></span>
    <span class="font-bold text-gray-900 px-3 py-1 bg-gray-100 rounded"><?php echo $count; ?></span>
</div>
<?php
    }
}