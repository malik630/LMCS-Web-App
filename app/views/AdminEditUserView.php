<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';

class AdminEditUserView extends View
{
    protected $pageTitle = 'Modifier un Utilisateur - Admin';
    
    public function render()
    {
        $this->renderHeader();
        echo '<div class="container mx-auto px-4 py-8">';
        $this->renderContent();
        echo '</div>';
        $this->renderFooter();
    }
    
    private function renderContent()
    {
        $user = $this->get('user');
        
        ?>
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <?php echo HtmlHelper::button('← Retour', BASE_URL . 'admin/users', 'secondary'); ?>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold mb-6">Modifier l'Utilisateur</h1>

        <form action="<?php echo BASE_URL; ?>admin/updateUser/<?php echo $user['id_user']; ?>" method="POST">

            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Prénom *</label>
                    <input type="text" name="prenom" value="<?php echo $this->escape($user['prenom']); ?>" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nom *</label>
                    <input type="text" name="nom" value="<?php echo $this->escape($user['nom']); ?>" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                <input type="email" name="email" value="<?php echo $this->escape($user['email']); ?>" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Grade *</label>
                    <input type="text" name="grade" value="<?php echo $this->escape($user['grade']); ?>" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Poste</label>
                    <input type="text" name="poste" value="<?php echo $this->escape($user['poste'] ?? ''); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Rôle *</label>
                    <select name="role" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>
                            Administrateur</option>
                        <option value="enseignant-chercheur"
                            <?php echo $user['role'] === 'enseignant-chercheur' ? 'selected' : ''; ?>>
                            Enseignant-Chercheur</option>
                        <option value="doctorant" <?php echo $user['role'] === 'doctorant' ? 'selected' : ''; ?>>
                            Doctorant</option>
                        <option value="etudiant" <?php echo $user['role'] === 'etudiant' ? 'selected' : ''; ?>>Étudiant
                        </option>
                        <option value="invite" <?php echo $user['role'] === 'invite' ? 'selected' : ''; ?>>Invité
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Statut *</label>
                    <select name="statut" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="actif" <?php echo $user['statut'] === 'actif' ? 'selected' : ''; ?>>Actif
                        </option>
                        <option value="suspendu" <?php echo $user['statut'] === 'suspendu' ? 'selected' : ''; ?>>
                            Suspendu</option>
                        <option value="inactif" <?php echo $user['statut'] === 'inactif' ? 'selected' : ''; ?>>Inactif
                        </option>
                    </select>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit"
                    class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
                    Enregistrer les Modifications
                </button>
                <a href="<?php echo BASE_URL; ?>admin/users"
                    class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg font-semibold transition text-center">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
<?php
    }
}
?>