<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/components/PageHeader.php';
require_once __DIR__ . '/components/Form.php';
require_once __DIR__ . '/components/Section.php';

class AdminCreateTeamView extends View
{
    protected $pageTitle = 'Créer une Équipe - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $users = $this->get('users', []);
        
        PageHeader::render([
            'title' => 'Créer une Nouvelle Équipe',
            'subtitle' => 'Ajouter une équipe de recherche au laboratoire'
        ]);
 
        $userOptions = [];
        foreach ($users as $user) {
            $userOptions[$user['id_user']] = $user['prenom'] . ' ' . $user['nom'] . ' - ' . $user['grade'];
        }
        
        Section::create('Informations de l\'équipe', function() use ($userOptions) {
            Form::render([
                'action' => BASE_URL . 'admin/storeTeam',
                'method' => 'POST',
                'class' => 'max-w-3xl',
                'fields' => [
                    [
                        'type' => 'text',
                        'name' => 'nom',
                        'label' => 'Nom de l\'équipe',
                        'required' => true,
                        'placeholder' => 'Ex: Équipe Intelligence Artificielle'
                    ],
                    [
                        'type' => 'text',
                        'name' => 'thematique',
                        'label' => 'Thématique de recherche',
                        'placeholder' => 'Ex: Machine Learning et Deep Learning'
                    ],
                    [
                        'type' => 'textarea',
                        'name' => 'description',
                        'label' => 'Description',
                        'rows' => 6,
                        'placeholder' => 'Description détaillée de l\'équipe, ses objectifs, ses axes de recherche...'
                    ],
                    [
                        'type' => 'select',
                        'name' => 'chef_id',
                        'label' => 'Chef d\'équipe',
                        'required' => true,
                        'empty_option' => '-- Sélectionner un chef d\'équipe --',
                        'options' => $userOptions,
                        'helper' => 'Le chef d\'équipe sera automatiquement ajouté comme membre'
                    ]
                ],
                'buttons' => [
                    [
                        'type' => 'submit',
                        'text' => 'Créer l\'équipe',
                        'style' => 'primary',
                        'icon' => 'save'
                    ],
                    [
                        'type' => 'link',
                        'text' => 'Annuler',
                        'url' => BASE_URL . 'admin/equipes',
                        'style' => 'secondary'
                    ]
                ]
            ]);
        }, 'bg-white');
        
        PageHeader::close();
        $this->renderFooter();
    }
}
?>