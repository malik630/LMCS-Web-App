<?php

require_once __DIR__ . '/../helpers/DateHelper.php';
require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/ImageHelper.php';

class DashboardView extends View
{
    protected $pageTitle = 'Tableau de bord - LMCS';
    
    public function render()
    {
        $this->renderHeader();
        $this->renderDashboard();
        $this->renderFooter();
    }
    
    private function renderDashboard()
    {
        $user = $this->get('user');
        $projets = $this->get('projets', []);
        $publications = $this->get('publications', []);
        $reservations = $this->get('reservations', []);
        $equipes = $this->get('equipes', []);
        
        ?>
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <div class="flex items-center gap-6">
            <div class="flex-shrink-0">
                <?php $this->renderUserPhoto($user); ?>
            </div>
            <div class="flex-grow">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    Bienvenue, <?php echo $this->escape($user['prenom'] . ' ' . $user['nom']); ?>
                </h1>
                <p class="text-gray-600 mb-1">
                    <span class="font-semibold">Grade:</span> <?php echo $this->escape($user['grade']); ?>
                </p>
                <?php if (!empty($user['poste'])): ?>
                <p class="text-gray-600 mb-1">
                    <span class="font-semibold">Poste:</span> <?php echo $this->escape($user['poste']); ?>
                </p>
                <?php endif; ?>
                <p class="text-gray-600">
                    <span class="font-semibold">Rôle:</span> <?php echo $this->escape(ucfirst($user['role'])); ?>
                </p>
            </div>
            <div>
                <a href="<?php echo BASE_URL; ?>dashboard/profile"
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Modifier mon profil
                </a>
            </div>
        </div>
    </div>
    <div class="grid lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
                Mes Projets
            </h2>
            <?php if (!empty($projets)): ?>
            <div class="space-y-4 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                <?php foreach (array_slice($projets, 0, 5) as $projet): ?>
                <?php $this->renderProjetCard($projet); ?>
                <?php endforeach; ?>
            </div>
            <?php if (count($projets) > 5): ?>
            <div class="text-center mt-4">
                <a href="<?php echo BASE_URL; ?>project" class="text-blue-600 hover:text-blue-800 font-semibold">
                    Voir tous les projets (<?php echo count($projets); ?>)
                </a>
            </div>
            <?php endif; ?>
            <?php else: ?>
            <p class="text-gray-500 text-center py-8">Aucun projet en cours</p>
            <?php endif; ?>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
                Mes Publications
            </h2>
            <?php if (!empty($publications)): ?>
            <div class="space-y-4 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                <?php foreach (array_slice($publications, 0, 5) as $publication): ?>
                <?php $this->renderPublicationCard($publication); ?>
                <?php endforeach; ?>
            </div>
            <?php if (count($publications) > 5): ?>
            <div class="text-center mt-4">
                <a href="<?php echo BASE_URL; ?>publication" class="text-green-600 hover:text-green-800 font-semibold">
                    Voir toutes les publications (<?php echo count($publications); ?>)
                </a>
            </div>
            <?php endif; ?>
            <?php else: ?>
            <p class="text-gray-500 text-center py-8">Aucune publication</p>
            <?php endif; ?>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
                Mes Réservations
            </h2>
            <?php if (!empty($reservations)): ?>
            <div class="space-y-4 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                <?php foreach ($reservations as $reservation): ?>
                <?php $this->renderReservationCard($reservation); ?>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-gray-500 text-center py-8">Aucune réservation</p>
            <?php endif; ?>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
                Mes Équipes
            </h2>
            <?php if (!empty($equipes)): ?>
            <div class="space-y-4 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                <?php foreach ($equipes as $equipe): ?>
                <?php $this->renderEquipeCard($equipe); ?>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-gray-500 text-center py-8">Aucune équipe</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
    }
    
