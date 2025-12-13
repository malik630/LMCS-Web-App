<?php

class ImageHelper
{
    public static function url($filename)
    {
        if (empty($filename)) return null;
        return ASSETS_URL . 'images/' . $filename;
    }
    
    public static function placeholder($width = 400, $height = 200, $color = '#667eea')
    {
        return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='{$width}' height='{$height}'%3E%3Crect width='{$width}' height='{$height}' fill='{$color}'/%3E%3C/svg%3E";
    }
}
?>