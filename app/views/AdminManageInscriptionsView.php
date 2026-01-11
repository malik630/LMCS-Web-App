<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/DateHelper.php';
require_once 'components/PageHeader.php';
require_once 'components/Section.php';
require_once 'components/Table.php';
require_once 'components/InfoGrid.php';

class AdminManageInscriptionsView extends View
{
    protected $pageTitle = 'Gestion des Inscriptions - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $event = $this->get('event');
        $inscriptions = $this->get('inscriptions', []);
        $nbConfirmees = $this->get('nb_confirmees', 0);
        
        if (!$event) {
            echo '<div class="container mx-auto px-4 py-8">';
            echo '<p class="text-red-600">Événement introuvable.</p>';
            echo '</div>';
            $this->renderFooter();
            return;
        }
        
        PageHeader::render([
            'title' => 'Gestion des inscriptions',
            'subtitle' => $event['titre'],
            'back_link' => [
                'url' => BASE_URL . 'admin/evenements',
                'text' => 'Retour à la liste des événements'
            ]
        ]);
        
        $this->renderEventInfo($event, $nbConfirmees);
        $this->renderInscriptionsTable($inscriptions, $event);
        
        PageHeader::close();
        $this->renderFooter();
    }
    
    private function renderEventInfo($event, $nbConfirmees)
    {
        Section::create('Informations sur l\'événement', function() use ($event, $nbConfirmees) {
            $capaciteMax = $event['capacite_max'] ?? 'Illimitée';
            $capaciteClass = '';
            $capaciteInfo = '';
            
            if ($event['capacite_max']) {
                $pourcentage = ($nbConfirmees / $event['capacite_max']) * 100;
                if ($nbConfirmees >= $event['capacite_max']) {
                    $capaciteClass = 'text-red-600 font-bold';
                    $capaciteInfo = ' (COMPLET)';
                } elseif ($pourcentage >= 80) {
                    $capaciteClass = 'text-orange-600 font-bold';
                    $capaciteInfo = ' (Presque complet)';
                }
            }
            
            InfoGrid::render([
                [
                    'label' => 'Type',
                    'value' => $event['externe'] ? 'Externe (public)' : 'Interne (membres uniquement)'
                ],
                [
                    'label' => 'Date de début',
                    'value' => $event['date_debut'],
                    'format' => 'date'
                ],
                [
                    'label' => 'Date de fin',
                    'value' => $event['date_fin'],
                    'format' => 'date'
                ],
                [
                    'label' => 'Lieu',
                    'value' => $event['lieu']
                ],
                [
                    'label' => 'Statut',
                    'value' => $event['statut']
                ],
                [
                    'label' => 'Inscriptions confirmées',
                    'value' => "$nbConfirmees / $capaciteMax $capaciteInfo"
                ]
            ], 3);
            
            if ($event['capacite_max'] && $nbConfirmees >= $event['capacite_max']) {
                echo '<div class="mt-4 p-4 bg-red-50 border-l-4 border-red-500 rounded">';
                echo '<p class="text-red-800 font-semibold">';
                echo HtmlHelper::icon('warning', 'w-5 h-5 inline mr-2');
                echo 'Capacité maximale atteinte ! Vous devez d\'abord rejeter une inscription pour en confirmer une autre.';
                echo '</p>';
                echo '</div>';
            }
        });
    }
    
    private function renderInscriptionsTable($inscriptions, $event)
    {
        $hasCapacity = !$event['capacite_max'] || 
                       $this->get('nb_confirmees', 0) < $event['capacite_max'];
        
        Section::create('Liste des inscriptions', function() use ($inscriptions, $hasCapacity) {
            Table::render([
                'id' => 'inscriptions-table',
                'headers' => [
                    ['label' => 'Participant'],
                    ['label' => 'Email'],
                    ['label' => 'Date d\'inscription'],
                    ['label' => 'Statut'],
                    ['label' => 'Actions', 'class' => 'w-64']
                ],
                'data' => $this->generateTableData($inscriptions, $hasCapacity),
                'searchable' => true,
                'sortable' => true,
                'filterable' => true,
                'filters' => [
                    [
                        'id' => 'statut',
                        'label' => 'Statut',
                        'column' => 3,
                        'options' => [
                            'en_attente' => 'En attente',
                            'confirmee' => 'Confirmée',
                            'annulee' => 'Annulée',
                            'demande_annulation' => 'Demande d\'annulation'
                        ]
                    ]
                ],
                'empty_message' => 'Aucune inscription'
            ]);
            
            $this->renderTableScript();
        });
    }
    
    private function generateTableData($inscriptions, $hasCapacity)
    {
        if (empty($inscriptions)) return '';
        return implode('', array_map(function($i) use ($hasCapacity) {
            return $this->generateRow($i, $hasCapacity);
        }, $inscriptions));
    }
    
    private function generateRow($i, $hasCapacity)
    {
        $nom = $i['usr_id'] 
            ? ($i['prenom'] . ' ' . $i['nom'])
            : $i['nom'];
        
        $email = $i['usr_id'] ? $i['user_email'] : $i['email'];
        
        $searchData = strtolower($nom . ' ' . $email);
        
        ob_start();
        ?>
<tr class="border-b hover:bg-gray-50" data-search="<?php echo $searchData; ?>"
    data-filter-3="<?php echo $i['statut']; ?>">
    <td class="px-6 py-4" data-sort="<?php echo $this->escape($nom); ?>">
        <div class="font-medium"><?php echo $this->escape($nom); ?></div>
        <?php if ($i['usr_id']): ?>
        <div class="text-xs text-gray-500">Membre inscrit</div>
        <?php else: ?>
        <div class="text-xs text-gray-500">Participant externe</div>
        <?php endif; ?>
    </td>
    <td class="px-6 py-4" data-sort="<?php echo $this->escape($email); ?>">
        <?php echo $this->escape($email); ?>
    </td>
    <td class="px-6 py-4" data-sort="<?php echo $i['date_inscription']; ?>">
        <?php echo DateHelper::format($i['date_inscription'], 'd/m/Y H:i'); ?>
    </td>
    <td class="px-6 py-4" data-sort="<?php echo $i['statut']; ?>">
        <?php echo $this->getStatutBadge($i['statut']); ?>
    </td>
    <td class="px-6 py-4">
        <?php $this->renderRowActions($i, $hasCapacity); ?>
    </td>
</tr>
<?php
        return ob_get_clean();
    }
    
    private function getStatutBadge($statut)
    {
        $badges = [
            'en_attente' => ['text' => 'En attente', 'type' => 'warning'],
            'confirmee' => ['text' => 'Confirmée', 'type' => 'success'],
            'annulee' => ['text' => 'Annulée', 'type' => 'danger'],
            'demande_annulation' => ['text' => 'Demande d\'annulation', 'type' => 'orange']
        ];
        
        $badge = $badges[$statut] ?? ['text' => $statut, 'type' => 'info'];
        return HtmlHelper::badge($badge['text'], $badge['type']);
    }
    
    private function renderRowActions($i, $hasCapacity)
    {
        echo '<div class="flex gap-2">';
        
        if ($i['statut'] === 'en_attente') {
            if ($hasCapacity) {
                echo '<a href="' . BASE_URL . 'admin/confirmerInscription/' . $i['id'] . '" ';
                echo 'title="Confirmer" ';
                echo 'class="text-green-600 hover:text-green-800 inline-flex items-center gap-1 px-3 py-1 bg-green-50 rounded">';
                echo HtmlHelper::icon('check', 'w-4 h-4');
                echo '<span class="text-sm font-medium">Confirmer</span>';
                echo '</a>';
            } else {
                echo '<span class="text-gray-400 inline-flex items-center gap-1 px-3 py-1 bg-gray-50 rounded" ';
                echo 'title="Capacité maximale atteinte">';
                echo HtmlHelper::icon('check', 'w-4 h-4');
                echo '<span class="text-sm">Confirmer</span>';
                echo '</span>';
            }
            
            echo '<a href="' . BASE_URL . 'admin/rejeterInscription/' . $i['id'] . '" ';
            echo 'title="Rejeter" ';
            echo 'onclick="return confirm(\'Rejeter cette inscription ?\')" ';
            echo 'class="text-red-600 hover:text-red-800 inline-flex items-center gap-1 px-3 py-1 bg-red-50 rounded">';
            echo HtmlHelper::icon('close', 'w-4 h-4');
            echo '<span class="text-sm font-medium">Rejeter</span>';
            echo '</a>';
        } elseif ($i['statut'] === 'confirmee') {
            echo '<a href="' . BASE_URL . 'admin/annulerInscription/' . $i['id'] . '" ';
            echo 'title="Annuler" ';
            echo 'onclick="return confirm(\'Annuler cette inscription ?\')" ';
            echo 'class="text-orange-600 hover:text-orange-800 inline-flex items-center gap-1 px-3 py-1 bg-orange-50 rounded">';
            echo HtmlHelper::icon('close', 'w-4 h-4');
            echo '<span class="text-sm font-medium">Annuler</span>';
            echo '</a>';
        } elseif ($i['statut'] === 'demande_annulation') {
            echo '<a href="' . BASE_URL . 'admin/annulerInscription/' . $i['id'] . '" ';
            echo 'title="Traiter la demande d\'annulation" ';
            echo 'class="text-blue-600 hover:text-blue-800 inline-flex items-center gap-1 px-3 py-1 bg-blue-50 rounded">';
            echo HtmlHelper::icon('check', 'w-4 h-4');
            echo '<span class="text-sm font-medium">Traiter</span>';
            echo '</a>';
        } else {
            echo '<span class="text-gray-400 text-sm">Aucune action</span>';
        }
        
        echo '</div>';
    }
    
    private function renderTableScript()
    {
        ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    if (typeof TableManager !== "undefined") {
        new TableManager("inscriptions-table", null, {
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