<?php

require_once __DIR__ . '/../helpers/DateHelper.php';
require_once __DIR__ . '/../helpers/ImageHelper.php';
require_once __DIR__ . '/../helpers/HtmlHelper.php';

class ProfileView extends View
{
    protected $pageTitle = 'Mon Profil - LMCS';
    
    public function render()
    {
        $this->renderHeader();
        $this->renderProfile();
        $this->renderFooter();
    }
    
    private function renderProfile()
    {
        ?>
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-white mb-2">Mon Profil</h1>
        <?php echo HtmlHelper::linkWithIcon('Retour au tableau de bord', BASE_URL . 'dashboard', 'arrow-left', 'text-white hover:text-blue-200 transition'); ?>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        <?php $this->renderSidebar(); ?>
        <?php $this->renderMainContent(); ?>
    </div>
</div>
<?php
    }
    
    private function renderSidebar()
    {
        $user = $this->get('user');
        ?>
<div class="lg:col-span-1">
    <div class="bg-white rounded-lg shadow-lg p-6 sticky top-24">
        <div class="text-center mb-6">
            <?php ImageHelper::renderUserPhotoLarge($user); ?>
            <h2 class="text-xl font-bold text-gray-900 mt-4">
                <?php echo $this->escape($user['prenom'] . ' ' . $user['nom']); ?>
            </h2>
            <p class="text-gray-600"><?php echo $this->escape($user['grade']); ?></p>
            <?php if (!empty($user['poste'])): ?>
            <p class="text-gray-500 text-sm"><?php echo $this->escape($user['poste']); ?></p>
            <?php endif; ?>
        </div>

        <div class="border-t pt-4">
            <?php 
            echo HtmlHelper::infoList([
                ['label' => 'Username', 'value' => $user['username']],
                ['label' => 'Email', 'value' => $user['email']],
                ['label' => 'Rôle', 'value' => ucfirst($user['role'])],
                ['label' => 'Membre depuis', 'value' => DateHelper::format($user['date_creation'], 'd/m/Y')]
            ]);
            ?>
        </div>
    </div>
</div>
<?php
    }
    
    private function renderMainContent()
    {
        ?>
<div class="lg:col-span-2 space-y-6">
    <?php 
    $this->renderPersonalInfoForm();
    $this->renderPasswordForm();
    $this->renderDocumentsSection();
    ?>
</div>
<?php
    }
    
    private function renderPersonalInfoForm()
    {
        $user = $this->get('user');
        ?>
<div class="bg-white rounded-lg shadow-lg p-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-900">Informations personnelles</h2>
    <form action="<?php echo BASE_URL; ?>dashboard/updateProfile" method="POST" enctype="multipart/form-data">
        <div class="grid md:grid-cols-2 gap-6">
            <?php $this->renderFormField('text', 'nom', 'Nom', $user['nom'], true); ?>
            <?php $this->renderFormField('text', 'prenom', 'Prénom', $user['prenom'], true); ?>
            <?php $this->renderFormField('email', 'email', 'Email', $user['email'], true); ?>
            <?php $this->renderFormField('text', 'grade', 'Grade', $user['grade'], false, true); ?>

            <div class="md:col-span-2">
                <?php $this->renderFormField('text', 'poste', 'Poste', $user['poste'], false, true); ?>
            </div>

            <div class="md:col-span-2">
                <?php $this->renderFormField('text', 'domaine_recherche', 'Domaine de recherche', $user['domaine_recherche']); ?>
            </div>

            <div class="md:col-span-2">
                <label class="block text-gray-700 font-semibold mb-2">Biographie</label>
                <textarea name="biographie" rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo $this->escape($user['biographie']); ?></textarea>
            </div>

            <div class="md:col-span-2">
                <label class="block text-gray-700 font-semibold mb-2">Photo de profil</label>
                <input type="file" name="photo" accept="image/*"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-500 mt-1">Formats acceptés: JPG, PNG, GIF (max 2 MB)</p>
            </div>
        </div>

        <div class="mt-6">
            <?php echo HtmlHelper::button('Enregistrer les modifications', null, 'primary', '', ['type' => 'submit']); ?>
        </div>
    </form>
</div>
<?php
    }
    
    private function renderPasswordForm()
    {
        ?>
<div class="bg-white rounded-lg shadow-lg p-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-900">Changer le mot de passe</h2>
    <form action="<?php echo BASE_URL; ?>dashboard/changePassword" method="POST">
        <div class="space-y-4">
            <?php 
            $this->renderFormField('password', 'old_password', 'Mot de passe actuel', '', true);
            $this->renderFormField('password', 'new_password', 'Nouveau mot de passe', '', true, false, 6, 'Minimum 6 caractères');
            $this->renderFormField('password', 'confirm_password', 'Confirmer le nouveau mot de passe', '', true, false, 6);
            ?>
        </div>

        <div class="mt-6">
            <?php echo HtmlHelper::button('Changer le mot de passe', null, 'primary', '', ['type' => 'submit']); ?>
        </div>
    </form>
</div>
<?php
    }
    
