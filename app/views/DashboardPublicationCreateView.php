<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/components/PageHeader.php';
require_once __DIR__ . '/components/Form.php';

class DashboardPublicationCreateView extends View
{
    protected $pageTitle = 'Nouvelle Publication - LMCS';
    
    public function render()
    {
        $this->renderHeader();
        $types = $this->get('types', []);
        $projets = $this->get('projets', []);
        $users = $this->get('users', []);
        
        PageHeader::render([
            'title' => 'Nouvelle Publication',
            'back_link' => [
                'url' => BASE_URL . 'dashboardpublication/index',
                'text' => 'Retour à mes publications'
            ]
        ]);

        $typeOptions = [];
        foreach ($types as $type) {
            $typeOptions[$type['id_type']] = ucfirst($type['libelle']);
        }
        
        $projetOptions = [];
        foreach ($projets as $projet) {
            $projetOptions[$projet['id_projet']] = $projet['titre'];
        }
        
        $userOptions = [];
        foreach ($users as $user) {
            $userOptions[$user['id_user']] = $user['prenom'] . ' ' . $user['nom'] . ' - ' . $user['grade'];
        }
        
        $fields = [
            [
                'type' => 'text',
                'name' => 'titre',
                'label' => 'Titre',
                'required' => true
            ],
            [
                'type' => 'grid',
                'columns' => 2,
                'fields' => [
                    [
                        'type' => 'select',
                        'name' => 'type_publication_id',
                        'label' => 'Type de publication',
                        'required' => true,
                        'empty_option' => '-- Sélectionner --',
                        'options' => $typeOptions
                    ],
                    [
                        'type' => 'number',
                        'name' => 'annee',
                        'label' => 'Année',
                        'required' => true,
                        'min' => '1900',
                        'max' => (string)(date('Y') + 5),
                        'value' => (string)date('Y')
                    ]
                ]
            ],
            [
                'type' => 'grid',
                'columns' => 2,
                'fields' => [
                    [
                        'type' => 'text',
                        'name' => 'domaine',
                        'label' => 'Domaine'
                    ],
                    [
                        'type' => 'text',
                        'name' => 'doi',
                        'label' => 'DOI',
                        'placeholder' => '10.1234/example'
                    ]
                ]
            ],
            [
                'type' => 'textarea',
                'name' => 'resume',
                'label' => 'Résumé',
                'rows' => 5
            ]
        ];

        if (!empty($projets)) {
            $fields[] = [
                'type' => 'select',
                'name' => 'projet_id',
                'label' => 'Projet associé',
                'empty_option' => '-- Aucun --',
                'options' => $projetOptions
            ];
        }

        $fields[] = [
            'type' => 'select',
            'name' => 'coauteurs',
            'label' => 'Co-auteurs',
            'multiple' => true,
            'size' => 8,
            'options' => $userOptions,
            'helper' => 'Maintenez Ctrl (Windows) ou Cmd (Mac) pour sélectionner plusieurs auteurs'
        ];
        
        // Date et fichier
        $fields[] = [
            'type' => 'date',
            'name' => 'date_publication',
            'label' => 'Date de publication',
            'container_class' => 'mb-6'
        ];
        
        $fields[] = [
            'type' => 'file',
            'name' => 'fichier_pdf',
            'label' => 'Fichier PDF',
            'accept' => '.pdf',
            'helper' => 'Taille maximale : 10 MB'
        ];
        
        Form::render([
            'action' => BASE_URL . 'dashboardpublication/store',
            'method' => 'POST',
            'enctype' => 'multipart/form-data',
            'fields' => $fields,
            'buttons' => [
                [
                    'type' => 'submit',
                    'text' => 'Créer la publication',
                    'style' => 'primary',
                    'icon' => 'save'
                ],
                [
                    'type' => 'link',
                    'text' => 'Annuler',
                    'url' => BASE_URL . 'dashboardpublication/index',
                    'style' => 'secondary'
                ]
            ],
            'note' => [
                'type' => 'info',
                'title' => 'Note :',
                'message' => 'Votre publication sera créée avec le statut "En attente". Vous pourrez ensuite la soumettre pour validation par l\'administrateur.'
            ]
        ]);
        
        PageHeader::close();
        $this->renderFooter();
    }
}
?>