<?php

class HtmlHelper
{
    private static $icons = [
        'calendar' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>',
        'location' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>',
        'external-link' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>',
        'arrow-left' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>',
        'arrow-right' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>',
        'user' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>',
        'email' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>',
        'phone' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>',
        'download' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>',
        'trash' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>',
        'edit' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>',
        'check' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
        'close' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
    ];
    public static function icon($name, $class = 'w-5 h-5')
    {
        if (!isset(self::$icons[$name])) {
            return '';
        }
        
        return str_replace('class="w-5 h-5"', 'class="' . $class . '"', self::$icons[$name]);
    }

    public static function badge($text, $type = 'primary')
    {
        $colors = [
            'primary' => 'bg-blue-100 text-blue-800',
            'success' => 'bg-green-100 text-green-800',
            'warning' => 'bg-yellow-100 text-yellow-800',
            'danger' => 'bg-red-100 text-red-800',
            'info' => 'bg-gray-100 text-gray-800',
            'orange' => 'bg-orange-100 text-orange-800'
        ];
        
        $colorClass = $colors[$type] ?? $colors['primary'];
        return '<span class="px-2 py-1 rounded text-xs font-semibold ' . $colorClass . '">' . 
               htmlspecialchars($text) . '</span>';
    }

    public static function imageWithFallback($src, $alt, $class = '', $fallback = null)
    {
        $fallback = $fallback ?? ImageHelper::placeholder(400, 200, '#667eea');
        return '<img src="' . htmlspecialchars($src) . '" alt="' . htmlspecialchars($alt) . 
               '" class="' . $class . '" onerror="this.src=\'' . $fallback . '\'">';
    }

    public static function button($text, $url = null, $type = 'primary', $icon = null, $attributes = [])
    {
        $colors = [
            'primary' => 'bg-blue-600 hover:bg-blue-700 text-white',
            'secondary' => 'bg-gray-600 hover:bg-gray-700 text-white',
            'success' => 'bg-green-600 hover:bg-green-700 text-white',
            'danger' => 'bg-red-600 hover:bg-red-700 text-white'
        ];
        
        $colorClass = $colors[$type] ?? $colors['primary'];
        $baseClass = 'px-4 py-2 rounded-lg font-semibold transition inline-flex items-center gap-2';
        $class = $baseClass . ' ' . $colorClass;
        
        $attrs = '';
        foreach ($attributes as $key => $value) {
            $attrs .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
        }
        
        $content = '';
        if ($icon) {
            $content .= self::icon($icon, 'w-5 h-5');
        }
        $content .= htmlspecialchars($text);
        
        if ($url) {
            return '<a href="' . htmlspecialchars($url) . '" class="' . $class . '"' . $attrs . '>' . $content . '</a>';
        } else {
            return '<button class="' . $class . '"' . $attrs . '>' . $content . '</button>';
        }
    }

    public static function infoList($items, $class = 'space-y-2 text-sm')
    {
        $html = '<div class="' . $class . '">';
        foreach ($items as $item) {
            if (!empty($item['value'])) {
                $html .= '<p>';
                if (!empty($item['icon'])) {
                    $html .= '<span class="inline-flex items-center gap-2">';
                    $html .= self::icon($item['icon']);
                }
                $html .= '<span class="font-semibold">' . htmlspecialchars($item['label']) . ':</span> ';
                $html .= htmlspecialchars($item['value']);
                if (!empty($item['icon'])) {
                    $html .= '</span>';
                }
                $html .= '</p>';
            }
        }
        $html .= '</div>';
        return $html;
    }

    public static function linkWithIcon($text, $url, $icon, $class = 'text-blue-600 hover:text-blue-800')
    {
        return '<a href="' . htmlspecialchars($url) . '" class="inline-flex items-center gap-2 ' . $class . '">' .
               self::icon($icon, 'w-4 h-4') . 
               '<span>' . htmlspecialchars($text) . '</span>' .
               '</a>';
    }

    public static function emptyState($message, $icon = null, $actionText = null, $actionUrl = null)
    {
        $html = '<div class="text-center py-8 text-gray-500">';
        if ($icon) {
            $html .= '<div class="mb-4 flex justify-center">' . self::icon($icon, 'w-12 h-12') . '</div>';
        }
        $html .= '<p class="mb-4">' . htmlspecialchars($message) . '</p>';
        if ($actionText && $actionUrl) {
            $html .= self::button($actionText, $actionUrl, 'primary');
        }
        $html .= '</div>';
        return $html;
    }
}
?>