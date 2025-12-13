<?php

require_once __DIR__ . '/../helpers/DateHelper.php';
require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/ImageHelper.php';
require_once 'components/Slider.php';
require_once 'components/ActualiteCard.php';
require_once 'components/EventCard.php';
require_once 'components/PartnerCard.php';
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
        
        $section = new Section([
            'title' => 'Actualités Récentes',
            'content' => function() use ($actualites) {
                if (!empty($actualites)) {
                    echo '<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">';
                    foreach ($actualites as $actu) {
                        ActualiteCard::renderFromData($actu);
                    }
                    echo '</div>';
                } else {
                    echo '<p class="text-gray-500 text-center">Aucune actualité disponible.</p>';
                }
            }
        ]);
        $section->render();
    }
    
    private function renderPresentationSection()
    {
        $section = new Section([
            'content' => function() {
                echo <<<HTML
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
        <a href="HTML;
                echo BASE_URL . 'team';
                echo <<<HTML
" class="inline-block mt-4 text-blue-600 hover:text-blue-800 font-semibold">
            Voir toutes les équipes →
        </a>
    </div>
</div>
HTML;
            }
        ]);
        $section->render();
    }
    
    private function renderEvenementsSection()
    {
        $evenements = $this->get('evenements') ?? [];
        $eventsPerPage = 3;
        
        $section = new Section([
            'title' => 'Événements à Venir',
            'content' => function() use ($evenements, $eventsPerPage) {
                if (!empty($evenements)) {
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
                            EventCard::renderFromData($event);
                        }
                        
                        echo '</div>';
                        echo '</div>';
                    }
                    
                    if ($totalPages > 1) {
                        echo '<div class="flex items-center justify-center gap-4 mt-8">';
                        echo '<button id="events-prev" class="p-2 rounded-full bg-gray-200 hover:bg-gray-300 transition disabled:opacity-50 disabled:cursor-not-allowed" disabled>';
                        echo HtmlHelper::icon('arrow-left', 'w-5 h-5');
                        echo '</button>';
                        echo '<div class="flex gap-2">';
                        for ($i = 0; $i < $totalPages; $i++) {
                            $activeClass = $i === 0 ? 'bg-blue-600' : 'bg-gray-300';
                            echo '<button class="page-indicator w-10 h-10 rounded-full ' . $activeClass . ' hover:bg-blue-500 transition text-white font-semibold" data-page="' . $i . '">';
                            echo ($i + 1);
                            echo '</button>';
                        }
                        $nextDisabled = $totalPages <= 1 ? 'disabled' : '';
                        echo '<button id="events-next" class="p-2 rounded-full bg-gray-200 hover:bg-gray-300 transition disabled:opacity-50 disabled:cursor-not-allowed" ' . $nextDisabled . '>';
                        echo HtmlHelper::icon('arrow-right', 'w-5 h-5');
                        echo '</button>';
                        echo '</div>';
                    }
                    
                    echo '</div>';
                    echo '<div class="text-center mt-8">';
                    echo '<a href="' . BASE_URL . 'event" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">';
                    echo 'Voir tous les événements';
                    echo '</a>';
                    echo '</div>';
                } else {
                    echo '<p class="text-gray-500 text-center mb-6">Aucun événement à venir.</p>';
                    echo '<div class="text-center">';
                    echo '<a href="' . BASE_URL . 'event" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition inline-block">';
                    echo 'Voir tous les événements';
                    echo '</a>';
                    echo '</div>';
                }
            }
        ]);
        $section->render();
    }
    
    private function renderPartenairesSection()
    {
        $partenaires = $this->get('partenaires') ?? [];
        $section = new Section([
            'title' => 'Nos Partenaires',
            'content' => function() use ($partenaires) {
                if (!empty($partenaires)) {
                    echo '<div class="space-y-6">';
                    foreach ($partenaires as $partner) {
                        PartnerCard::renderFromData($partner);
                    }
                    echo '</div>';
                } else {
                    echo '<p class="text-gray-500 text-center">Aucun partenaire enregistré.</p>';
                }
            }
        ]);
        $section->render();
    }
}
?>