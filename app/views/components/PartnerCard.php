<?php

class PartnerCard extends View
{
    public function render()
    {
        ?>
<div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition">
    <div class="flex flex-col md:flex-row gap-6">
        <div class="flex-grow">
            <?php $this->renderHeader(); ?>
            <?php $this->renderDescription(); ?>
            <?php $this->renderMetadata(); ?>
        </div>
        <?php $this->renderLogo(); ?>
    </div>
</div>
<?php
    }
    
    protected function renderHeader()
    {
        ?>
<div class="flex items-center gap-3 mb-3">
    <h3 class="text-xl font-bold text-gray-900">
        <?php echo $this->escape($this->get('nom')); ?>
    </h3>
</div>
<?php
    }
    
    protected function renderDescription()
    {
        if ($description = $this->get('description')) {
            ?>
<p class="text-gray-600 mb-4 leading-relaxed">
    <?php echo $this->escape($description); ?>
</p>
<?php
        }
    }
    
    protected function renderMetadata()
    {
        ?>
<div class="flex flex-wrap gap-4 text-sm text-gray-500">
    <?php if ($pays = $this->get('pays')): ?>
    <div class="flex items-center gap-2">
        <?php echo HtmlHelper::icon('location'); ?>
        <span><?php echo $this->escape($pays); ?></span>
    </div>
    <?php endif; ?>

    <?php if ($date = $this->get('date_partenariat')): ?>
    <div class="flex items-center gap-2">
        <?php echo HtmlHelper::icon('calendar'); ?>
        <span>Partenariat depuis <?php echo DateHelper::format($date, 'Y'); ?></span>
    </div>
    <?php endif; ?>

    <?php if ($site = $this->get('site_web')): ?>
    <a href="<?php echo $this->escape($site); ?>" target="_blank" rel="noopener noreferrer"
        class="flex items-center gap-2 text-blue-600 hover:text-blue-800 transition">
        <?php echo HtmlHelper::icon('external-link'); ?>
        <span>Visiter le site</span>
    </a>
    <?php endif; ?>
</div>
<?php
    }
    
    protected function renderLogo()
    {
        ?>
<div class="flex-shrink-0 flex items-center justify-center md:w-48">
    <?php if ($logo = $this->get('logo')): ?>
    <?php 
        $src = ImageHelper::url($logo);
        $alt = 'Logo ' . $this->escape($this->get('nom'));
    ?>
    <img src="<?php echo $src; ?>" alt="<?php echo $alt; ?>"
        class="max-h-32 max-w-full object-contain grayscale hover:grayscale-0 transition"
        onerror="this.outerHTML='<div class=\'text-center text-gray-400 text-sm p-4 border-2 border-dashed border-gray-300 rounded\'><?php echo $this->escape($this->get('nom')); ?></div>'">
    <?php else: ?>
    <div class="text-center text-gray-400 text-sm p-4 border-2 border-dashed border-gray-300 rounded w-full">
        <?php echo $this->escape($this->get('nom')); ?>
    </div>
    <?php endif; ?>
</div>
<?php
    }
    
    public static function renderFromData($partner)
    {
        $card = new self($partner);
        $card->render();
    }
}
?>