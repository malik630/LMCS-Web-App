<?php

class Card
{
    public static function render($config)
    {
        $type = $config['type'] ?? 'default';
        $hover = $config['hover'] ?? true;
        $hoverClass = $hover ? 'hover:shadow-xl transition transform hover:-translate-y-1' : '';
        
        $containerClass = match($type) {
            'dashboard' => 'border border-gray-200 rounded-lg p-4 hover:shadow-md transition',
            'partner' => 'border border-gray-200 rounded-lg p-6 hover:shadow-lg transition',
            default => 'bg-white rounded-lg shadow-lg overflow-hidden ' . $hoverClass . ' flex flex-col h-full'
        };
        
        ?>
<div class="<?php echo $containerClass; ?>">
    <?php 
    if ($type === 'partner') {
        self::renderPartnerLayout($config);
    } elseif ($type === 'dashboard') {
        self::renderDashboardLayout($config);
    } else {
        self::renderDefaultLayout($config);
    }
    ?>
</div>
<?php
    }
    
    private static function renderDefaultLayout($config)
    {
        self::renderImage($config);
        echo '<div class="p-6 flex flex-col flex-grow">';
        self::renderBadge($config);
        self::renderTitle($config);
        self::renderDescription($config);
        self::renderItems($config);
        self::renderMeta($config);
        self::renderFooter($config);
        echo '</div>';
    }
    
    private static function renderDashboardLayout($config)
    {
        echo '<div class="flex justify-between items-start mb-2">';
        echo '<h3 class="font-bold text-gray-900">' . htmlspecialchars($config['title']) . '</h3>';
        self::renderBadge($config);
        echo '</div>';
        self::renderDescription($config);
        self::renderItems($config);
        self::renderMeta($config);
        self::renderFooter($config);
    }
    
    private static function renderPartnerLayout($config)
    {
        echo '<div class="flex flex-col md:flex-row gap-6">';
        echo '<div class="flex-grow">';
        self::renderTitle($config, 'text-xl font-bold text-gray-900 mb-3');
        self::renderDescription($config);
        self::renderItems($config);
        self::renderMeta($config);
        echo '</div>';
        self::renderLogo($config);
        echo '</div>';
    }
    
    private static function renderImage($config)
    {        
        if (isset($config['image'])) {
            $src = ASSETS_URL . 'images/' . $config['image'];
            $alt = htmlspecialchars($config['title'] ?? 'Image');
            $fallback = 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22200%22%3E%3Crect width=%22400%22 height=%22200%22 fill=%22%23667eea%22/%3E%3C/svg%3E';
            echo '<img src="' . htmlspecialchars($src) . '" alt="' . $alt . '" class="w-full h-48 object-cover" onerror="this.src=\'' . $fallback . '\'">';
        }
    }
    
    private static function renderLogo($config)
    {
        if (isset($config['logo'])) {
            echo '<div class="flex-shrink-0 flex items-center justify-center md:w-48">';
            if (!empty($config['logo'])) {
                $src = ImageHelper::url($config['logo']);
                $alt = 'Logo ' . htmlspecialchars($config['title']);
                echo '<img src="' . htmlspecialchars($src) . '" alt="' . $alt . '" ';
                echo 'class="max-h-32 max-w-full object-contain grayscale hover:grayscale-0 transition" ';
                echo 'onerror="this.outerHTML=\'<div class=\\\'text-center text-gray-400 text-sm p-4 border-2 border-dashed border-gray-300 rounded\\\'>' . htmlspecialchars($config['title']) . '</div>\'">';
            } else {
                echo '<div class="text-center text-gray-400 text-sm p-4 border-2 border-dashed border-gray-300 rounded w-full">';
                echo htmlspecialchars($config['title']);
                echo '</div>';
            }
            echo '</div>';
        }
    }
    
    private static function renderBadge($config)
    {
        if (!empty($config['badge'])) {
            $badgeType = $config['badge_type'] ?? 'primary';
            echo '<div class="mb-2">' . HtmlHelper::badge($config['badge'], $badgeType) . '</div>';
        }
    }
    
    private static function renderTitle($config, $class = 'text-xl font-bold mt-2 mb-3')
    {
        if (!empty($config['title']) && !isset($config['type']) || $config['type'] !== 'dashboard') {
            echo '<h3 class="' . $class . '">' . htmlspecialchars($config['title']) . '</h3>';
        }
    }
    
