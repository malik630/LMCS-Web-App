<?php

class Slider extends View
{
    public function render()
    {
        if (empty($this->data)) return;
        
        ?>
<div class="relative bg-white shadow-lg overflow-hidden" style="height: 400px;">
    <div id="slider" class="relative h-full">
        <?php $this->renderSlides(); ?>
    </div>
    <?php $this->renderControls(); ?>
</div>
<?php
    }
    
    protected function renderSlides()
    {
        foreach ($this->data as $slide) {
            ?>
<div class="slide absolute w-full h-full">
    <?php $this->renderSlideImage($slide); ?>
    <?php $this->renderSlideContent($slide); ?>
</div>
<?php
        }
    }
    
    protected function renderSlideImage($slide)
    {
        if (!empty($slide['image'])) {
            $src = ImageHelper::url($slide['image']);
            $alt = $this->escape($slide['titre'] ?? 'Slide');
            $fallback = ImageHelper::placeholder(800, 400, '#667eea');
            echo "<img src='{$src}' alt='{$alt}' class='w-full h-full object-cover' onerror=\"this.src='{$fallback}'\">";
        } else {
            echo '<div class="w-full h-full bg-gradient-to-r from-blue-600 to-purple-600"></div>';
        }
    }
    
    protected function renderSlideContent($slide)
    {
        ?>
<div class="absolute inset-0 bg-black bg-opacity-40 flex items-center">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl text-white">
            <?php echo HtmlHelper::badge($slide['type_libelle'] ?? 'Actualité', 'primary'); ?>

            <h2 class="text-4xl font-bold mt-4 mb-3">
                <?php echo $this->escape($slide['titre'] ?? ''); ?>
            </h2>

            <div class="text-lg mb-4 max-h-32 overflow-y-auto pr-2 custom-scrollbar">
                <p><?php echo nl2br($this->escape($slide['contenu'] ?? '')); ?></p>
            </div>

            <?php if (!empty($slide['detail'])): ?>
            <a href="<?php echo BASE_URL . 'actualite/view/' . $slide['id_actualite']; ?>"
                class="bg-white text-blue-600 px-6 py-2 rounded font-semibold hover:bg-blue-50 transition inline-block">
                En savoir plus
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
    }
    
    protected function renderControls()
    {
        ?>
<button id="slide-prev"
    class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-50 hover:bg-opacity-75 text-gray-800 p-3 rounded-full transition z-[3]">
    <?php echo HtmlHelper::icon('arrow-left', 'w-6 h-6'); ?>
</button>
<button id="slide-next"
    class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-50 hover:bg-opacity-75 text-gray-800 p-3 rounded-full transition z-[3]">
    <?php echo HtmlHelper::icon('arrow-right', 'w-6 h-6'); ?>
</button>
<?php
    }
    
    public static function renderFromData($slides)
    {
        $slider = new self($slides);
        $slider->render();
    }
}
?>