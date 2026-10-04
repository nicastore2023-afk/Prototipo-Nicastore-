<?php

declare(strict_types=1);

namespace Nicastore\Core;

use Nicastore\Config\SessionConfig;

class Session
{
    public static function init(): void
    {
        SessionConfig::init();
    }
    
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }
    
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }
    
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }
    
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }
    
    public static function flash(string $key, mixed $value = null): mixed
    {
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return null;
        }
        
        $value = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }
    
    public static function getFlash(string $key, mixed $default = null): mixed
    {
        return $_SESSION['_flash'][$key] ?? $default;
    }
    
    public static function regenerate(): void
    {
        SessionConfig::regenerate();
    }
    
    public static function destroy(): void
    {
        SessionConfig::destroy();
    }
    
    public static function getId(): string
    {
        return session_id();
    }
    
    public static function setUser(array $user): void
    {
        self::set('user', $user);
        self::set('user_id', $user['id']);
        self::set('logged_in', true);
    }
    
    public static function getUser(): ?array
    {
        return self::get('user');
    }
    
    public static function getUserId(): ?int
    {
        return self::get('user_id');
    }
    
    public static function isLoggedIn(): bool
    {
        return self::get('logged_in', false);
    }
    
    public static function logout(): void
    {
        self::remove('user');
        self::remove('user_id');
        self::remove('logged_in');
        self::destroy();
    }
}