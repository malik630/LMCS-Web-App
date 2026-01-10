<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/components/PageHeader.php';

class AdminSettingsView extends View
{
    protected $pageTitle = 'Paramètres - Administration LMCS';
    
    public function render()
    {
        $this->renderHeader();
        
        PageHeader::render([
            'title' => 'Paramètres Généraux',
            'subtitle' => 'Configuration de l\'application',
            'backUrl' => 'admin'
        ]);
        
        $this->renderTabs();
        $this->renderTabContent();
        
        PageHeader::close();
        $this->renderFooter();
    }
    
    private function renderTabs()
    {
        $tabs = [
            'theme' => ['icon' => 'palette', 'label' => 'Thème & Couleurs'],
            'logos' => ['icon' => 'image', 'label' => 'Logos'],
            'backup' => ['icon' => 'database', 'label' => 'Sauvegarde & Restauration']
        ];
        ?>
<div class="mb-6">
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <?php $first = true; foreach ($tabs as $id => $tab): ?>
            <button
                class="settings-tab whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors <?php echo $first ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>"
                data-tab="<?php echo $id; ?>">
                <?php echo HtmlHelper::icon($tab['icon'], 'w-5 h-5'); ?>
                <?php echo $tab['label']; ?>
            </button>
            <?php $first = false; endforeach; ?>
        </nav>
    </div>
</div>
<?php
    }
    
