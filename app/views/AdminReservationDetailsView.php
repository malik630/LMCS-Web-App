<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/DateHelper.php';

class AdminReservationDetailsView extends View
{
    protected $pageTitle = 'Détails Réservation - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $reservation = $this->get('reservation');
        
        $statutColors = [
            'en_attente' => 'warning',
            'confirmee' => 'success',
            'annulee' => 'danger',
            'terminee' => 'info',
            'demande_annulation' => 'orange'
        ];
        ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex items-center justify-between">
        <h1 class="text-4xl font-bold text-white">Détails de la Réservation</h1>
        <?php echo HtmlHelper::button('← Retour', BASE_URL . 'admin/reservations', 'secondary'); ?>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8 max-w-4xl">
        <div class="mb-6">
            <h2 class="text-xl font-bold mb-3">Statut</h2>
            <?php echo HtmlHelper::badge($reservation['statut'], $statutColors[$reservation['statut']] ?? 'primary'); ?>
        </div>

        <div class="mb-6">
            <h2 class="text-xl font-bold mb-3">Équipement</h2>
            <div class="bg-gray-50 p-4 rounded">
                <p class="font-semibold text-lg"><?php echo $this->escape($reservation['equipement_nom']); ?></p>
                <div class="mt-2 text-sm space-y-1">
                    <p><strong>Type :</strong> <?php echo $this->escape($reservation['type_equipement']); ?></p>
                    <p><strong>Localisation :</strong> <?php echo $this->escape($reservation['localisation']); ?></p>
                    <p><strong>Capacité :</strong> <?php echo $this->escape($reservation['capacite']); ?> personnes</p>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <h2 class="text-xl font-bold mb-3">Demandeur</h2>
            <div class="bg-gray-50 p-4 rounded">
                <p class="font-semibold">
                    <?php echo $this->escape($reservation['user_prenom'] . ' ' . $reservation['user_nom']); ?></p>
                <p class="text-sm text-gray-600 mt-1"><?php echo $this->escape($reservation['user_email']); ?></p>
            </div>
        </div>

        <div class="mb-6">
            <h2 class="text-xl font-bold mb-3">Période de réservation</h2>
            <div class="bg-gray-50 p-4 rounded">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Date début</p>
                        <p class="font-semibold">
                            <?php echo DateHelper::format($reservation['date_debut'], 'd/m/Y H:i'); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Date fin</p>
                        <p class="font-semibold">
                            <?php echo DateHelper::format($reservation['date_fin'], 'd/m/Y H:i'); ?></p>
                    </div>
                </div>
                <div class="mt-3">
                    <p class="text-sm text-gray-600">Demandé le</p>
                    <p><?php echo DateHelper::format($reservation['date_reservation'], 'd/m/Y H:i'); ?></p>
                </div>
            </div>
        </div>

        <?php if (!empty($reservation['motif'])): ?>
        <div class="mb-6">
            <h2 class="text-xl font-bold mb-3">Motif</h2>
            <div class="bg-gray-50 p-4 rounded">
                <p><?php echo nl2br($this->escape($reservation['motif'])); ?></p>
            </div>
        </div>
        <?php endif; ?>

        <div class="flex gap-4 pt-6 border-t">
            <?php if ($reservation['statut'] == 'en_attente'): ?>
            <a href="<?php echo BASE_URL . 'admin/confirmerReservation/' . $reservation['id_reservation']; ?>"
                class="flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700"
                onclick="return confirm('Confirmer cette réservation ?')">
                <?php echo HtmlHelper::icon('check') ?> Confirmer
            </a>
            <a href="<?php echo BASE_URL . 'admin/rejeterReservation/' . $reservation['id_reservation']; ?>"
                class="flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700"
                onclick="return confirm('Rejeter cette demande ?')">
                <?php echo HtmlHelper::icon('close') ?> Rejeter
            </a>
            <?php endif; ?>

            <?php if ($reservation['statut'] == 'demande_annulation'): ?>
            <a href="<?php echo BASE_URL . 'admin/annulerReservation/' . $reservation['id_reservation']; ?>"
                class="flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700"
                onclick="return confirm('Accepter l\'annulation ?')">
                <?php echo HtmlHelper::icon('check') ?> Accepter annulation
            </a>
            <a href="<?php echo BASE_URL . 'admin/rejeterReservation/' . $reservation['id_reservation']; ?>"
                class="flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
                onclick="return confirm('Refuser l\'annulation ?')">
                <?php echo HtmlHelper::icon('close') ?> Rejeter
            </a>
            <?php endif; ?>

            <?php if ($reservation['statut'] == 'confirmee'): ?>
            <a href="<?php echo BASE_URL . 'admin/annulerReservation/' . $reservation['id_reservation']; ?>"
                class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700"
                onclick="return confirm('Annuler cette réservation ?')">
                Annuler la réservation
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
        $this->renderFooter();
    }
}