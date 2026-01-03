<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once 'components/Section.php';

class AdminCreateTeamView extends View
{
    protected $pageTitle = 'Créer une Équipe - Admin';
    
    public function render()
    {
        $this->renderHeader();
        echo '<div class="container mx-auto px-4 py-8">';
        $this->renderPageHeader();
        $this->renderForm();
        echo '</div>';
        $this->renderFooter();
    }
    
    private function renderPageHeader()
    {
        ?>
<div class="mb-8">
    <h1 class="text-4xl font-bold text-white mb-3">Créer une Nouvelle Équipe</h1>
    <p class="text-blue-100 text-lg">Ajouter une équipe de recherche au laboratoire</p>
</div>
<?php
    }
    
    private function renderForm()
    {
        $users = $this->get('users', []);
        
        Section::create('Informations de l\'équipe', function() use ($users) {
            ?>
<form action="<?php echo BASE_URL; ?>admin/storeTeam" method="POST" class="max-w-3xl">

    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Nom de l'équipe *
        </label>
        <input type="text" name="nom" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            placeholder="Ex: Équipe Intelligence Artificielle">
    </div>

    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Thématique de recherche
        </label>
        <input type="text" name="thematique"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            placeholder="Ex: Machine Learning et Deep Learning">
    </div>

    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Description
        </label>
        <textarea name="description" rows="6"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            placeholder="Description détaillée de l'équipe, ses objectifs, ses axes de recherche..."></textarea>
    </div>

    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Chef d'équipe *
        </label>
        <select name="chef_id" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">-- Sélectionner un chef d'équipe --</option>
            <?php foreach ($users as $user): ?>
            <option value="<?php echo $user['id_user']; ?>">
                <?php echo $this->escape($user['prenom'] . ' ' . $user['nom'] . ' - ' . $user['grade']); ?>
            </option>
            <?php endforeach; ?>
        </select>
        <p class="text-xs text-gray-500 mt-1">Le chef d'équipe sera automatiquement ajouté comme membre</p>
    </div>

    <div class="flex gap-4 pt-4">
        <button type="submit"
            class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition">
            Créer l'équipe
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