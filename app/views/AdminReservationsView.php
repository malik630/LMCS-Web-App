<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/DateHelper.php';
require_once 'components/Section.php';
require_once 'components/Table.php';

class AdminReservationsView extends View
{
    protected $pageTitle = 'Gestion des Réservations - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $reservations = $this->get('reservations', []);
        $stats = $this->get('stats', []);
        $conflits = $this->get('conflits', []);
        ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-white mb-3">Gestion des Réservations</h1>
        </div>
        <div class="flex gap-4">
            <?php echo HtmlHelper::button('← Retour', BASE_URL . 'admin/equipements', 'secondary'); ?>
        </div>
    </div>

    <div class="grid md:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Total</span>
                <?php echo HtmlHelper::icon('clipboard', 'w-5 h-5 text-gray-400'); ?>
            </div>
            <div class="text-3xl font-bold text-gray-900"><?php echo $stats['total']; ?></div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">En attente</span>
                <?php echo HtmlHelper::icon('clock', 'w-5 h-5 text-gray-400'); ?>
            </div>
            <div class="text-3xl font-bold text-orange-600"><?php echo $stats['en_attente']; ?></div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Demandes annulation</span>
                <?php echo HtmlHelper::icon('warning', 'w-5 h-5 text-gray-400'); ?>
            </div>
            <div class="text-3xl font-bold text-red-600"><?php echo $stats['demande_annulation']; ?></div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Confirmées</span>
                <?php echo HtmlHelper::icon('check', 'w-5 h-5 text-gray-400'); ?>
            </div>
            <div class="text-3xl font-bold text-green-600"><?php echo $stats['confirmee']; ?></div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Conflits</span>
                <?php echo HtmlHelper::icon('error', 'w-5 h-5 text-gray-400'); ?>
            </div>
            <div class="text-3xl font-bold text-purple-600"><?php echo $stats['conflits']; ?></div>
        </div>
    </div>

