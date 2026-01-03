<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/ImageHelper.php';
require_once 'components/Section.php';
require_once 'components/Table.php';

class AdminUsersView extends View
{
    protected $pageTitle = 'Gestion des Utilisateurs - Admin';
    
    private $roleLabels = [
        'admin' => 'Administrateur',
        'enseignant-chercheur' => 'Enseignant-Chercheur',
        'doctorant' => 'Doctorant',
        'etudiant' => 'Étudiant',
        'invite' => 'Invité'
    ];
    
    private $statutLabels = [
        'actif' => 'Actif',
        'suspendu' => 'Suspendu',
        'inactif' => 'Inactif'
    ];
    
    public function render()
    {
        $this->renderHeader();
        echo '<div class="container mx-auto px-4 py-8">';
        $this->renderPageHeader();
        $this->renderUsersTable();
        echo '</div>';
        $this->renderFooter();
    }
    
    private function renderPageHeader()
    {
        ?>
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-4xl font-bold text-white mb-3">Gestion des Utilisateurs</h1>
        <p class="text-blue-100 text-lg">Liste complète des membres du laboratoire</p>
    </div>
    <div class="flex gap-4">
        <?php echo HtmlHelper::button('+ Créer un utilisateur', BASE_URL . 'admin/createUser', 'success'); ?>
        <?php echo HtmlHelper::button('Templates Permissions', BASE_URL . 'admin/permissions', 'secondary', 'user'); ?>
        <?php echo HtmlHelper::button('← Retour', BASE_URL . 'admin', 'secondary'); ?>
    </div>
</div>
<?php
    }
    
