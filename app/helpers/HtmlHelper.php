<?php
class HtmlHelper {
    public static function badge($text, $type = 'default')
    {
    $classes = [
    'default' => 'bg-gray-100 text-gray-800',
    'primary' => 'bg-blue-600 text-white'
    ];

    $class = $classes[$type] ?? $classes['default'];

    return "<span class='px-3 py-1 rounded-full text-xs font-semibold {$class}'>"
        . htmlspecialchars($text) . "</span>";
    }

    public static function icon($name, $class = 'w-4 h-4')
    {
    $icons = [
    'location' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
    </svg>',

    'calendar' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
    </svg>',

    'external-link' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
    </svg>',

    'arrow-left' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
    </svg>',

    'arrow-right' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
    </svg>'
    ];

    return $icons[$name] ?? '';
    }

    public static function imageWithFallback($src, $alt, $class = '', $fallbackColor = '#ddd')
    {
    $fallback = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='200'%3E%3Crect width='400'
    height='200' fill='{$fallbackColor}'/%3E%3C/svg%3E";

    return "<img src='" . htmlspecialchars($src) . "' alt='" . htmlspecialchars($alt) . "' class='{$class}'
        onerror=\"this.src='{$fallback}' \">";
    }

}
?>