<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/components/PageHeader.php';
require_once __DIR__ . '/components/Form.php';

class AdminCreateProjetView extends View
{
    protected $pageTitle = 'Créer un Projet - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $users = $this->get('users', []);
        
        PageHeader::render([
            'title' => 'Créer un Projet'
        ]);

        $userOptions = [];
        foreach ($users as $u) {
            $userOptions[$u['id_user']] = $u['prenom'] . ' ' . $u['nom'];
        }
        
        Form::render([
            'action' => BASE_URL . 'admin/storeProjet',
            'method' => 'POST',
            'class' => 'bg-white rounded-lg shadow-lg p-8 max-w-3xl',
            'fields' => [
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
                            'name' => 'responsable_id',
                            'label' => 'Responsable',
                            'required' => true,
                            'empty_option' => '-- Sélectionner --',
                            'options' => $userOptions
                        ],
                        [
                            'type' => 'select',
                            'name' => 'statut',
                            'label' => 'Statut',
                            'required' => true,
                            'value' => 'en_cours',
                            'options' => [
                                'soumis' => 'Soumis',
                                'en_cours' => 'En cours',
                                'termine' => 'Terminé'
                            ]
                        ]
                    ]
                ],
                [
                    'type' => 'grid',
                    'columns' => 2,
                    'fields' => [
                        [
                            'type' => 'text',
                            'name' => 'thematique',
                            'label' => 'Thématique'
                        ],
                        [
                            'type' => 'number',
                            'name' => 'budget',
                            'label' => 'Budget (DA)'
                        ]
                    ]
                ],
                [
                    'type' => 'textarea',
                    'name' => 'description',
                    'label' => 'Description',
                    'rows' => 4
                ]
            ],
            'buttons' => [
                [
                    'type' => 'submit',
                    'text' => 'Créer',
                    'style' => 'primary',
                    'icon' => 'save'
                ],
                [
                    'type' => 'link',
                    'text' => 'Annuler',
                    'url' => BASE_URL . 'admin/projets',
                    'style' => 'secondary'
                ]
            ]
        ]);
        
        PageHeader::close();
        $this->renderFooter();
    }
}
?>