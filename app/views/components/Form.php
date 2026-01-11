<?php

require_once __DIR__ . '/../../helpers/HtmlHelper.php';
class Form
{
    private $config = [];
    
    public function __construct(array $config)
    {
        $this->config = array_merge([
            'action' => '',
            'method' => 'POST',
            'enctype' => null,
            'class' => 'bg-white rounded-lg shadow-lg p-8 max-w-4xl',
            'fields' => [],
            'buttons' => [],
            'note' => null
        ], $config);
    }
    
    public static function render(array $config)
    {
        $form = new self($config);
        $form->display();
    }
    
    private function display()
    {
        echo '<div class="' . $this->config['class'] . '">';
        echo '<form action="' . htmlspecialchars($this->config['action']) . '" ';
        echo 'method="' . htmlspecialchars($this->config['method']) . '"';
        
        if ($this->config['enctype']) {
            echo ' enctype="' . htmlspecialchars($this->config['enctype']) . '"';
        }
        
        echo '>';
        
        $this->renderFields();
        $this->renderButtons();
        $this->renderNote();
        
        echo '</form>';
        echo '</div>';
    }
    
    private function renderFields()
    {
        foreach ($this->config['fields'] as $field) {
            $this->renderField($field);
        }
    }
    
    private function renderField(array $field)
    {
        $type = $field['type'] ?? 'text';
        $containerClass = $field['container_class'] ?? 'mb-6';
        
        echo '<div class="' . $containerClass . '">';
        
        switch ($type) {
            case 'grid':
                $this->renderGridFields($field);
                break;
            case 'textarea':
                $this->renderTextarea($field);
                break;
            case 'select':
                $this->renderSelect($field);
                break;
            case 'file':
                $this->renderFileInput($field);
                break;
            case 'date':
                $this->renderDateInput($field);
                break;
            case 'number':
                $this->renderNumberInput($field);
                break;
            case 'radio':
                $this->renderRadioGroup($field);
                break;
            default:
                $this->renderTextInput($field);
        }
        
        echo '</div>';
    }
    
    private function renderGridFields(array $field)
    {
        $columns = $field['columns'] ?? 2;
        $gap = $field['gap'] ?? 6;
        
        echo '<div class="grid md:grid-cols-' . $columns . ' gap-' . $gap . '">';
        
        foreach ($field['fields'] as $subField) {
            echo '<div>';
            $this->renderField(array_merge($subField, ['container_class' => '']));
            echo '</div>';
        }
        
        echo '</div>';
    }
    
    private function renderLabel(array $field)
    {
        if (empty($field['label'])) return;
        
        $required = !empty($field['required']) ? ' <span class="text-red-500">*</span>' : '';
        echo '<label class="block font-medium mb-2 text-gray-700">';
        echo htmlspecialchars($field['label']) . $required;
        echo '</label>';
    }
    
    private function renderTextInput(array $field)
    {
        $this->renderLabel($field);
        
        $inputClass = $field['class'] ?? 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent';
        
        echo '<input type="' . htmlspecialchars($field['type'] ?? 'text') . '" ';
        echo 'name="' . htmlspecialchars($field['name']) . '" ';
        
        if (!empty($field['value'])) {
            echo 'value="' . htmlspecialchars($field['value']) . '" ';
        }
        
        if (!empty($field['placeholder'])) {
            echo 'placeholder="' . htmlspecialchars($field['placeholder']) . '" ';
        }
        
        if (!empty($field['required'])) {
            echo 'required ';
        }
        
        echo 'class="' . $inputClass . '">';
        
        $this->renderHelper($field);
    }
    
    private function renderTextarea(array $field)
    {
        $this->renderLabel($field);
        
        $rows = $field['rows'] ?? 5;
        $inputClass = $field['class'] ?? 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent';
        
        echo '<textarea name="' . htmlspecialchars($field['name']) . '" ';
        echo 'rows="' . $rows . '" ';
        
        if (!empty($field['required'])) {
            echo 'required ';
        }
        
        echo 'class="' . $inputClass . '">';
        
        if (!empty($field['value'])) {
            echo htmlspecialchars($field['value']);
        }
        
        echo '</textarea>';
        
        $this->renderHelper($field);
    }
    
