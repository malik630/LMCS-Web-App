<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once 'components/Section.php';

class AdminPermissionsView extends View
{
    protected $pageTitle = 'Gestion des Permissions - Admin';
    
    private $roleLabels = [
        'admin' => ['label' => 'Administrateur', 'color' => 'red'],
        'enseignant-chercheur' => ['label' => 'Enseignant-Chercheur', 'color' => 'blue'],
        'doctorant' => ['label' => 'Doctorant', 'color' => 'green'],
        'etudiant' => ['label' => 'Étudiant', 'color' => 'purple'],
        'invite' => ['label' => 'Invité', 'color' => 'gray']
    ];
    
    public function render()
    {
        $this->renderHeader();
        echo '<div class="container mx-auto px-4 py-8">';
        $this->renderPageHeader();
        $this->renderPermissionsManagement();
        echo '</div>';
        $this->renderScript();
        $this->renderFooter();
    }
    
    private function renderPageHeader()
    {
        ?>
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-4xl font-bold text-white mb-3">Gestion des Permissions</h1>
        <p class="text-blue-100 text-lg">Configurer les permissions par rôle</p>
    </div>
    <div>
        <?php echo HtmlHelper::button('← Retour', BASE_URL . 'admin/users', 'secondary'); ?>
    </div>
</div>
<?php
    }
    
    private function renderPermissionsManagement()
    {
        $roles = $this->get('roles', []);
        
        Section::create('Permissions par Rôle', function() use ($roles) {
            ?>
<div class="mb-6">
    <label class="block text-sm font-semibold text-gray-700 mb-3">Sélectionner un rôle à configurer</label>
    <div class="flex gap-3">
        <?php foreach ($roles as $role): ?>
        <?php $this->renderRoleButton($role); ?>
        <?php endforeach; ?>
    </div>
</div>

<div id="permissions-container">
</div>
<?php
        }, 'bg-white');
    }
    
    private function renderRoleButton($role)
    {
        $roleInfo = $this->roleLabels[$role] ?? ['label' => $role, 'color' => 'gray'];
        $colorClass = [
            'red' => 'bg-red-600 hover:bg-red-700',
            'blue' => 'bg-blue-600 hover:bg-blue-700',
            'green' => 'bg-green-600 hover:bg-green-700',
            'purple' => 'bg-purple-600 hover:bg-purple-700',
            'gray' => 'bg-gray-600 hover:bg-gray-700'
        ][$roleInfo['color']] ?? 'bg-blue-600 hover:bg-blue-700';
        
        ?>
<button onclick="loadPermissions('<?php echo $role; ?>')"
    class="role-btn px-6 py-3 <?php echo $colorClass; ?> text-white rounded-lg font-semibold transition"
    data-role="<?php echo $role; ?>">
    <?php echo $this->escape($roleInfo['label']); ?>
</button>
<?php
    }
    
    private function renderScript()
    {
        $roles = $this->get('roles', []);
        $permissions = $this->get('permissions', []);
        $rolePermissions = $this->get('rolePermissions', []);
  
        $rolePermissionsMap = [];
        foreach ($roles as $role) {
            $rolePermissionsMap[$role] = array_column($rolePermissions[$role] ?? [], 'id_permission');
        }
        
        ?>
<script>
const allPermissions = <?php echo json_encode($permissions); ?>;
const rolePermissionsMap = <?php echo json_encode($rolePermissionsMap); ?>;

function loadPermissions(role) {
    document.querySelectorAll('.role-btn').forEach(btn => {
        btn.classList.remove('ring-4', 'ring-white', 'ring-offset-2');
    });
    document.querySelector(`[data-role="${role}"]`).classList.add('ring-4', 'ring-white', 'ring-offset-2');

    const container = document.getElementById('permissions-container');
    const userPermissions = rolePermissionsMap[role] || [];

    let html = `
        <form action="<?php echo BASE_URL; ?>admin/updatePermissions" method="POST" class="space-y-6">
            <input type="hidden" name="role" value="${role}">
    `;

    for (const [categorie, perms] of Object.entries(allPermissions)) {
        html += `
            <div class="border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">${categorie}</h3>
                <div class="space-y-3">
        `;

        perms.forEach(perm => {
            const checked = userPermissions.includes(perm.id_permission) ? 'checked' : '';
            html += `
                <label class="flex items-start gap-3 cursor-pointer hover:bg-gray-50 p-2 rounded">
                    <input type="checkbox" name="permissions[]" value="${perm.id_permission}" 
                           ${checked}
                           class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <div>
                        <div class="font-medium text-gray-900">${perm.nom}</div>
                        <div class="text-sm text-gray-600">${perm.description || ''}</div>
                    </div>
                </label>
            `;
        });

        html += `
                </div>
            </div>
        `;
    }

    html += `
            <div class="flex gap-4">
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
                    Enregistrer les Permissions
                </button>
            </div>
        </form>
    `;

    container.innerHTML = html;
}

document.addEventListener('DOMContentLoaded', function() {
    const roles = <?php echo json_encode($roles); ?>;
    if (roles.length > 0) {
        loadPermissions(roles[0]);
    }
});
</script>
<?php
    }
}
?>