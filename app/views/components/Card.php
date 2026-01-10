<?php

require_once __DIR__ . '/CardRenderer.php';
require_once __DIR__ . '/CardFactory.php';

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
            'project' => 'item-card bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition',
            'publication' => 'item-card bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition',
            'event' => 'bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition transform hover:-translate-y-1',
            'equipement' => 'bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition transform hover:-translate-y-1 flex flex-col h-full',
            'actualite_detail' => 'item-card bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition scroll-mt-20',
             default => 'bg-white rounded-lg shadow-lg overflow-hidden ' . $hoverClass . ' flex flex-col h-full'
        };

        $dataAttributes = '';
        if (isset($config['data_attributes'])) {
            foreach ($config['data_attributes'] as $key => $value) {
                $dataAttributes .= ' data-' . $key . '="' . htmlspecialchars($value) . '"';
            }
        }

        $idAttribute = '';
        if (isset($config['id'])) {
            $idAttribute = ' id="' . htmlspecialchars($config['id']) . '"';
        }
        
        echo '<div class="' . $containerClass . '"' . $idAttribute . $dataAttributes . '>';
        echo CardRenderer::renderLayout($type, $config);
        echo '</div>';
    }

    public static function project($data)
    {
        self::render(CardFactory::project($data));
    }
    
    public static function publication($data)
    {
        self::render(CardFactory::publication($data));
    }
    
    public static function actualite($data)
    {
        self::render(CardFactory::actualite($data));
    }

    public static function actualiteDetail($data)
    {
        self::render(CardFactory::actualiteDetail($data));
    }
    
    public static function event($data)
    {
        self::render(CardFactory::event($data));
    }

    public static function equipement($data)
    {
        self::render(CardFactory::equipement($data));
    }
    
    public static function partner($data)
    {
        self::render(CardFactory::partner($data));
    }
    
    public static function dashboard($data)
    {
        self::render(CardFactory::dashboard($data));
    }
}
?>