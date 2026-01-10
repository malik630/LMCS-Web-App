<?php

class CardRenderer
{
    public static function renderLayout($type, $config)
    {
        switch ($type) {
            case 'project':
                return self::renderProjectLayout($config);
            case 'publication':
                return self::renderPublicationLayout($config);
            case 'event':
                return self::renderEventLayout($config);
            case 'actualite_detail':
                return self::renderActualiteDetailLayout($config);
            case 'equipement':
                return self::renderEquipementLayout($config);
            case 'partner':
                return self::renderPartnerLayout($config);
            case 'dashboard':
                return self::renderDashboardLayout($config);
            default:
                return self::renderDefaultLayout($config);
        }
    }
    
    private static function renderProjectLayout($config)
    {
        ob_start();
        ?>
<div class="flex items-start justify-between gap-4 mb-4">
    <div class="flex-grow">
        <?php self::renderBadges($config); ?>
        <?php self::renderTitle($config, 'text-2xl font-bold text-gray-900 mt-2'); ?>
    </div>
    <?php self::renderStats($config); ?>
</div>

<?php 
        self::renderDescription($config);
        self::renderItems($config);
        self::renderMeta($config);
        self::renderFooterButtons($config);
        self::renderDetailsSection($config);
        ?>
<?php
        return ob_get_clean();
    }
    
    private static function renderPublicationLayout($config)
    {
        ob_start();
        ?>
<?php self::renderBadges($config); ?>
<?php self::renderTitle($config, 'text-xl font-bold text-gray-900 mb-2'); ?>
<?php self::renderItems($config); ?>
<?php self::renderDescription($config); ?>
<?php self::renderMeta($config); ?>
<?php self::renderFooterButtons($config); ?>
<?php
        return ob_get_clean();
    }
    
    private static function renderEventLayout($config)
    {
        ob_start();
        echo '<div class="p-6">';
        self::renderBadges($config);
        self::renderTitle($config, 'text-xl font-bold mb-3');
        self::renderDescription($config);
        self::renderItems($config);
        self::renderFooterButtons($config);
        echo '</div>';
        return ob_get_clean();
    }

