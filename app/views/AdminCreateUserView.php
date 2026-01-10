<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/components/PageHeader.php';
require_once __DIR__ . '/components/Form.php';

class AdminCreateUserView extends View
{
    protected $pageTitle = 'Créer un Utilisateur - Admin';
    
    public function render()
    {
        $this->renderHeader();
        
        echo '<div class="container mx-auto px-4 py-8">';
        echo '<div class="max-w-3xl mx-auto">';
        
        echo '<div class="mb-6">';
        echo HtmlHelper::button('← Retour', BASE_URL . 'admin/users', 'secondary');
        echo '</div>';
        
        echo '<div class="bg-white rounded-lg shadow-lg p-8">';
        echo '<h1 class="text-3xl font-bold mb-6">Créer un Nouvel Utilisateur</h1>';
        
        Form::render([
            'action' => BASE_URL . 'admin/storeUser',
            'method' => 'POST',
            'class' => '',
            'fields' => [
                [
                    'type' => 'grid',
                    'columns' => 2,
                    'fields' => [
                        [
                            'type' => 'text',
                            'name' => 'username',
                            'label' => 'Nom d\'utilisateur',
                            'required' => true
                        ],
                        [
                            'type' => 'password',
                            'name' => 'password',
                            'label' => 'Mot de passe',
                            'required' => true
                        ]
                    ]
                ],
                [
                    'type' => 'grid',
                    'columns' => 2,
                    'fields' => [
                        [
                            'type' => 'text',
                            'name' => 'prenom',
                            'label' => 'Prénom',
                            'required' => true
                        ],
                        [
                            'type' => 'text',
                            'name' => 'nom',
                            'label' => 'Nom',
                            'required' => true
                        ]
                    ]
                ],
                [
                    'type' => 'text',
                    'name' => 'email',
                    'label' => 'Email',
                    'required' => true
                ],
                [
                    'type' => 'grid',
                    'columns' => 2,
                    'fields' => [
                        [
                            'type' => 'text',
                            'name' => 'grade',
                            'label' => 'Grade',
                            'required' => true,
                            'placeholder' => 'Ex: Professeur, Maître de conférences...'
                        ],
                        [
                            'type' => 'text',
                            'name' => 'poste',
                            'label' => 'Poste',
                            'placeholder' => 'Ex: Chef de département...'
                        ]
                    ]
                ],
                [
                    'type' => 'select',
                    'name' => 'role',
                    'label' => 'Rôle',
                    'required' => true,
                    'options' => [
                        'enseignant-chercheur' => 'Enseignant-Chercheur',
                        'doctorant' => 'Doctorant',
                        'etudiant' => 'Étudiant',
                        'invite' => 'Invité',
                        'admin' => 'Administrateur'
                    ],
                    'helper' => 'Les permissions par défaut du rôle seront automatiquement attribuées'
                ]
            ],
            'buttons' => [
                [
                    'type' => 'submit',
                    'text' => 'Créer l\'Utilisateur',
                    'style' => 'primary'
                ],
                [
                    'type' => 'link',
                    'text' => 'Annuler',
                    'url' => BASE_URL . 'admin/users',
                    'style' => 'secondary'
                ]
            ]
        ]);
        
        echo '</div>';
        echo '</div>';
        echo '</div>';
        
        $this->renderFooter();
    }
}
?>