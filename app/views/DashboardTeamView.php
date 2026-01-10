<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/components/PageHeader.php';
require_once __DIR__ . '/components/StatsCard.php';
require_once __DIR__ . '/components/Section.php';

class DashboardTeamView extends View
{
    protected $pageTitle = 'Mes Équipes - LMCS';
    
    public function render()
    {
        $this->renderHeader();
        $user = $this->get('user');
        $teams = $this->get('teams');

        $asChef = count(array_filter($teams, fn($t) => $t['chef_id'] == $user['id_user']));
        $asMembre = count(array_filter($teams, fn($t) => $t['chef_id'] != $user['id_user']));
        
        PageHeader::render([
            'title' => 'Mes Équipes',
            'subtitle' => count($teams) . ' équipe' . (count($teams) > 1 ? 's' : '')
        ]);

        StatsCard::renderGrid([
            [
                'label' => 'En tant que chef',
                'value' => $asChef,
                'icon' => 'star',
                'color' => 'blue'
            ],
            [
                'label' => 'En tant que membre',
                'value' => $asMembre,
                'icon' => 'users',
                'color' => 'green'
            ]
        ], 2);
        
        if (!empty($teams)) {
            Section::create('Mes Équipes', function() use ($teams, $user) {
                echo '<div class="grid md:grid-cols-2 gap-6">';
                foreach ($teams as $team) {
                    $this->renderTeamCard($team, $user);
                }
                echo '</div>';
            }, 'bg-white');
        } else {
            $this->renderEmptyState();
        }
        
        PageHeader::close();
        $this->renderFooter();
    }
    
    private function renderTeamCard($team, $user)
    {
        $isChef = ($team['chef_id'] == $user['id_user']);
        $nbMembres = $team['nb_membres'] ?? 0;
        ?>
<div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
    <div class="flex justify-between items-start mb-3">
        <div class="flex-grow">
            <div class="flex items-center gap-2 mb-2">
                <?php if ($isChef): ?>
                <?php echo HtmlHelper::badge('Chef d\'équipe', 'primary'); ?>
                <?php else: ?>
                <?php echo HtmlHelper::badge($team['role_dans_equipe'] ?? 'Membre', 'info'); ?>
                <?php endif; ?>
            </div>
            <h3 class="font-bold text-gray-900 text-xl"><?php echo $this->escape($team['nom']); ?></h3>
        </div>
    </div>

    <?php if (!empty($team['thematique'])): ?>
    <p class="text-gray-600 text-sm mb-4">
        <span class="font-semibold">Thématique:</span> <?php echo $this->escape($team['thematique']); ?>
    </p>
    <?php endif; ?>

    <?php if (!empty($team['description'])): ?>
    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
        <?php echo $this->escape(substr($team['description'], 0, 150)); ?><?php echo strlen($team['description']) > 150 ? '...' : ''; ?>
    </p>
    <?php endif; ?>

    <div class="grid grid-cols-2 gap-4 mb-4 p-3 bg-gray-50 rounded">
        <div class="text-center">
            <p class="text-2xl font-bold text-blue-600"><?php echo $nbMembres; ?></p>
            <p class="text-xs text-gray-600">Membres</p>
        </div>
        <div class="text-center">
            <p class="text-2xl font-bold text-green-600">
                <?php echo !empty($team['chef_nom']) ? '1' : '0'; ?>
            </p>
            <p class="text-xs text-gray-600">Chef</p>
        </div>
    </div>

    <?php if (!empty($team['chef_nom'])): ?>
    <p class="text-sm text-gray-600 mb-4">
        <span class="font-semibold">Chef:</span>
        <?php echo $this->escape($team['chef_prenom'] . ' ' . $team['chef_nom']); ?>
    </p>
    <?php endif; ?>

    <div class="pt-4 border-t border-gray-200 flex gap-2">
        <a href="<?php echo BASE_URL; ?>dashboardteam/detail/<?php echo $team['id_team']; ?>"
            class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition inline-flex items-center justify-center gap-2">
            <?php echo HtmlHelper::icon('eye', 'w-4 h-4'); ?>
            Voir détails
        </a>

        <?php if ($isChef): ?>
        <a href="<?php echo BASE_URL; ?>dashboardteam/rapportEquipe/<?php echo $team['id_team']; ?>"
            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition inline-flex items-center gap-2">
            <?php echo HtmlHelper::icon('download', 'w-4 h-4'); ?>
            Rapport
        </a>
        <?php endif; ?>
    </div>
</div>
<?php
    }
    
    private function renderEmptyState()
    {
        ?>
<div class="bg-white rounded-lg shadow-lg p-12 text-center">
    <div class="mb-4 flex justify-center">
        <?php echo HtmlHelper::icon('users', 'w-16 h-16 text-gray-400'); ?>
    </div>
    <h3 class="text-xl font-semibold text-gray-700 mb-2">Aucune équipe</h3>
    <p class="text-gray-500">Vous ne faites partie d'aucune équipe pour le moment</p>
</div>
<?php
    }
}
?>