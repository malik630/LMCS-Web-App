<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/DateHelper.php';
require_once 'components/Section.php';
require_once 'components/Table.php';

class AdminPublicationsView extends View
{
    protected $pageTitle = 'Gestion des Publications - Admin';
    
    public function render()
    {
        $this->renderHeader();
        $publications = $this->get('publications', []);
        $stats = $this->get('stats', []);
        $currentFilter = $this->get('currentFilter', 'all');
        ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-white mb-3">Gestion des Publications</h1>
        </div>
        <div class="flex gap-4">
            <?php echo HtmlHelper::button('Rapports', BASE_URL . 'adminPublication/rapports', 'secondary'); ?>
            <?php echo HtmlHelper::button('+ Nouvelle publication', BASE_URL . 'adminPublication/create', 'success'); ?>
            <?php echo HtmlHelper::button('← Retour', BASE_URL . 'admin', 'secondary'); ?>
        </div>
    </div>

    <div class="grid md:grid-cols-4 gap-6 mb-8">
        <a href="<?php echo BASE_URL; ?>adminPublication/publications"
            class="bg-white rounded-lg shadow-sm border-2 <?php echo $currentFilter === 'all' ? 'border-blue-500' : 'border-gray-200'; ?> p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Toutes</span>
                <?php echo HtmlHelper::icon('document', 'w-5 h-5 text-gray-400'); ?>
            </div>
            <div class="text-3xl font-bold text-gray-900"><?php echo $stats['total'] ?? 0; ?></div>
        </a>

        <a href="<?php echo BASE_URL; ?>adminPublication/publications?filter=pending"
            class="bg-white rounded-lg shadow-sm border-2 <?php echo $currentFilter === 'pending' ? 'border-orange-500' : 'border-gray-200'; ?> p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">En attente</span>
                <?php echo HtmlHelper::icon('clock', 'w-5 h-5 text-gray-400'); ?>
            </div>
            <div class="text-3xl font-bold text-orange-600"><?php echo $stats['pending'] ?? 0; ?></div>
        </a>

        <a href="<?php echo BASE_URL; ?>adminPublication/publications?filter=published"
            class="bg-white rounded-lg shadow-sm border-2 <?php echo $currentFilter === 'published' ? 'border-green-500' : 'border-gray-200'; ?> p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Publiées</span>
                <?php echo HtmlHelper::icon('check', 'w-5 h-5 text-gray-400'); ?>
            </div>
            <div class="text-3xl font-bold text-green-600"><?php echo $stats['published'] ?? 0; ?></div>
        </a>

        <a href="<?php echo BASE_URL; ?>adminPublication/publications?filter=rejected"
            class="bg-white rounded-lg shadow-sm border-2 <?php echo $currentFilter === 'rejected' ? 'border-red-500' : 'border-gray-200'; ?> p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Rejetées</span>
                <?php echo HtmlHelper::icon('close', 'w-5 h-5 text-gray-400'); ?>
            </div>
            <div class="text-3xl font-bold text-red-600"><?php echo $stats['rejected'] ?? 0; ?></div>
        </a>
    </div>

    <?php 
    Section::create('Liste des Publications', function() use ($publications) {
        $tableData = $this->generateTableData($publications);
        
        Table::render([
            'id' => 'publications-table',
            'headers' => [
                ['label' => 'Publication'],
                ['label' => 'Auteurs'],
                ['label' => 'Année'],
                ['label' => 'Type'],
                ['label' => 'Statut'],
                ['label' => 'Actions', 'class' => 'w-56']
            ],
            'data' => $tableData,
            'searchable' => true,
            'sortable' => true,
            'filterable' => false,
            'empty_message' => 'Aucune publication trouvée'
        ]);
        
        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof TableManager !== "undefined") {
                new TableManager("publications-table", null, {
                    searchable: true,
                    sortable: true,
                    filterable: false
                });
            }
        });
        </script>';
    }, 'bg-white');
    ?>
</div>

