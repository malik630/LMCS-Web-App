<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/DateHelper.php';
require_once 'components/Section.php';
require_once 'components/Table.php';

class AdminTeamsView extends View
{
    protected $pageTitle = 'Gestion des Équipes - Admin';
    
    public function render()
    {
        $this->renderHeader();
        echo '<div class="container mx-auto px-4 py-8">';
        $this->renderPageHeader();
        $this->renderTeamsTable();
        echo '</div>';
        $this->renderFooter();
    }
    
    private function renderPageHeader()
    {
        ?>
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-4xl font-bold text-white mb-3">Gestion des Équipes</h1>
        <p class="text-blue-100 text-lg">Liste complète des équipes de recherche</p>
    </div>
    <div class="flex gap-4">
        <?php echo HtmlHelper::button('+ Créer une équipe', BASE_URL . 'admin/createTeam', 'success'); ?>
        <?php echo HtmlHelper::button('← Retour', BASE_URL . 'admin', 'secondary'); ?>
    </div>
</div>
<?php
    }
    
    private function renderTeamsTable()
    {
        $teams = $this->get('teams', []);
        
        Section::create('Liste des Équipes', function() use ($teams) {
            $tableData = $this->generateTableData($teams);
            
            Table::render([
                'id' => 'teams-table-admin',
                'headers' => [
                    ['label' => 'Équipe'],
                    ['label' => 'Chef d\'équipe'],
                    ['label' => 'Thématique'],
                    ['label' => 'Membres'],
                    ['label' => 'Publications'],
                    ['label' => 'Date création'],
                    ['label' => 'Actions', 'class' => 'w-48']
                ],
                'data' => $tableData,
                'searchable' => true,
                'sortable' => true,
                'filterable' => false,
                'ajax_url' => null,
                'empty_message' => 'Aucune équipe trouvée'
            ]);
            echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                if (typeof TableManager !== "undefined") {
                    window.adminTeamsTable = new TableManager("teams-table-admin", null, {
                        searchable: true,
                        sortable: true,
                        filterable: false
                    });
                }
            });
            </script>';
        }, 'bg-white');
    }
    
    private function generateTableData($teams)
    {
        if (empty($teams)) return '';
        
        $html = '';
        foreach ($teams as $team) {
            $html .= $this->generateTeamRow($team);
        }
        return $html;
    }
    
    private function generateTeamRow($team)
    {
        ob_start();
        ?>
<tr class="border-b border-gray-200 hover:bg-gray-50 transition"
    data-search="<?php echo $this->escape(strtolower($team['nom'] . ' ' . ($team['thematique'] ?? '') . ' ' . ($team['chef_prenom'] ?? '') . ' ' . ($team['chef_nom'] ?? ''))); ?>">

    <td class="px-6 py-4" data-sort="<?php echo $this->escape($team['nom']); ?>">
        <div class="font-bold text-gray-900 text-lg">
            <?php echo $this->escape($team['nom']); ?>
        </div>
        <a href="<?php echo BASE_URL . 'team/detail/' . $team['id_team']; ?>"
            class="text-sm text-blue-600 hover:text-blue-800 inline-flex items-center gap-1 mt-1">
            Voir détails publics
            <?php echo HtmlHelper::icon('arrow-right', 'w-3 h-3'); ?>
        </a>
    </td>

    <td class="px-6 py-4"
        data-sort="<?php echo $this->escape(($team['chef_nom'] ?? '') . ' ' . ($team['chef_prenom'] ?? '')); ?>">
        <?php if (!empty($team['chef_nom'])): ?>
        <div class="font-semibold text-gray-900">
            <?php echo $this->escape($team['chef_prenom'] . ' ' . $team['chef_nom']); ?>
        </div>
        <div class="text-xs text-gray-500">
            <?php echo $this->escape($team['chef_grade'] ?? ''); ?>
        </div>
        <?php else: ?>
        <span class="text-gray-400 italic">Non assigné</span>
        <?php endif; ?>
    </td>

    <td class="px-6 py-4" data-sort="<?php echo $this->escape($team['thematique'] ?? ''); ?>">
        <div class="text-sm text-gray-700 max-w-xs">
            <?php echo !empty($team['thematique']) ? $this->escape($team['thematique']) : '<span class="text-gray-400 italic">Non définie</span>'; ?>
        </div>
    </td>

    <td class="px-6 py-4 text-center" data-sort="<?php echo $team['nb_membres']; ?>">
        <span class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-700 rounded-full font-semibold">
            <?php echo HtmlHelper::icon('user', 'w-4 h-4'); ?>
            <?php echo $team['nb_membres']; ?>
        </span>
    </td>

    <td class="px-6 py-4 text-center" data-sort="<?php echo $team['nb_publications']; ?>">
        <span class="text-lg font-bold text-gray-900"><?php echo $team['nb_publications']; ?></span>
    </td>

    <td class="px-6 py-4 text-sm text-gray-600" data-sort="<?php echo $team['date_creation']; ?>">
        <?php echo DateHelper::format($team['date_creation'], 'd/m/Y'); ?>
    </td>

    <td class="px-6 py-4">
        <div class="flex gap-2">
            <a href="<?php echo BASE_URL . 'admin/manageTeamMembers/' . $team['id_team']; ?>"
                class="text-purple-600 hover:text-purple-800" title="Gérer les membres">
                <?php echo HtmlHelper::icon('user', 'w-5 h-5'); ?>
            </a>

            <a href="<?php echo BASE_URL . 'admin/editTeam/' . $team['id_team']; ?>"
                class="text-blue-600 hover:text-blue-800" title="Modifier">
                <?php echo HtmlHelper::icon('edit', 'w-5 h-5'); ?>
            </a>

            <a href="<?php echo BASE_URL . 'admin/deleteTeam/' . $team['id_team']; ?>"
                onclick="return confirm('Supprimer cette équipe ? Les membres ne seront pas supprimés.')"
                class="text-red-600 hover:text-red-800" title="Supprimer">
                <?php echo HtmlHelper::icon('trash', 'w-5 h-5'); ?>
            </a>
        </div>
    </td>
</tr>
<?php
        return ob_get_clean();
    }
}
?>