    private function renderDocumentsSection()
    {
        $documents = $this->get('documents', []);
        ?>
<div class="bg-white rounded-lg shadow-lg p-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-900">Documents personnels</h2>
    <?php $this->renderDocumentUploadForm(); ?>
    <?php $this->renderDocumentsList($documents); ?>
</div>
<?php
    }
    
    private function renderDocumentUploadForm()
    {
        ?>
<form action="<?php echo BASE_URL; ?>dashboard/uploadDocument" method="POST" enctype="multipart/form-data"
    class="mb-6 p-4 bg-gray-50 rounded-lg">
    <h3 class="font-bold text-gray-900 mb-4">Ajouter un document</h3>
    <div class="grid md:grid-cols-2 gap-4">
        <?php $this->renderFormField('text', 'titre', 'Titre', '', true); ?>

        <div>
            <label class="block text-gray-700 font-semibold mb-2">Type</label>
            <select name="type"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Non spécifié</option>
                <option value="CV">CV</option>
                <option value="Diplôme">Diplôme</option>
                <option value="Certificat">Certificat</option>
                <option value="Publication">Publication</option>
                <option value="Rapport">Rapport</option>
                <option value="Autre">Autre</option>
            </select>
        </div>

        <div class="md:col-span-2">
            <label class="block text-gray-700 font-semibold mb-2">Fichier</label>
            <input type="file" name="document" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <p class="text-sm text-gray-500 mt-1">Formats acceptés: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP (max 10
                MB)</p>
        </div>
    </div>

    <div class="mt-4">
        <?php echo HtmlHelper::button('Ajouter le document', null, 'primary', null, ['type' => 'submit']); ?>
    </div>
</form>
<?php
    }
    
    private function renderDocumentsList($documents)
    {
        if (empty($documents)) {
            echo HtmlHelper::emptyState('Aucun document');
            return;
        }
        ?>
<div class="space-y-3">
    <h3 class="font-bold text-gray-900 mb-3">Mes documents (<?php echo count($documents); ?>)</h3>
    <?php foreach ($documents as $doc): ?>
    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:shadow-md transition">
        <div class="flex-grow">
            <h4 class="font-bold text-gray-900"><?php echo $this->escape($doc['titre']); ?></h4>
            <div class="flex items-center gap-4 text-sm text-gray-600 mt-1">
                <?php if (!empty($doc['type'])): ?>
                <?php echo HtmlHelper::badge($doc['type'], 'primary'); ?>
                <?php endif; ?>
                <span><?php echo DateHelper::format($doc['date_upload'], 'd/m/Y'); ?></span>
                <span><?php echo round($doc['taille_fichier'] / 1024, 2); ?> KB</span>
            </div>
        </div>

        <div class="flex gap-2">
            <?php echo HtmlHelper::button('Télécharger', ASSETS_URL . 'documents/' . $doc['fichier'], 'primary', 'download', ['target' => '_blank']); ?>
            <?php echo HtmlHelper::button('Supprimer', BASE_URL . 'dashboard/deleteDocument/' . $doc['id_document'], 'danger', 'trash', ['onclick' => 'return confirm(\'Êtes-vous sûr de vouloir supprimer ce document ?\')']); ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php
    }
    
    private function renderFormField($type, $name, $label, $value = '', $required = false, $disabled = false, $minlength = null, $help = null)
    {
        $requiredAttr = $required ? 'required' : '';
        $disabledClass = $disabled ? 'text-gray-500 cursor-not-allowed' : '';
        $disabledAttr = $disabled ? 'disabled' : '';
        $minlengthAttr = $minlength ? "minlength=\"$minlength\"" : '';
        
        ?>
<div>
    <label class="block text-gray-700 font-semibold mb-2"><?php echo $label; ?></label>
    <input type="<?php echo $type; ?>" name="<?php echo $name; ?>" value="<?php echo $this->escape($value); ?>"
        <?php echo $requiredAttr; ?> <?php echo $disabledAttr; ?> <?php echo $minlengthAttr; ?>
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?php echo $disabledClass; ?>">
    <?php if ($help): ?>
    <p class="text-sm text-gray-500 mt-1"><?php echo $help; ?></p>
    <?php endif; ?>
</div>
<?php
    }
}
?>