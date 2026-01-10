<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/components/PageHeader.php';
require_once __DIR__ . '/components/Form.php';

class AdminEditEquipementView extends View
{
    protected $pageTitle = 'Modifier Équipement - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $equipement = $this->get('equipement');
        $types = $this->get('types', []);
        
        PageHeader::render([
            'title' => 'Modifier l\'Équipement'
        ]);
        
        $typeOptions = [];
        foreach ($types as $t) {
            $typeOptions[$t['id_type']] = $t['libelle'];
        }
        
        Form::render([
            'action' => BASE_URL . 'admin/updateEquipement/' . $equipement['id_equipement'],
            'method' => 'POST',
            'class' => 'bg-white rounded-lg shadow-lg p-8 max-w-3xl',
            'fields' => [
                ['type' => 'text', 'name' => 'nom', 'label' => 'Nom', 'required' => true, 'value' => $equipement['nom']],
                [
                    'type' => 'grid',
                    'columns' => 2,
                    'fields' => [
                        ['type' => 'select', 'name' => 'type_equipement_id', 'label' => 'Type', 'required' => true, 'value' => $equipement['type_equipement_id'], 'options' => $typeOptions],
                        ['type' => 'select', 'name' => 'etat', 'label' => 'État', 'required' => true, 'value' => $equipement['etat'], 'options' => ['libre' => 'Libre', 'reserve' => 'Réservé', 'maintenance' => 'Maintenance', 'hors_service' => 'Hors service']]
                    ]
                ],
                [
                    'type' => 'grid',
                    'columns' => 2,
                    'fields' => [
                        ['type' => 'text', 'name' => 'localisation', 'label' => 'Localisation', 'value' => $equipement['localisation'] ?? ''],
                        ['type' => 'number', 'name' => 'capacite', 'label' => 'Capacité', 'value' => $equipement['capacite'] ?? '']
                    ]
                ],
                ['type' => 'textarea', 'name' => 'description', 'label' => 'Description', 'rows' => 4, 'value' => $equipement['description'] ?? '']
            ],
            'buttons' => [
                ['type' => 'submit', 'text' => 'Enregistrer', 'style' => 'primary', 'icon' => 'save'],
                ['type' => 'link', 'text' => 'Annuler', 'url' => BASE_URL . 'admin/equipements', 'style' => 'secondary']
            ]
        ]);
        
        PageHeader::close();
        $this->renderFooter();
    }
}
?>