<?php

require_once __DIR__ . '/../helpers/DateHelper.php';
require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/ImageHelper.php';
require_once 'components/Slider.php';
require_once 'components/Card.php';
require_once 'components/Section.php';

class HomeView extends View
{
    protected $pageTitle = 'Accueil - LMCS';
    
    public function render()
    {
        $this->renderHeader();
        $this->renderSlider();
        $this->renderContent();
        $this->renderFooter();
    }
    
    private function renderSlider()
    {
        if (!empty($this->get('slider'))) {
            Slider::renderFromData($this->get('slider'));
        }
    }
    
    private function renderContent()
    {
        echo '<div class="container mx-auto px-4 py-8">';
        $this->renderActualitesSection();
        $this->renderPresentationSection();
        $this->renderEvenementsSection();
        $this->renderPartenairesSection();
        echo '</div>';
    }
    
    private function renderActualitesSection()
    {
        $actualites = $this->get('actualites') ?? [];
        
        Section::create('Actualités Récentes', function() use ($actualites) {
            if (!empty($actualites)) {
                echo '<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">';
                foreach ($actualites as $actu) {
                    Card::actualite($actu);
                }
                echo '</div>';
            } else {
                echo HtmlHelper::emptyState('Aucune actualité disponible.');
            }
        });
    }
    
    private function renderPresentationSection()
    {
        $director = $this->get('director');
        $chefEquipes = $this->get('chef_equipes') ?? [];

        Section::create(null, function () use ($director, $chefEquipes) {
            ?>
<h2 class="text-3xl font-bold mb-6">À propos du Laboratoire</h2>
<div class="grid md:grid-cols-2 gap-8">
    <div>
        <p class="text-gray-700 mb-4 leading-relaxed">
            Le laboratoire de méthodes de conception des systèmes de l'École Supérieure d'Informatique est un centre
            d'excellence dédié à la recherche fondamentale et appliquée dans divers domaines de l'informatique et des
            technologies de l'information.
        </p>
        <p class="text-gray-700 mb-4 leading-relaxed">
            Nos équipes de chercheurs travaillent sur des thématiques variées incluant l'intelligence artificielle,
            la sécurité informatique, les systèmes distribués, et bien d'autres domaines de pointe.
        </p>
    </div>
    <div>
        <h3 class="text-xl font-bold mb-4">Nos Équipes</h3>
        <div class="space-y-6 mb-6">
            <?php if ($director): ?>
            <div class="bg-blue-50 border-2 border-blue-600 rounded-lg p-4">
                <div class="text-xs font-semibold text-blue-600 mb-2 text-center">Directeur du laboratoire</div>
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        <?php 
                        ob_start();
                        ImageHelper::renderUserPhoto($director, 12);
                        $photoHtml = ob_get_clean();
                        echo str_replace('w-24 h-24', 'w-16 h-16', $photoHtml);
                        ?>
                    </div>
                    <div>
                        <div class="font-bold text-gray-900 text-sm">
                            <?php echo $this->escape($director['prenom'] . ' ' . $director['nom']); ?>
                        </div>
                        <div class="text-xs text-gray-600"><?php echo $this->escape($director['grade']); ?></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($chefEquipes)): ?>
            <div class="bg-green-50 border border-green-300 rounded-lg p-4">
                <div class="text-xs font-semibold text-green-700 mb-3 text-center">Chefs d'Équipes</div>
                <div class="space-y-3">
                    <?php foreach ($chefEquipes as $chef): ?>
                    <div class="flex items-center gap-3 pb-2 border-b border-green-200 last:border-0">
                        <div class="flex-shrink-0">
                            <?php 
                            ob_start();
                            ImageHelper::renderUserPhoto($chef, 12);
                            $photoHtml = ob_get_clean();
                            echo str_replace('w-24 h-24', 'w-12 h-12', $photoHtml);
                            ?>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 text-xs">
                                <?php echo $this->escape($chef['prenom'] . ' ' . $chef['nom']); ?>
                            </div>
                            <div class="text-xs text-green-700 font-medium">
                                <?php echo $this->escape($chef['team_nom']); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php echo HtmlHelper::linkWithIcon('Voir toutes les équipes', BASE_URL . 'team', 'arrow-right', 'text-blue-600 hover:text-blue-800 font-semibold mt-4'); ?>
    </div>
</div>
<?php
        });
    }
    
    private function renderEvenementsSection()
    {
        $evenements = $this->get('evenements') ?? [];
        $eventsPerPage = 3;
        
        Section::create('Événements à Venir', function() use ($evenements, $eventsPerPage) {
            if (!empty($evenements)) {
                $this->renderEventsPagination($evenements, $eventsPerPage);
            } else {
                echo HtmlHelper::emptyState('Aucun événement à venir.');
                echo '<div class="text-center mt-4">';
                echo HtmlHelper::button('Voir tous les événements', BASE_URL . 'event');
                echo '</div>';
            }
        });
    }
    
    private function renderEventsPagination($evenements, $eventsPerPage)
    {
        $totalEvents = count($evenements);
        $totalPages = ceil($totalEvents / $eventsPerPage);
        
        echo '<div id="events-container" class="relative">';
        
        for ($page = 0; $page < $totalPages; $page++) {
            $start = $page * $eventsPerPage;
            $pageEvents = array_slice($evenements, $start, $eventsPerPage);
            $displayClass = $page === 0 ? 'block' : 'hidden';
            
            echo '<div class="events-page ' . $displayClass . '" data-page="' . $page . '">';
            echo '<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">';
            
            foreach ($pageEvents as $event) {
                Card::event($event);
            }
            
            echo '</div></div>';
        }
        
        if ($totalPages > 1) {
            $this->renderPaginationControls($totalPages);
        }
        
        echo '</div>';
        echo '<div class="text-center mt-8">';
        echo HtmlHelper::button('Voir tous les événements', BASE_URL . 'event');
        echo '</div>';
    }
    
    private function renderPaginationControls($totalPages)
    {
        ?>
<div class="flex items-center justify-center gap-4 mt-8">
    <button id="events-prev"
        class="p-2 rounded-full bg-gray-200 hover:bg-gray-300 transition disabled:opacity-50 disabled:cursor-not-allowed"
        disabled>
        <?php echo HtmlHelper::icon('arrow-left'); ?>
    </button>
    <div class="flex gap-2">
        <?php for ($i = 0; $i < $totalPages; $i++): ?>
        <button
            class="page-indicator w-10 h-10 rounded-full <?php echo $i === 0 ? 'bg-blue-600' : 'bg-gray-300'; ?> hover:bg-blue-500 transition text-white font-semibold"
            data-page="<?php echo $i; ?>">
            <?php echo $i + 1; ?>
        </button>
        <?php endfor; ?>
    </div>
    <button id="events-next"
        class="p-2 rounded-full bg-gray-200 hover:bg-gray-300 transition disabled:opacity-50 disabled:cursor-not-allowed"
        <?php echo $totalPages <= 1 ? 'disabled' : ''; ?>>
        <?php echo HtmlHelper::icon('arrow-right'); ?>
    </button>
</div>
<?php
    }
    
    private function renderPartenairesSection()
    {
        $partenaires = $this->get('partenaires') ?? [];
        
        Section::create('Nos Partenaires', function() use ($partenaires) {
            if (!empty($partenaires)) {
                echo '<div class="space-y-6">';
                foreach ($partenaires as $partner) {
                    Card::partner($partner);
                }
                echo '</div>';
            } else {
                echo HtmlHelper::emptyState('Aucun partenaire enregistré.');
            }
        });
    }
}
?>