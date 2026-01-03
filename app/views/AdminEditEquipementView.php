<?php

class AdminEditEquipementView extends View
{
    protected $pageTitle = 'Modifier Équipement - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $equipement = $this->get('equipement');
        $types = $this->get('types', []);
        ?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-4xl font-bold text-white mb-8">Modifier l'Équipement</h1>

    <div class="bg-white rounded-lg shadow-lg p-8 max-w-3xl">
        <form action="<?php echo BASE_URL; ?>admin/updateEquipement/<?php echo $equipement['id_equipement']; ?>"
            method="POST">

            <div class="mb-4">
                <label class="block font-medium mb-2">Nom *</label>
                <input type="text" name="nom" value="<?php echo $this->escape($equipement['nom']); ?>" required
                    class="w-full px-4 py-2 border rounded-lg">
            </div>

            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block font-medium mb-2">Type *</label>
                    <select name="type_equipement_id" required class="w-full px-4 py-2 border rounded-lg">
                        <?php foreach ($types as $t): ?>
                        <option value="<?php echo $t['id_type']; ?>"
                            <?php echo ($t['id_type'] == $equipement['type_equipement_id']) ? 'selected' : ''; ?>>
                            <?php echo $this->escape($t['libelle']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block font-medium mb-2">État *</label>
                    <select name="etat" required class="w-full px-4 py-2 border rounded-lg">
                        <option value="libre" <?php echo ($equipement['etat'] == 'libre') ? 'selected' : ''; ?>>Libre
                        </option>
                        <option value="reserve" <?php echo ($equipement['etat'] == 'reserve') ? 'selected' : ''; ?>>
                            Réservé</option>
                        <option value="maintenance"
                            <?php echo ($equipement['etat'] == 'maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                        <option value="hors_service"
                            <?php echo ($equipement['etat'] == 'hors_service') ? 'selected' : ''; ?>>Hors service
                        </option>
                    </select>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block font-medium mb-2">Localisation</label>
                    <input type="text" name="localisation"
                        value="<?php echo $this->escape($equipement['localisation'] ?? ''); ?>"
                        class="w-full px-4 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="block font-medium mb-2">Capacité</label>
                    <input type="number" name="capacite" value="<?php echo $equipement['capacite'] ?? ''; ?>"
                        class="w-full px-4 py-2 border rounded-lg">
                </div>
            </div>

            <div class="mb-4">
                <label class="block font-medium mb-2">Description</label>
                <textarea name="description" rows="4"
                    class="w-full px-4 py-2 border rounded-lg"><?php echo $this->escape($equipement['description'] ?? ''); ?></textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg">Enregistrer</button>
                <a href="<?php echo BASE_URL; ?>admin/equipements" class="px-6 py-3 bg-gray-300 rounded-lg">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php
        $this->renderFooter();
    }
}