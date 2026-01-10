<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/components/PageHeader.php';
require_once __DIR__ . '/components/Form.php';
class AdminEditProjetView extends View
{
    protected $pageTitle = 'Modifier Projet - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $projet = $this->get('projet');
        $users = $this->get('users', []);
        
        PageHeader::render(['title' => 'Modifier le Projet']);
        
        $userOptions = [];
        foreach ($users as $u) {
            $userOptions[$u['id_user']] = $u['prenom'] . ' ' . $u['nom'];
        }
        
        Form::render([
            'action' => BASE_URL . 'admin/updateProjet/' . $projet['id_projet'],
            'method' => 'POST',
            'class' => 'bg-white rounded-lg shadow-lg p-8 max-w-3xl',
            'fields' => [
                ['type' => 'text', 'name' => 'titre', 'label' => 'Titre', 'required' => true, 'value' => $projet['titre']],
                [
                    'type' => 'grid',
                    'columns' => 2,
                    'fields' => [
                        ['type' => 'select', 'name' => 'responsable_id', 'label' => 'Responsable', 'required' => true, 'value' => $projet['responsable_id'], 'options' => $userOptions],
                        ['type' => 'select', 'name' => 'statut', 'label' => 'Statut', 'required' => true, 'value' => $projet['statut'], 'options' => ['soumis' => 'Soumis', 'en_cours' => 'En cours', 'termine' => 'Terminé']]
                    ]
                ],
                [
                    'type' => 'grid',
                    'columns' => 2,
                    'fields' => [
                        ['type' => 'text', 'name' => 'thematique', 'label' => 'Thématique', 'value' => $projet['thematique'] ?? ''],
                        ['type' => 'number', 'name' => 'budget', 'label' => 'Budget (DA)', 'value' => $projet['budget'] ?? '']
                    ]
                ],
                ['type' => 'textarea', 'name' => 'description', 'label' => 'Description', 'rows' => 4, 'value' => $projet['description'] ?? '']
            ],
            'buttons' => [
                ['type' => 'submit', 'text' => 'Enregistrer', 'style' => 'primary', 'icon' => 'save'],
                ['type' => 'link', 'text' => 'Annuler', 'url' => BASE_URL . 'admin/projets', 'style' => 'secondary']
            ]
        ]);
        
        PageHeader::close();
        $this->renderFooter();
    }
}
?>