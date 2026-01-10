<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/components/PageHeader.php';
require_once __DIR__ . '/components/Form.php';

class AdminCreateEquipementView extends View
{
    protected $pageTitle = 'Créer Équipement - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $types = $this->get('types', []);
        
        PageHeader::render([
            'title' => 'Nouvel Équipement'
        ]);

        $typeOptions = [];
        foreach ($types as $t) {
            $typeOptions[$t['id_type']] = $t['libelle'];
        }
        
        Form::render([
            'action' => BASE_URL . 'admin/storeEquipement',
            'method' => 'POST',
            'class' => 'bg-white rounded-lg shadow-lg p-8 max-w-3xl',
            'fields' => [
                [
                    'type' => 'text',
                    'name' => 'nom',
                    'label' => 'Nom',
                    'required' => true
                ],
                [
                    'type' => 'grid',
                    'columns' => 2,
                    'fields' => [
                        [
                            'type' => 'select',
                            'name' => 'type_equipement_id',
                            'label' => 'Type',
                            'required' => true,
                            'empty_option' => 'Sélectionner...',
                            'options' => $typeOptions
                        ],
                        [
                            'type' => 'select',
                            'name' => 'etat',
                            'label' => 'État',
                            'required' => true,
                            'value' => 'libre',
                            'options' => [
                                'libre' => 'Libre',
                                'reserve' => 'Réservé',
                                'maintenance' => 'Maintenance',
                                'hors_service' => 'Hors service'
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
                            'name' => 'localisation',
                            'label' => 'Localisation'
                        ],
                        [
                            'type' => 'number',
                            'name' => 'capacite',
                            'label' => 'Capacité'
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
                    'style' => 'primary'
                ],
                [
                    'type' => 'link',
                    'text' => 'Annuler',
                    'url' => BASE_URL . 'admin/equipements',
                    'style' => 'secondary'
                ]
            ]
        ]);
        
        PageHeader::close();
        $this->renderFooter();
    }
}
?>