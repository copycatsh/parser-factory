<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

final class Connection
{
    private static ?PDO $instance = null;

    public static function get(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                getenv('DB_HOST') ?: 'postgres',
                getenv('DB_PORT') ?: '5432',
                getenv('DB_NAME') ?: 'parser_db',
            );

            self::$instance = new PDO(
                $dsn,
                getenv('DB_USER') ?: 'app_user',
                getenv('DB_PASSWORD') ?: '',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ],
            );
        }

        return self::$instance;
    }
}
