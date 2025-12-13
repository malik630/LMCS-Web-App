<?php

class ActualiteCard extends View
{
        public function render()
    {
        ?>
<div
    class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition transform hover:-translate-y-1 flex flex-col">
    <?php $this->renderImage(); ?>
    <div class="p-6 flex flex-col flex-grow">
        <?php $this->renderBadge(); ?>
        <?php $this->renderTitle(); ?>
        <?php $this->renderContent(); ?>
        <?php $this->renderFooter(); ?>
    </div>
</div>
<?php
    }
    
    protected function renderImage()
    {
        if (!empty($this->get('image'))) {
            $src = ImageHelper::url($this->get('image'));
            $alt = $this->escape($this->get('title'));
            echo HtmlHelper::imageWithFallback($src, $alt, 'w-full h-48 object-cover');
        }
    }
    
    protected function renderBadge()
    {
        if ($badge = $this->get('badge')) {
            echo '<div class="mb-2">' . HtmlHelper::badge($badge, $this->get('badge_type', 'primary')) . '</div>';
        }
    }
    
    protected function renderTitle()
    {
        if ($title = $this->get('title')) {
            echo '<h3 class="text-xl font-bold mt-2 mb-3">' . $this->escape($title) . '</h3>';
        }
    }
    
    protected function renderContent()
    {
        if ($content = $this->get('content')) {
            ?>
<div class="text-gray-600 mb-4 max-h-24 overflow-y-auto pr-2 custom-scrollbar flex-grow">
    <p><?php echo nl2br($this->escape($content)); ?></p>
</div>
<?php
        }
    }
    
    protected function renderFooter()
    {
        $link = $this->get('link');
        $date = $this->get('date');
        
        if ($link || $date) {
            ?>
<div class="mt-auto">
    <?php if ($link): ?>
    <a href="<?php echo $this->escape($link); ?>"
        class="text-blue-600 font-semibold hover:text-blue-800 transition inline-block mb-3">
        En savoir plus →
    </a>
    <?php endif; ?>

    <?php if ($date): ?>
    <p class="text-sm text-gray-500"><?php echo DateHelper::format($date); ?></p>
    <?php endif; ?>
</div>
<?php
        }
    }
    public static function renderFromData($actu)
    {
        $card = new self([
            'image' => $actu['image'] ?? null,
            'badge' => $actu['type_libelle'] ?? 'Actualité',
            'badge_type' => 'primary',
            'title' => $actu['titre'] ?? '',
            'content' => $actu['contenu'] ?? '',
            'link' => !empty($actu['detail']) ? BASE_URL . 'actualite/view/' . $actu['id_actualite'] : null,
            'date' => $actu['date_publication'] ?? null
        ]);
        
        $card->render();
    }
}
?>