    private function renderUserPhoto($user)
    {
        if (!empty($user['photo'])) {
            $src = ASSETS_URL . 'images/users/' . $user['photo'];
            echo '<img src="' . $src . '" alt="Photo de profil" class="w-24 h-24 rounded-full object-cover border-4 border-blue-600" onerror="this.src=\'' . ImageHelper::placeholder(100, 100, '#667eea') . '\'">';
        } else {
            echo '<div class="w-24 h-24 rounded-full bg-blue-600 flex items-center justify-center text-white text-3xl font-bold border-4 border-blue-700">';
            echo strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1));
            echo '</div>';
        }
    }
    
    private function renderProjetCard($projet)
    {
        $statusColors = [
            'en_cours' => 'bg-blue-100 text-blue-800',
            'termine' => 'bg-green-100 text-green-800',
            'soumis' => 'bg-yellow-100 text-yellow-800'
        ];
        $statusLabels = [
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'soumis' => 'Soumis'
        ];
        
        ?>
<div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
    <div class="flex justify-between items-start mb-2">
        <h3 class="font-bold text-gray-900"><?php echo $this->escape($projet['titre']); ?></h3>
        <span class="px-2 py-1 rounded text-xs font-semibold <?php echo $statusColors[$projet['statut']]; ?>">
            <?php echo $statusLabels[$projet['statut']]; ?>
        </span>
    </div>
    <?php if (!empty($projet['thematique'])): ?>
    <p class="text-sm text-gray-600 mb-2">
        <span class="font-semibold">Thématique:</span> <?php echo $this->escape($projet['thematique']); ?>
    </p>
    <?php endif; ?>
    <?php if (!empty($projet['role_projet'])): ?>
    <p class="text-sm text-gray-600">
        <span class="font-semibold">Rôle:</span> <?php echo $this->escape($projet['role_projet']); ?>
    </p>
    <?php endif; ?>
</div>
<?php
    }
    
    private function renderPublicationCard($publication)
    {
        ?>
<div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
    <h3 class="font-bold text-gray-900 mb-2"><?php echo $this->escape($publication['titre']); ?></h3>
    <div class="flex items-center gap-4 text-sm text-gray-600">
        <span class="font-semibold"><?php echo $publication['annee']; ?></span>
        <?php if (!empty($publication['type_libelle'])): ?>
        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">
            <?php echo $this->escape($publication['type_libelle']); ?>
        </span>
        <?php endif; ?>
    </div>
</div>
<?php
    }
    
    private function renderReservationCard($reservation)
    {
        $statusColors = [
            'confirmee' => 'bg-green-100 text-green-800',
            'annulee' => 'bg-red-100 text-red-800',
            'terminee' => 'bg-gray-100 text-gray-800'
        ];
        
        ?>
<div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
    <h3 class="font-bold text-gray-900 mb-2"><?php echo $this->escape($reservation['equipement_nom']); ?></h3>
    <div class="space-y-1 text-sm text-gray-600">
        <p><span class="font-semibold">Du:</span>
            <?php echo DateHelper::format($reservation['date_debut'], 'd/m/Y H:i'); ?></p>
        <p><span class="font-semibold">Au:</span>
            <?php echo DateHelper::format($reservation['date_fin'], 'd/m/Y H:i'); ?></p>
        <span
            class="inline-block mt-2 px-2 py-1 rounded text-xs font-semibold <?php echo $statusColors[$reservation['statut']]; ?>">
            <?php echo ucfirst($reservation['statut']); ?>
        </span>
    </div>
</div>
<?php
    }
    
    private function renderEquipeCard($equipe)
    {
        ?>
<div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
    <h3 class="font-bold text-gray-900 mb-2"><?php echo $this->escape($equipe['nom']); ?></h3>
    <?php if (!empty($equipe['thematique'])): ?>
    <p class="text-sm text-gray-600 mb-2"><?php echo $this->escape($equipe['thematique']); ?></p>
    <?php endif; ?>
    <?php if (!empty($equipe['role_dans_equipe'])): ?>
    <span class="inline-block px-2 py-1 bg-orange-100 text-orange-800 rounded text-xs font-semibold">
        <?php echo $this->escape($equipe['role_dans_equipe']); ?>
    </span>
    <?php endif; ?>
</div>
<?php
    }
}
?>