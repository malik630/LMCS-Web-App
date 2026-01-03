<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';

class AdminDashboardView extends View
{
    protected $pageTitle = 'Administration - LMCS';
    
    private $categories = [
        [
            'title' => 'Utilisateurs',
            'description' => 'Gérer les comptes et permissions',
            'icon' => 'users',
            'url' => 'admin/users',
            'color' => 'blue'
        ],
        [
            'title' => 'Projets',
            'description' => 'Superviser les projets de recherche',
            'icon' => 'chart',
            'url' => 'admin/projets',
            'color' => 'blue'
        ],
        [
            'title' => 'Publications',
            'description' => 'Valider les publications',
            'icon' => 'document',
            'url' => 'admin/publications',
            'color' => 'blue'
        ],
        [
            'title' => 'Équipements',
            'description' => 'Gérer le matériel',
            'icon' => 'briefcase',
            'url' => 'admin/equipements',
            'color' => 'blue'
        ],
        [
            'title' => 'Événements',
            'description' => 'Organiser les événements',
            'icon' => 'calendar',
            'url' => 'admin/evenements',
            'color' => 'blue'
        ],
        [
            'title' => 'Équipes',
            'description' => 'Gérer les équipes de recherche',
            'icon' => 'users',
            'url' => 'admin/equipes',
            'color' => 'blue'
        ],
        [
            'title' => 'Contenu',
            'description' => 'Actualités et partenaires',
            'icon' => 'document',
            'url' => 'admin/contenu',
            'color' => 'blue'
        ]
    ];
    
    public function render()
    {
        $this->renderHeader();
        echo '<div class="container mx-auto px-4 py-8">';
        $this->renderPageHeader();
        $this->renderCategoriesGrid();
        echo '</div>';
        $this->renderFooter();
    }
    
    private function renderPageHeader()
    {
        ?>
<div class="mb-12">
    <h1 class="text-4xl font-bold text-white mb-3">Panneau d'Administration</h1>
    <p class="text-blue-100 text-lg">Gestion complète du laboratoire LMCS</p>
</div>
<?php
    }
    
    private function renderCategoriesGrid()
    {
        ?>
<div class="mb-12">
    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <?php for ($i = 0; $i < 2; $i++): ?>
        <?php if (isset($this->categories[$i])): ?>
        <?php $this->renderCategoryCard($this->categories[$i], 'large'); ?>
        <?php endif; ?>
        <?php endfor; ?>
    </div>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <?php for ($i = 2; $i < 4; $i++): ?>
        <?php if (isset($this->categories[$i])): ?>
        <?php $this->renderCategoryCard($this->categories[$i], 'large'); ?>
        <?php endif; ?>
        <?php endfor; ?>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        <?php for ($i = 4; $i < 7; $i++): ?>
        <?php if (isset($this->categories[$i])): ?>
        <?php $this->renderCategoryCard($this->categories[$i], 'medium'); ?>
        <?php endif; ?>
        <?php endfor; ?>
    </div>
</div>
<?php
    }
    
    private function renderCategoryCard($category, $size = 'large')
    {
        $heightClass = $size === 'large' ? 'min-h-[180px]' : 'min-h-[160px]';
        ?>
<a href="<?php echo BASE_URL . $category['url']; ?>"
    class="group block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden transition-all duration-200 hover:shadow-md hover:border-blue-400 <?php echo $heightClass; ?>">

    <div class="p-6 flex flex-col h-full">
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center text-white">
                    <?php echo HtmlHelper::icon($category['icon'], 'w-6 h-6'); ?>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-1">
                        <?php echo $this->escape($category['title']); ?>
                    </h3>
                    <p class="text-sm text-gray-600">
                        <?php echo $this->escape($category['description']); ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
            <span
                class="text-sm font-medium text-gray-400 flex items-center gap-1 group-hover:text-blue-600 transition-colors">
                Gérer
                <?php echo HtmlHelper::icon('arrow-right', 'w-4 h-4'); ?>
            </span>
        </div>
    </div>
</a>
<?php
    }
}
?>