    private static function renderDescription($config)
    {
        if (!empty($config['description'])) {
            $maxHeight = $config['description_max_height'] ?? 'max-h-24';
            echo '<div class="text-gray-600 mb-4 ' . $maxHeight . ' overflow-y-auto pr-2 custom-scrollbar flex-grow">';
            echo '<p>' . nl2br(htmlspecialchars($config['description'])) . '</p>';
            echo '</div>';
        }
    }
    
    private static function renderItems($config)
    {
        if (!empty($config['items'])) {
            $containerClass = isset($config['items_container_class']) ? $config['items_container_class'] : 'space-y-2 mb-4';
            echo '<div class="' . $containerClass . '">';
            
            foreach ($config['items'] as $item) {
                if (!empty($item['value'])) {
                    $itemClass = $item['class'] ?? 'text-sm text-gray-600';
                    echo '<div class="flex items-start gap-2 ' . $itemClass . '">';
                    
                    if (!empty($item['icon'])) {
                        echo '<span class="flex-shrink-0 mt-0.5">' . HtmlHelper::icon($item['icon']) . '</span>';
                    }
                    
                    echo '<span class="break-words">';
                    if (!empty($item['label'])) {
                        echo '<span class="font-semibold">' . htmlspecialchars($item['label']) . ':</span> ';
                    }
                    
                    echo htmlspecialchars($item['value']);
                    echo '</span>';
                    echo '</div>';
                }
            }
            
            echo '</div>';
        }
    }
    
    private static function renderMeta($config)
    {
        if (!empty($config['meta'])) {
            echo '<div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">';
            
            foreach ($config['meta'] as $meta) {
                if ($meta['type'] === 'badge' && !empty($meta['value'])) {
                    echo HtmlHelper::badge($meta['value'], $meta['badge_type'] ?? 'success');
                } elseif ($meta['type'] === 'text' && !empty($meta['value'])) {
                    echo '<span class="font-semibold">' . htmlspecialchars($meta['value']) . '</span>';
                } elseif ($meta['type'] === 'icon_text') {
                    echo '<div class="flex items-center gap-2">';
                    echo HtmlHelper::icon($meta['icon'] ?? 'calendar');
                    echo '<span>' . htmlspecialchars($meta['value']) . '</span>';
                    echo '</div>';
                } elseif ($meta['type'] === 'link' && !empty($meta['url'])) {
                    echo HtmlHelper::linkWithIcon($meta['text'], $meta['url'], $meta['icon'] ?? 'external-link');
                }
            }
            
            echo '</div>';
        }
    }
    
    private static function renderFooter($config)
    {
        $hasButton = !empty($config['footer_button']);
        $hasText = !empty($config['footer_text']);
        $hasLink = !empty($config['footer_link']);
        
        if ($hasButton || $hasText || $hasLink) {
            echo '<div class="mt-auto' . ($hasButton ? ' pt-4 border-t border-gray-200' : '') . '">';
            
            if ($hasLink) {
                $link = $config['footer_link'];
                $icon = $link['icon'] ?? 'arrow-right';
                $class = $link['class'] ?? 'text-blue-600 font-semibold hover:text-blue-800 transition inline-block mb-3';
                $target = !empty($link['target']) ? ' target="' . htmlspecialchars($link['target']) . '"' : '';
                
                echo '<a href="' . htmlspecialchars($link['url']) . '" class="inline-flex items-center gap-2 ' . $class . '"' . $target . '>';
                echo HtmlHelper::icon($icon, 'w-4 h-4');
                echo '<span>' . htmlspecialchars($link['text']) . '</span>';
                echo '</a>';
            }
            
            if ($hasButton) {
                self::renderButton($config['footer_button']);
            }
            
            if ($hasText) {
                echo '<p class="text-sm text-gray-500 mt-3">' . htmlspecialchars($config['footer_text']) . '</p>';
            }
            
            echo '</div>';
        }
    }
    
