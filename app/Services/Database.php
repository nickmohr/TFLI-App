<?php

declare(strict_types=1);

namespace App\Services;

use PDO;

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            self::$connection = new PDO(DB_DSN, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }

        return self::$connection;
    }

    /**
     * Create the database file and schema if missing.
     */
    public static function init(): void
    {
        self::getConnection()->exec(
            'CREATE TABLE IF NOT EXISTS urls (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                code TEXT NOT NULL,
                long_url TEXT NOT NULL,
                expires_at TEXT NULL,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE(code),
                UNIQUE(long_url, expires_at)
            )',
        );
    }
}
