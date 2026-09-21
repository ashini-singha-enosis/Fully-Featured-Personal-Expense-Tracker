<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Shared view helpers.
 */
class Helper
{
    public static function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public static function url(string $page = 'expenses'): string
    {
        return 'index.php?page=' . rawurlencode($page);
    }
}