    private function renderTabContent()
    {
        ?>
<div id="settings-content">
    <?php 
        $this->renderThemeTab();
        $this->renderLogosTab();
        $this->renderBackupTab();
    ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.settings-tab');
    const contents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.dataset.tab;

            tabs.forEach(t => {
                t.classList.remove('border-blue-600', 'text-blue-600');
                t.classList.add('border-transparent', 'text-gray-500');
            });
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('border-blue-600', 'text-blue-600');

            contents.forEach(content => {
                if (content.id === targetTab + '-tab') {
                    content.classList.remove('hidden');
                } else {
                    content.classList.add('hidden');
                }
            });
        });
    });
});
</script>
<?php
    }
    
    private function renderThemeTab()
    {
        $settings = $this->get('settings', []);
        ?>
<div id="theme-tab" class="tab-content">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Personnalisation du Thème</h2>

        <form method="POST" action="<?php echo BASE_URL; ?>admin/updateTheme" class="space-y-6">
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Couleur Principale
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="primary_color"
                            value="<?php echo $this->escape($settings['theme_primary_color'] ?? '#1e40af'); ?>"
                            class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                        <input type="text" readonly
                            value="<?php echo $this->escape($settings['theme_primary_color'] ?? '#1e40af'); ?>"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Couleur Secondaire
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="secondary_color"
                            value="<?php echo $this->escape($settings['theme_secondary_color'] ?? '#3b82f6'); ?>"
                            class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                        <input type="text" readonly
                            value="<?php echo $this->escape($settings['theme_secondary_color'] ?? '#3b82f6'); ?>"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Couleur d'Accent
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="accent_color"
                            value="<?php echo $this->escape($settings['theme_accent_color'] ?? '#60a5fa'); ?>"
                            class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                        <input type="text" readonly
                            value="<?php echo $this->escape($settings['theme_accent_color'] ?? '#60a5fa'); ?>"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Mode d'Affichage
                </label>
                <select name="theme_mode"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="light"
                        <?php echo ($settings['theme_mode'] ?? 'light') === 'light' ? 'selected' : ''; ?>>Clair
                    </option>
                    <option value="dark"
                        <?php echo ($settings['theme_mode'] ?? 'light') === 'dark' ? 'selected' : ''; ?>>Sombre
                    </option>
                </select>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                    <?php echo HtmlHelper::icon('save', 'w-5 h-5'); ?>
                    Appliquer le Thème
                </button>
            </div>
        </form>
    </div>
</div>
<?php
    }
    
    private function renderLogosTab()
    {
        $logoLabo = $this->get('logo_labo');
        $logoEsi = $this->get('logo_esi');
        ?>
<div id="logos-tab" class="tab-content hidden">
    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Logo du Laboratoire (LMCS)</h3>

            <div class="mb-4 p-4 bg-gray-50 rounded-lg border-2 border-gray-200">
                <?php if ($logoLabo): ?>
                <img src="<?php echo ASSETS_URL . 'images/logo_labo/' . $logoLabo; ?>?v=<?php echo time(); ?>"
                    alt="Logo LMCS" class="max-h-32 mx-auto object-contain">
                <?php else: ?>
                <div class="h-32 flex items-center justify-center">
                    <span class="text-gray-400">Aucun logo</span>
                </div>
                <?php endif; ?>
            </div>

            <form method="POST" action="<?php echo BASE_URL; ?>admin/uploadLogo" enctype="multipart/form-data">
                <input type="hidden" name="logo_type" value="labo">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Choisir un nouveau logo
                    </label>
                    <input type="file" name="logo" accept="image/*"
                        class="w-full border border-gray-300 rounded-lg p-2">
                    <p class="text-xs text-gray-500 mt-1">Formats acceptés: JPG, PNG, GIF, SVG</p>
                </div>
                <button type="submit"
                    class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2">
                    <?php echo HtmlHelper::icon('upload', 'w-5 h-5'); ?>
                    Télécharger
                </button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Logo ESI</h3>

            <div class="mb-4 p-4 bg-gray-50 rounded-lg border-2 border-gray-200">
                <?php if ($logoEsi): ?>
                <img src="<?php echo ASSETS_URL . 'images/logo_esi/' . $logoEsi; ?>?v=<?php echo time(); ?>"
                    alt="Logo ESI" class="max-h-32 mx-auto object-contain">
                <?php else: ?>
                <div class="h-32 flex items-center justify-center">
                    <span class="text-gray-400">Aucun logo</span>
                </div>
                <?php endif; ?>
            </div>

            <form method="POST" action="<?php echo BASE_URL; ?>admin/uploadLogo" enctype="multipart/form-data">
                <input type="hidden" name="logo_type" value="esi">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Choisir un nouveau logo
                    </label>
                    <input type="file" name="logo" accept="image/*"
                        class="w-full border border-gray-300 rounded-lg p-2">
                    <p class="text-xs text-gray-500 mt-1">Formats acceptés: JPG, PNG, GIF, SVG</p>
                </div>
                <button type="submit"
                    class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2">
                    <?php echo HtmlHelper::icon('upload', 'w-5 h-5'); ?>
                    Télécharger
                </button>
            </form>
        </div>
    </div>

    <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4">
        <p class="text-blue-700 text-sm">
            <strong>Note:</strong> Le logo remplacera le fichier existant dans le dossier. Les changements seront
            visibles immédiatement sur le site.
        </p>
    </div>
</div>
<?php
    }
    
    private function renderBackupTab()
    {
        ?>
<div id="backup-tab" class="tab-content hidden">
    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Créer une Sauvegarde</h3>
            <p class="text-gray-600 mb-4">
                Créez une copie complète de la base de données. Cette sauvegarde pourra être restaurée ultérieurement.
            </p>
            <form method="POST" action="<?php echo BASE_URL; ?>admin/backupDatabase">
                <button type="submit"
                    class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                    <?php echo HtmlHelper::icon('download', 'w-5 h-5'); ?>
                    Créer une Sauvegarde
                </button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Restaurer la Base de Données</h3>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                <p class="text-red-700 font-medium">
                    ⚠️ Attention : Cette opération écrasera toutes les données actuelles !
                </p>
            </div>
            <form method="POST" action="<?php echo BASE_URL; ?>admin/restoreDatabase" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Sélectionner un fichier de sauvegarde (.sql)
                    </label>
                    <input type="file" name="backup_file" accept=".sql"
                        class="w-full border border-gray-300 rounded-lg p-2">
                </div>
                <button type="submit"
                    class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition flex items-center gap-2"
                    onclick="return confirm('Êtes-vous sûr de vouloir restaurer la base de données ? Toutes les données actuelles seront perdues.')">
                    <?php echo HtmlHelper::icon('upload', 'w-5 h-5'); ?>
                    Restaurer
                </button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Sauvegardes Disponibles</h3>
            <div id="backups-list" class="space-y-2">
                <p class="text-gray-500">Chargement...</p>
            </div>
        </div>
    </div>
</div>

<script>
async function loadBackups() {
    try {
        const response = await fetch('<?php echo BASE_URL; ?>admin/listBackups');
        const data = await response.json();
        const container = document.getElementById('backups-list');

        if (data.backups && data.backups.length > 0) {
            container.innerHTML = data.backups.map(backup => `
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <div>
                        <p class="font-medium text-gray-900">${backup.filename}</p>
                        <p class="text-sm text-gray-500">
                            ${new Date(backup.created_at).toLocaleString('fr-FR')} • 
                            ${(backup.filesize / 1024 / 1024).toFixed(2)} MB
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <a href="<?php echo BASE_URL; ?>admin/downloadBackup/${backup.filename}"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition text-sm">
                            Télécharger
                        </a>
                        <button onclick="deleteBackup('${backup.filename}')"
                            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition text-sm">
                            Supprimer
                        </button>
                    </div>
                </div>
            `).join('');
        } else {
            container.innerHTML = '<p class="text-gray-500">Aucune sauvegarde disponible</p>';
        }
    } catch (error) {
        console.error('Erreur:', error);
        document.getElementById('backups-list').innerHTML =
            '<p class="text-red-500">Erreur lors du chargement des sauvegardes</p>';
    }
}

function deleteBackup(filename) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette sauvegarde ?')) {
        window.location.href = `<?php echo BASE_URL; ?>admin/deleteBackup/${filename}`;
    }
}

document.addEventListener('DOMContentLoaded', loadBackups);
</script>
<?php
    }
}
?>