<?php

class LoginView extends View
{
    protected $pageTitle = 'Connexion - LMCS';
    
    public function render()
    {
        $this->renderHeader();
        $this->renderLoginForm();
        $this->renderFooter();
    }
    
    private function renderLoginForm()
    {
        ?>
<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-purple-800 p-6 text-center">
                <h2 class="text-3xl font-bold text-white mb-2">Connexion</h2>
                <p class="text-blue-100">Accédez à votre espace personnel</p>
            </div>
            <form action="<?php echo BASE_URL; ?>auth/authenticate" method="POST" class="p-8">
                <div class="mb-6">
                    <label for="username" class="block text-gray-700 font-semibold mb-2">
                        Nom d'utilisateur
                    </label>
                    <input type="text" id="username" name="username" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Entrez votre nom d'utilisateur">
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 font-semibold mb-2">
                        Mot de passe
                    </label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Entrez votre mot de passe">
                </div>
                <button type="submit"
                    class="w-full bg-purple-600 text-white py-3 rounded-lg font-semibold hover:bg-purple-700 transition transform hover:scale-[1.02] active:scale-[0.98]">
                    Se connecter
                </button>
            </form>
            <div class="px-8 pb-8 text-center">
                <p class="text-gray-600 text-sm">
                    Vous n'avez pas de compte ? Contactez l'administrateur.
                </p>
            </div>
        </div>
        <div class="mt-6 text-center">
            <a href="<?php echo BASE_URL; ?>" class="text-white hover:text-blue-200 transition">
                ← Retour à l'accueil
            </a>
        </div>
    </div>
</div>
<?php
$hash = password_hash("user", PASSWORD_DEFAULT);
echo $hash;
    }
}
?>