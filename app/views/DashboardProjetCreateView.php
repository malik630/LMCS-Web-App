<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/components/PageHeader.php';
require_once __DIR__ . '/components/Form.php';

class DashboardProjetCreateView extends View
{
    protected $pageTitle = 'Nouveau Projet - LMCS';
    
    public function render()
    {
        $this->renderHeader();
        
        PageHeader::render([
            'title' => 'Nouveau Projet',
            'back_link' => [
                'url' => BASE_URL . 'dashboardprojet/index',
                'text' => 'Retour à mes projets'
            ]
        ]);
        
        Form::render([
            'action' => BASE_URL . 'dashboardprojet/store',
            'method' => 'POST',
            'fields' => [
                [
                    'type' => 'text',
                    'name' => 'titre',
                    'label' => 'Titre',
                    'required' => true
                ],
                [
                    'type' => 'textarea',
                    'name' => 'description',
                    'label' => 'Description',
                    'rows' => 5
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
                            'type' => 'select',
                            'name' => 'type_financement',
                            'label' => 'Type de financement',
                            'empty_option' => '-- Sélectionner --',
                            'options' => [
                                'PRFU' => 'PRFU',
                                'PNR' => 'PNR',
                                'Européen' => 'Européen',
                                'Autre' => 'Autre'
                            ]
                        ]
                    ]
                ],
                [
                    'type' => 'grid',
                    'columns' => 2,
                    'fields' => [
                        [
                            'type' => 'date',
                            'name' => 'date_debut',
                            'label' => 'Date de début'
                        ],
                        [
                            'type' => 'date',
                            'name' => 'date_fin',
                            'label' => 'Date de fin'
                        ]
                    ]
                ],
                [
                    'type' => 'number',
                    'name' => 'budget',
                    'label' => 'Budget (DA)',
                    'step' => '0.01',
                    'min' => '0'
                ]
            ],
            'buttons' => [
                [
                    'type' => 'submit',
                    'text' => 'Créer le projet',
                    'style' => 'primary',
                    'icon' => 'save'
                ],
                [
                    'type' => 'link',
                    'text' => 'Annuler',
                    'url' => BASE_URL . 'dashboardprojet/index',
                    'style' => 'secondary'
                ]
            ],
            'note' => [
                'type' => 'info',
                'title' => 'Note :',
                'message' => 'Vous serez automatiquement désigné comme responsable de ce projet. Vous pourrez ajouter des membres après la création.'
            ]
        ]);
        
        PageHeader::close();
        $this->renderFooter();
    }
}
?>