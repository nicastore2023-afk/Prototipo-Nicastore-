<?php

declare(strict_types=1);

namespace Nicastore\Config;

class SessionConfig
{
    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $lifetime = (int)($_ENV['SESSION_LIFETIME'] ?? 120);
            $secure = filter_var($_ENV['SESSION_SECURE'] ?? 'false', FILTER_VALIDATE_BOOLEAN);
            $httpOnly = filter_var($_ENV['SESSION_HTTP_ONLY'] ?? 'true', FILTER_VALIDATE_BOOLEAN);
            
            session_set_cookie_params([
                'lifetime' => $lifetime * 60,
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => $httpOnly,
                'samesite' => 'Lax'
            ]);
            
            session_start();
        }
    }
    
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }
    
    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
    }
}