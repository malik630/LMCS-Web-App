<?php

require_once __DIR__ . '/../helpers/ImageHelper.php';

class AdminProjetMembersView extends View
{
    protected $pageTitle = 'Gestion Membres - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $projet = $this->get('projet');
        $membres = $this->get('membres', []);
        $partenaires = $this->get('partenaires', []);
        $availableUsers = $this->get('availableUsers', []);
        $availablePartenaires = $this->get('availablePartenaires', []);
        ?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-4xl font-bold text-white mb-2">Gestion Membres & Partenaires</h1>
    <p class="text-blue-100 text-lg mb-8"><?php echo $this->escape($projet['titre']); ?></p>
    <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
        <h2 class="text-2xl font-bold mb-6">Membres (<?php echo count($membres); ?>)</h2>

        <?php if (!empty($membres)): ?>
        <div class="grid md:grid-cols-3 gap-4 mb-8">
            <?php foreach ($membres as $m): ?>
            <div class="border rounded-lg p-4">
                <div class="flex items-center gap-3 mb-3">
                    <?php ImageHelper::renderUserPhoto($m, 12); ?>
                    <div>
                        <div class="font-bold"><?php echo $this->escape($m['prenom'] . ' ' . $m['nom']); ?></div>
                        <div class="text-sm text-gray-600"><?php echo $this->escape($m['grade']); ?></div>
                    </div>
                </div>

                <?php if ($projet['responsable_id'] != $m['id_user']): ?>
                <a href="<?php echo BASE_URL . 'admin/removeProjetMember/' . $projet['id_projet'] . '/' . $m['id_user']; ?>"
                    onclick="return confirm('Retirer ?')" class="text-sm text-red-600 hover:underline">
                    Retirer
                </a>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($availableUsers)): ?>
        <div class="border-t pt-6">
            <h3 class="font-bold mb-4">Ajouter un membre</h3>
            <form action="<?php echo BASE_URL; ?>admin/addProjetMember" method="POST" class="flex gap-4">
                <input type="hidden" name="projet_id" value="<?php echo $projet['id_projet']; ?>">
                <select name="user_id" required class="flex-1 px-4 py-2 border rounded-lg">
                    <option value="">-- Sélectionner --</option>
                    <?php foreach ($availableUsers as $u): ?>
                    <option value="<?php echo $u['id_user']; ?>">
                        <?php echo $this->escape($u['prenom'] . ' ' . $u['nom']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg">Ajouter</button>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
        <h2 class="text-2xl font-bold mb-6">Partenaires (<?php echo count($partenaires); ?>)</h2>

        <?php if (!empty($partenaires)): ?>
        <div class="grid md:grid-cols-3 gap-4 mb-8">
            <?php foreach ($partenaires as $p): ?>
            <div class="border rounded-lg p-4">
                <div class="font-bold mb-2"><?php echo $this->escape($p['nom']); ?></div>
                <div class="text-sm text-gray-600 mb-3"><?php echo $this->escape($p['pays'] ?? ''); ?></div>
                <a href="<?php echo BASE_URL . 'admin/removeProjetPartenaire/' . $projet['id_projet'] . '/' . $p['id_partenaire']; ?>"
                    onclick="return confirm('Retirer ?')" class="text-sm text-red-600 hover:underline">
                    Retirer
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($availablePartenaires)): ?>
        <div class="border-t pt-6">
            <h3 class="font-bold mb-4">Ajouter un partenaire</h3>
            <form action="<?php echo BASE_URL; ?>admin/addProjetPartenaire" method="POST" class="flex gap-4">
                <input type="hidden" name="projet_id" value="<?php echo $projet['id_projet']; ?>">
                <select name="partenaire_id" required class="flex-1 px-4 py-2 border rounded-lg">
                    <option value="">-- Sélectionner --</option>
                    <?php foreach ($availablePartenaires as $p): ?>
                    <option value="<?php echo $p['id_partenaire']; ?>">
                        <?php echo $this->escape($p['nom'] . ' - ' . $p['pays']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg">Ajouter</button>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <a href="<?php echo BASE_URL; ?>admin/projets" class="px-6 py-3 bg-gray-300 rounded-lg inline-block">← Retour</a>
</div>

<?php
        $this->renderFooter();
    }
}
?>