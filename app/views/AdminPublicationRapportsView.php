<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once 'components/Section.php';

class AdminPublicationRapportsView extends View
{
    protected $pageTitle = 'Rapports Bibliographiques - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $years = $this->get('years', []);
        $authors = $this->get('authors', []);
        ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-white mb-3">Rapports Bibliographiques</h1>
            <p class="text-blue-100">Générer des rapports par année ou par auteur</p>
        </div>
        <div>
            <?php echo HtmlHelper::button('← Retour', BASE_URL . 'adminPublication/publications', 'secondary'); ?>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-8">
        <?php 
        Section::create('Rapport par Année', function() use ($years) {
            ?>
        <form action="<?php echo BASE_URL; ?>adminPublication/generateRapport" method="POST" class="space-y-6">
            <input type="hidden" name="type" value="year">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Sélectionner une année
                </label>
                <select name="year" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Choisir une année --</option>
                    <?php foreach ($years as $y): ?>
                    <option value="<?php echo $y['annee']; ?>"><?php echo $y['annee']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Format de sortie
                </label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="format" value="pdf" checked class="w-4 h-4 text-blue-600">
                        <span class="flex items-center gap-2">
                            <?php echo HtmlHelper::icon('document', 'w-5 h-5 text-red-600'); ?>
                            PDF
                        </span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="format" value="excel" class="w-4 h-4 text-blue-600">
                        <span class="flex items-center gap-2">
                            <?php echo HtmlHelper::icon('document', 'w-5 h-5 text-green-600'); ?>
                            Excel/CSV
                        </span>
                    </label>
                </div>
            </div>

            <button type="submit"
                class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition flex items-center justify-center gap-2">
                <?php echo HtmlHelper::icon('download', 'w-5 h-5'); ?>
                Générer le rapport
            </button>
        </form>

        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-800">
                <strong>Le rapport inclura :</strong>
            </p>
            <ul class="text-sm text-blue-700 mt-2 space-y-1 ml-5 list-disc">
                <li>Toutes les publications de l'année sélectionnée</li>
                <li>Statistiques par type et domaine</li>
                <li>Liste complète des auteurs</li>
                <li>Informations bibliographiques détaillées</li>
            </ul>
        </div>
        <?php
        }, 'bg-white');
        ?>

        <?php 
        Section::create('Rapport par Auteur', function() use ($authors) {
            ?>
        <form action="<?php echo BASE_URL; ?>adminPublication/generateRapport" method="POST" class="space-y-6">
            <input type="hidden" name="type" value="author">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Sélectionner un auteur
                </label>
                <select name="author_id" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Choisir un auteur --</option>
                    <?php foreach ($authors as $author): ?>
                    <option value="<?php echo $author['id_user']; ?>">
                        <?php echo $this->escape($author['prenom'] . ' ' . $author['nom']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Format de sortie
                </label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="format" value="pdf" checked class="w-4 h-4 text-blue-600">
                        <span class="flex items-center gap-2">
                            <?php echo HtmlHelper::icon('document', 'w-5 h-5 text-red-600'); ?>
                            PDF
                        </span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="format" value="excel" class="w-4 h-4 text-blue-600">
                        <span class="flex items-center gap-2">
                            <?php echo HtmlHelper::icon('document', 'w-5 h-5 text-green-600'); ?>
                            Excel/CSV
                        </span>
                    </label>
                </div>
            </div>

            <button type="submit"
                class="w-full px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition flex items-center justify-center gap-2">
                <?php echo HtmlHelper::icon('download', 'w-5 h-5'); ?>
                Générer le rapport
            </button>
        </form>

        <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-sm text-green-800">
                <strong>Le rapport inclura :</strong>
            </p>
            <ul class="text-sm text-green-700 mt-2 space-y-1 ml-5 list-disc">
                <li>Toutes les publications de l'auteur</li>
                <li>Publications en tant que premier auteur</li>
                <li>Statistiques de publication par année</li>
                <li>Indicateurs de productivité</li>
            </ul>
        </div>
        <?php
        }, 'bg-white');
        ?>
    </div>

    <div class="mt-8">
        <?php 
        Section::create('Informations', function() {
            ?>
        <div class="grid md:grid-cols-3 gap-6">
            <div class="p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg border border-blue-200">
                <div class="flex items-center gap-3 mb-3">
                    <?php echo HtmlHelper::icon('chart', 'w-8 h-8 text-blue-600'); ?>
                    <h3 class="font-bold text-gray-900">Rapport Annuel</h3>
                </div>
                <p class="text-sm text-gray-700">
                    Vue d'ensemble complète de toutes les publications d'une année avec statistiques détaillées et
                    classement par type.
                </p>
            </div>

            <div class="p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-green-200">
                <div class="flex items-center gap-3 mb-3">
                    <?php echo HtmlHelper::icon('user', 'w-8 h-8 text-green-600'); ?>
                    <h3 class="font-bold text-gray-900">Rapport Auteur</h3>
                </div>
                <p class="text-sm text-gray-700">
                    Bilan complet des publications d'un chercheur avec évolution temporelle et indicateurs de
                    performance.
                </p>
            </div>

            <div class="p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg border border-purple-200">
                <div class="flex items-center gap-3 mb-3">
                    <?php echo HtmlHelper::icon('document', 'w-8 h-8 text-purple-600'); ?>
                    <h3 class="font-bold text-gray-900">Formats Multiples</h3>
                </div>
                <p class="text-sm text-gray-700">
                    Exportez vos rapports en PDF pour présentation ou en CSV/Excel pour traitement et analyse des
                    données.
                </p>
            </div>
        </div>
        <?php
        }, 'bg-white');
        ?>
    </div>
</div>

<?php
        $this->renderFooter();
    }
}
?>