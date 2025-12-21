<?php

require_once __DIR__ . '/../helpers/DateHelper.php';
require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once 'components/Section.php';

class ReservationView extends View
{
    protected $pageTitle = 'Mes Réservations - LMCS';
    
    public function render()
    {
        $this->renderHeader();
        echo '<div class="container mx-auto px-4 py-8">';
        $this->renderPageHeader();
        $this->renderStatistics();
        
        if ($this->get('isAdmin')) {
            $this->renderPendingReservations();
        }
        
        $this->renderUpcomingReservations();
        $this->renderAllReservations();
        
        echo '</div>';
        $this->renderFooter();
    }
    
    private function renderPageHeader()
    {
        $isAdmin = $this->get('isAdmin');
        ?>
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-4xl font-bold text-white mb-2">
            <?php echo $isAdmin ? 'Gestion des Réservations' : 'Mes Réservations'; ?>
        </h1>
        <p class="text-blue-100 text-lg">
            <?php echo $isAdmin ? 'Gérez toutes les réservations d\'équipements' : 'Consultez et gérez vos réservations'; ?>
        </p>
    </div>
    <div class="flex gap-4">
        <?php echo HtmlHelper::button(
            'Calendrier',
            BASE_URL . 'reservation/calendar',
            'secondary',
            'calendar'
        ); ?>
        <?php echo HtmlHelper::button(
            'Nouvelle réservation',
            BASE_URL . 'reservation/create',
            'primary',
            'calendar'
        ); ?>
    </div>
</div>
<?php
    }
    
    private function renderStatistics()
    {
        $stats = $this->get('statistics');
        if (!$stats) return;
        
        ?>
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
    <?php $this->renderStatCard('Total', $stats['total'], 'info'); ?>
    <?php $this->renderStatCard('En attente', $stats['en_attente'], 'warning'); ?>
    <?php $this->renderStatCard('Confirmées', $stats['confirmees'], 'success'); ?>
    <?php $this->renderStatCard('Annulées', $stats['annulees'], 'danger'); ?>
    <?php $this->renderStatCard('Terminées', $stats['terminees'], 'info'); ?>
</div>
<?php
    }
    
    private function renderStatCard($label, $value, $type)
    {
        $colors = [
            'info' => 'bg-blue-500',
            'success' => 'bg-green-500',
            'warning' => 'bg-yellow-500',
            'danger' => 'bg-red-500'
        ];
        
        $bgColor = $colors[$type] ?? $colors['info'];
        ?>
<div class="<?php echo $bgColor; ?> rounded-lg p-6 text-white">
    <h3 class="text-3xl font-bold mb-1"><?php echo $value; ?></h3>
    <p class="text-sm opacity-90"><?php echo $label; ?></p>
</div>
<?php
    }
    
    private function renderPendingReservations()
    {
        $pending = $this->get('pending', []);
        
        Section::create('Demandes en attente', function() use ($pending) {
            if (empty($pending)) {
                echo HtmlHelper::emptyState('Aucune demande en attente');
                return;
            }
            
            echo '<div class="space-y-4">';
            foreach ($pending as $reservation) {
                $this->renderReservationCard($reservation, true);
            }
            echo '</div>';
        });
    }
    
    private function renderUpcomingReservations()
    {
        $upcoming = $this->get('upcoming', []);
        
        Section::create('Réservations à venir', function() use ($upcoming) {
            if (empty($upcoming)) {
                echo HtmlHelper::emptyState('Aucune réservation à venir');
                return;
            }
            
            echo '<div class="space-y-4">';
            foreach ($upcoming as $reservation) {
                $this->renderReservationCard($reservation, false);
            }
            echo '</div>';
        });
    }
    
    private function renderAllReservations()
    {
        $reservations = $this->get('reservations', []);
        
        Section::create('Historique des réservations', function() use ($reservations) {
            if (empty($reservations)) {
                echo HtmlHelper::emptyState('Aucune réservation');
                return;
            }
            
            echo '<div class="overflow-x-auto">';
            echo '<table class="w-full">';
            echo '<thead class="bg-gray-50">';
            echo '<tr>';
            echo '<th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Équipement</th>';
            if ($this->get('isAdmin')) {
                echo '<th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Utilisateur</th>';
            }
            echo '<th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Période</th>';
            echo '<th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Statut</th>';
            echo '<th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody class="divide-y divide-gray-200">';
            
            foreach ($reservations as $reservation) {
                $this->renderReservationRow($reservation);
            }
            
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        });
    }
    
