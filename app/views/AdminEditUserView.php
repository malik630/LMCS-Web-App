<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/components/PageHeader.php';
require_once __DIR__ . '/components/Form.php';
class AdminEditUserView extends View
{
    protected $pageTitle = 'Modifier un Utilisateur - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $user = $this->get('user');
        
        echo '<div class="container mx-auto px-4 py-8"><div class="max-w-3xl mx-auto">';
        echo '<div class="mb-6">' . HtmlHelper::button('← Retour', BASE_URL . 'admin/users', 'secondary') . '</div>';
        echo '<div class="bg-white rounded-lg shadow-lg p-8"><h1 class="text-3xl font-bold mb-6">Modifier l\'Utilisateur</h1>';
        
        Form::render([
            'action' => BASE_URL . 'admin/updateUser/' . $user['id_user'],
            'method' => 'POST',
            'class' => '',
            'fields' => [
                ['type' => 'grid', 'columns' => 2, 'fields' => [
                    ['type' => 'text', 'name' => 'prenom', 'label' => 'Prénom', 'required' => true, 'value' => $user['prenom']],
                    ['type' => 'text', 'name' => 'nom', 'label' => 'Nom', 'required' => true, 'value' => $user['nom']]
                ]],
                ['type' => 'text', 'name' => 'email', 'label' => 'Email', 'required' => true, 'value' => $user['email']],
                ['type' => 'grid', 'columns' => 2, 'fields' => [
                    ['type' => 'text', 'name' => 'grade', 'label' => 'Grade', 'required' => true, 'value' => $user['grade']],
                    ['type' => 'text', 'name' => 'poste', 'label' => 'Poste', 'value' => $user['poste'] ?? '']
                ]],
                ['type' => 'grid', 'columns' => 2, 'fields' => [
                    ['type' => 'select', 'name' => 'role', 'label' => 'Rôle', 'required' => true, 'value' => $user['role'], 'options' => ['admin' => 'Administrateur', 'enseignant-chercheur' => 'Enseignant-Chercheur', 'doctorant' => 'Doctorant', 'etudiant' => 'Étudiant', 'invite' => 'Invité']],
                    ['type' => 'select', 'name' => 'statut', 'label' => 'Statut', 'required' => true, 'value' => $user['statut'], 'options' => ['actif' => 'Actif', 'suspendu' => 'Suspendu', 'inactif' => 'Inactif']]
                ]]
            ],
            'buttons' => [
                ['type' => 'submit', 'text' => 'Enregistrer les Modifications', 'style' => 'primary'],
                ['type' => 'link', 'text' => 'Annuler', 'url' => BASE_URL . 'admin/users', 'style' => 'secondary']
            ]
        ]);
        
        echo '</div></div></div>';
        $this->renderFooter();
    }
}
?>