    // ✅ NOUVELLE MÉTHODE: Rendu des boutons simplifié
    private static function renderButton($btn)
    {
        $colors = [
            'primary' => 'bg-blue-600 hover:bg-blue-700 text-white',
            'secondary' => 'bg-gray-600 hover:bg-gray-700 text-white',
            'success' => 'bg-green-600 hover:bg-green-700 text-white',
            'danger' => 'bg-red-600 hover:bg-red-700 text-white'
        ];
        
        $colorClass = $colors[$btn['type'] ?? 'primary'] ?? $colors['primary'];
        
        // ✅ Classe personnalisée ou par défaut
        $customClass = $btn['class'] ?? 'w-full justify-center';
        $baseClass = 'px-4 py-2 rounded-lg font-semibold transition inline-flex items-center gap-2';
        $class = $baseClass . ' ' . $colorClass . ' ' . $customClass;
        
        // Si c'est un lien avec confirmation
        if (isset($btn['url']) && isset($btn['onclick_confirm'])) {
            $onclick = "return confirm('" . addslashes($btn['onclick_confirm']) . "')";
            echo '<a href="' . htmlspecialchars($btn['url']) . '" onclick="' . $onclick . '" class="' . $class . '">';
            if (!empty($btn['icon'])) {
                echo HtmlHelper::icon($btn['icon'], 'w-5 h-5');
            }
            echo htmlspecialchars($btn['text']);
            echo '</a>';
        }
        // Si c'est un onclick personnalisé
        elseif (isset($btn['onclick'])) {
            echo '<button onclick="' . htmlspecialchars($btn['onclick']) . '" class="' . $class . '">';
            if (!empty($btn['icon'])) {
                echo HtmlHelper::icon($btn['icon'], 'w-5 h-5');
            }
            echo htmlspecialchars($btn['text']);
            echo '</button>';
        }
        // Sinon c'est un lien simple
        else {
            echo HtmlHelper::button(
                $btn['text'],
                $btn['url'],
                $btn['type'] ?? 'primary',
                $btn['icon'] ?? null,
                ['class' => $customClass]
            );
        }
    }

    public static function actualite($data)
    {
        return self::render([
            'image' => $data['image'] ?? null,
            'badge' => $data['type_libelle'] ?? 'Actualité',
            'badge_type' => 'primary',
            'title' => $data['titre'] ?? '',
            'description' => $data['contenu'] ?? '',
            'footer_link' => !empty($data['detail']) ? [
                'text' => 'En savoir plus',
                'url' => BASE_URL . 'actualite/view/' . $data['id_actualite']
            ] : null,
            'footer_text' => !empty($data['date_publication']) ? DateHelper::format($data['date_publication']) : null
        ]);
    }
    
    public static function event($data)
    {
        return self::render([
            'image' => 'event.jpg',
            'badge' => $data['type_libelle'] ?? 'Événement',
            'badge_type' => 'primary',
            'title' => $data['titre'] ?? '',
            'description' => $data['description'] ?? '',
            'description_max_height' => 'max-h-20',
            'items' => array_filter([
                [
                    'icon' => 'calendar',
                    'value' => !empty($data['date_debut']) ? DateHelper::format($data['date_debut'], 'd/m/Y') . 
                               (!empty($data['date_fin']) ? ' - ' . DateHelper::format($data['date_fin'], 'd/m/Y') : '') : null
                ],
                [
                    'icon' => 'location',
                    'value' => $data['lieu'] ?? null
                ]
            ], fn($item) => !empty($item['value'])),
            'footer_button' => !empty($data['id_evenement']) ? [
                'text' => 'Participer',
                'url' => BASE_URL . 'event/register/' . $data['id_evenement']
            ] : null
        ]);
    }
    
    public static function partner($data)
    {
        return self::render([
            'type' => 'partner',
            'title' => $data['nom'] ?? '',
            'description' => $data['description'] ?? null,
            'logo' => $data['logo'] ?? null,
            'meta' => array_filter([
                [
                    'type' => 'icon_text',
                    'icon' => 'location',
                    'value' => $data['pays'] ?? null
                ],
                [
                    'type' => 'icon_text',
                    'icon' => 'calendar',
                    'value' => !empty($data['date_partenariat']) ? 
                               'Partenariat depuis ' . DateHelper::format($data['date_partenariat'], 'Y') : null
                ],
                [
                    'type' => 'link',
                    'icon' => 'external-link',
                    'text' => 'Visiter le site',
                    'url' => $data['site_web'] ?? null
                ]
            ], fn($item) => !empty($item['value']) || !empty($item['url']))
        ]);
    }
    
    public static function dashboard($data)
    {
        return self::render(array_merge(['type' => 'dashboard'], $data));
    }
}
?>