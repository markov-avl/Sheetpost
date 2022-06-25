<?php

namespace Sheetpost\Database;

use Dotenv\Dotenv;
use PDO;
use PDOStatement;

abstract class ActiveRecord
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

    protected static abstract function wrapRecords(PDOStatement $statement): array;

    /**
     * @return array
     */
    public static abstract function all(): array;

    /**
     * @return void
     */
    public abstract function save(): void;

    /**
     * @return void
     */
    public abstract function remove(): void;

    /**
     * @param array $fields
     * @return array
     */
    public abstract static function getByFields(array $fields): array;
}