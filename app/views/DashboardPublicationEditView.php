<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/components/PageHeader.php';
require_once __DIR__ . '/components/Form.php';

class DashboardPublicationEditView extends View
{
    protected $pageTitle = 'Modifier Publication - LMCS';
    
    public function render()
    {
        $this->renderHeader();
        $publication = $this->get('publication');
        $types = $this->get('types', []);
        $projets = $this->get('projets', []);
        $users = $this->get('users', []);
        $auteurs = $this->get('auteurs', []);
        
        $auteursIds = array_column($auteurs, 'id_user');
        
        PageHeader::render([
            'title' => 'Modifier la Publication',
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
            if ($user['id_user'] != $_SESSION['user_id']) {
                $userOptions[$user['id_user']] = $user['prenom'] . ' ' . $user['nom'] . ' - ' . $user['grade'];
            }
        }

        $selectedAuthors = array_filter($auteursIds, fn($id) => $id != $_SESSION['user_id']);
        
        $fields = [
            [
                'type' => 'text',
                'name' => 'titre',
                'label' => 'Titre',
                'required' => true,
                'value' => $publication['titre']
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
                        'value' => $publication['type_publication_id'],
                        'options' => $typeOptions
                    ],
                    [
                        'type' => 'number',
                        'name' => 'annee',
                        'label' => 'Année',
                        'required' => true,
                        'min' => '1900',
                        'max' => (string)(date('Y') + 5),
                        'value' => (string)$publication['annee']
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
                        'label' => 'Domaine',
                        'value' => $publication['domaine'] ?? ''
                    ],
                    [
                        'type' => 'text',
                        'name' => 'doi',
                        'label' => 'DOI',
                        'placeholder' => '10.1234/example',
                        'value' => $publication['doi'] ?? ''
                    ]
                ]
            ],
            [
                'type' => 'textarea',
                'name' => 'resume',
                'label' => 'Résumé',
                'rows' => 5,
                'value' => $publication['resume'] ?? ''
            ]
        ];

        if (!empty($projets)) {
            $fields[] = [
                'type' => 'select',
                'name' => 'projet_id',
                'label' => 'Projet associé',
                'empty_option' => '-- Aucun --',
                'value' => $publication['projet_id'] ?? '',
                'options' => $projetOptions
            ];
        }

        $fields[] = [
            'type' => 'select',
            'name' => 'coauteurs',
            'label' => 'Co-auteurs',
            'multiple' => true,
            'size' => 8,
            'value' => $selectedAuthors,
            'options' => $userOptions,
            'helper' => 'Maintenez Ctrl (Windows) ou Cmd (Mac) pour sélectionner plusieurs auteurs'
        ];

        $fields[] = [
            'type' => 'date',
            'name' => 'date_publication',
            'label' => 'Date de publication',
            'value' => $publication['date_publication'] ?? ''
        ];
        
        $fields[] = [
            'type' => 'file',
            'name' => 'fichier_pdf',
            'label' => 'Fichier PDF',
            'accept' => '.pdf',
            'current_file' => !empty($publication['fichier_pdf']) ? $publication['fichier_pdf'] : null,
            'current_file_url' => !empty($publication['lien_telechargement']) ? $publication['lien_telechargement'] : null,
            'helper' => 'Laissez vide pour conserver le fichier actuel. Le fichier sera automatiquement accessible via un lien de téléchargement. Taille maximale : 10 MB'
        ];
        
        Form::render([
            'action' => BASE_URL . 'dashboardpublication/update/' . $publication['id_publication'],
            'method' => 'POST',
            'enctype' => 'multipart/form-data',
            'fields' => $fields,
            'buttons' => [
                [
                    'type' => 'submit',
                    'text' => 'Enregistrer les modifications',
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
                'message' => 'Après modification, vous pourrez soumettre la publication pour validation si nécessaire.'
            ]
        ]);
        
        PageHeader::close();
        $this->renderFooter();
    }
}
?>