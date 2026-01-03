<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/DateHelper.php';
require_once 'components/Section.php';

class AdminEquipementHistoriqueView extends View
{
    protected $pageTitle = 'Historique Équipements - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $historique = $this->get('historique', []);
        $equipementId = $this->get('equipementId');
        $equipement = $this->get('equipement');
        ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-white">Historique des Actions</h1>
            <?php if ($equipement): ?>
            <p class="text-white mt-2">Équipement : <strong><?php echo $this->escape($equipement['nom']); ?></strong>
            </p>
            <?php endif; ?>
        </div>
        <?php echo HtmlHelper::button('← Retour', BASE_URL . 'admin/equipements', 'secondary'); ?>
    </div>

    <?php 
    Section::create('Dernières Actions', function() use ($historique) {
        $actionLabels = [
            'reservation' => 'Réservation',
            'annulation' => 'Annulation',
            'debut_utilisation' => 'Début utilisation',
            'fin_utilisation' => 'Fin utilisation',
            'maintenance' => 'Maintenance',
            'etat_change' => 'Changement d\'état'
        ];
        
        $actionColors = [
            'reservation' => 'primary',
            'annulation' => 'danger',
            'debut_utilisation' => 'success',
            'fin_utilisation' => 'info',
            'maintenance' => 'warning',
            'etat_change' => 'orange'
        ];
        ?>

    <?php if (empty($historique)): ?>
    <p class="text-center text-gray-500 py-8">Aucune action enregistrée</p>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Date</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Équipement</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Type</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Action</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Utilisateur</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php foreach ($historique as $h): ?>
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
                        <?php 
                        $label = $actionLabels[$h['action']] ?? $h['action'];
                        $color = $actionColors[$h['action']] ?? 'primary';
                        echo HtmlHelper::badge($label, $color);
                        ?>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <?php if ($h['user_nom']): ?>
                        <?php echo $this->escape($h['user_prenom'] . ' ' . $h['user_nom']); ?>
                        <?php else: ?>
                        <span class="text-gray-400">Système</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    <?php
    }, 'bg-white');
    ?>
</div>

<?php
        $this->renderFooter();
    }
}