    private function renderSelect(array $field)
    {
        $this->renderLabel($field);
        
        $inputClass = $field['class'] ?? 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent';
        $multiple = !empty($field['multiple']);
        
        echo '<select name="' . htmlspecialchars($field['name']) . ($multiple ? '[]' : '') . '" ';
        
        if ($multiple) {
            echo 'multiple ';
            if (!empty($field['size'])) {
                echo 'size="' . intval($field['size']) . '" ';
            }
        }
        
        if (!empty($field['required'])) {
            echo 'required ';
        }
        
        echo 'class="' . $inputClass . '">';
        
        if (!empty($field['empty_option'])) {
            echo '<option value="">' . htmlspecialchars($field['empty_option']) . '</option>';
        }
        
        foreach ($field['options'] as $value => $label) {
            $selected = '';
            if (isset($field['value'])) {
                if (is_array($field['value'])) {
                    $selected = in_array($value, $field['value']) ? ' selected' : '';
                } else {
                    $selected = ($value == $field['value']) ? ' selected' : '';
                }
            }
            
            echo '<option value="' . htmlspecialchars($value) . '"' . $selected . '>';
            echo htmlspecialchars($label);
            echo '</option>';
        }
        
        echo '</select>';
        
        $this->renderHelper($field);
    }
    
    private function renderFileInput(array $field)
    {
        $this->renderLabel($field);
        
        if (!empty($field['current_file'])) {
            echo '<div class="mb-3 p-3 bg-gray-50 rounded-lg">';
            echo '<p class="text-sm text-gray-600 mb-1">Fichier actuel :</p>';
            echo '<div class="flex items-center gap-2">';
            echo '<span class="text-sm font-medium text-gray-900">' . htmlspecialchars($field['current_file']) . '</span>';
            
            if (!empty($field['current_file_url'])) {
                echo '<a href="' . htmlspecialchars($field['current_file_url']) . '" target="_blank" ';
                echo 'class="text-blue-600 hover:text-blue-800 text-sm">Télécharger</a>';
            }
            
            echo '</div>';
            echo '</div>';
        }
        
        $inputClass = $field['class'] ?? 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent';
        
        echo '<input type="file" ';
        echo 'name="' . htmlspecialchars($field['name']) . '" ';
        
        if (!empty($field['accept'])) {
            echo 'accept="' . htmlspecialchars($field['accept']) . '" ';
        }
        
        if (!empty($field['required'])) {
            echo 'required ';
        }
        
        echo 'class="' . $inputClass . '">';
        
        $this->renderHelper($field);
    }
    
    private function renderDateInput(array $field)
    {
        $this->renderLabel($field);
        
        $inputClass = $field['class'] ?? 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent';
        
        echo '<input type="date" ';
        echo 'name="' . htmlspecialchars($field['name']) . '" ';
        
        if (!empty($field['value'])) {
            echo 'value="' . htmlspecialchars($field['value']) . '" ';
        }
        
        if (!empty($field['required'])) {
            echo 'required ';
        }
        
        echo 'class="' . $inputClass . '">';
        
        $this->renderHelper($field);
    }
    
    private function renderNumberInput(array $field)
    {
        $this->renderLabel($field);
        
        $inputClass = $field['class'] ?? 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent';
        
        echo '<input type="number" ';
        echo 'name="' . htmlspecialchars($field['name']) . '" ';
        
        if (!empty($field['value'])) {
            echo 'value="' . htmlspecialchars($field['value']) . '" ';
        }
        
        if (isset($field['min'])) {
            echo 'min="' . htmlspecialchars($field['min']) . '" ';
        }
        
        if (isset($field['max'])) {
            echo 'max="' . htmlspecialchars($field['max']) . '" ';
        }
        
        if (isset($field['step'])) {
            echo 'step="' . htmlspecialchars($field['step']) . '" ';
        }
        
        if (!empty($field['required'])) {
            echo 'required ';
        }
        
        echo 'class="' . $inputClass . '">';
        
        $this->renderHelper($field);
    }

