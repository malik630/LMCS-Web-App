<?php

require_once __DIR__ . '/../../helpers/DateHelper.php';
class InfoGrid
{
    public static function render(array $items, int $columns = 2)
    {
        echo '<div class="grid md:grid-cols-' . $columns . ' gap-6">';
        
        foreach ($items as $item) {
            if (!empty($item['value'])) {
                echo '<div>';
                echo '<p class="text-sm text-gray-600 font-semibold mb-1">' . htmlspecialchars($item['label']) . '</p>';
                echo '<p class="text-gray-900">';
                
                if (!empty($item['format'])) {
                    echo self::formatValue($item['value'], $item['format']);
                } else {
                    echo htmlspecialchars($item['value']);
                }
                
                echo '</p>';
                echo '</div>';
            }
        }
        
        echo '</div>';
    }
    
    private static function formatValue($value, $format)
    {
        switch ($format) {
            case 'date':
                return DateHelper::format($value);
            case 'datetime':
                return DateHelper::format($value, 'd/m/Y H:i');
            case 'currency':
                return number_format($value, 2, ',', ' ') . ' DA';
            case 'number':
                return number_format($value, 0, ',', ' ');
            default:
                return htmlspecialchars($value);
        }
    }
    
    public static function renderWithDescription(array $items, $description = null, int $columns = 2)
    {
        self::render($items, $columns);
        
        if ($description) {
            echo '<div class="mt-4">';
            echo '<p class="text-sm text-gray-600 font-semibold mb-1">Description</p>';
            echo '<p class="text-gray-900 whitespace-pre-wrap">' . nl2br(htmlspecialchars($description)) . '</p>';
            echo '</div>';
        }
    }
}
?>