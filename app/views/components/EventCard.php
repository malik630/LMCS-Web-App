<?php

class EventCard extends View
{
    public function render()
    {
        ?>
<div
    class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition transform hover:-translate-y-1 flex flex-col h-full">
    <?php $this->renderImage(); ?>
    <div class="p-6 flex flex-col flex-grow">
        <?php $this->renderBadge(); ?>
        <?php $this->renderTitle(); ?>
        <?php $this->renderDescription(); ?>
        <?php $this->renderDetails(); ?>
        <?php $this->renderFooter(); ?>
    </div>
</div>
<?php
    }
    
    protected function renderImage()
    {
            $src = ImageHelper::url('event.jpg');
            $alt = $this->escape($this->get('titre'));
            echo HtmlHelper::imageWithFallback($src, $alt, 'w-full h-48 object-cover');
    }
    
    protected function renderBadge()
    {
        if ($badge = $this->get('type_libelle')) {
            echo '<div class="mb-2">' . HtmlHelper::badge($badge, 'primary') . '</div>';
        }
    }
    
    protected function renderTitle()
    {
        if ($title = $this->get('titre')) {
            echo '<h3 class="text-xl font-bold mt-2 mb-3">' . $this->escape($title) . '</h3>';
        }
    }
    
    protected function renderDescription()
    {
        if ($description = $this->get('description')) {
            ?>
<div class="text-gray-600 mb-4 max-h-20 overflow-y-auto pr-2 custom-scrollbar flex-grow">
    <p><?php echo nl2br($this->escape($description)); ?></p>
</div>
<?php
        }
    }
    
    protected function renderDetails()
    {
        ?>
<div class="space-y-2 mb-4">
    <?php if ($dateDebut = $this->get('date_debut')): ?>
    <div class="flex items-center gap-2 text-sm text-gray-600">
        <?php echo HtmlHelper::icon('calendar'); ?>
        <span><?php echo DateHelper::format($dateDebut, 'd/m/Y'); ?></span>
        <?php if ($dateFin = $this->get('date_fin')): ?>
        <span>- <?php echo DateHelper::format($dateFin, 'd/m/Y'); ?></span>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($lieu = $this->get('lieu')): ?>
    <div class="flex items-center gap-2 text-sm text-gray-600">
        <?php echo HtmlHelper::icon('location'); ?>
        <span><?php echo $this->escape($lieu); ?></span>
    </div>
    <?php endif; ?>
</div>
<?php
    }
    
    protected function renderFooter()
    {
        $eventId = $this->get('id_evenement');
        
        if ($eventId) {
            ?>
<div class="mt-auto pt-4 border-t border-gray-200">
    <a href="<?php echo BASE_URL . 'event/register/' . $eventId; ?>"
        class="block w-full bg-blue-600 text-white text-center px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
        Participer
    </a>
</div>
<?php
        }
    }
    
    public static function renderFromData($event)
    {
        $card = new self([
            'image' => $event['image'] ?? null,
            'type_libelle' => $event['type_libelle'] ?? 'Événement',
            'titre' => $event['titre'] ?? '',
            'description' => $event['description'] ?? '',
            'date_debut' => $event['date_debut'] ?? null,
            'date_fin' => $event['date_fin'] ?? null,
            'lieu' => $event['lieu'] ?? null,
            'id_evenement' => $event['id_evenement'] ?? null
        ]);
        
        $card->render();
    }
}
?>