    private function renderRadioGroup(array $field)
    {
        $this->renderLabel($field);

        foreach ($field['options'] as $value => $label) {
            $checked = ($field['value'] ?? '') === $value ? 'checked' : '';

            echo '<label class="inline-flex items-center mr-4">';
            echo '<input type="radio" name="' . htmlspecialchars($field['name']) . '" ';
            echo 'value="' . htmlspecialchars($value) . '" ' . $checked . ' ';
            if (!empty($field['required'])) echo 'required ';
            echo 'class="text-green-600">';
            echo '<span class="ml-2">' . htmlspecialchars($label) . '</span>';
            echo '</label>';
        }

        $this->renderHelper($field);
    }

    
    private function renderHelper(array $field)
    {
        if (!empty($field['helper'])) {
            echo '<p class="text-sm text-gray-500 mt-1">' . htmlspecialchars($field['helper']) . '</p>';
        }
    }
    
    private function renderButtons()
    {
        if (empty($this->config['buttons'])) return;
        
        echo '<div class="flex gap-4 pt-4">';
        
        foreach ($this->config['buttons'] as $button) {
            $this->renderButton($button);
        }
        
        echo '</div>';
    }
    
    private function renderButton(array $button)
    {
        $type = $button['type'] ?? 'button';
        $style = $button['style'] ?? 'primary';
        
        $colors = [
            'primary' => 'bg-green-600 hover:bg-green-700 text-white',
            'secondary' => 'bg-gray-300 hover:bg-gray-400 text-gray-800',
            'danger' => 'bg-red-600 hover:bg-red-700 text-white'
        ];
        
        $colorClass = $colors[$style] ?? $colors['primary'];
        $baseClass = 'px-6 py-3 rounded-lg font-semibold transition inline-flex items-center gap-2';
        
        if ($type === 'link') {
            echo '<a href="' . htmlspecialchars($button['url'] ?? '#') . '" ';
            echo 'class="' . $baseClass . ' ' . $colorClass . '">';
            
            if (!empty($button['icon'])) {
                echo HtmlHelper::icon($button['icon'], 'w-5 h-5');
            }
            
            echo htmlspecialchars($button['text']);
            echo '</a>';
        } else {
            echo '<button type="' . htmlspecialchars($type) . '" ';
            
            if (!empty($button['onclick'])) {
                echo 'onclick="' . htmlspecialchars($button['onclick']) . '" ';
            }
            
            echo 'class="' . $baseClass . ' ' . $colorClass . '">';
            
            if (!empty($button['icon'])) {
                echo HtmlHelper::icon($button['icon'], 'w-5 h-5');
            }
            
            echo htmlspecialchars($button['text']);
            echo '</button>';
        }
    }
    
    private function renderNote()
    {
        if (empty($this->config['note'])) return;
        
        $type = $this->config['note']['type'] ?? 'info';
        
        $colors = [
            'info' => 'bg-blue-50 border-blue-500 text-blue-800',
            'warning' => 'bg-yellow-50 border-yellow-500 text-yellow-800',
            'success' => 'bg-green-50 border-green-500 text-green-800',
            'danger' => 'bg-red-50 border-red-500 text-red-800'
        ];
        
        $colorClass = $colors[$type] ?? $colors['info'];
        
        echo '<div class="mt-4 p-4 ' . $colorClass . ' border-l-4 rounded">';
        echo '<p class="text-sm">';
        
        if (!empty($this->config['note']['title'])) {
            echo '<strong>' . htmlspecialchars($this->config['note']['title']) . '</strong> ';
        }
        
        echo htmlspecialchars($this->config['note']['message']);
        echo '</p>';
        echo '</div>';
    }
}
?>