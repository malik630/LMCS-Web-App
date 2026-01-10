<?php

require_once __DIR__ . '/../../helpers/HtmlHelper.php';
class PageHeader
{
    public static function render(array $config)
    {
        $title = $config['title'] ?? '';
        $subtitle = $config['subtitle'] ?? null;
        $backLink = $config['back_link'] ?? null;
        $actions = $config['actions'] ?? [];
        $badges = $config['badges'] ?? [];
        
        echo '<div class="container mx-auto px-4 py-8">';

        if ($backLink) {
            echo '<div class="mb-6">';
            echo '<a href="' . htmlspecialchars($backLink['url']) . '" ';
            echo 'class="inline-flex items-center gap-2 text-white hover:text-gray-200 transition">';
            echo HtmlHelper::icon('arrow-left', 'w-5 h-5');
            echo htmlspecialchars($backLink['text']);
            echo '</a>';
            echo '</div>';
        }

        echo '<div class="flex justify-between items-start mb-8">';
        echo '<div>';
        echo '<h1 class="text-4xl font-bold text-white mb-2">' . htmlspecialchars($title) . '</h1>';

        if ($subtitle) {
            echo '<p class="text-gray-300">' . htmlspecialchars($subtitle) . '</p>';
        }
        
        if (!empty($badges)) {
            echo '<div class="flex gap-2 items-center mt-2">';
            foreach ($badges as $badge) {
                echo HtmlHelper::badge($badge['text'], $badge['type'] ?? 'primary');
            }
            echo '</div>';
        }
        
        echo '</div>';

        if (!empty($actions)) {
            echo '<div class="flex gap-3">';
            foreach ($actions as $action) {
                self::renderAction($action);
            }
            echo '</div>';
        }
        
        echo '</div>';
    }
    
    private static function renderAction(array $action)
    {
        $type = $action['type'] ?? 'primary';
        
        $colors = [
            'primary' => 'bg-green-600 hover:bg-green-700',
            'secondary' => 'bg-blue-600 hover:bg-blue-700',
            'warning' => 'bg-orange-600 hover:bg-orange-700',
            'danger' => 'bg-red-600 hover:bg-red-700'
        ];
        
        $colorClass = $colors[$type] ?? $colors['primary'];
        
        echo '<a href="' . htmlspecialchars($action['url']) . '" ';
        
        if (!empty($action['onclick'])) {
            echo 'onclick="' . htmlspecialchars($action['onclick']) . '" ';
        }
        
        echo 'class="px-6 py-3 ' . $colorClass . ' text-white rounded-lg font-semibold transition inline-flex items-center gap-2">';
        
        if (!empty($action['icon'])) {
            echo HtmlHelper::icon($action['icon'], 'w-5 h-5');
        }
        
        echo htmlspecialchars($action['text']);
        echo '</a>';
    }
    
    public static function close()
    {
        echo '</div>'; 
    }
}
?>