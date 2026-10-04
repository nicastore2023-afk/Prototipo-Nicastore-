<?php

declare(strict_types=1);

namespace Nicastore\Core;

use Nicastore\Config\DatabaseConfig;
use PDO;

class Database
{
    private static ?PDO $instance = null;
    
    private function __construct() {}
    
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = DatabaseConfig::getConnection();
        }
        return self::$instance;
    }
    
    public static function resetInstance(): void
    {
        self::$instance = null;
    }
    
    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = self::query($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    public static function fetchAll(string $sql, array $params = []): array
    {
        $stmt = self::query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public static function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        self::query($sql, $data);
        return (int)self::getInstance()->lastInsertId();
    }
    
    public static function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $set = [];
        foreach (array_keys($data) as $column) {
            $set[] = "{$column} = :{$column}";
        }
        $sql = "UPDATE {$table} SET " . implode(', ', $set) . " WHERE {$where}";
        
        // Use named parameters for WHERE clause to avoid mixing named and positional params
        $whereNamed = $where;
        $whereNamedParams = [];
        if (!empty($whereParams)) {
            $i = 0;
            $whereNamed = preg_replace_callback('/\?/', function($matches) use ($whereParams, &$i, &$whereNamedParams) {
                $key = 'where_' . $i++;
                $whereNamedParams[$key] = array_shift($whereParams);
                return ':' . $key;
            }, $where);
        }
        
        $sql = "UPDATE {$table} SET " . implode(', ', $set) . " WHERE {$whereNamed}";
        
        $params = array_merge($data, $whereNamedParams);
        $stmt = self::query($sql, $params);
        return $stmt->rowCount();
    }
    
    public static function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = self::query($sql, $params);
        return $stmt->rowCount();
    }
    
    public static function beginTransaction(): void
    {
        self::getInstance()->beginTransaction();
    }
    
    public static function commit(): void
    {
        self::getInstance()->commit();
    }
    
    public static function rollback(): void
    {
        self::getInstance()->rollBack();
    }
}