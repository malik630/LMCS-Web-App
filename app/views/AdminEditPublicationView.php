<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/components/PageHeader.php';
require_once __DIR__ . '/components/Section.php';
require_once __DIR__ . '/components/Form.php';

class AdminEditPublicationView extends View
{
    protected $pageTitle = 'Modifier Publication - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $publication = $this->get('publication');
        $types = $this->get('types', []);
        $users = $this->get('users', []);
        $projets = $this->get('projets', []);
        $auteurs = $this->get('auteurs', []);
        
        if (!$publication) {
            echo '<div class="container mx-auto px-4 py-8 text-center text-red-800">Publication introuvable</div>';
            $this->renderFooter();
            return;
        }
        
        PageHeader::render([
            'title' => 'Modifier Publication',
            'subtitle' => $publication['titre'],
            'back_link' => [
                'url' => BASE_URL . 'adminPublication/publications',
                'text' => 'Retour aux publications'
            ],
            'badges' => [
                ['text' => 'Soumis le ' . date('d/m/Y H:i', strtotime($publication['date_soumission'])), 'type' => 'primary']
            ]
        ]);
        
        Section::create('Informations de la publication', function() use ($publication, $types, $users, $projets, $auteurs) {
            $this->renderForm($publication, $types, $users, $projets, $auteurs);
        }, 'bg-white');
        
        PageHeader::close();
        $this->renderFooter();
    }
    
    private function renderForm($publication, $types, $users, $projets, $auteurs)
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
        
        $selectedAuthors = array_column($auteurs, 'id_user');
        
        $fields = [
            ['type' => 'text', 'name' => 'titre', 'label' => 'Titre', 'value' => $publication['titre'], 'required' => true],
            
            ['type' => 'grid', 'columns' => 2, 'fields' => [
                ['type' => 'select', 'name' => 'type_publication_id', 'label' => 'Type', 'value' => $publication['type_publication_id'], 'empty_option' => '-- Type --', 'options' => $typeOpts],
                ['type' => 'number', 'name' => 'annee', 'label' => 'Année', 'value' => $publication['annee'], 'min' => 1900, 'max' => date('Y') + 5, 'required' => true]
            ]],
            
            ['type' => 'grid', 'columns' => 2, 'fields' => [
                ['type' => 'text', 'name' => 'domaine', 'label' => 'Domaine', 'value' => $publication['domaine'] ?? ''],
                ['type' => 'text', 'name' => 'doi', 'label' => 'DOI', 'value' => $publication['doi'] ?? '', 'placeholder' => '10.1234/example.doi']
            ]],
            
            ['type' => 'textarea', 'name' => 'resume', 'label' => 'Résumé', 'value' => $publication['resume'] ?? '', 'rows' => 5],
            
            ['type' => 'grid', 'columns' => 2, 'fields' => [
                ['type' => 'date', 'name' => 'date_publication', 'label' => 'Date publication', 'value' => $publication['date_publication'] ?? date('Y-m-d')],
                ['type' => 'select', 'name' => 'projet_id', 'label' => 'Projet associé', 'value' => $publication['projet_id'] ?? '', 'empty_option' => '-- Aucun --', 'options' => $projetOpts]
            ]],
            
            ['type' => 'select', 'name' => 'auteurs[]', 'label' => 'Auteurs', 'multiple' => true, 'size' => 6, 'value' => $selectedAuthors, 'required' => true, 'options' => $userOpts, 'helper' => 'Maintenez Ctrl/Cmd pour sélectionner plusieurs'],
            ['type' => 'radio', 'name' => 'statut', 'label' => 'Statut', 'required' => true, 'value' => $publication['statut'], 'options' => ['publie' => 'Publiée', 'en_attente' => 'En attente', 'rejete' => 'Rejetée']]
        ];

        if (!empty($publication['fichier_pdf'])) {
            $fields[] = [
                'type' => 'file', 
                'name' => 'fichier_pdf', 
                'label' => 'Fichier PDF', 
                'accept' => '.pdf',
                'current_file' => $publication['fichier_pdf'],
                'current_file_url' => $publication['lien_telechargement'] ?? null,
                'helper' => 'Laissez vide pour conserver le fichier actuel. Taille maximale : 10 MB'
            ];
        } else {
            $fields[] = [
                'type' => 'file', 
                'name' => 'fichier_pdf', 
                'label' => 'Fichier PDF', 
                'accept' => '.pdf',
                'helper' => 'Taille maximale : 10 MB'
            ];
        }
        
        Form::render([
            'action' => BASE_URL . 'adminPublication/update/' . $publication['id_publication'],
            'method' => 'POST',
            'enctype' => 'multipart/form-data',
            'class' => '',
            'fields' => $fields,
            'buttons' => [
                ['type' => 'submit', 'text' => 'Enregistrer', 'style' => 'primary', 'icon' => 'save'],
                ['type' => 'link', 'text' => 'Annuler', 'url' => BASE_URL . 'adminPublication/publications', 'style' => 'secondary']
            ]
        ]);
    }
}
?>