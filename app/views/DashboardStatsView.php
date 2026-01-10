<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/components/PageHeader.php';
require_once __DIR__ . '/components/StatsCard.php';

class DashboardStatsView extends View
{
    protected $pageTitle = 'Mes Statistiques - LMCS';
    
    public function render()
    {
        $this->renderHeader();
        $user = $this->get('user');
        $pubStats = $this->get('publication_stats');
        $projetStats = $this->get('projet_stats');
        $resStats = $this->get('reservation_stats');
        $teamStats = $this->get('team_stats');
        
        PageHeader::render([
            'title' => 'Mes Statistiques',
            'actions' => [
                [
                    'url' => BASE_URL . 'dashboardstats/rapportComplet',
                    'text' => 'Rapport Complet PDF',
                    'type' => 'primary',
                    'icon' => 'download'
                ]
            ]
        ]);

        StatsCard::renderGrid([
            [
                'label' => 'Publications',
                'value' => $pubStats['total'],
                'sublabel' => $pubStats['publie'] . ' publiées',
                'icon' => 'document',
                'color' => 'blue'
            ],
            [
                'label' => 'Projets',
                'value' => $projetStats['total'],
                'sublabel' => $projetStats['en_cours'] . ' en cours',
                'icon' => 'briefcase',
                'color' => 'green'
            ],
            [
                'label' => 'Réservations',
                'value' => $resStats['total'],
                'sublabel' => round($resStats['heures_totales']) . 'h totales',
                'icon' => 'calendar',
                'color' => 'purple'
            ],
            [
                'label' => 'Équipes',
                'value' => $teamStats['total'],
                'sublabel' => $teamStats['as_chef'] . ' en tant que chef',
                'icon' => 'users',
                'color' => 'orange'
            ]
        ], 4);
        
        echo '<div class="grid md:grid-cols-2 gap-8 mb-8">';

        $this->renderPublicationStats($pubStats);

        $this->renderProjetStats($projetStats);
        
        echo '</div>';
        
        echo '<div class="grid md:grid-cols-2 gap-8">';

        $this->renderReservationStats($resStats);

        $this->renderTeamStats($teamStats);
        
        echo '</div>';
        
        PageHeader::close();
        $this->renderFooter();
    }
    
    private function renderPublicationStats($stats)
    {
        ?>
<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Publications</h2>
        <a href="<?php echo BASE_URL; ?>dashboardstats/rapportPublications"
            class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
            Rapport PDF →
        </a>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="text-center p-4 bg-green-50 rounded-lg">
            <p class="text-2xl font-bold text-green-600"><?php echo $stats['publie']; ?></p>
            <p class="text-sm text-gray-600">Publiées</p>
        </div>
        <div class="text-center p-4 bg-yellow-50 rounded-lg">
            <p class="text-2xl font-bold text-yellow-600"><?php echo $stats['en_attente']; ?></p>
            <p class="text-sm text-gray-600">En attente</p>
        </div>
    </div>

    <div class="space-y-3">
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Productivité moyenne</span>
            <span class="font-semibold"><?php echo $stats['productivite_moyenne']; ?> /an</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Années actives</span>
            <span class="font-semibold"><?php echo $stats['annees_actives']; ?></span>
        </div>
        <?php if ($stats['derniere_annee']): ?>
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Dernière publication</span>
            <span class="font-semibold"><?php echo $stats['derniere_annee']; ?></span>
        </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($stats['par_type'])): ?>
    <div class="mt-6 pt-6 border-t">
        <h3 class="font-semibold mb-3">Par type</h3>
        <?php 
                arsort($stats['par_type']);
                foreach (array_slice($stats['par_type'], 0, 3, true) as $type => $count): 
                ?>
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm text-gray-600"><?php echo $this->escape($type); ?></span>
            <span class="font-semibold"><?php echo $count; ?></span>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?php
    }
    
    private function renderProjetStats($stats)
    {
        ?>
<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Projets</h2>
        <a href="<?php echo BASE_URL; ?>dashboardstats/rapportProjets"
            class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
            Rapport PDF →
        </a>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="text-center p-4 bg-blue-50 rounded-lg">
            <p class="text-2xl font-bold text-blue-600"><?php echo $stats['as_responsable']; ?></p>
            <p class="text-sm text-gray-600">Responsable</p>
        </div>
        <div class="text-center p-4 bg-purple-50 rounded-lg">
            <p class="text-2xl font-bold text-purple-600"><?php echo $stats['as_membre']; ?></p>
            <p class="text-sm text-gray-600">Membre</p>
        </div>
    </div>

    <div class="space-y-3">
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Budget total</span>
            <span class="font-semibold"><?php echo number_format($stats['budget_total'], 0, ',', ' '); ?> DA</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Collaborateurs uniques</span>
            <span class="font-semibold"><?php echo $stats['nb_collaborateurs']; ?></span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Publications liées</span>
            <span class="font-semibold"><?php echo $stats['publications_totales']; ?></span>
        </div>
    </div>

    <?php if (!empty($stats['par_thematique'])): ?>
    <div class="mt-6 pt-6 border-t">
        <h3 class="font-semibold mb-3">Par thématique</h3>
        <?php 
                arsort($stats['par_thematique']);
                foreach (array_slice($stats['par_thematique'], 0, 3, true) as $them => $count): 
                ?>
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm text-gray-600"><?php echo $this->escape($them); ?></span>
            <span class="font-semibold"><?php echo $count; ?></span>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?php
    }
    
    private function renderReservationStats($stats)
    {
        ?>
<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Réservations</h2>
        <a href="<?php echo BASE_URL; ?>dashboardstats/rapportReservations"
            class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
            Rapport PDF →
        </a>
    </div>

    <div class="space-y-3">
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Total réservations</span>
            <span class="font-semibold"><?php echo $stats['total']; ?></span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Heures totales</span>
            <span class="font-semibold"><?php echo round($stats['heures_totales'], 1); ?>h</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Équipements utilisés</span>
            <span class="font-semibold"><?php echo $stats['nb_equipements']; ?></span>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-2 gap-4">
        <div class="text-center p-4 bg-green-50 rounded-lg">
            <p class="text-xl font-bold text-green-600"><?php echo $stats['confirmee']; ?></p>
            <p class="text-xs text-gray-600">Confirmées</p>
        </div>
        <div class="text-center p-4 bg-blue-50 rounded-lg">
            <p class="text-xl font-bold text-blue-600"><?php echo $stats['terminee']; ?></p>
            <p class="text-xs text-gray-600">Terminées</p>
        </div>
    </div>
</div>
<?php
    }
    
    private function renderTeamStats($stats)
    {
        ?>
<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Équipes</h2>
        <a href="<?php echo BASE_URL; ?>dashboardteam/index"
            class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
            Voir équipes →
        </a>
    </div>

    <div class="space-y-3">
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Total équipes</span>
            <span class="font-semibold"><?php echo $stats['total']; ?></span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">En tant que chef</span>
            <span class="font-semibold"><?php echo $stats['as_chef']; ?></span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">En tant que membre</span>
            <span class="font-semibold"><?php echo $stats['as_membre']; ?></span>
        </div>
    </div>
</div>
<?php
    }
}
?>