    private static function renderActualiteDetailLayout($config)
    {
        ob_start();
        echo '<div class="grid md:grid-cols-3 gap-0">';

        echo '<div class="md:col-span-1">';
        if (!empty($config['image'])) {
            $imageSrc = ASSETS_URL . 'images/' . $config['image'];
            $fallback = ImageHelper::placeholder(400, 300, '#667eea');
            echo '<img src="' . htmlspecialchars($imageSrc) . '" alt="' . htmlspecialchars($config['title']) . '" ';
            echo 'class="w-full h-full object-cover min-h-[300px]" onerror="this.src=\'' . $fallback . '\'">';
        } else {
            echo '<div class="w-full h-full min-h-[300px] bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center text-white">';
            echo HtmlHelper::icon('document', 'w-16 h-16');
            echo '</div>';
        }
        echo '</div>';

        echo '<div class="md:col-span-2 p-6">';

        if (!empty($config['badges']) || !empty($config['meta'])) {
            echo '<div class="flex flex-wrap items-center gap-3 mb-4">';
            self::renderBadges($config);
            if (!empty($config['meta'])) {
                foreach ($config['meta'] as $meta) {
                    if ($meta['type'] === 'icon_text' && !empty($meta['value'])) {
                        echo '<div class="flex items-center gap-2 text-sm text-gray-600">';
                        echo HtmlHelper::icon($meta['icon'] ?? 'calendar', 'w-4 h-4');
                        echo '<span>' . htmlspecialchars($meta['value']) . '</span>';
                        echo '</div>';
                    }
                }
            }
            echo '</div>';
        }
        
        self::renderTitle($config, 'text-2xl font-bold text-gray-900 mb-3');

        if (!empty($config['description'])) {
            echo '<div class="text-gray-700 mb-4 leading-relaxed">';
            echo nl2br(htmlspecialchars($config['description']));
            echo '</div>';
        }

        if (!empty($config['detail_section'])) {
            echo '<div class="mt-4 p-4 bg-blue-50 border-l-4 border-blue-600 rounded">';
            echo '<h4 class="font-semibold text-blue-900 mb-2 flex items-center gap-2">';
            echo HtmlHelper::icon('info', 'w-4 h-4');
            echo 'Informations complémentaires';
            echo '</h4>';
            echo '<p class="text-sm text-gray-700">' . nl2br(htmlspecialchars($config['detail_section'])) . '</p>';
            echo '</div>';
        }

        if (!empty($config['footer_buttons'])) {
            echo '<div class="mt-4 pt-4 border-t border-gray-200">';
            echo '<div class="flex items-center justify-between">';
            echo '<div class="flex gap-2">';
            foreach ($config['footer_buttons'] as $btn) {
                $colorClass = match($btn['type']) {
                    'primary' => 'bg-blue-600 hover:bg-blue-700',
                    'secondary' => 'bg-gray-600 hover:bg-gray-700',
                    default => 'bg-blue-600 hover:bg-blue-700'
                };
                echo '<button onclick="' . htmlspecialchars($btn['onclick']) . '" ';
                echo 'class="p-2 ' . $colorClass . ' text-white rounded transition" ';
                echo 'title="' . htmlspecialchars($btn['text']) . '">';
                echo HtmlHelper::icon($btn['icon'], 'w-4 h-4');
                echo '</button>';
            }
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        
        echo '</div>';
        echo '</div>';
        return ob_get_clean();
    }
    
    private static function renderEquipementLayout($config)
    {
        ob_start();
        echo '<div class="p-6 flex flex-col flex-grow">';
        self::renderBadges($config);
        self::renderTitle($config, 'text-xl font-bold mt-2 mb-3');
        self::renderDescription($config);
        self::renderItems($config);
        self::renderFooterButtons($config);
        echo '</div>';
        return ob_get_clean();
    }

    private static function renderPartnerLayout($config)
    {
        ob_start();
        ?>
<div class="flex flex-col md:flex-row gap-6">
    <div class="flex-grow">
        <?php self::renderTitle($config, 'text-xl font-bold text-gray-900 mb-3'); ?>
        <?php self::renderDescription($config); ?>
        <?php self::renderItems($config); ?>
        <?php self::renderMeta($config); ?>
    </div>
    <?php self::renderLogo($config); ?>
</div>
<?php
        return ob_get_clean();
    }
    
    private static function renderDashboardLayout($config)
    {
        ob_start();
        ?>
<div class="flex justify-between items-start mb-2">
    <h3 class="font-bold text-gray-900"><?php echo htmlspecialchars($config['title']); ?></h3>
    <?php self::renderBadges($config); ?>
</div>
<?php 
        self::renderDescription($config);
        self::renderItems($config);
        self::renderMeta($config);
        self::renderFooterButtons($config);
        ?>
<?php
        return ob_get_clean();
    }
    
    private static function renderDefaultLayout($config)
    {
        ob_start();
        self::renderImage($config);
        echo '<div class="p-6 flex flex-col flex-grow">';
        self::renderBadges($config);
        self::renderTitle($config);
        self::renderDescription($config);
        self::renderItems($config);
        self::renderMeta($config);
        self::renderFooter($config);
        echo '</div>';
        return ob_get_clean();
    }
    
    private static function renderImage($config)
    {
        if (!isset($config['image'])) return;
        
        $src = ASSETS_URL . 'images/' . $config['image'];
        $alt = htmlspecialchars($config['title'] ?? 'Image');
        $fallback = 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22200%22%3E%3Crect width=%22400%22 height=%22200%22 fill=%22%23667eea%22/%3E%3C/svg%3E';
        echo '<img src="' . htmlspecialchars($src) . '" alt="' . $alt . '" class="w-full h-48 object-cover" onerror="this.src=\'' . $fallback . '\'">';
    }
    
    private static function renderLogo($config)
    {
        if (!isset($config['logo'])) return;
        
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
    
    private static function renderBadges($config)
    {
        if (empty($config['badges']) && empty($config['badge'])) return;
        
        if (!empty($config['badges'])) {
            echo '<div class="flex flex-wrap gap-2 mb-2">';
            foreach ($config['badges'] as $badge) {
                echo HtmlHelper::badge($badge['text'], $badge['type'] ?? 'primary');
            }
            echo '</div>';
        } elseif (!empty($config['badge'])) {
            echo '<div class="mb-2">' . HtmlHelper::badge($config['badge'], $config['badge_type'] ?? 'primary') . '</div>';
        }
    }
    
    private static function renderTitle($config, $class = 'text-xl font-bold mt-2 mb-3')
    {
        if (empty($config['title']) || (isset($config['type']) && $config['type'] === 'dashboard')) return;
        echo '<h3 class="' . $class . '">' . htmlspecialchars($config['title']) . '</h3>';
    }
    
    private static function renderDescription($config)
    {
        if (empty($config['description'])) return;
        
        $maxHeight = $config['description_max_height'] ?? 'max-h-24';
        echo '<div class="text-gray-600 mb-4 ' . $maxHeight . ' overflow-y-auto pr-2 custom-scrollbar flex-grow">';
        echo '<p>' . nl2br(htmlspecialchars($config['description'])) . '</p>';
        echo '</div>';
    }
    
    private static function renderItems($config)
    {
        if (empty($config['items'])) return;
        
        $containerClass = $config['items_container_class'] ?? 'space-y-2 mb-4';
        echo '<div class="' . $containerClass . '">';
        
        foreach ($config['items'] as $item) {
            if (empty($item['value'])) continue;
            
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
            echo '</span></div>';
        }
        
        echo '</div>';
    }
    
    private static function renderMeta($config)
    {
        if (empty($config['meta'])) return;
        
        $containerClass = $config['meta_container_class'] ?? 'flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-4';
        echo '<div class="' . $containerClass . '">';
        
        foreach ($config['meta'] as $meta) {
            if ($meta['type'] === 'badge' && !empty($meta['value'])) {
                echo HtmlHelper::badge($meta['value'], $meta['badge_type'] ?? 'success');
            } elseif ($meta['type'] === 'text' && !empty($meta['value'])) {
                echo '<span class="font-semibold">' . htmlspecialchars($meta['value']) . '</span>';
            } elseif ($meta['type'] === 'icon_text' && !empty($meta['value'])) {
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
    
    private static function renderStats($config)
    {
        if (empty($config['stats'])) return;
        
        echo '<div class="flex gap-4 flex-shrink-0">';
        foreach ($config['stats'] as $stat) {
            echo '<div class="text-center">';
            echo '<div class="text-2xl font-bold text-blue-600">' . $stat['value'] . '</div>';
            echo '<div class="text-xs text-gray-600">' . $stat['label'] . '</div>';
            echo '</div>';
        }
        echo '</div>';
    }
    
    private static function renderFooterButtons($config)
    {
        if (empty($config['footer_buttons'])) return;
        
        echo '<div class="pt-4 border-t border-gray-200 flex gap-2">';
        foreach ($config['footer_buttons'] as $btn) {
            self::renderButton($btn);
        }
        echo '</div>';
    }
    
    private static function renderDetailsSection($config)
    {
        if (empty($config['details_section'])) return;
        
        $detailsId = $config['details_section']['id'];
        echo '<div id="' . $detailsId . '" class="hidden mt-4"></div>';
    }
    
    private static function renderFooter($config)
    {
        $hasButton = !empty($config['footer_button']);
        $hasText = !empty($config['footer_text']);
        $hasLink = !empty($config['footer_link']);
        
        if (!$hasButton && !$hasText && !$hasLink) return;
        
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
    
    private static function renderButton($btn)
    {
        $colors = [
            'primary' => 'bg-blue-600 hover:bg-blue-700 text-white',
            'secondary' => 'bg-gray-600 hover:bg-gray-700 text-white',
            'success' => 'bg-green-600 hover:bg-green-700 text-white',
            'danger' => 'bg-red-600 hover:bg-red-700 text-white'
        ];
        
        $colorClass = $colors[$btn['type'] ?? 'primary'] ?? $colors['primary'];
        $customClass = $btn['class'] ?? 'w-full justify-center';
        $baseClass = 'px-4 py-2 rounded-lg font-semibold transition inline-flex items-center gap-2';
        $class = $baseClass . ' ' . $colorClass . ' ' . $customClass;
        
        if (isset($btn['url']) && isset($btn['onclick_confirm'])) {
            $onclick = "return confirm('" . addslashes($btn['onclick_confirm']) . "')";
            echo '<a href="' . htmlspecialchars($btn['url']) . '" onclick="' . $onclick . '" class="' . $class . '">';
            if (!empty($btn['icon'])) echo HtmlHelper::icon($btn['icon'], 'w-5 h-5');
            echo htmlspecialchars($btn['text']);
            echo '</a>';
        } elseif (isset($btn['onclick'])) {
            echo '<button onclick="' . htmlspecialchars($btn['onclick']) . '" class="' . $class . '">';
            if (!empty($btn['icon'])) echo HtmlHelper::icon($btn['icon'], 'w-5 h-5');
            echo htmlspecialchars($btn['text']);
            echo '</button>';
        } else {
            echo HtmlHelper::button(
                $btn['text'],
                $btn['url'] ?? '#',
                $btn['type'] ?? 'primary',
                $btn['icon'] ?? null,
                ['class' => $customClass]
            );
        }
    }
}
?>