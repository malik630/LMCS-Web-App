<?php

class AdminEditProjetView extends View
{
    protected $pageTitle = 'Modifier Projet - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $projet = $this->get('projet');
        $users = $this->get('users', []);
        ?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-4xl font-bold text-white mb-8">Modifier le Projet</h1>

    <div class="bg-white rounded-lg shadow-lg p-8 max-w-3xl">
        <form action="<?php echo BASE_URL; ?>admin/updateProjet/<?php echo $projet['id_projet']; ?>" method="POST">

            <div class="mb-4">
                <label class="block font-medium mb-2">Titre *</label>
                <input type="text" name="titre" value="<?php echo $this->escape($projet['titre']); ?>" required
                    class="w-full px-4 py-2 border rounded-lg">
            </div>

            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block font-medium mb-2">Responsable *</label>
                    <select name="responsable_id" required class="w-full px-4 py-2 border rounded-lg">
                        <?php foreach ($users as $u): ?>
                        <option value="<?php echo $u['id_user']; ?>"
                            <?php echo ($u['id_user'] == $projet['responsable_id']) ? 'selected' : ''; ?>>
                            <?php echo $this->escape($u['prenom'] . ' ' . $u['nom']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block font-medium mb-2">Statut *</label>
                    <select name="statut" required class="w-full px-4 py-2 border rounded-lg">
                        <option value="soumis" <?php echo ($projet['statut'] == 'soumis') ? 'selected' : ''; ?>>Soumis
                        </option>
                        <option value="en_cours" <?php echo ($projet['statut'] == 'en_cours') ? 'selected' : ''; ?>>En
                            cours</option>
                        <option value="termine" <?php echo ($projet['statut'] == 'termine') ? 'selected' : ''; ?>>
                            Terminé</option>
                    </select>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block font-medium mb-2">Thématique</label>
                    <input type="text" name="thematique"
                        value="<?php echo $this->escape($projet['thematique'] ?? ''); ?>"
                        class="w-full px-4 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="block font-medium mb-2">Budget (da)</label>
                    <input type="number" name="budget" value="<?php echo $projet['budget'] ?? ''; ?>"
                        class="w-full px-4 py-2 border rounded-lg">
                </div>
            </div>

            <div class="mb-4">
                <label class="block font-medium mb-2">Description</label>
                <textarea name="description" rows="4"
                    class="w-full px-4 py-2 border rounded-lg"><?php echo $this->escape($projet['description'] ?? ''); ?></textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg">Enregistrer</button>
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