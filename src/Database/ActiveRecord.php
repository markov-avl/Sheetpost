<?php

namespace Sheetpost\Database;

use Dotenv\Dotenv;
use PDO;
use PDOStatement;

abstract class ActiveRecord
{
    protected static ?PDO $connection = null;

    protected static function snakeCaseToCamelCase(string $s): string
    {
        return lcfirst(implode('', array_map('ucfirst', explode('_', $s))));
    }

    protected static function camelCaseToSnakeCase(string $s): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $s));
    }

    /**
     * @param object $classInstance
     * @return array of column => value from getters of the instance
     */
    protected static function getColumnValuesByClassInstance(object $classInstance): array
    {
        $methods = get_class_methods($classInstance);
        $getters = array_filter($methods, function ($method) use ($methods) {
            return str_starts_with($method, 'get');
        });
        return array_combine(
            array_map(function ($getter) { return self::camelCaseToSnakeCase(substr($getter, 3)); }, $getters),
            array_map(function ($getter) use ($classInstance) { return $classInstance->$getter(); }, $getters)
        );
    }

    protected static function getTableNameByClassName(string $className): string
    {
        $classPath = explode('\\', $className);
        return strtolower(end($classPath)) . 's';
    }

    protected static function getConnection(): PDO
    {
        if (self::$connection === null) {
            Dotenv::createImmutable(dirname(__DIR__, 2))->load();
            self::$connection = new PDO(
                "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}",
                $_ENV['DB_USER'],
                $_ENV['DB_PASSWORD']
            );
        }
        return self::$connection;
    }

    protected static function wrapRecords(PDOStatement $statement, $className): array
    {
        return array_map(function (array $record) use ($className) {
            $newRecord = [];
            foreach ($record as $key => $value) {
                if (str_contains($key, '_')) {
                    $key = self::snakeCaseToCamelCase($key);
                }
                $newRecord[$key] = $value;
            }
            return new $className(...$newRecord);
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Updates if record exists else inserts
     *
     * @return void
     */
    public function save(): void
    {
        $tableName = self::getTableNameByClassName($this::class);
        $columnValues = self::getColumnValuesByClassInstance($this);
        $columns = join(', ', array_keys($columnValues));
        $values = join(', ', array_map(function ($column) {
            return ":$column";
        }, array_keys($columnValues)));
        $array = join(', ', array_map(function ($column) {
            return "$column = :$column";
        }, array_keys($columnValues)));
        $statement = self::getConnection()->prepare(
            "INSERT INTO $tableName ($columns) VALUES ($values) ON DUPLICATE KEY UPDATE $array"
        );
        $statement->execute(
            array_combine(array_map(function ($column) {
                return ":$column";
            }, array_keys($columnValues)), $columnValues)
        );
    }

    /**
     * @return void
     */
    public function remove(): void
    {
        $tableName = self::getTableNameByClassName($this::class);
        $columnValues = self::getColumnValuesByClassInstance($this);
        $array = join(' AND ', array_map(function ($column) {
            return "$column = :$column";
        }, array_keys($columnValues)));
        $statement = self::getConnection()->prepare("DELETE FROM $tableName WHERE $array");
        $statement->execute(
            array_combine(array_map(function ($column) {
                return ":$column";
            }, array_keys($columnValues)), $columnValues)
        );
    }

    /**
     * @param string $className
     * @param array $primaryKeys
     * @return mixed
     */
    protected static function getByPrimaryKeys(string $className, array $primaryKeys): mixed
    {
        return self::getByFields($className, $primaryKeys)[0];
    }

    /**
     * @param string $className
     * @param array $fields
     * @return array
     */
    protected static function getByFields(string $className, array $fields): array
    {
        $tableName = self::getTableNameByClassName($className);
        $array = join(' AND ', array_map(function ($column) {
            return "$column = :$column";
        }, array_keys($fields)));
        $statement = self::getConnection()->prepare("SELECT * FROM $tableName WHERE $array");
        $statement->execute(
            array_combine(array_map(function ($column) {
                return ":$column";
            }, array_keys($fields)), $fields)
        );
        return self::wrapRecords($statement, $className);
    }

    public static abstract function all(): array;

    protected static function finaAll(string $className): array
    {
        $tableName = self::getTableNameByClassName($className);
        $statement = self::getConnection()->query("SELECT * FROM $tableName");
        return self::wrapRecords($statement, $className);
    }
}