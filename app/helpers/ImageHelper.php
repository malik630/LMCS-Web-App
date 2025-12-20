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

    public static function renderUserPhoto(array $user, int $size = 24)
    {
        $allowedSizes = [8, 10, 12, 16, 20, 24, 32];
        $size = in_array($size, $allowedSizes) ? $size : 24;

        $sizeClass   = "w-$size h-$size";
        $borderClass = $size >= 16 ? 'border-4' : 'border-2';
        $textClass   = $size >= 16 ? 'text-3xl' : 'text-sm';

        $initials = strtoupper(
            substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)
        );

        if (!empty($user['photo'])) {
            echo '<img src="' . ASSETS_URL . 'images/users/' . $user['photo'] . '" ';
            echo 'alt="' . $user['prenom'] . ' ' . $user['nom'] . '" ';
            echo 'class="' . $sizeClass . ' rounded-full object-cover ' . $borderClass . ' border-grey-600" ';
            echo 'onerror="this.outerHTML=\'';
            echo '<div class=\\\'' . $sizeClass . ' rounded-full bg-black flex items-center justify-center text-white font-bold ' . $borderClass . ' border-grey-700 ' . $textClass . '\\\'>';
            echo $initials;
            echo '</div>\'">';
        } else {
            echo '<div class="' . $sizeClass . ' rounded-full bg-black flex items-center justify-center text-white font-bold ' . $borderClass . ' border-grey-700 ' . $textClass . '">';
            echo $initials;
        echo '</div>';
        }
    }
    
}
?>