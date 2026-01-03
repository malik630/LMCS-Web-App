<?php

require_once __DIR__ . '/../helpers/DateHelper.php';
require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once 'components/Section.php';

class EventRegisterView extends View
{
    protected $pageTitle = 'Inscription à un événement - LMCS';
    
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
        $event = $this->get('event');
        
        ?>
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <?php echo HtmlHelper::button('← Retour', BASE_URL . 'event', 'secondary'); ?>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold mb-6">Inscription à l'événement</h1>

        <div class="bg-blue-50 border-l-4 border-blue-600 p-4 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-2"><?php echo $this->escape($event['titre']); ?></h2>

            <div class="space-y-2 text-sm text-gray-700">
                <div class="flex items-center gap-2">
                    <?php echo HtmlHelper::icon('calendar'); ?>
                    <span>
                        <?php echo DateHelper::format($event['date_debut'], 'd/m/Y à H:i'); ?>
                    </span>
                </div>

                <?php if (!empty($event['lieu'])): ?>
                <div class="flex items-center gap-2">
                    <?php echo HtmlHelper::icon('location'); ?>
                    <span><?php echo $this->escape($event['lieu']); ?></span>
                </div>
                <?php endif; ?>

                <?php if (!empty($event['type_libelle'])): ?>
                <div class="flex items-center gap-2">
                    <?php echo HtmlHelper::icon('calendar'); ?>
                    <span>Type : <?php echo ucfirst($event['type_libelle']); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <form action="<?php echo BASE_URL; ?>event/submitRegistration/<?php echo $event['id_evenement']; ?>"
            method="POST">

            <?php if (isset($_SESSION['user_id'])): ?>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <p class="text-green-800">
                    <strong>Connecté en tant que :</strong>
                    <?php 
                    $nomComplet = $_SESSION['nom_complet'] ?? 'Utilisateur';
                    $email = $_SESSION['email'] ?? '';
                    echo $this->escape($nomComplet);
                    if ($email) {
                        echo ' (' . $this->escape($email) . ')';
                    }
                    ?>
                </p>
            </div>

            <?php else: ?>
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Prénom *</label>
                <input type="text" name="prenom" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nom *</label>
                <input type="text" name="nom" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                <input type="email" name="email" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <?php endif; ?>

            <div class="flex gap-4">
                <button type="submit"
                    class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
                    Confirmer l'inscription
                </button>
                <a href="<?php echo BASE_URL; ?>event"
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