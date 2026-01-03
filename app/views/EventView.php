<?php

require_once __DIR__ . '/../helpers/DateHelper.php';
require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once 'components/Section.php';

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
                $this->renderEventCard($event);
            }
            echo '</div>';
        }, 'bg-white');
    }
    
    private function renderEventCard($event)
    {
        $statutConfig = [
            'a_venir' => ['text' => 'À venir', 'type' => 'primary'],
            'en_cours' => ['text' => 'En cours', 'type' => 'success'],
            'termine' => ['text' => 'Terminé', 'type' => 'info'],
            'annule' => ['text' => 'Annulé', 'type' => 'danger']
        ];
        
        $statut = $statutConfig[$event['statut']] ?? ['text' => $event['statut'], 'type' => 'info'];
        
        ?>
<div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition transform hover:-translate-y-1">
    <div class="p-6">
        <div class="flex gap-2 mb-3">
            <?php echo HtmlHelper::badge($statut['text'], $statut['type']); ?>
            <?php if (!empty($event['type_libelle'])): ?>
            <?php echo HtmlHelper::badge(ucfirst($event['type_libelle']), 'orange'); ?>
            <?php endif; ?>
            <?php if ($event['externe']): ?>
            <?php echo HtmlHelper::badge('Ouvert au public', 'success'); ?>
            <?php else: ?>
            <?php echo HtmlHelper::badge('Interne', 'warning'); ?>
            <?php endif; ?>
        </div>

        <h3 class="text-xl font-bold mb-3"><?php echo $this->escape($event['titre']); ?></h3>

        <?php if (!empty($event['description'])): ?>
        <p class="text-gray-600 mb-4 line-clamp-3"><?php echo $this->escape($event['description']); ?></p>
        <?php endif; ?>

        <div class="space-y-2 mb-4 text-sm text-gray-600">
            <div class="flex items-center gap-2">
                <?php echo HtmlHelper::icon('calendar'); ?>
                <span>
                    <?php echo DateHelper::format($event['date_debut'], 'd/m/Y H:i'); ?>
                    <?php if ($event['date_fin']): ?>
                    - <?php echo DateHelper::format($event['date_fin'], 'd/m/Y H:i'); ?>
                    <?php endif; ?>
                </span>
            </div>

            <?php if (!empty($event['lieu'])): ?>
            <div class="flex items-center gap-2">
                <?php echo HtmlHelper::icon('location'); ?>
                <span><?php echo $this->escape($event['lieu']); ?></span>
            </div>
            <?php endif; ?>

            <?php if ($event['capacite_max']): ?>
            <div class="flex items-center gap-2">
                <?php echo HtmlHelper::icon('user'); ?>
                <span>Capacité : <?php echo $event['capacite_max']; ?> places</span>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($event['statut'] === 'a_venir'): ?>
        <div class="pt-4 border-t border-gray-200">
            <?php echo HtmlHelper::button(
                'S\'inscrire',
                BASE_URL . 'event/register/' . $event['id_evenement'],
                'primary',
                'check',
                ['class' => 'w-full justify-center']
            ); ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php
    }
}
?>