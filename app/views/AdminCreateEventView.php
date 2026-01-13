<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/components/PageHeader.php';
require_once __DIR__ . '/components/Form.php';

class AdminCreateEventView extends View
{
    protected $pageTitle = 'Créer Événement - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $types = $this->get('types', []);
        $organisateurs = $this->get('organisateurs', []);
        
        PageHeader::render([
            'title' => 'Nouvel Événement',
            'back_link' => [
                'url' => BASE_URL . 'admin/evenements',
                'text' => 'Retour à la liste'
            ]
        ]);

        $typeOptions = [];
        foreach ($types as $t) {
            $typeOptions[$t['id_type']] = $t['libelle'];
        }
        
        $organisateurOptions = [];
        foreach ($organisateurs as $o) {
            $organisateurOptions[$o['id_user']] = $o['prenom'] . ' ' . $o['nom'];
        }
        
        Form::render([
            'action' => BASE_URL . 'admin/storeEvent',
            'method' => 'POST',
            'class' => 'bg-white rounded-lg shadow-lg p-8 max-w-4xl',
            'fields' => [
                [
                    'type' => 'text',
                    'name' => 'titre',
                    'label' => 'Titre',
                    'required' => true,
                    'placeholder' => 'Ex: Conférence sur l\'IA'
                ],
                [
                    'type' => 'grid',
                    'columns' => 3,
                    'fields' => [
                        [
                            'type' => 'select',
                            'name' => 'type_evenement_id',
                            'label' => 'Type d\'événement',
                            'required' => true,
                            'empty_option' => 'Sélectionner...',
                            'options' => $typeOptions
                        ],
                        [
                            'type' => 'select',
                            'name' => 'organisateur_id',
                            'label' => 'Organisateur',
                            'required' => true,
                            'empty_option' => 'Sélectionner...',
                            'options' => $organisateurOptions
                        ],
                        [
                            'type' => 'select',
                            'name' => 'statut',
                            'label' => 'Statut',
                            'required' => true,
                            'value' => 'a_venir',
                            'options' => [
                                'a_venir' => 'À venir',
                                'en_cours' => 'En cours',
                                'termine' => 'Terminé',
                                'annule' => 'Annulé'
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
                            'label' => 'Date de début',
                            'required' => true
                        ],
                        [
                            'type' => 'date',
                            'name' => 'date_fin',
                            'label' => 'Date de fin'
                        ]
                    ]
                ],
                [
                    'type' => 'grid',
                    'columns' => 2,
                    'fields' => [
                        [
                            'type' => 'text',
                            'name' => 'lieu',
                            'label' => 'Lieu',
                            'placeholder' => 'Ex: Amphithéâtre principal'
                        ],
                        [
                            'type' => 'number',
                            'name' => 'capacite_max',
                            'label' => 'Capacité maximale',
                            'min' => 1,
                            'helper' => 'Laisser vide pour aucune limite'
                        ]
                    ]
                ],
                [
                    'type' => 'textarea',
                    'name' => 'description',
                    'label' => 'Description',
                    'rows' => 5,
                    'placeholder' => 'Description détaillée de l\'événement...'
                ],
                [
                    'type' => 'grid',
                    'columns' => 1,
                    'fields' => [
                        [
                            'type' => 'text',
                            'name' => 'externe',
                            'label' => '',
                            'container_class' => 'flex items-center'
                        ]
                    ]
                ]
            ],
            'buttons' => [
                [
                    'type' => 'submit',
                    'text' => 'Créer l\'événement',
                    'style' => 'primary',
                    'icon' => 'plus'
                ],
                [
                    'type' => 'link',
                    'text' => 'Annuler',
                    'url' => BASE_URL . 'admin/evenements',
                    'style' => 'secondary'
                ]
            ],
            'note' => [
                'type' => 'info',
                'title' => 'Note :',
                'message' => 'Les événements internes nécessitent que les participants soient connectés pour s\'inscrire. Les événements externes sont ouverts à tous.'
            ]
        ]);
        $this->renderExterneCheckbox();
        
        PageHeader::close();
        $this->renderFooter();
    }
    
    private function renderExterneCheckbox()
    {
        ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const externeField = document.querySelector('input[name="externe"]');
    if (externeField) {
        const container = externeField.closest('.mb-6');
        container.innerHTML = `
            <div class="flex items-center gap-3 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <input type="checkbox" 
                       id="externe" 
                       name="externe" 
                       class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="externe" class="font-medium text-gray-900 cursor-pointer">
                    Événement externe (ouvert au grand public)
                </label>
            </div>
        `;
    }
});
</script>
<?php
    }
}