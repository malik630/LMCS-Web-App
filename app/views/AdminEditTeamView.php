<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/components/PageHeader.php';
require_once __DIR__ . '/components/Form.php';
require_once __DIR__ . '/components/Section.php';

class AdminEditTeamView extends View
{
    protected $pageTitle = 'Modifier une Équipe - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $team = $this->get('team');
        $users = $this->get('users', []);
        
        PageHeader::render([
            'title' => 'Modifier l\'Équipe',
            'subtitle' => $team['nom']
        ]);
        
        $userOptions = [];
        foreach ($users as $user) {
            $userOptions[$user['id_user']] = $user['prenom'] . ' ' . $user['nom'] . ' - ' . $user['grade'];
        }
        
        Section::create('Informations de l\'équipe', function() use ($team, $userOptions) {
            Form::render([
                'action' => BASE_URL . 'admin/updateTeam/' . $team['id_team'],
                'method' => 'POST',
                'class' => 'max-w-3xl',
                'fields' => [
                    ['type' => 'text', 'name' => 'nom', 'label' => 'Nom de l\'équipe', 'required' => true, 'value' => $team['nom']],
                    ['type' => 'text', 'name' => 'thematique', 'label' => 'Thématique de recherche', 'value' => $team['thematique'] ?? ''],
                    ['type' => 'textarea', 'name' => 'description', 'label' => 'Description', 'rows' => 6, 'value' => $team['description'] ?? ''],
                    ['type' => 'select', 'name' => 'chef_id', 'label' => 'Chef d\'équipe', 'required' => true, 'value' => $team['chef_id'], 'options' => $userOptions]
                ],
                'buttons' => [
                    ['type' => 'submit', 'text' => 'Enregistrer les modifications', 'style' => 'primary', 'icon' => 'save'],
                    ['type' => 'link', 'text' => 'Annuler', 'url' => BASE_URL . 'admin/equipes', 'style' => 'secondary']
                ]
            ]);
        }, 'bg-white');
        
        PageHeader::close();
        $this->renderFooter();
    }
}
?>