    <?php if (!empty($conflits)): ?>
    <?php 
    Section::create('Conflits de Capacité', function() use ($conflits) {
        ?>
    <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg mb-6">
        <p class="text-sm text-yellow-800">
            <strong>Note:</strong> Les conflits sont automatiquement résolus lorsque vous acceptez une demande.
            Les demandes incompatibles seront automatiquement rejetées.
        </p>
    </div>

    <div class="space-y-6">
        <?php foreach ($conflits as $conflit): ?>
        <div class="bg-purple-50 border-l-4 border-purple-500 p-6 rounded">
            <div class="mb-4">
                <h3 class="font-bold text-purple-900 text-lg">
                    <?php echo $this->escape($conflit['equipement_nom']); ?>
                    <span class="text-sm font-normal text-purple-700">
                        (<?php echo $this->escape($conflit['type_equipement']); ?>)
                    </span>
                </h3>

                <?php if ($conflit['type_equipement'] !== 'salles'): ?>
                <p class="text-sm text-purple-700 mt-1">
                    <strong>Capacité:</strong> <?php echo $conflit['capacite']; ?> unités |
                    <strong>Total demandé:</strong> <?php echo $conflit['total_instances']; ?> unités |
                    <strong class="text-red-700">Dépassement:</strong> <?php echo $conflit['depassement'] ?? 0; ?>
                    unités
                </p>
                <?php endif; ?>
            </div>

            <div class="space-y-3">
                <?php foreach ($conflit['reservations'] as $r): ?>
                <div
                    class="bg-white border border-purple-200 rounded-lg p-4 <?php echo $r['statut'] === 'confirmee' ? 'border-l-4 border-l-green-500' : 'border-l-4 border-l-orange-500'; ?>">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <?php echo HtmlHelper::badge($r['statut'], $r['statut'] === 'confirmee' ? 'success' : 'warning'); ?>
                                <span class="font-medium text-gray-900">
                                    <?php echo $this->escape($r['user_prenom'] . ' ' . $r['user_nom']); ?>
                                </span>
                            </div>

                            <div class="text-sm text-gray-700 space-y-1">
                                <p>
                                    <strong>Période:</strong>
                                    <?php echo DateHelper::format($r['date_debut'], 'd/m/Y H:i'); ?> →
                                    <?php echo DateHelper::format($r['date_fin'], 'd/m/Y H:i'); ?>
                                </p>

                                <?php if ($conflit['type_equipement'] !== 'salles'): ?>
                                <p>
                                    <strong>Instances demandées:</strong>
                                    <?php echo $r['nb_instances']; ?> unité(s)
                                </p>
                                <?php endif; ?>

                                <p>
                                    <strong>Demandé le:</strong>
                                    <?php echo DateHelper::format($r['date_reservation'], 'd/m/Y H:i'); ?>
                                </p>

                                <?php if (!empty($r['motif'])): ?>
                                <p><strong>Motif:</strong> <?php echo $this->escape($r['motif']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($r['statut'] === 'en_attente'): ?>
                        <div class="flex gap-2 ml-4">
                            <a href="<?php echo BASE_URL . 'admin/confirmerReservation/' . $r['id_reservation']; ?>"
                                class="flex items-center px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700"
                                onclick="return confirm('Confirmer cette réservation ? Les demandes incompatibles seront automatiquement rejetées.')">
                                <?php echo HtmlHelper::icon('check') ?> Confirmer
                            </a>
                            <a href="<?php echo BASE_URL . 'admin/rejeterReservation/' . $r['id_reservation']; ?>"
                                class="flex items-center px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700"
                                onclick="return confirm('Rejeter cette demande ?')">
                                <?php echo HtmlHelper::icon('close') ?> Rejeter
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-4 p-3 bg-purple-100 rounded text-sm text-purple-800">
                <strong>Recommandation:</strong>
                <?php if ($conflit['type_equipement'] === 'salles'): ?>
                Acceptez la demande la plus ancienne ou rejetez toutes sauf une.
                <?php else: ?>
                <?php
                    $nbConfirmees = count(array_filter($conflit['reservations'], fn($r) => $r['statut'] === 'confirmee'));
                    $nbEnAttente = count(array_filter($conflit['reservations'], fn($r) => $r['statut'] === 'en_attente'));
                    ?>
                Il y a <?php echo $nbConfirmees; ?> réservation(s) confirmée(s) et <?php echo $nbEnAttente; ?>
                demande(s) en attente.
                Acceptez les demandes dans l'ordre chronologique jusqu'à atteindre la capacité maximale.
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php
    }, 'bg-white');
    ?>
    <?php endif; ?>

    <?php 
    $enAttente = array_filter($reservations, fn($r) => $r['statut'] == 'en_attente');
    if (!empty($enAttente)):
    ?>
    <?php 
    Section::create('Demandes en Attente', function() use ($enAttente) {
        ?>
    <div class="space-y-4">
        <?php foreach ($enAttente as $r): ?>
        <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h3 class="font-bold text-gray-900">
                        <?php echo $this->escape($r['equipement_nom']); ?>
                    </h3>
                    <div class="mt-2 text-sm text-gray-700">
                        <p><strong>Demandeur :</strong>
                            <?php echo $this->escape($r['user_prenom'] . ' ' . $r['user_nom']); ?>
                            (<?php echo $this->escape($r['user_email']); ?>)</p>
                        <p><strong>Période :</strong>
                            <?php echo DateHelper::format($r['date_debut'], 'd/m/Y H:i'); ?> -
                            <?php echo DateHelper::format($r['date_fin'], 'd/m/Y H:i'); ?></p>
                        <p><strong>Demandé le :</strong>
                            <?php echo DateHelper::format($r['date_reservation'], 'd/m/Y H:i'); ?></p>
                        <?php if ($r['type_equipement'] !== 'salles' && $r['nb_instances'] > 1): ?>
                        <p><strong>Instances :</strong> <?php echo $r['nb_instances']; ?></p>
                        <?php endif; ?>
                        <?php if (!empty($r['motif'])): ?>
                        <p><strong>Motif :</strong> <?php echo $this->escape($r['motif']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex gap-2 ml-4">
                    <a href="<?php echo BASE_URL . 'admin/confirmerReservation/' . $r['id_reservation']; ?>"
                        class="flex items-center px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700"
                        onclick="return confirm('Confirmer cette réservation ?')">
                        <?php echo HtmlHelper::icon('check') ?> Confirmer
                    </a>
                    <a href="<?php echo BASE_URL . 'admin/rejeterReservation/' . $r['id_reservation']; ?>"
                        class="flex items-center px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700"
                        onclick="return confirm('Rejeter cette demande ?')">
                        <?php echo HtmlHelper::icon('close') ?> Rejeter
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php
    }, 'bg-white');
    ?>
    <?php endif; ?>

    <?php 
    $demandeAnnulation = array_filter($reservations, fn($r) => $r['statut'] == 'demande_annulation');
    if (!empty($demandeAnnulation)):
    ?>
    <?php 
    Section::create('Demandes d\'Annulation', function() use ($demandeAnnulation) {
        ?>
    <div class="space-y-4">
        <?php foreach ($demandeAnnulation as $r): ?>
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h3 class="font-bold text-gray-900">
                        <?php echo $this->escape($r['equipement_nom']); ?>
                    </h3>
                    <div class="mt-2 text-sm text-gray-700">
                        <p><strong>Utilisateur :</strong>
                            <?php echo $this->escape($r['user_prenom'] . ' ' . $r['user_nom']); ?></p>
                        <p><strong>Période :</strong>
                            <?php echo DateHelper::format($r['date_debut'], 'd/m/Y H:i'); ?> -
                            <?php echo DateHelper::format($r['date_fin'], 'd/m/Y H:i'); ?></p>
                        <?php if (!empty($r['motif'])): ?>
                        <p><strong>Motif initial :</strong> <?php echo $this->escape($r['motif']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex gap-2 ml-4">
                    <a href="<?php echo BASE_URL . 'admin/annulerReservation/' . $r['id_reservation']; ?>"
                        class="flex items-center px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700"
                        onclick="return confirm('Accepter l\'annulation ?')">
                        <?php echo HtmlHelper::icon('check') ?> Accepter annulation
                    </a>
                    <a href="<?php echo BASE_URL . 'admin/rejeterReservation/' . $r['id_reservation']; ?>"
                        class="flex items-center px-3 py-1 bg-gray-600 text-white text-sm rounded hover:bg-gray-700"
                        onclick="return confirm('Refuser l\'annulation ?')">
                        <?php echo HtmlHelper::icon('close') ?> Rejeter
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php
    }, 'bg-white');
    ?>
    <?php endif; ?>

    <?php 
    Section::create('Toutes les Réservations', function() use ($reservations) {
        $tableData = $this->generateTableData($reservations);
        
        Table::render([
            'id' => 'reservations-table',
            'headers' => [
                ['label' => 'Équipement'],
                ['label' => 'Utilisateur'],
                ['label' => 'Période'],
                ['label' => 'Statut'],
                ['label' => 'Actions', 'class' => 'w-48']
            ],
            'data' => $tableData,
            'searchable' => true,
            'sortable' => true,
            'filterable' => false,
            'empty_message' => 'Aucune réservation trouvée'
        ]);
        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof TableManager !== "undefined") {
                new TableManager("reservations-table", null, {
                    searchable: true,
                    sortable: true,
                    filterable: false
                });
            }
        });
        </script>';
    }, 'bg-white');
    ?>
</div>

<?php
        $this->renderFooter();
    }
    
    private function generateTableData($reservations)
    {
        if (empty($reservations)) return '';
        
        $html = '';
        foreach ($reservations as $r) {
            $html .= $this->generateRow($r);
        }
        return $html;
    }
    
    private function generateRow($r)
    {
        $statutColors = [
            'en_attente' => 'warning',
            'confirmee' => 'success',
            'annulee' => 'danger',
            'terminee' => 'info',
            'demande_annulation' => 'orange'
        ];
        
        ob_start();
        ?>
<tr class="border-b hover:bg-gray-50"
    data-search="<?php echo strtolower($r['equipement_nom'] . ' ' . $r['user_nom'] . ' ' . $r['user_prenom']); ?>">
    <td class="px-6 py-4" data-sort="<?php echo $this->escape($r['equipement_nom']); ?>">
        <div class="font-medium"><?php echo $this->escape($r['equipement_nom']); ?></div>
        <div class="text-sm text-gray-600"><?php echo $this->escape($r['localisation']); ?></div>
    </td>
    <td class="px-6 py-4" data-sort="<?php echo $this->escape($r['user_nom']); ?>">
        <div><?php echo $this->escape($r['user_prenom'] . ' ' . $r['user_nom']); ?></div>
        <div class="text-sm text-gray-600"><?php echo $this->escape($r['user_email']); ?></div>
    </td>
    <td class="px-6 py-4" data-sort="<?php echo $r['date_debut']; ?>">
        <div class="text-sm">
            <div><?php echo DateHelper::format($r['date_debut'], 'd/m/Y H:i'); ?></div>
            <div class="text-gray-600">→ <?php echo DateHelper::format($r['date_fin'], 'd/m/Y H:i'); ?></div>
        </div>
    </td>
    <td class="px-6 py-4" data-sort="<?php echo $r['statut']; ?>">
        <?php echo HtmlHelper::badge($r['statut'], $statutColors[$r['statut']] ?? 'primary'); ?>
    </td>
    <td class="px-6 py-4">
        <div class="flex gap-2">
            <?php if ($r['statut'] == 'en_attente'): ?>
            <a href="<?php echo BASE_URL . 'admin/confirmerReservation/' . $r['id_reservation']; ?>" title="Confirmer"
                class="text-green-600 hover:text-green-800" onclick="return confirm('Confirmer ?')">
                <?php echo HtmlHelper::icon('check', 'w-5 h-5'); ?>
            </a>
            <?php endif; ?>

            <?php if ($r['statut'] == 'confirmee' || $r['statut'] == 'en_attente'): ?>
            <a href="<?php echo BASE_URL . 'admin/annulerReservation/' . $r['id_reservation']; ?>" title="Annuler"
                class="text-red-600 hover:text-red-800" onclick="return confirm('Annuler ?')">
                <?php echo HtmlHelper::icon('close', 'w-5 h-5'); ?>
            </a>
            <?php endif; ?>

            <a href="<?php echo BASE_URL . 'admin/detailsReservation/' . $r['id_reservation']; ?>" title="Détails"
                class="text-blue-600 hover:text-blue-800">
                <?php echo HtmlHelper::icon('eye', 'w-5 h-5'); ?>
            </a>
        </div>
    </td>
</tr>
<?php
        return ob_get_clean();
    }
}