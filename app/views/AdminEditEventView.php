<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/components/PageHeader.php';
require_once __DIR__ . '/components/Form.php';

class AdminEditEventView extends View
{
    protected $pageTitle = 'Modifier Événement - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $event = $this->get('event');
        $types = $this->get('types', []);
        $organisateurs = $this->get('organisateurs', []);
        
        if (!$event) {
            echo '<div class="container mx-auto px-4 py-8">';
            echo '<p class="text-red-600">Événement introuvable.</p>';
            echo '</div>';
            $this->renderFooter();
            return;
        }
        
        PageHeader::render([
            'title' => 'Modifier l\'événement',
            'subtitle' => $event['titre'],
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
            'action' => BASE_URL . 'admin/updateEvent/' . $event['id_evenement'],
            'method' => 'POST',
            'class' => 'bg-white rounded-lg shadow-lg p-8 max-w-4xl',
            'fields' => [
                [
                    'type' => 'text',
                    'name' => 'titre',
                    'label' => 'Titre',
                    'required' => true,
                    'value' => $event['titre']
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
                            'options' => $typeOptions,
                            'value' => $event['type_evenement_id']
                        ],
                        [
                            'type' => 'select',
                            'name' => 'organisateur_id',
                            'label' => 'Organisateur',
                            'required' => true,
                            'empty_option' => 'Sélectionner...',
                            'options' => $organisateurOptions,
                            'value' => $event['organisateur_id']
                        ],
                        [
                            'type' => 'select',
                            'name' => 'statut',
                            'label' => 'Statut',
                            'required' => true,
                            'value' => $event['statut'],
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
                            'required' => true,
                            'value' => $event['date_debut']
                        ],
                        [
                            'type' => 'date',
                            'name' => 'date_fin',
                            'label' => 'Date de fin',
                            'value' => $event['date_fin']
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
                            'value' => $event['lieu']
                        ],
                        [
                            'type' => 'number',
                            'name' => 'capacite_max',
                            'label' => 'Capacité maximale',
                            'min' => 1,
                            'value' => $event['capacite_max'],
                            'helper' => 'Laisser vide pour aucune limite'
                        ]
                    ]
                ],
                [
                    'type' => 'textarea',
                    'name' => 'description',
                    'label' => 'Description',
                    'rows' => 5,
                    'value' => $event['description']
                ],
                [
                    'type' => 'text',
                    'name' => 'externe',
                    'label' => '',
                    'container_class' => 'flex items-center'
                ]
            ],
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
                    'url' => BASE_URL . 'admin/evenements',
                    'style' => 'secondary'
                ]
            ]
        ]);
        
        $this->renderExterneCheckbox($event['externe']);
        
        PageHeader::close();
        $this->renderFooter();
    }
    
    private function renderExterneCheckbox($isExterne)
    {
        ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const externeField = document.querySelector('input[name="externe"]');
    if (externeField) {
        const container = externeField.closest('.mb-6');
        const isChecked = <?php echo $isExterne ? 'true' : 'false'; ?>;
        container.innerHTML = `
            <div class="flex items-center gap-3 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <input type="checkbox" 
                       id="externe" 
                       name="externe" 
                       ${isChecked ? 'checked' : ''}
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