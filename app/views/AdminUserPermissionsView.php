<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once 'components/Section.php';

class AdminUserPermissionsView extends View
{
    protected $pageTitle = 'Permissions Utilisateur - Admin';
    
    public function render()
    {
        $this->renderHeader();
        echo '<div class="container mx-auto px-4 py-8">';
        $this->renderContent();
        echo '</div>';
        $this->renderFooter();
    }
    
    private function renderContent()
    {
        $user = $this->get('user');
        $permissions = $this->get('permissions', []);
        $userPermissions = $this->get('userPermissions', []);
        
        $userPermissionIds = array_column($userPermissions, 'id_permission');
        
        ?>
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <?php echo HtmlHelper::button('← Retour', BASE_URL . 'admin/users', 'secondary'); ?>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="mb-8 pb-6 border-b border-gray-200">
            <h1 class="text-3xl font-bold mb-4">Permissions de l'Utilisateur</h1>
            <div class="flex items-center gap-4">
                <div class="font-semibold text-gray-900 text-lg">
                    <?php echo $this->escape($user['prenom'] . ' ' . $user['nom']); ?>
                </div>
                <div class="text-sm text-gray-600">
                    <?php echo HtmlHelper::badge(ucfirst($user['role']), 'primary'); ?>
                    <?php echo HtmlHelper::badge($this->escape($user['grade']), 'success'); ?>
                </div>
            </div>
        </div>

        <div class="mb-6 flex gap-3">
            <button type="button" onclick="selectAllPermissions()"
                class="px-4 py-2 bg-green-100 hover:bg-green-200 text-green-800 rounded-lg font-medium transition text-sm">
                Tout sélectionner
            </button>
            <button type="button" onclick="deselectAllPermissions()"
                class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-800 rounded-lg font-medium transition text-sm">
                Tout désélectionner
            </button>
        </div>

        <form action="<?php echo BASE_URL; ?>admin/updateUserPermissions/<?php echo $user['id_user']; ?>" method="POST"
            class="space-y-6">

            <?php foreach ($permissions as $categorie => $perms): ?>
            <div class="border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4"><?php echo $this->escape($categorie); ?></h3>
                <div class="space-y-3">
                    <?php foreach ($perms as $perm): ?>
                    <?php $checked = in_array($perm['id_permission'], $userPermissionIds) ? 'checked' : ''; ?>
                    <label class="flex items-start gap-3 cursor-pointer hover:bg-gray-50 p-2 rounded">
                        <input type="checkbox" name="permissions[]" value="<?php echo $perm['id_permission']; ?>"
                            <?php echo $checked; ?>
                            class="permission-checkbox mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <div>
                            <div class="font-medium text-gray-900"><?php echo $this->escape($perm['nom']); ?></div>
                            <?php if (!empty($perm['description'])): ?>
                            <div class="text-sm text-gray-600"><?php echo $this->escape($perm['description']); ?></div>
                            <?php endif; ?>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="flex gap-4 pt-4">
                <button type="submit"
                    class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
                    Enregistrer les Permissions
                </button>
                <a href="<?php echo BASE_URL; ?>admin/users"
                    class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg font-semibold transition text-center">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function selectAllPermissions() {
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllPermissions() {
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
}
</script>
<?php
    }
}
?>