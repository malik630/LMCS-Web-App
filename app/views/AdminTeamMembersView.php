<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/ImageHelper.php';
require_once 'components/Section.php';

class AdminTeamMembersView extends View
{
    protected $pageTitle = 'Gestion des Membres - Admin';
    
    public function render()
    {
        $team = $this->get('team');
        $this->pageTitle = 'Gestion des membres - ' . $team['nom'];
        
        $this->renderHeader();
        echo '<div class="container mx-auto px-4 py-8">';
        $this->renderPageHeader($team);
        $this->renderCurrentMembers();
        $this->renderAddMemberSection();
        echo '</div>';
        $this->renderFooter();
    }
    
    private function renderPageHeader($team)
    {
        ?>
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-4xl font-bold text-white mb-3">Gestion des Membres</h1>
        <p class="text-blue-100 text-lg"><?php echo $this->escape($team['nom']); ?></p>
    </div>
    <div class="flex gap-4">
        <?php echo HtmlHelper::button('← Retour aux équipes', BASE_URL . 'admin/equipes', 'secondary'); ?>
    </div>
</div>
<?php
    }
    
    private function renderCurrentMembers()
    {
        $members = $this->get('members', []);
        $team = $this->get('team');
        
        Section::create('Membres actuels (' . count($members) . ')', function() use ($members, $team) {
            if (empty($members)) {
                echo HtmlHelper::emptyState('Aucun membre dans cette équipe pour le moment.');
                return;
            }
            
            echo '<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">';
            foreach ($members as $member) {
                $this->renderMemberCard($member, $team);
            }
            echo '</div>';
        });
    }
    
    private function renderMemberCard($member, $team)
    {
        $isChef = ($team['chef_id'] == $member['id_user']);
        $cardClass = $isChef 
            ? 'bg-gradient-to-br from-yellow-50 to-orange-50 border-2 border-yellow-400' 
            : 'bg-white border border-gray-200';
        
        ?>
<div class="<?php echo $cardClass; ?> rounded-lg p-6 hover:shadow-lg transition">
    <div class="flex items-start gap-4 mb-4">
        <div class="flex-shrink-0">
            <?php ImageHelper::renderUserPhoto($member, 16); ?>
        </div>
        <div class="flex-grow">
            <?php if ($isChef): ?>
            <div class="mb-2">
                <?php echo HtmlHelper::badge('Chef d\'équipe', 'warning'); ?>
            </div>
            <?php endif; ?>

            <h3 class="font-bold text-gray-900 mb-1">
                <?php echo $this->escape($member['prenom'] . ' ' . $member['nom']); ?>
            </h3>

            <div class="text-sm text-gray-600 mb-1">
                <?php echo $this->escape($member['grade']); ?>
            </div>

            <?php if (!empty($member['role_dans_equipe'])): ?>
            <div class="text-sm text-blue-600 font-medium">
                Rôle: <?php echo $this->escape($member['role_dans_equipe']); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <form action="<?php echo BASE_URL; ?>admin/updateMemberRole" method="POST" class="mb-3">
        <input type="hidden" name="team_id" value="<?php echo $team['id_team']; ?>">
        <input type="hidden" name="user_id" value="<?php echo $member['id_user']; ?>">

        <label class="block text-sm font-medium text-gray-700 mb-2">Modifier le rôle</label>
        <div class="flex gap-2">
            <input type="text" name="role" value="<?php echo $this->escape($member['role_dans_equipe'] ?? ''); ?>"
                placeholder="Ex: Chercheur principal"
                class="flex-grow px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
            <button type="submit"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition text-sm">
                OK
            </button>
        </div>
    </form>

    <?php if (!$isChef): ?>
    <a href="<?php echo BASE_URL . 'admin/removeMember/' . $team['id_team'] . '/' . $member['id_user']; ?>"
        onclick="return confirm('Retirer ce membre de l\'équipe ?')"
        class="block w-full text-center px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-medium transition text-sm">
        Retirer de l'équipe
    </a>
    <?php endif; ?>
</div>
<?php
    }
    
    private function renderAddMemberSection()
    {
        $availableUsers = $this->get('availableUsers', []);
        $team = $this->get('team');
        
        Section::create('Ajouter un membre', function() use ($availableUsers, $team) {
            if (empty($availableUsers)) {
                echo '<p class="text-gray-600">Tous les utilisateurs sont déjà membres de cette équipe.</p>';
                return;
            }
            
            ?>
<form action="<?php echo BASE_URL; ?>admin/addMember" method="POST" class="max-w-2xl">
    <input type="hidden" name="team_id" value="<?php echo $team['id_team']; ?>">

    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Sélectionner un utilisateur *
        </label>
        <select name="user_id" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">-- Choisir un utilisateur --</option>
            <?php foreach ($availableUsers as $user): ?>
            <option value="<?php echo $user['id_user']; ?>">
                <?php echo $this->escape($user['prenom'] . ' ' . $user['nom'] . ' - ' . $user['grade']); ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Rôle dans l'équipe (optionnel)
        </label>
        <input type="text" name="role" placeholder="Ex: Doctorant, Chercheur principal, Post-doctorant..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        <p class="text-xs text-gray-500 mt-1">Laissez vide si aucun rôle spécifique</p>
    </div>

    <div class="flex gap-4">
        <button type="submit"
            class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition">
            Ajouter le membre
        </button>
        <a href="<?php echo BASE_URL; ?>admin/equipes"
            class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg font-semibold transition">
            Annuler
        </a>
    </div>
</form>
<?php
        });
    }
}
?>