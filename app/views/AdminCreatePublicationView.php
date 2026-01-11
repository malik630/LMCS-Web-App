<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/components/PageHeader.php';
require_once __DIR__ . '/components/Form.php';

class AdminCreatePublicationView extends View
{
    protected $pageTitle = 'Nouvelle Publication - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $types = $this->get('types', []);
        $users = $this->get('users', []);
        $projets = $this->get('projets', []);
        
        PageHeader::render([
            'title' => 'Nouvelle Publication',
            'back_link' => [
                'url' => BASE_URL . 'adminPublication/publications',
                'text' => 'Retour aux publications'
            ]
        ]);
        
        $this->renderForm($types, $users, $projets);
        
        PageHeader::close();
        $this->renderFooter();
    }
    
    private function renderForm($types, $users, $projets)
    {
        $typeOpts = [];
        foreach ($types as $t) {
            $typeOpts[$t['id_type']] = $t['libelle'];
        }
        
        $userOpts = [];
        foreach ($users as $u) {
            $userOpts[$u['id_user']] = $u['prenom'] . ' ' . $u['nom'];
        }
        
        $projetOpts = [];
        foreach ($projets as $p) {
            $projetOpts[$p['id_projet']] = $p['titre'];
        }
        
        $fields = [
            ['type' => 'text', 'name' => 'titre', 'label' => 'Titre', 'required' => true],
            
            ['type' => 'grid', 'columns' => 2, 'fields' => [
                ['type' => 'select', 'name' => 'type_publication_id', 'label' => 'Type', 'required' => true, 'empty_option' => '-- Type --', 'options' => $typeOpts],
                ['type' => 'number', 'name' => 'annee', 'label' => 'Année', 'value' => date('Y'), 'min' => 1900, 'max' => date('Y') + 5, 'required' => true]
            ]],
            
            ['type' => 'grid', 'columns' => 2, 'fields' => [
                ['type' => 'text', 'name' => 'domaine', 'label' => 'Domaine'],
                ['type' => 'text', 'name' => 'doi', 'label' => 'DOI', 'placeholder' => '10.1234/example.doi']
            ]],
            
            ['type' => 'textarea', 'name' => 'resume', 'label' => 'Résumé', 'rows' => 5],
            
            ['type' => 'date', 'name' => 'date_publication', 'label' => 'Date publication', 'required' => true]
        ];
        
        if (!empty($projets)) {
            $fields[] = ['type' => 'select', 'name' => 'projet_id', 'label' => 'Projet associé', 'empty_option' => '-- Aucun --', 'options' => $projetOpts];
        }
        
        $fields[] = ['type' => 'select', 'name' => 'auteurs[]', 'label' => 'Auteurs', 'multiple' => true, 'size' => 6, 'required' => true, 'options' => $userOpts, 'helper' => 'Maintenez Ctrl/Cmd pour sélectionner plusieurs'];
        
        $fields[] = ['type' => 'file', 'name' => 'fichier_pdf', 'label' => 'Fichier PDF', 'accept' => '.pdf', 'helper' => 'Taille maximale : 10 MB'];
        
        Form::render([
            'action' => BASE_URL . 'adminPublication/store',
            'method' => 'POST',
            'enctype' => 'multipart/form-data',
            'fields' => $fields,
            'buttons' => [
                ['type' => 'submit', 'text' => 'Créer', 'style' => 'primary', 'icon' => 'save'],
                ['type' => 'link', 'text' => 'Annuler', 'url' => BASE_URL . 'adminPublication/publications', 'style' => 'secondary']
            ]
        ]);
    }
}
?>