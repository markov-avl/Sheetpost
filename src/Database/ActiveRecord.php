<?php

namespace Sheetpost\Database;

use Dotenv\Dotenv;
use PDO;

abstract class ActiveRecord
{
    protected static ?PDO $connection = null;

    /**
     * @param object $classInstance
     * @return array of column => value from getters of the instance
     */
    protected static function getColumnValuesByClassInstance(object $classInstance): array
    {
        $methods = get_class_methods($classInstance);
        $getters = array_filter($methods, function ($method) use ($methods) {
            return str_starts_with($method, 'get') && in_array('set' . substr($method, 3), $methods);
        });
        var_dump($getters);
        return array_combine(
            array_map(function ($getter) {
                return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', substr($getter, 3)));
            }, $getters),
            array_map(function ($getter) use ($classInstance) {
                return $classInstance->$getter();
            }, $getters)
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
        $values = join(', ', array_map(function ($column) { return ":$column"; }, array_keys($columnValues)));
        $array = join(', ', array_map(function ($column) { return "$column = :$column"; }, array_keys($columnValues)));
        $statement = self::getConnection()->prepare(
            "INSERT INTO $tableName ($columns) VALUES ($values) ON DUPLICATE KEY UPDATE $array"
        );
        $statement->execute(
            array_combine(array_map(function ($column) { return ":$column"; }, array_keys($columnValues)), $columnValues)
        );
    }

    /**
     * @return void
     */
    public function remove(): void
    {
        $tableName = self::getTableNameByClassName($this::class);
        $columnValues = self::getColumnValuesByClassInstance($this);

        $array = join(' AND ', array_map(function ($column) { return "$column = :$column"; }, array_keys($columnValues)));
        $statement = self::getConnection()->prepare("DELETE FROM $tableName WHERE $array");
        $statement->execute(
            array_combine(array_map(function ($column) { return ":$column"; }, array_keys($columnValues)), $columnValues)
        );
    }

    /**
     * @param string $className
     * @param array $primaryKeys
     * @return ActiveRecord
     */
    protected static function getByPrimaryKeys(string $className, array $primaryKeys): ActiveRecord
    {
        $tableName = self::getTableNameByClassName($className);

        $array = join(' AND ', array_map(function ($column) { return "$column = :$column"; }, array_keys($primaryKeys)));
        $statement = self::getConnection()->prepare("SELECT * FROM $tableName WHERE $array");
        $statement->execute(
            array_combine(array_map(function ($column) { return ":$column"; }, array_keys($primaryKeys)), $primaryKeys)
        );
        return new $className(...$statement->fetch(PDO::FETCH_ASSOC));
    }

    public static abstract function all(): array;

    protected static function finaAll(string $className): array
    {
        $tableName = self::getTableNameByClassName($className);
        $statement = self::getConnection()->query("SELECT * FROM $tableName");
        return array_map(function (array $record) use ($className) {
            return new $className(...$record);
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }
}