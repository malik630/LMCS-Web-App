<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/DateHelper.php';
require_once 'components/Section.php';
require_once 'components/PageHeader.php';

class AdminEquipementHistoriqueView extends View
{
    protected $pageTitle = 'Historique Équipements - Admin';
    
    private $actionLabels = [
        'reservation' => 'Réservation',
        'annulation' => 'Annulation',
        'debut_utilisation' => 'Début utilisation',
        'fin_utilisation' => 'Fin utilisation',
        'maintenance' => 'Maintenance',
        'etat_change' => 'Changement d\'état'
    ];
    
    private $actionColors = [
        'reservation' => 'primary',
        'annulation' => 'danger',
        'debut_utilisation' => 'success',
        'fin_utilisation' => 'info',
        'maintenance' => 'warning',
        'etat_change' => 'orange'
    ];
    
    public function render()
    {
        $this->renderHeader();
        $historique = $this->get('historique', []);
        $equipementId = $this->get('equipementId');
        $equipement = $this->get('equipement');
        
        echo '<div class="container mx-auto px-4 py-8">';
        $this->renderPageHeader($equipement);
        $this->renderHistoriqueSection($historique);
        echo '</div>';
        
        $this->renderFooter();
    }
    
    private function renderPageHeader($equipement)
    {
        ?>
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-4xl font-bold text-white">Historique des Actions</h1>
        <?php if ($equipement): ?>
        <p class="text-white mt-2">Équipement : <strong><?php echo $this->escape($equipement['nom']); ?></strong></p>
        <?php endif; ?>
    </div>
    <?php echo HtmlHelper::button('← Retour', BASE_URL . 'admin/equipements', 'secondary'); ?>
</div>
<?php
    }
    
    private function renderHistoriqueSection($historique)
    {
        Section::create('Dernières Actions', function() use ($historique) {
            if (empty($historique)) {
                $this->renderEmptyState();
            } else {
                $this->renderHistoriqueTable($historique);
            }
        }, 'bg-white');
    }
    
    private function renderEmptyState()
    {
        echo '<p class="text-center text-gray-500 py-8">Aucune action enregistrée</p>';
    }
    
    private function renderHistoriqueTable($historique)
    {
        ?>
<div class="overflow-x-auto">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <?php $this->renderTableHeaders(); ?>
            </tr>
        </thead>
        <tbody class="divide-y">
            <?php foreach ($historique as $h): ?>
            <?php $this->renderHistoriqueRow($h); ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
    }
    
    private function renderTableHeaders()
    {
        $headers = ['Date', 'Équipement', 'Type', 'Action', 'Utilisateur'];
        
        foreach ($headers as $header) {
            echo '<th class="px-6 py-3 text-left text-sm font-semibold">' . $header . '</th>';
        }
    }
    
    private function renderHistoriqueRow($h)
    {
        ?>
<tr class="hover:bg-gray-50">
    <td class="px-6 py-4 text-sm">
        <?php echo DateHelper::format($h['date_action'], 'd/m/Y H:i'); ?>
    </td>
    <td class="px-6 py-4 font-medium">
        <?php echo $this->escape($h['equipement_nom']); ?>
    </td>
    <td class="px-6 py-4 text-sm">
        <?php echo $this->escape($h['type_equipement'] ?? '-'); ?>
    </td>
    <td class="px-6 py-4">
        <?php $this->renderActionBadge($h['action']); ?>
    </td>
    <td class="px-6 py-4 text-sm">
        <?php $this->renderUser($h); ?>
    </td>
</tr>
<?php
    }
    
    private function renderActionBadge($action)
    {
        $label = $this->actionLabels[$action] ?? $action;
        $color = $this->actionColors[$action] ?? 'primary';
        echo HtmlHelper::badge($label, $color);
    }
    
    private function renderUser($h)
    {
        if ($h['user_nom']) {
            echo $this->escape($h['user_prenom'] . ' ' . $h['user_nom']);
        } else {
            echo '<span class="text-gray-400">Système</span>';
        }
    }
}
?>