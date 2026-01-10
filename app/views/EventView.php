<?php

require_once __DIR__ . '/../helpers/DateHelper.php';
require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/components/Card.php';
require_once __DIR__ . '/components/Section.php';

class EventView extends View
{
    protected $pageTitle = 'Événements - LMCS';
    
    public function render()
    {
        $this->renderHeader();
        echo '<div class="container mx-auto px-4 py-8">';
        $this->renderPageHeader();
        $this->renderEvents();
        echo '</div>';
        $this->renderFooter();
    }
    
    private function renderPageHeader()
    {
        ?>
<div class="mb-8">
    <h1 class="text-4xl font-bold text-white mb-3">Événements</h1>
    <p class="text-blue-100 text-lg">Découvrez nos événements scientifiques et académiques</p>
</div>
<?php
    }
    
    private function renderEvents()
    {
        $events = $this->get('events', []);
        
        if (empty($events)) {
            Section::create('Liste des événements', function() {
                echo HtmlHelper::emptyState('Aucun événement disponible');
            });
            return;
        }
        
        Section::create('Liste des événements', function() use ($events) {
            echo '<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">';
            foreach ($events as $event) {
                Card::event($event);
            }
            echo '</div>';
        }, 'bg-white');
    }
}
?>