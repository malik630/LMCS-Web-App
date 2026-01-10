<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/components/Form.php';

class AdminCreatePublicationView extends View
{
    protected $pageTitle = 'Nouvelle Publication - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $types = $this->get('types', []);
        $users = $this->get('users', []);
        
        echo '<div class="container mx-auto px-4 py-8">';
        echo '<div class="mb-8 flex items-center justify-between">';
        echo '<h1 class="text-4xl font-bold text-white">Nouvelle Publication</h1>';
        echo HtmlHelper::button('← Retour', BASE_URL . 'adminPublication/publications', 'secondary');
        echo '</div>';
        
        $this->renderForm(BASE_URL . 'adminPublication/store', [], $types, $users);
        echo '</div>';
        $this->renderFooter();
    }
    
    private function renderForm($action, $data, $types, $users)
    {
        $typeOpts = [];
        foreach ($types as $t) $typeOpts[$t['id_type']] = $t['libelle'];
        
        $userOpts = [];
        foreach ($users as $u) $userOpts[$u['id_user']] = $u['prenom'] . ' ' . $u['nom'];
        
        Form::render([
            'action' => $action,
            'method' => 'POST',
            'enctype' => 'multipart/form-data',
            'class' => 'bg-white rounded-lg shadow-lg p-8 max-w-4xl mx-auto',
            'fields' => [
                ['type' => 'text', 'name' => 'titre', 'label' => 'Titre', 'value' => $data['titre'] ?? '', 'required' => true],
                ['type' => 'grid', 'columns' => 2, 'fields' => [
                    ['type' => 'select', 'name' => 'type_publication_id', 'label' => 'Type', 'value' => $data['type_publication_id'] ?? '', 'required' => true, 'empty_option' => '-- Type --', 'options' => $typeOpts],
                    ['type' => 'text', 'name' => 'domaine', 'label' => 'Domaine', 'value' => $data['domaine'] ?? '']
                ]],
                ['type' => 'textarea', 'name' => 'resume', 'label' => 'Résumé', 'value' => $data['resume'] ?? '', 'rows' => 5],
                ['type' => 'grid', 'columns' => 2, 'fields' => [
                    ['type' => 'date', 'name' => 'date_publication', 'label' => 'Date publication', 'value' => $data['date_publication'] ?? '', 'required' => true],
                    ['type' => 'number', 'name' => 'annee', 'label' => 'Année', 'value' => $data['annee'] ?? date('Y'), 'min' => 1900, 'max' => date('Y') + 1, 'required' => true]
                ]],
                ['type' => 'text', 'name' => 'doi', 'label' => 'DOI', 'value' => $data['doi'] ?? '', 'placeholder' => '10.1234/example.doi'],
                ['type' => 'text', 'name' => 'lien_telechargement', 'label' => 'Lien téléchargement', 'value' => $data['lien_telechargement'] ?? ''],
                ['type' => 'select', 'name' => 'auteurs[]', 'label' => 'Auteurs', 'value' => $data['auteurs'] ?? [], 'multiple' => true, 'size' => 6, 'required' => true, 'options' => $userOpts, 'helper' => 'Maintenez Ctrl/Cmd pour sélectionner plusieurs'],
                ['type' => 'file', 'name' => 'fichier_pdf', 'label' => 'Fichier PDF (optionnel)', 'accept' => '.pdf']
            ],
            'buttons' => [
                ['type' => 'submit', 'text' => empty($data) ? 'Créer' : 'Enregistrer', 'style' => 'primary', 'icon' => 'save'],
                ['type' => 'link', 'text' => 'Annuler', 'url' => BASE_URL . 'adminPublication/publications', 'style' => 'secondary']
            ]
        ]);
    }
}
?>