<?php

class ItemList
{
    public static function render(array $config)
    {
        $items = $config['items'] ?? [];
        $layout = $config['layout'] ?? 'grid';
        $columns = $config['columns'] ?? 3;
        $renderer = $config['renderer'] ?? null;
        $emptyMessage = $config['empty_message'] ?? 'Aucun élément';
        
        if (empty($items)) {
            echo HtmlHelper::emptyState($emptyMessage);
            return;
        }
        
        if ($layout === 'grid') {
            echo '<div class="grid md:grid-cols-2 lg:grid-cols-' . $columns . ' gap-4">';
        } else {
            echo '<div class="space-y-4">';
        }
        
        foreach ($items as $item) {
            if ($renderer && is_callable($renderer)) {
                $renderer($item);
            } else {
                self::renderDefaultItem($item);
            }
        }
        
        echo '</div>';
    }
    
    private static function renderDefaultItem($item)
    {
        ?>
<div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
    <?php if (!empty($item['title'])): ?>
    <h4 class="font-semibold text-gray-900 mb-2"><?php echo htmlspecialchars($item['title']); ?></h4>
    <?php endif; ?>

    <?php if (!empty($item['description'])): ?>
    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($item['description']); ?></p>
    <?php endif; ?>

    <?php if (!empty($item['actions'])): ?>
    <div class="mt-3 flex gap-2">
        <?php foreach ($item['actions'] as $action): ?>
        <a href="<?php echo htmlspecialchars($action['url']); ?>" class="text-sm text-blue-600 hover:text-blue-800">
            <?php echo htmlspecialchars($action['text']); ?>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?php
    }
    
    public static function renderWithAddForm(array $config)
    {
        self::render($config);
        
        if (!empty($config['add_form'])) {
            self::renderAddForm($config['add_form']);
        }
    }
    
    private static function renderAddForm(array $formConfig)
    {
        $title = $formConfig['title'] ?? 'Ajouter un élément';
        $action = $formConfig['action'] ?? '';
        $fields = $formConfig['fields'] ?? [];
        $buttonText = $formConfig['button_text'] ?? 'Ajouter';
        
        ?>
<div class="mt-6 p-4 bg-blue-50 rounded-lg">
    <h4 class="font-semibold text-gray-900 mb-3"><?php echo htmlspecialchars($title); ?></h4>
    <form action="<?php echo htmlspecialchars($action); ?>" method="POST" class="flex gap-3 flex-wrap">
        <?php foreach ($fields as $field): ?>
        <?php self::renderFormField($field); ?>
        <?php endforeach; ?>

        <button type="submit"
            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
            <?php echo htmlspecialchars($buttonText); ?>
        </button>
    </form>
</div>
<?php
    }
    
    private static function renderFormField(array $field)
    {
        $type = $field['type'] ?? 'text';
        $class = $field['class'] ?? 'px-4 py-2 border border-gray-300 rounded-lg';
        
        if ($type === 'select') {
            ?>
<select name="<?php echo htmlspecialchars($field['name']); ?>"
    <?php echo !empty($field['required']) ? 'required' : ''; ?> class="<?php echo $class; ?>">
    <?php if (!empty($field['empty_option'])): ?>
    <option value=""><?php echo htmlspecialchars($field['empty_option']); ?></option>
    <?php endif; ?>

    <?php foreach ($field['options'] as $value => $label): ?>
    <option value="<?php echo htmlspecialchars($value); ?>">
        <?php echo htmlspecialchars($label); ?>
    </option>
    <?php endforeach; ?>
</select>
<?php
        } else {
            ?>
<input type="<?php echo htmlspecialchars($type); ?>" name="<?php echo htmlspecialchars($field['name']); ?>"
    placeholder="<?php echo htmlspecialchars($field['placeholder'] ?? ''); ?>"
    <?php echo !empty($field['required']) ? 'required' : ''; ?> class="<?php echo $class; ?>">
<?php
        }
    }
}
?>