<?php
        $this->renderFooter();
    }
    
    private function generateTableData($publications)
    {
        if (empty($publications)) return '';
        
        $html = '';
        foreach ($publications as $pub) {
            $html .= $this->generateRow($pub);
        }
        return $html;
    }
    
    private function generateRow($pub)
    {
        $statutConfig = [
            'publie' => ['text' => 'Publiée', 'type' => 'success'],
            'en_attente' => ['text' => 'En attente', 'type' => 'warning'],
            'rejete' => ['text' => 'Rejetée', 'type' => 'danger']
        ];
        
        $statut = $statutConfig[$pub['statut']] ?? ['text' => $pub['statut'], 'type' => 'info'];
        
        ob_start();
        ?>
<tr class="border-b hover:bg-gray-50"
    data-search="<?php echo strtolower($pub['titre'] . ' ' . ($pub['auteurs'] ?? '') . ' ' . ($pub['domaine'] ?? '')); ?>">
    <td class="px-6 py-4" data-sort="<?php echo $this->escape($pub['titre']); ?>">
        <div class="font-bold text-gray-900"><?php echo $this->escape($pub['titre']); ?></div>
        <?php if (!empty($pub['domaine'])): ?>
        <div class="text-sm text-gray-600"><?php echo $this->escape($pub['domaine']); ?></div>
        <?php endif; ?>
        <?php if (!empty($pub['doi'])): ?>
        <div class="text-xs text-gray-500 mt-1">DOI: <?php echo $this->escape($pub['doi']); ?></div>
        <?php endif; ?>
    </td>
    <td class="px-6 py-4" data-sort="<?php echo $this->escape($pub['auteurs'] ?? ''); ?>">
        <div class="text-sm"><?php echo $this->escape($pub['auteurs'] ?? 'Non spécifié'); ?></div>
        <?php if (!empty($pub['nb_auteurs'])): ?>
        <div class="text-xs text-gray-500"><?php echo $pub['nb_auteurs']; ?>
            auteur<?php echo $pub['nb_auteurs'] > 1 ? 's' : ''; ?></div>
        <?php endif; ?>
    </td>
    <td class="px-6 py-4 text-center" data-sort="<?php echo $pub['annee']; ?>">
        <span class="font-semibold text-gray-900"><?php echo $pub['annee']; ?></span>
    </td>
    <td class="px-6 py-4" data-sort="<?php echo $this->escape($pub['type_libelle'] ?? ''); ?>">
        <?php echo HtmlHelper::badge($pub['type_libelle'] ?? 'Non spécifié', 'primary'); ?>
    </td>
    <td class="px-6 py-4" data-sort="<?php echo $pub['statut']; ?>">
        <?php echo HtmlHelper::badge($statut['text'], $statut['type']); ?>
        <?php if ($pub['statut'] === 'en_attente'): ?>
        <div class="text-xs text-gray-500 mt-1">
            Soumis le <?php echo DateHelper::format($pub['date_soumission'], 'd/m/Y'); ?>
        </div>
        <?php endif; ?>
    </td>
    <td class="px-6 py-4">
        <div class="flex gap-2 items-center">
            <?php if ($pub['statut'] === 'en_attente'): ?>
            <a href="<?php echo BASE_URL . 'adminPublication/publish/' . $pub['id_publication']; ?>"
                onclick="return confirm('Publier cette publication ?')" title="Publier"
                class="text-green-600 hover:text-green-800">
                <?php echo HtmlHelper::icon('check', 'w-5 h-5'); ?>
            </a>
            <a href="<?php echo BASE_URL . 'adminPublication/reject/' . $pub['id_publication']; ?>"
                onclick="return confirm('Rejeter cette publication ?')" title="Rejeter"
                class="text-red-600 hover:text-red-800">
                <?php echo HtmlHelper::icon('close', 'w-5 h-5'); ?>
            </a>
            <?php endif; ?>

            <a href="<?php echo BASE_URL . 'adminPublication/edit/' . $pub['id_publication']; ?>" title="Modifier"
                class="text-blue-600 hover:text-blue-800">
                <?php echo HtmlHelper::icon('edit', 'w-5 h-5'); ?>
            </a>

            <a href="<?php echo BASE_URL . 'adminPublication/delete/' . $pub['id_publication']; ?>"
                onclick="return confirm('Supprimer cette publication ?')" title="Supprimer"
                class="text-red-600 hover:text-red-800">
                <?php echo HtmlHelper::icon('trash', 'w-5 h-5'); ?>
            </a>
        </div>
    </td>
</tr>
<?php
        return ob_get_clean();
    }
}
?>