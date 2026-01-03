<?php

class AdminCreateProjetView extends View
{
    protected $pageTitle = 'Créer un Projet - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $users = $this->get('users', []);
        ?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-4xl font-bold text-white mb-8">Créer un Projet</h1>

    <div class="bg-white rounded-lg shadow-lg p-8 max-w-3xl">
        <form action="<?php echo BASE_URL; ?>admin/storeProjet" method="POST">

            <div class="mb-4">
                <label class="block font-medium mb-2">Titre *</label>
                <input type="text" name="titre" required class="w-full px-4 py-2 border rounded-lg">
            </div>

            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block font-medium mb-2">Responsable *</label>
                    <select name="responsable_id" required class="w-full px-4 py-2 border rounded-lg">
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($users as $u): ?>
                        <option value="<?php echo $u['id_user']; ?>">
                            <?php echo $this->escape($u['prenom'] . ' ' . $u['nom']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block font-medium mb-2">Statut *</label>
                    <select name="statut" required class="w-full px-4 py-2 border rounded-lg">
                        <option value="soumis">Soumis</option>
                        <option value="en_cours" selected>En cours</option>
                        <option value="termine">Terminé</option>
                    </select>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block font-medium mb-2">Thématique</label>
                    <input type="text" name="thematique" class="w-full px-4 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="block font-medium mb-2">Budget (da)</label>
                    <input type="number" name="budget" class="w-full px-4 py-2 border rounded-lg">
                </div>
            </div>

            <div class="mb-4">
                <label class="block font-medium mb-2">Description</label>
                <textarea name="description" rows="4" class="w-full px-4 py-2 border rounded-lg"></textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg">Créer</button>
                <a href="<?php echo BASE_URL; ?>admin/projets" class="px-6 py-3 bg-gray-300 rounded-lg">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php
        $this->renderFooter();
    }
}
?>