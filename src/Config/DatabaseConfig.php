<?php

declare(strict_types=1);

namespace Nicastore\Config;

class DatabaseConfig
{
    public static function getConnection(): \PDO
    {
        // Raíz del proyecto: PROTOTIPO/
        $basePath = dirname(__DIR__, 2);

        // Base de datos SQLite
        $dbPath = $basePath . '/database/nicastore.sqlite';

        // Crear carpeta database si no existe
        $dbDir = dirname($dbPath);

        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0755, true);
        }

        $pdo = new \PDO("sqlite:" . $dbPath);

        $pdo->setAttribute(
            \PDO::ATTR_ERRMODE,
            \PDO::ERRMODE_EXCEPTION
        );

        $pdo->setAttribute(
            \PDO::ATTR_DEFAULT_FETCH_MODE,
            \PDO::FETCH_ASSOC
        );

        $pdo->setAttribute(
            \PDO::ATTR_EMULATE_PREPARES,
            false
        );

        // Activar claves foráneas en SQLite
        $pdo->exec('PRAGMA foreign_keys = ON');

        return $pdo;
    }
}