<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once 'components/Section.php';

class AdminEditTeamView extends View
{
    protected $pageTitle = 'Modifier une Équipe - Admin';
    
    public function render()
    {
        $team = $this->get('team');
        $this->pageTitle = 'Modifier ' . $team['nom'] . ' - Admin';
        
        $this->renderHeader();
        echo '<div class="container mx-auto px-4 py-8">';
        $this->renderPageHeader($team);
        $this->renderForm($team);
        echo '</div>';
        $this->renderFooter();
    }
    
    private function renderPageHeader($team)
    {
        ?>
<div class="mb-8">
    <h1 class="text-4xl font-bold text-white mb-3">Modifier l'Équipe</h1>
    <p class="text-blue-100 text-lg"><?php echo $this->escape($team['nom']); ?></p>
</div>
<?php
    }
    
    private function renderForm($team)
    {
        $users = $this->get('users', []);
        
        Section::create('Informations de l\'équipe', function() use ($team, $users) {
            ?>
<form action="<?php echo BASE_URL; ?>admin/updateTeam/<?php echo $team['id_team']; ?>" method="POST" class="max-w-3xl">

    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Nom de l'équipe *
        </label>
        <input type="text" name="nom" required value="<?php echo $this->escape($team['nom']); ?>"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
    </div>

    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Thématique de recherche
        </label>
        <input type="text" name="thematique" value="<?php echo $this->escape($team['thematique'] ?? ''); ?>"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
    </div>

    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Description
        </label>
        <textarea name="description" rows="6"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"><?php echo $this->escape($team['description'] ?? ''); ?></textarea>
    </div>

    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Chef d'équipe *
        </label>
        <select name="chef_id" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <?php foreach ($users as $user): ?>
            <option value="<?php echo $user['id_user']; ?>"
                <?php echo ($user['id_user'] == $team['chef_id']) ? 'selected' : ''; ?>>
                <?php echo $this->escape($user['prenom'] . ' ' . $user['nom'] . ' - ' . $user['grade']); ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="flex gap-4 pt-4">
        <button type="submit"
            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
            Enregistrer les modifications
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