<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once 'components/Section.php';

class AdminEquipementRapportsView extends View
{
    protected $pageTitle = 'Rapports Équipements - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $rapport = $this->get('rapport', []);
        $parUtilisateur = $this->get('parUtilisateur', []);
        $dateDebut = $this->get('dateDebut');
        $dateFin = $this->get('dateFin');
        ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex items-center justify-between">
        <h1 class="text-4xl font-bold text-white">Rapports d'Utilisation</h1>
        <div class="flex gap-4">
            <?php echo HtmlHelper::button('Export PDF', BASE_URL . 'admin/exportEquipementsPDF?date_debut=' . $dateDebut . '&date_fin=' . $dateFin, 'secondary'); ?>
            <?php echo HtmlHelper::button('← Retour', BASE_URL . 'admin/equipements', 'secondary'); ?>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <form method="GET" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium mb-2">Date début</label>
                <input type="date" name="date_debut" value="<?php echo $dateDebut; ?>"
                    class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium mb-2">Date fin</label>
                <input type="date" name="date_fin" value="<?php echo $dateFin; ?>"
                    class="w-full px-4 py-2 border rounded-lg">
            </div>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Filtrer
            </button>
        </form>
    </div>

    <?php 
    Section::create('Taux d\'Occupation par Équipement', function() use ($rapport) {
        ?>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Équipement</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Localisation</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold">Réservations</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold">Heures Total</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold">Utilisateurs</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold">Taux</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php 
                $heuresMax = max(array_column($rapport, 'heures_total')) ?: 1;
                foreach ($rapport as $r): 
                    $taux = $r['heures_total'] ? round(($r['heures_total'] / $heuresMax) * 100) : 0;
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium"><?php echo $this->escape($r['equipement']); ?></td>
                    <td class="px-6 py-4"><?php echo $this->escape($r['localisation']); ?></td>
                    <td class="px-6 py-4 text-center"><?php echo $r['nb_reservations']; ?></td>
                    <td class="px-6 py-4 text-center"><?php echo $r['heures_total']; ?>h</td>
                    <td class="px-6 py-4 text-center"><?php echo $r['nb_utilisateurs']; ?></td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo $taux; ?>%"></div>
                            </div>
                            <span class="text-sm font-semibold"><?php echo $taux; ?>%</span>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    }, 'bg-white');
    ?>

    <?php 
    Section::create('Demandes par Utilisateur', function() use ($parUtilisateur) {
        ?>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Utilisateur</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Email</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold">Demandes</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold">Heures Total</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold">Moyenne/Demande</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php foreach ($parUtilisateur as $u): 
                    $moyenne = $u['nb_demandes'] ? round($u['heures_total'] / $u['nb_demandes'], 1) : 0;
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium">
                        <?php echo $this->escape($u['prenom'] . ' ' . $u['nom']); ?>
                    </td>
                    <td class="px-6 py-4"><?php echo $this->escape($u['email']); ?></td>
                    <td class="px-6 py-4 text-center">
                        <span
                            class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold"><?php echo $u['nb_demandes']; ?></span>
                    </td>
                    <td class="px-6 py-4 text-center"><?php echo $u['heures_total']; ?>h</td>
                    <td class="px-6 py-4 text-center"><?php echo $moyenne; ?>h</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    }, 'bg-white');
    ?>

    <div class="grid md:grid-cols-3 gap-6 mt-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Total Réservations</h3>
            <div class="text-3xl font-bold text-blue-600">
                <?php echo array_sum(array_column($rapport, 'nb_reservations')); ?>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Total Heures</h3>
            <div class="text-3xl font-bold text-green-600">
                <?php echo array_sum(array_column($rapport, 'heures_total')); ?>h
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Utilisateurs Actifs</h3>
            <div class="text-3xl font-bold text-purple-600">
                <?php echo count($parUtilisateur); ?>
            </div>
        </div>
    </div>
</div>

<?php
        $this->renderFooter();
    }
}