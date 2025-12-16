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

    public static function renderUserPhoto($user)
    {
        if (!empty($user['photo'])) {
            $src = ASSETS_URL . 'images/users/' . $user['photo'];
            echo '<img src="' . $src . '" alt="Photo de profil" class="w-24 h-24 rounded-full object-cover border-4 border-blue-600" onerror="this.src=\'' . ImageHelper::placeholder(100, 100, '#667eea') . '\'">';
        } else {
            echo '<div class="w-24 h-24 rounded-full bg-blue-600 flex items-center justify-center text-white text-3xl font-bold border-4 border-blue-700">';
            echo strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1));
            echo '</div>';
        }
    }

    public static function renderUserPhotoLarge($user)
    {
        if (!empty($user['photo'])) {
            $src = ASSETS_URL . 'images/users/' . $user['photo'];
            echo '<img src="' . $src . '" alt="Photo de profil" class="w-32 h-32 rounded-full object-cover mx-auto border-4 border-blue-600" onerror="this.src=\'' . ImageHelper::placeholder(128, 128, '#667eea') . '\'">';
        } else {
            echo '<div class="w-32 h-32 rounded-full bg-blue-600 flex items-center justify-center text-white text-4xl font-bold mx-auto border-4 border-blue-700">';
            echo strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1));
            echo '</div>';
        }
    }
}
?>