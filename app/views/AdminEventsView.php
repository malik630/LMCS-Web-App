<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/DateHelper.php';
require_once 'components/Section.php';
require_once 'components/Table.php';
require_once 'components/StatsCard.php';

class AdminEventsView extends View
{
    protected $pageTitle = 'Gestion des Événements - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $events = $this->get('events', []);
        $stats = $this->get('statistics', []);
        
        echo '<div class="container mx-auto px-4 py-8">';
        $this->renderPageHeader();
        $this->renderStatsCards($stats);
        $this->renderEventsTable($events);
        echo '</div>';
        
        $this->renderFooter();
    }
    
    private function renderPageHeader()
    {
        ?>
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-4xl font-bold text-white mb-3">Gestion des Événements</h1>
    </div>
    <div class="flex gap-4">
        <?php echo HtmlHelper::button('+ Nouvel événement', BASE_URL . 'admin/createEvent', 'success'); ?>
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
                'icon' => 'calendar',
                'color' => 'blue'
            ],
            [
                'label' => 'À venir',
                'value' => $stats['a_venir'] ?? 0,
                'icon' => 'clock',
                'color' => 'orange'
            ],
            [
                'label' => 'En cours',
                'value' => $stats['en_cours'] ?? 0,
                'icon' => 'refresh',
                'color' => 'blue'
            ],
            [
                'label' => 'Terminés',
                'value' => $stats['termine'] ?? 0,
                'icon' => 'check',
                'color' => 'green'
            ],
            [
                'label' => 'Externes',
                'value' => $stats['externe'] ?? 0,
                'icon' => 'external-link',
                'color' => 'purple'
            ],
            [
                'label' => 'Internes',
                'value' => $stats['interne'] ?? 0,
                'icon' => 'users',
                'color' => 'yellow'
            ]
        ];
        
        StatsCard::renderGrid($cards, 6);
    }
    
    private function renderEventsTable($events)
    {
        Section::create('Liste des Événements', function() use ($events) {
            Table::render([
                'id' => 'events-table',
                'headers' => [
                    ['label' => 'Événement'],
                    ['label' => 'Type'],
                    ['label' => 'Dates'],
                    ['label' => 'Statut'],
                    ['label' => 'Inscriptions'],
                    ['label' => 'Actions', 'class' => 'w-56']
                ],
                'data' => $this->generateTableData($events),
                'searchable' => true,
                'sortable' => true,
                'filterable' => true,
                'filters' => [
                    [
                        'id' => 'statut',
                        'label' => 'Statut',
                        'column' => 3,
                        'options' => [
                            'a_venir' => 'À venir',
                            'en_cours' => 'En cours',
                            'termine' => 'Terminé',
                            'annule' => 'Annulé'
                        ]
                    ],
                    [
                        'id' => 'type',
                        'label' => 'Type',
                        'column' => 4,
                        'options' => [
                            'interne' => 'Interne',
                            'externe' => 'Externe'
                        ]
                    ]
                ],
                'empty_message' => 'Aucun événement trouvé'
            ]);
            
            $this->renderTableScript();
        }, 'bg-white');
    }
    
    private function generateTableData($events)
    {
        if (empty($events)) return '';
        return implode('', array_map([$this, 'generateRow'], $events));
    }
    
    private function generateRow($e)
    {
        $eventModel = new Event();
        $nbInscrits = $eventModel->countInscriptions($e['id_evenement']);
        $capaciteText = $e['capacite_max'] ? "$nbInscrits / {$e['capacite_max']}" : $nbInscrits;
        
        $searchData = strtolower($e['titre'] . ' ' . $e['type_libelle'] . ' ' . $e['lieu']);
        
        $typeFilter = $e['externe'] ? 'externe' : 'interne';
        
        ob_start();
        ?>
<tr class="border-b hover:bg-gray-50" data-search="<?php echo $searchData; ?>"
    data-filter-3="<?php echo $e['statut']; ?>" data-filter-4="<?php echo $typeFilter; ?>">
    <td class="px-6 py-4" data-sort="<?php echo $this->escape($e['titre']); ?>">
        <div class="font-bold"><?php echo $this->escape($e['titre']); ?></div>
        <div class="text-sm text-gray-600">
            <?php echo HtmlHelper::icon('location', 'w-4 h-4 inline'); ?>
            <?php echo $this->escape($e['lieu'] ?? 'Non défini'); ?>
        </div>
    </td>
    <td class="px-6 py-4" data-sort="<?php echo $this->escape($e['type_libelle']); ?>">
        <div><?php echo $this->escape($e['type_libelle'] ?? 'Non défini'); ?></div>
        <div class="text-xs mt-1">
            <?php echo HtmlHelper::badge($e['externe'] ? 'Externe' : 'Interne', $e['externe'] ? 'info' : 'primary'); ?>
        </div>
    </td>
    <td class="px-6 py-4" data-sort="<?php echo $e['date_debut']; ?>">
        <div class="text-sm">
            <div><?php echo HtmlHelper::icon('calendar', 'w-4 h-4 inline'); ?>
                <?php echo DateHelper::format($e['date_debut']); ?></div>
            <?php if ($e['date_fin']): ?>
            <div class="text-gray-500">au <?php echo DateHelper::format($e['date_fin']); ?></div>
            <?php endif; ?>
        </div>
    </td>
    <td class="px-6 py-4" data-sort="<?php echo $e['statut']; ?>">
        <?php echo $this->getStatutBadge($e['statut']); ?>
    </td>
    <td class="px-6 py-4 text-center" data-sort="<?php echo $nbInscrits; ?>">
        <a href="<?php echo BASE_URL; ?>admin/manageInscriptions/<?php echo $e['id_evenement']; ?>"
            class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800">
            <?php echo HtmlHelper::icon('users', 'w-4 h-4'); ?>
            <span class="font-semibold"><?php echo $capaciteText; ?></span>
        </a>
    </td>
    <td class="px-6 py-4">
        <?php $this->renderRowActions($e); ?>
    </td>
</tr>
<?php
        return ob_get_clean();
    }
    
    private function getStatutBadge($statut)
    {
        $badges = [
            'a_venir' => ['text' => 'À venir', 'type' => 'warning'],
            'en_cours' => ['text' => 'En cours', 'type' => 'primary'],
            'termine' => ['text' => 'Terminé', 'type' => 'success'],
            'annule' => ['text' => 'Annulé', 'type' => 'danger']
        ];
        
        $badge = $badges[$statut] ?? ['text' => $statut, 'type' => 'info'];
        return HtmlHelper::badge($badge['text'], $badge['type']);
    }
    
    private function renderRowActions($e)
    {
        $actions = [
            [
                'url' => BASE_URL . 'admin/manageInscriptions/' . $e['id_evenement'],
                'icon' => 'users',
                'title' => 'Gérer inscriptions',
                'class' => 'text-blue-600 hover:text-blue-800'
            ],
            [
                'url' => BASE_URL . 'admin/editEvent/' . $e['id_evenement'],
                'icon' => 'edit',
                'title' => 'Modifier',
                'class' => 'text-gray-600 hover:text-gray-800'
            ],
            [
                'url' => BASE_URL . 'admin/deleteEvent/' . $e['id_evenement'],
                'icon' => 'trash',
                'title' => 'Supprimer',
                'class' => 'text-red-600 hover:text-red-800',
                'onclick' => "return confirm('Supprimer cet événement ?')"
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
        new TableManager("events-table", null, {
            searchable: true,
            sortable: true,
            filterable: true
        });
    }
});
</script>
<?php
    }
}