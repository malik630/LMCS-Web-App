<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';

class AdminCreateUserView extends View
{
    protected $pageTitle = 'Créer un Utilisateur - Admin';
    
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
        ?>
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <?php echo HtmlHelper::button('← Retour', BASE_URL . 'admin/users', 'secondary'); ?>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold mb-6">Créer un Nouvel Utilisateur</h1>

        <form action="<?php echo BASE_URL; ?>admin/storeUser" method="POST">

            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nom d'utilisateur *</label>
                    <input type="text" name="username" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Mot de passe *</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Prénom *</label>
                    <input type="text" name="prenom" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nom *</label>
                    <input type="text" name="nom" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                <input type="email" name="email" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Grade *</label>
                    <input type="text" name="grade" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Ex: Professeur, Maître de conférences...">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Poste</label>
                    <input type="text" name="poste"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Ex: Chef de département...">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Rôle *</label>
                <select name="role" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="enseignant-chercheur">Enseignant-Chercheur</option>
                    <option value="doctorant">Doctorant</option>
                    <option value="etudiant">Étudiant</option>
                    <option value="invite">Invité</option>
                    <option value="admin">Administrateur</option>
                </select>
                <p class="text-sm text-gray-500 mt-1">Les permissions par défaut du rôle seront automatiquement
                    attribuées</p>
            </div>

            <div class="flex gap-4">
                <button type="submit"
                    class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
                    Créer l'Utilisateur
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