<?php

class Section extends View
{
    public function render()
    {
        $title = $this->get('title');
        $content = $this->get('content');
        $bgClass = $this->get('bg_class', 'bg-white');
        
        ?>
<section class="mb-12">
    <?php if ($title): ?>
    <h2 class="text-3xl font-bold text-white mb-6"><?php echo $this->escape($title); ?></h2>
    <?php endif; ?>

    <div class="<?php echo $bgClass; ?> rounded-lg shadow-lg p-8">
        <?php 
        if (is_callable($content)) {
            $content();
        } else {
            echo $content;
        }
        ?>
    </div>
</section>
<?php
    }
    
    public static function create($title, $content, $bgClass = 'bg-white')
    {
        $section = new self([
            'title' => $title,
            'content' => $content,
            'bg_class' => $bgClass
        ]);
        $section->render();
    }
}
?>