    private function renderUsersTable()
    {
        $users = $this->get('users', []);
        
        Section::create('Liste des Utilisateurs', function() use ($users) {
            $tableData = $this->generateTableData($users);
            $filters = $this->prepareFilters($users);
            
            Table::render([
                'id' => 'users-table',
                'headers' => [
                    ['label' => 'Utilisateur'],
                    ['label' => 'Email'],
                    ['label' => 'Rôle'],
                    ['label' => 'Grade'],
                    ['label' => 'Statut'],
                    ['label' => 'Publications'],
                    ['label' => 'Projets'],
                    ['label' => 'Actions', 'class' => 'w-32']
                ],
                'data' => $tableData,
                'searchable' => true,
                'sortable' => true,
                'filterable' => true,
                'filters' => $filters,
                'ajax_url' => null,
                'empty_message' => 'Aucun utilisateur trouvé'
            ]);
            echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                if (typeof TableManager !== "undefined") {
                    window.adminUsersTable = new TableManager("users-table", null, {
                        searchable: true,
                        sortable: true,
                        filterable: true
                    });
                }
            });
            </script>';
        }, 'bg-white');
    }
    
    private function generateTableData($users)
    {
        if (empty($users)) return '';
        
        $html = '';
        foreach ($users as $user) {
            $html .= $this->generateUserRow($user);
        }
        return $html;
    }
    
    private function generateUserRow($user)
    {
        $statutBadges = [
            'actif' => 'success',
            'suspendu' => 'danger',
            'inactif' => 'warning'
        ];
        
        $searchText = strtolower($user['prenom'] . ' ' . $user['nom'] . ' ' . $user['email'] . ' ' . $user['grade']);
        
        ob_start();
        ?>
<tr class="border-b border-gray-200 hover:bg-gray-50 transition" data-role="<?php echo $user['role']; ?>"
    data-statut="<?php echo $user['statut']; ?>" data-grade="<?php echo $this->escape($user['grade']); ?>"
    data-search="<?php echo $this->escape($searchText); ?>">

    <td class="px-6 py-4" data-sort="<?php echo $this->escape($user['nom'] . ' ' . $user['prenom']); ?>">
        <div class="flex items-center gap-3">
            <?php ImageHelper::renderUserPhoto($user, 12); ?>
            <div>
                <div class="font-semibold text-gray-900">
                    <?php echo $this->escape($user['prenom'] . ' ' . $user['nom']); ?>
                </div>
                <div class="text-xs text-gray-500">
                    ID: <?php echo $user['id_user']; ?>
                </div>
            </div>
        </div>
    </td>

    <td class="px-6 py-4 text-sm text-gray-700" data-sort="<?php echo $this->escape($user['email']); ?>">
        <?php echo $this->escape($user['email']); ?>
    </td>

    <td class="px-6 py-4" data-sort="<?php echo $this->escape($user['role']); ?>">
        <?php echo HtmlHelper::badge($this->roleLabels[$user['role']] ?? $user['role'], 'primary'); ?>
    </td>

    <td class="px-6 py-4 text-sm text-gray-700" data-sort="<?php echo $this->escape($user['grade']); ?>">
        <?php echo $this->escape($user['grade']); ?>
    </td>

    <td class="px-6 py-4" data-sort="<?php echo $this->escape($user['statut']); ?>">
        <?php 
        $badgeType = $statutBadges[$user['statut']] ?? 'info';
        echo HtmlHelper::badge($this->statutLabels[$user['statut']] ?? $user['statut'], $badgeType); 
        ?>
    </td>

    <td class="px-6 py-4 text-center" data-sort="<?php echo $user['nb_publications']; ?>">
        <span class="font-semibold text-gray-900"><?php echo $user['nb_publications']; ?></span>
    </td>

    <td class="px-6 py-4 text-center" data-sort="<?php echo $user['nb_projets']; ?>">
        <span class="font-semibold text-gray-900"><?php echo $user['nb_projets']; ?></span>
    </td>

    <td class="px-6 py-4">
        <div class="flex gap-2">
            <a href="<?php echo BASE_URL . 'admin/userPermissions/' . $user['id_user']; ?>"
                class="text-purple-600 hover:text-purple-800" title="Permissions">
                <?php echo HtmlHelper::icon('user', 'w-5 h-5'); ?>
            </a>

            <a href="<?php echo BASE_URL . 'admin/editUser/' . $user['id_user']; ?>"
                class="text-blue-600 hover:text-blue-800" title="Modifier">
                <?php echo HtmlHelper::icon('edit', 'w-5 h-5'); ?>
            </a>

            <?php if ($user['statut'] === 'actif'): ?>
            <a href="<?php echo BASE_URL . 'admin/suspendUser/' . $user['id_user']; ?>"
                onclick="return confirm('Suspendre cet utilisateur ?')" class="text-orange-600 hover:text-orange-800"
                title="Suspendre">
                <?php echo HtmlHelper::icon('close', 'w-5 h-5'); ?>
            </a>
            <?php else: ?>
            <a href="<?php echo BASE_URL . 'admin/activateUser/' . $user['id_user']; ?>"
                onclick="return confirm('Activer cet utilisateur ?')" class="text-green-600 hover:text-green-800"
                title="Activer">
                <?php echo HtmlHelper::icon('check', 'w-5 h-5'); ?>
            </a>
            <?php endif; ?>

            <a href="<?php echo BASE_URL . 'admin/deleteUser/' . $user['id_user']; ?>"
                onclick="return confirm('Supprimer définitivement cet utilisateur ?')"
                class="text-red-600 hover:text-red-800" title="Supprimer">
                <?php echo HtmlHelper::icon('trash', 'w-5 h-5'); ?>
            </a>
        </div>
    </td>
</tr>
<?php
        return ob_get_clean();
    }
    
    private function prepareFilters($users)
    {
        return [
            [
                'id' => 'role',
                'label' => 'Rôle',
                'column' => 'role',
                'options' => $this->roleLabels
            ],
            [
                'id' => 'statut',
                'label' => 'Statut',
                'column' => 'statut',
                'options' => $this->statutLabels
            ],
            [
                'id' => 'grade',
                'label' => 'Grade',
                'column' => 'grade',
                'options' => $this->extractGradeOptions($users)
            ]
        ];
    }
    
    private function extractGradeOptions($users)
    {
        $grades = [];
        foreach ($users as $user) {
            $grades[$user['grade']] = $user['grade'];
        }
        ksort($grades);
        return $grades;
    }
}
?>