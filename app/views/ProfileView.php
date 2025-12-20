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
        echo '<div class="container mx-auto px-4 py-8">';
        $this->renderPageHeader();
        $this->renderProfileContent();
        echo '</div>';
        $this->renderFooter();
    }
    
    private function renderPageHeader()
    {
        ?>
<div class="mb-6">
    <h1 class="text-3xl font-bold text-white mb-2">Mon Profil</h1>
    <?php echo HtmlHelper::linkWithIcon(
        'Retour au tableau de bord',
        BASE_URL . 'dashboard',
        'arrow-left',
        'text-white hover:text-blue-200 transition'
    ); ?>
</div>
<?php
    }
    
    private function renderProfileContent()
    {
        ?>
<div class="grid lg:grid-cols-3 gap-8">
    <?php 
    $this->renderSidebar();
    $this->renderMainContent();
    ?>
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
            <div class="flex justify-center mb-4">
                <?php ImageHelper::renderUserPhoto($user, 32); ?>
            </div>
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
    <h2 class="text-2xl font-bold mb-6 text-gray-900">Mes informations</h2>
    <form action="<?php echo BASE_URL; ?>dashboard/updateProfile" method="POST" enctype="multipart/form-data">
        <div class="grid md:grid-cols-2 gap-6">
            <?php $this->renderFormFields($user); ?>
        </div>
        <div class="mt-6">
            <?php echo HtmlHelper::button('Enregistrer les modifications', null, 'primary', '', ['type' => 'submit']); ?>
        </div>
    </form>
</div>
<?php
    }
    
    private function renderFormFields(array $user)
    {
        $fields = [
            ['name' => 'nom', 'label' => 'Nom', 'value' => $user['nom'], 'required' => true],
            ['name' => 'prenom', 'label' => 'Prénom', 'value' => $user['prenom'], 'required' => true],
            ['name' => 'email', 'label' => 'Email', 'value' => $user['email'], 'required' => true, 'type' => 'email'],
            ['name' => 'grade', 'label' => 'Grade', 'value' => $user['grade'], 'disabled' => true],
            ['name' => 'poste', 'label' => 'Poste', 'value' => $user['poste'], 'disabled' => true, 'span' => 2],
            ['name' => 'domaine_recherche', 'label' => 'Domaine de recherche', 'value' => $user['domaine_recherche'], 'span' => 2]
        ];
        
        foreach ($fields as $field) {
            $this->renderField($field);
        }
        
        $this->renderBioField($user);
        $this->renderPhotoField();
    }
    
    private function renderField(array $field)
    {
        $span = $field['span'] ?? 1;
        $spanClass = $span > 1 ? "md:col-span-$span" : '';
        
        ?>
<div class="<?php echo $spanClass; ?>">
    <?php $this->renderFormField(
        $field['type'] ?? 'text',
        $field['name'],
        $field['label'],
        $field['value'] ?? '',
        $field['required'] ?? false,
        $field['disabled'] ?? false
    ); ?>
</div>
<?php
    }
    
    private function renderBioField(array $user)
    {
        ?>
<div class="md:col-span-2">
    <label class="block text-gray-700 font-semibold mb-2">Biographie</label>
    <textarea name="biographie" rows="4"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo $this->escape($user['biographie']); ?></textarea>
</div>
<?php
    }
    
    private function renderPhotoField()
    {
        ?>
<div class="md:col-span-2">
    <label class="block text-gray-700 font-semibold mb-2">Photo de profil</label>
    <input type="file" name="photo" accept="image/*"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    <p class="text-sm text-gray-500 mt-1">Formats acceptés: JPG, PNG, GIF (max 2 MB)</p>
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
    <h2 class="text-2xl font-bold mb-6 text-gray-900">Mes documents</h2>
    <?php 
    $this->renderDocumentUploadForm();
    $this->renderDocumentsList($documents);
    ?>
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
        <?php $this->renderDocumentTypeSelect(); ?>
        <?php $this->renderDocumentFileInput(); ?>
    </div>
    <div class="mt-4">
        <?php echo HtmlHelper::button('Ajouter le document', null, 'primary', null, ['type' => 'submit']); ?>
    </div>
</form>
<?php
    }
    
    private function renderDocumentTypeSelect()
    {
        $types = ['CV', 'Diplôme', 'Certificat', 'Publication', 'Rapport', 'Autre'];
        
        ?>
<div>
    <label class="block text-gray-700 font-semibold mb-2">Type</label>
    <select name="type"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">Non spécifié</option>
        <?php foreach ($types as $type): ?>
        <option value="<?php echo $type; ?>"><?php echo $type; ?></option>
        <?php endforeach; ?>
    </select>
</div>
<?php
    }
    
    private function renderDocumentFileInput()
    {
        ?>
<div class="md:col-span-2">
    <label class="block text-gray-700 font-semibold mb-2">Fichier</label>
    <input type="file" name="document" required
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    <p class="text-sm text-gray-500 mt-1">Formats acceptés: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP (max 10 MB)</p>
</div>
<?php
    }
    
    private function renderDocumentsList(array $documents)
    {
        if (empty($documents)) {
            echo HtmlHelper::emptyState('Aucun document');
            return;
        }
        
        ?>
<div class="space-y-3">
    <h3 class="font-bold text-gray-900 mb-3">Mes documents (<?php echo count($documents); ?>)</h3>
    <?php foreach ($documents as $doc): ?>
    <?php $this->renderDocumentCard($doc); ?>
    <?php endforeach; ?>
</div>
<?php
    }
    
    private function renderDocumentCard(array $doc)
    {
        ?>
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
        <a href="<?php echo ASSETS_URL . 'documents/' . $doc['fichier']; ?>" target="_blank"
            class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <?php echo HtmlHelper::icon('download', 'w-4 h-4'); ?>
            <span>Télécharger</span>
        </a>
        <a href="<?php echo BASE_URL . 'dashboard/deleteDocument/' . $doc['id_document']; ?>"
            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce document ?')"
            class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
            <?php echo HtmlHelper::icon('trash', 'w-4 h-4'); ?>
            <span>Supprimer</span>
        </a>
    </div>
</div>
<?php
    }
    
    private function renderFormField($type, $name, $label, $value = '', $required = false, $disabled = false, $minlength = null, $help = null)
    {
        $attrs = [
            'required' => $required,
            'disabled' => $disabled,
            'minlength' => $minlength
        ];
        
        $disabledClass = $disabled ? 'text-gray-500 cursor-not-allowed' : '';
        
        ?>
<div>
    <label class="block text-gray-700 font-semibold mb-2"><?php echo $label; ?></label>
    <input type="<?php echo $type; ?>" name="<?php echo $name; ?>" value="<?php echo $this->escape($value); ?>"
        <?php echo $attrs['required'] ? 'required' : ''; ?> <?php echo $attrs['disabled'] ? 'disabled' : ''; ?>
        <?php echo $attrs['minlength'] ? "minlength=\"{$attrs['minlength']}\"" : ''; ?>
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?php echo $disabledClass; ?>">
    <?php if ($help): ?>
    <p class="text-sm text-gray-500 mt-1"><?php echo $help; ?></p>
    <?php endif; ?>
</div>
<?php
    }
}
?>