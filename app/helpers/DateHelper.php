<?php

class DateHelper
{
    public static function format($date, $format = 'd/m/Y')
    {
        if (empty($date)) return '';
        return date($format, strtotime($date));
    }
    
    public static function relative($date)
    {
        $timestamp = strtotime($date);
        $diff = time() - $timestamp;
        
        if ($diff < 3600) {
            $minutes = floor($diff / 60);
            return "Il y a {$minutes} minute" . ($minutes > 1 ? 's' : '');
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return "Il y a {$hours} heure" . ($hours > 1 ? 's' : '');
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return "Il y a {$days} jour" . ($days > 1 ? 's' : '');
        } else {
            return self::format($date);
        }
    }
}
?>