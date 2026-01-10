<?php

require_once __DIR__ . '/../../helpers/HtmlHelper.php';
class StatsCard
{
    public static function render(array $config)
    {
        $label = $config['label'] ?? '';
        $value = $config['value'] ?? 0;
        $sublabel = $config['sublabel'] ?? null;
        $icon = $config['icon'] ?? 'chart';
        $color = $config['color'] ?? 'blue';
        
        $colorClasses = [
            'blue' => ['text' => 'text-blue-600', 'bg' => 'bg-blue-100'],
            'green' => ['text' => 'text-green-600', 'bg' => 'bg-green-100'],
            'yellow' => ['text' => 'text-yellow-600', 'bg' => 'bg-yellow-100'],
            'red' => ['text' => 'text-red-600', 'bg' => 'bg-red-100'],
            'purple' => ['text' => 'text-purple-600', 'bg' => 'bg-purple-100'],
            'orange' => ['text' => 'text-orange-600', 'bg' => 'bg-orange-100']
        ];
        
        $colors = $colorClasses[$color] ?? $colorClasses['blue'];
        
        echo '<div class="bg-white rounded-lg shadow-lg p-6">';
        echo '<div class="flex items-center justify-between">';
        echo '<div>';
        echo '<p class="text-gray-600 text-sm font-medium">' . htmlspecialchars($label) . '</p>';
        echo '<p class="text-3xl font-bold ' . $colors['text'] . ' mt-2">' . htmlspecialchars($value) . '</p>';
        
        if ($sublabel) {
            echo '<p class="text-xs text-gray-500 mt-1">' . htmlspecialchars($sublabel) . '</p>';
        }
        
        echo '</div>';
        echo '<div class="' . $colors['text'] . ' ' . $colors['bg'] . ' p-3 rounded-lg">';
        echo HtmlHelper::icon($icon, 'w-8 h-8');
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    
    public static function renderGrid(array $cards, int $columns = 3)
    {
        echo '<div class="grid md:grid-cols-' . $columns . ' gap-6 mb-8">';
        
        foreach ($cards as $card) {
            self::render($card);
        }
        
        echo '</div>';
    }
}
?>