    private function renderReservationCard($reservation, $isPending)
    {
        $statusConfig = [
            'en_attente' => ['text' => 'En attente', 'type' => 'warning'],
            'confirmee' => ['text' => 'Confirmée', 'type' => 'success'],
            'demande_annulation' => ['text' => 'Demande d\'annulation', 'type' => 'warning'],
            'annulee' => ['text' => 'Annulée', 'type' => 'danger'],
            'terminee' => ['text' => 'Terminée', 'type' => 'info']
        ];
        
        $status = $statusConfig[$reservation['statut']] ?? ['text' => $reservation['statut'], 'type' => 'info'];
        ?>
<div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
    <div class="flex justify-between items-start mb-4">
        <div class="flex-grow">
            <h3 class="text-xl font-bold text-gray-900 mb-2">
                <?php echo $this->escape($reservation['equipement_nom']); ?></h3>
            <?php if ($this->get('isAdmin')): ?>
            <p class="text-gray-600 mb-2">
                <span class="font-semibold">Demandeur :</span>
                <?php echo $this->escape($reservation['user_prenom'] . ' ' . $reservation['user_nom']); ?>
            </p>
            <?php endif; ?>
            <div class="flex items-center gap-4 text-sm text-gray-600">
                <div class="flex items-center gap-2">
                    <?php echo HtmlHelper::icon('calendar', 'w-4 h-4'); ?>
                    <span><?php echo DateHelper::format($reservation['date_debut'], 'd/m/Y H:i'); ?></span>
                </div>
                <span>→</span>
                <span><?php echo DateHelper::format($reservation['date_fin'], 'd/m/Y H:i'); ?></span>
            </div>
            <?php if (!empty($reservation['motif'])): ?>
            <p class="text-gray-600 mt-2 text-sm">
                <span class="font-semibold">Motif :</span> <?php echo $this->escape($reservation['motif']); ?>
            </p>
            <?php endif; ?>
        </div>
        <div>
            <?php echo HtmlHelper::badge($status['text'], $status['type']); ?>
        </div>
    </div>

    <div class="flex gap-2 pt-4 border-t">
        <?php if ($isPending && $this->get('isAdmin')): ?>
        <a href="<?php echo BASE_URL . 'reservation/confirm/' . $reservation['id_reservation']; ?>"
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm"
            onclick="return confirm('Confirmer cette réservation ?')">
            Confirmer
        </a>
        <a href="<?php echo BASE_URL . 'reservation/cancel/' . $reservation['id_reservation']; ?>"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm"
            onclick="return confirm('Refuser cette réservation ?')">
            Refuser
        </a>
        <?php endif; ?>

        <a href="<?php echo BASE_URL . 'reservation/view/' . $reservation['id_reservation']; ?>"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
            Détails
        </a>
    </div>
</div>
<?php
    }
    
    private function renderReservationRow($reservation)
    {
        $statusConfig = [
            'en_attente' => ['text' => 'En attente', 'type' => 'warning'],
            'confirmee' => ['text' => 'Confirmée', 'type' => 'success'],
            'demande_annulation' => ['text' => 'Demande d\'annulation', 'type' => 'warning'],
            'annulee' => ['text' => 'Annulée', 'type' => 'danger'],
            'terminee' => ['text' => 'Terminée', 'type' => 'info']
        ];
        
        $status = $statusConfig[$reservation['statut']] ?? ['text' => $reservation['statut'], 'type' => 'info'];
        ?>
<tr class="hover:bg-gray-50">
    <td class="px-4 py-3">
        <div class="font-semibold text-gray-900"><?php echo $this->escape($reservation['equipement_nom']); ?></div>
        <div class="text-sm text-gray-600"><?php echo $this->escape($reservation['type_equipement']); ?></div>
    </td>
    <?php if ($this->get('isAdmin')): ?>
    <td class="px-4 py-3">
        <div class="text-gray-900">
            <?php echo $this->escape($reservation['user_prenom'] . ' ' . $reservation['user_nom']); ?></div>
        <div class="text-sm text-gray-600"><?php echo $this->escape($reservation['user_email']); ?></div>
    </td>
    <?php endif; ?>
    <td class="px-4 py-3 text-sm">
        <div><?php echo DateHelper::format($reservation['date_debut'], 'd/m/Y H:i'); ?></div>
        <div class="text-gray-600"><?php echo DateHelper::format($reservation['date_fin'], 'd/m/Y H:i'); ?></div>
    </td>
    <td class="px-4 py-3">
        <?php echo HtmlHelper::badge($status['text'], $status['type']); ?>
    </td>
    <td class="px-4 py-3">
        <div class="flex gap-2">
            <a href="<?php echo BASE_URL . 'reservation/view/' . $reservation['id_reservation']; ?>"
                class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                Voir
            </a>

            <?php if ($reservation['statut'] === 'en_attente' && $this->get('isAdmin')): ?>
            <a href="<?php echo BASE_URL . 'reservation/confirm/' . $reservation['id_reservation']; ?>"
                class="text-green-600 hover:text-green-800 text-sm font-semibold"
                onclick="return confirm('Confirmer cette réservation ?')">
                Confirmer
            </a>
            <?php endif; ?>

            <?php if (in_array($reservation['statut'], ['en_attente', 'confirmee']) && 
                      ($reservation['usr_id'] == $_SESSION['user_id'] || $this->get('isAdmin'))): ?>
            <a href="<?php echo BASE_URL . 'reservation/requestCancellation/' . $reservation['id_reservation']; ?>"
                class="text-red-600 hover:text-red-800 text-sm font-semibold"
                onclick="return confirm('Demander l\'annulation de cette réservation ?')">
                Annuler
            </a>
            <?php endif; ?>
        </div>
    </td>
</tr>
<?php
    }
}
?>