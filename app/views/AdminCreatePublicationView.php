<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once 'components/Section.php';

class AdminCreatePublicationView extends View
{
    protected $pageTitle = 'Nouvelle Publication - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $types = $this->get('types', []);
        $users = $this->get('users', []);
        $projets = $this->get('projets', []);
        $inputClass = 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500';
        ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between">
        <div>
            <h1 class="text-4xl font-bold text-white mb-3">Nouvelle Publication</h1>
            <p class="text-blue-100">Créer une nouvelle publication</p>
        </div>
        <?php echo HtmlHelper::button('← Retour', BASE_URL . 'adminPublication/publications', 'secondary'); ?>
    </div>

    <?php 
    $self = $this;
    Section::create('Formulaire', function() use ($types, $users, $projets, $inputClass, $self) { ?>
    <form action="<?= BASE_URL ?>adminPublication/store" method="POST" class="space-y-6">
        <div class="grid md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label>Titre <span class="text-red-500">*</span></label>
                <input type="text" name="titre" required class="<?= $inputClass ?>">
            </div>

            <div>
                <label>Type</label>
                <select name="type_publication_id" class="<?= $inputClass ?>">
                    <option value="">-- Type --</option>
                    <?php foreach ($types as $t): ?>
                    <option value="<?= $t['id_type'] ?>"><?= htmlspecialchars($t['libelle']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label>Année <span class="text-red-500">*</span></label>
                <input type="number" name="annee" required value="<?= date('Y') ?>" class="<?= $inputClass ?>">
            </div>

            <div>
                <label>DOI</label>
                <input type="text" name="doi" class="<?= $inputClass ?>" placeholder="10.1234/exemple">
            </div>

            <div>
                <label>Domaine</label>
                <input type="text" name="domaine" class="<?= $inputClass ?>">
            </div>

            <div>
                <label>Projet</label>
                <select name="projet_id" class="<?= $inputClass ?>">
                    <option value="">-- Aucun --</option>
                    <?php foreach ($projets as $p): ?>
                    <option value="<?= $p['id_projet'] ?>"><?= htmlspecialchars($p['titre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label>Date publication</label>
                <input type="date" name="date_publication" value="<?= date('Y-m-d') ?>" class="<?= $inputClass ?>">
            </div>
        </div>

        <div>
            <label>Résumé</label>
            <textarea name="resume" rows="4" class="<?= $inputClass ?>"></textarea>
        </div>

        <div>
            <label>Lien téléchargement</label>
            <input type="url" name="lien_telechargement" class="<?= $inputClass ?>">
        </div>

        <div>
            <label>Auteurs <span class="text-red-500">*</span></label>
            <div id="auteurs" class="space-y-2">
                <?php $self->renderAuteurRow($users); ?>
            </div>
            <button type="button" onclick="addAuteur()"
                class="mt-2 px-4 py-2 bg-blue-50 text-blue-600 rounded hover:bg-blue-100">
                + Ajouter
            </button>
        </div>

        <div>
            <label>Statut</label>
            <div class="flex gap-4">
                <?php foreach(['publie' => 'Publié', 'en_attente' => 'En attente', 'rejete' => 'Rejeté'] as $val =>
                $lbl): ?>
                <label class="flex items-center gap-2">
                    <input type="radio" name="statut" value="<?= $val ?>" <?= $val==='publie' ? 'checked' : '' ?>>
                    <?= $lbl ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="flex gap-4 pt-6 border-t">
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700">
                Créer
            </button>
            <a href="<?= BASE_URL ?>adminPublication/publications"
                class="px-6 py-3 bg-gray-200 rounded hover:bg-gray-300">
                Annuler
            </a>
        </div>
    </form>

    <script>
    const opts =
        `<?php foreach ($users as $u): ?><option value="<?= $u['id_user'] ?>"><?= htmlspecialchars($u['prenom'].' '.$u['nom']) ?></option><?php endforeach; ?>`;

    function addAuteur() {
        document.getElementById('auteurs').insertAdjacentHTML('beforeend',
            `<div class="flex gap-2"><select name="auteurs[]" required class="flex-1 px-4 py-2 border rounded"><option value="">-- Auteur --</option>${opts}</select><button type="button" onclick="this.parentElement.remove()" class="px-4 py-2 bg-red-100 text-red-600 rounded">×</button></div>`
        );
    }
    </script>
    <?php }, 'bg-white'); ?>
</div>
<?php
        $this->renderFooter();
    }

    private function renderAuteurRow($users)
    {
        echo '<div class="flex gap-2">';
        echo '<select name="auteurs[]" required class="flex-1 px-4 py-2 border rounded">';
        echo '<option value="">-- Auteur --</option>';
        foreach ($users as $u)
            echo "<option value='{$u['id_user']}'>" . htmlspecialchars($u['prenom'].' '.$u['nom']) . "</option>";
        echo '</select>';
        echo '<button type="button" onclick="this.parentElement.remove()" class="px-4 py-2 bg-red-100 text-red-600 rounded">×</button>';
        echo '</div>';
    }
}
?>