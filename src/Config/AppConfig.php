<?php

declare(strict_types=1);

namespace Nicastore\Config;

class AppConfig
{
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $default;
    }
    
    public static function url(string $path = ''): string
    {
        $baseUrl = rtrim(self::get('APP_URL', ''), '/');
        return $baseUrl . '/' . ltrim($path, '/');
    }
    
    public static function asset(string $path): string
    {
        return self::url(ltrim($path, '/'));
    }
    
    public static function isDebug(): bool
    {
        return filter_var(self::get('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN);
    }
    
    public static function csrfTokenName(): string
    {
        return self::get('CSRF_TOKEN_NAME', '_token');
    }
    
    public static function productsPerPage(): int
    {
        return (int)(self::get('PRODUCTS_PER_PAGE', '12'));
    }
}