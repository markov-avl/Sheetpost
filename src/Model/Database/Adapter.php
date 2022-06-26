<?php

namespace Sheetpost\Model\Database;

use Dotenv\Dotenv;
use PDO;
use PDOStatement;

abstract class Adapter
{
    protected static ?PDO $connection = null;

    protected static function getConnection(): PDO
    {
        if (self::$connection === null) {
            Dotenv::createImmutable(dirname(__DIR__, 2))->load();
            self::$connection = new PDO(
                "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};port={$_ENV['DB_PORT']}",
                $_ENV['DB_USER'],
                $_ENV['DB_PASSWORD']
            );
        }
        return self::$connection;
    }

    public static function getStatement(string $query, array $values): PDOStatement
    {
        $statement = self::getConnection()->prepare($query);
        $statement->execute($values);
        return $statement;
    }

    public static function fetch(string $query, array $values = []): array
    {
        return self::getStatement($query, $values)->fetch(PDO::FETCH_ASSOC);
    }

    public static function fetchAll(string $query, array $values = []): array
    {
        return self::getStatement($query, $values)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function execute(string $query, array $values = []): void
    {
        self::getStatement($query, $values);
    }
}