<?php

namespace App\Helpers;

class ViewHelper
{
    /**
     * Retorna a classe CSS de cor baseado na severidade
     */
    public static function severityColor($severity): string
    {
        return match(strtolower($severity)) {
            'critical' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            'low' => 'success',
            default => 'secondary',
        };
    }

    /**
     * Retorna o ícone Bootstrap baseado na severidade
     */
    public static function severityIcon($severity): string
    {
        return match(strtolower($severity)) {
            'critical' => 'exclamation-octagon-fill',
            'high' => 'exclamation-triangle-fill',
            'medium' => 'info-circle-fill',
            'low' => 'check-circle-fill',
            default => 'question-circle-fill',
        };
    }

    /**
     * Retorna label formatado da severidade
     */
    public static function severityLabel($severity): string
    {
        return ucfirst(strtolower($severity));
    }

    /**
     * Trunca texto com limite
     */
    public static function truncate($text, $limit = 50): string
    {
        if (strlen($text) > $limit) {
            return substr($text, 0, $limit) . '...';
        }
        return $text;
    }
}
