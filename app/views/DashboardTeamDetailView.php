<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/ImageHelper.php';
require_once __DIR__ . '/components/PageHeader.php';
require_once __DIR__ . '/components/Section.php';
require_once __DIR__ . '/components/InfoGrid.php';
require_once __DIR__ . '/components/ItemList.php';

class DashboardTeamDetailView extends View
{
    protected $pageTitle = 'Détails de l\'équipe - LMCS';
    
    public function render()
    {
        $this->renderHeader();
        $team = $this->get('team');
        $membres = $this->get('membres');
        $projets = $this->get('projets');
        $publications = $this->get('publications');
        $isChef = $this->get('is_chef');
        
        // Page header
        $badges = [];
        if ($isChef) {
            $badges[] = ['text' => 'Vous êtes le chef de cette équipe', 'type' => 'primary'];
        }
        
        PageHeader::render([
            'title' => $team['nom'],
            'back_link' => [
                'url' => BASE_URL . 'dashboardteam/index',
                'text' => 'Retour à mes équipes'
            ],
            'badges' => $badges
        ]);
        
        // Informations de l'équipe
        Section::create('Informations de l\'équipe', function() use ($team) {
            $items = array_filter([
                ['label' => 'Thématique', 'value' => $team['thematique'] ?? null],
                ['label' => 'Date de création', 'value' => $team['date_creation'], 'format' => 'date']
            ], fn($item) => !empty($item['value']));
            
            InfoGrid::renderWithDescription($items, $team['description'] ?? null);
        }, 'bg-white');
        
        // Membres
        $this->renderMembresSection($membres);
        
        // Projets
        $this->renderProjetsSection($projets);
        
        // Publications
        $this->renderPublicationsSection($publications);
        
        PageHeader::close();
        $this->renderFooter();
    }
    
    private function renderMembresSection($membres)
    {
        Section::create('Membres de l\'équipe (' . count($membres) . ')', function() use ($membres) {
            ItemList::render([
                'items' => $membres,
                'layout' => 'grid',
                'columns' => 3,
                'empty_message' => 'Aucun membre dans cette équipe',
                'renderer' => function($membre) {
                    ?>
<div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
    <div class="flex items-center gap-3 mb-3">
        <?php if (!empty($membre['photo'])): ?>
        <?php echo ImageHelper::renderUserPhoto($membre); ?>
        <?php endif; ?>
        <div>
            <h4 class="font-semibold text-gray-900">
                <?php echo htmlspecialchars($membre['prenom'] . ' ' . $membre['nom']); ?>
            </h4>
            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($membre['grade']); ?></p>
        </div>
    </div>
    <?php if (!empty($membre['role_dans_equipe'])): ?>
    <p class="text-sm text-gray-600">
        <span class="font-semibold">Rôle:</span> <?php echo htmlspecialchars($membre['role_dans_equipe']); ?>
    </p>
    <?php endif; ?>
    <p class="text-xs text-gray-500 mt-2">
        Membre depuis <?php echo date('d/m/Y', strtotime($membre['date_adhesion'])); ?>
    </p>
</div>
<?php
                }
            ]);
        }, 'bg-white');
    }
    
    private function renderProjetsSection($projets)
    {
        Section::create('Projets de l\'équipe (' . count($projets) . ')', function() use ($projets) {
            ItemList::render([
                'items' => $projets,
                'layout' => 'stack',
                'empty_message' => 'Aucun projet associé à cette équipe',
                'renderer' => function($projet) {
                    ?>
<div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
    <div class="flex justify-between items-start mb-2">
        <h4 class="font-bold text-gray-900 text-lg"><?php echo htmlspecialchars($projet['titre']); ?></h4>
        <?php echo HtmlHelper::badge(ucfirst($projet['statut']), 'info'); ?>
    </div>
    <?php if (!empty($projet['description'])): ?>
    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
        <?php echo htmlspecialchars(substr($projet['description'], 0, 150)); ?>
    </p>
    <?php endif; ?>
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-600">
            <span class="font-semibold">Responsable:</span>
            <?php echo htmlspecialchars($projet['responsable_prenom'] . ' ' . $projet['responsable_nom']); ?>
        </p>
        <?php if (!empty($projet['nb_membres_equipe'])): ?>
        <span class="text-sm text-blue-600 font-semibold">
            <?php echo $projet['nb_membres_equipe']; ?> membre(s) de l'équipe
        </span>
        <?php endif; ?>
    </div>
</div>
<?php
                }
            ]);
        }, 'bg-white');
    }
    
    private function renderPublicationsSection($publications)
    {
        Section::create('Publications de l\'équipe (' . count($publications) . ')', function() use ($publications) {
            ItemList::render([
                'items' => $publications,
                'layout' => 'stack',
                'empty_message' => 'Aucune publication associée à cette équipe',
                'renderer' => function($pub) {
                    ?>
<div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
    <div class="flex justify-between items-start mb-2">
        <h4 class="font-bold text-gray-900"><?php echo htmlspecialchars($pub['titre']); ?></h4>
        <span class="text-sm font-semibold text-gray-600"><?php echo $pub['annee']; ?></span>
    </div>
    <p class="text-sm text-gray-600 mb-2">
        <span class="font-semibold">Type:</span>
        <?php echo htmlspecialchars($pub['type_libelle'] ?? 'Non spécifié'); ?>
    </p>
    <?php if (!empty($pub['auteurs'])): ?>
    <p class="text-sm text-gray-600 mb-2">
        <span class="font-semibold">Auteurs:</span> <?php echo htmlspecialchars($pub['auteurs']); ?>
    </p>
    <?php endif; ?>
    <?php if (!empty($pub['nb_auteurs_equipe'])): ?>
    <span class="text-sm text-green-600 font-semibold">
        <?php echo $pub['nb_auteurs_equipe']; ?> auteur(s) de l'équipe
    </span>
    <?php endif; ?>
</div>
<?php
                }
            ]);
        }, 'bg-white');
    }
}
?>