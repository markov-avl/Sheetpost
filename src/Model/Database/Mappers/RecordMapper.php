<?php

namespace Sheetpost\Model\Database\Mappers;

use Sheetpost\Model\Database\Adapter;
use Sheetpost\Model\Database\Records\Record;

abstract class RecordMapper
{
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
     * @return array of column => value from vars of the instance
     */
    protected static function getColumnValuesByClassInstance(object $classInstance): array
    {
        $columns = get_class_vars($classInstance::class);
        return array_combine(
            array_map(function ($column) {
                return self::camelCaseToSnakeCase($column);
            }, array_keys($columns)),
            array_map(function ($column) use ($classInstance) {
                return $classInstance->$column;
            }, array_keys($columns))
        );
    }

    protected static function wrapRecords(array $records, string $className): array
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
        }, $records);
    }

    /**
     * Updates if record exists else inserts
     *
     * @param Record $record
     * @return void
     */
    public static function save(Record $record): void
    {
        $tableName = $record->getTableName();
        $columnValues = self::getColumnValuesByClassInstance($record);
        $columns = join(', ', array_keys($columnValues));
        $values = join(', ', array_map(function ($column) {
            return ":$column";
        }, array_keys($columnValues)));
        $array = join(', ', array_map(function ($column) {
            return "$column = :$column";
        }, array_keys($columnValues)));
        Adapter::execute("
            INSERT INTO $tableName ($columns)
            VALUES ($values) ON DUPLICATE KEY UPDATE $array
        ", array_combine(array_map(function ($column) {
            return ":$column";
        }, array_keys($columnValues)), $columnValues));
    }

    /**
     * @param Record $record
     * @return void
     */
    public static function remove(Record $record): void
    {
        $tableName = $record->getTableName();
        $columnValues = self::getColumnValuesByClassInstance($record);
        $array = join(' AND ', array_map(function ($column) {
            return "$column = :$column";
        }, array_keys($columnValues)));
        Adapter::execute("
            DELETE FROM $tableName
            WHERE $array
        ", array_combine(array_map(function ($column) {
            return ":$column";
        }, array_keys($columnValues)), $columnValues));
    }

    /**
     * @param string $className
     * @param array $primaryKeys
     * @return array
     */
    public static function getByPrimaryKeys(string $className, array $primaryKeys): mixed
    {
        $tableName = call_user_func(array($className, 'getTableName'));
        $array = join(' AND ', array_map(function ($column) {
            return "$column = :$column";
        }, array_keys($primaryKeys)));
        $record = Adapter::fetchAll("
            SELECT *
            FROM $tableName
            WHERE $array
        ", array_combine(array_map(function ($column) {
            return ":$column";
        }, array_keys($primaryKeys)), $primaryKeys));
        return self::wrapRecords($record, $className)[0];
    }

    /**
     * @param string $className
     * @param array $fields
     * @return mixed
     */
    public static function getByFields(string $className, array $fields): array
    {
        $tableName = call_user_func(array($className, 'getTableName'));
        $array = join(' AND ', array_map(function ($column) {
            return "$column = :$column";
        }, array_keys($fields)));
        $records = Adapter::fetchAll("
            SELECT *
            FROM $tableName
            WHERE $array
        ", array_combine(array_map(function ($column) {
            return ":$column";
        }, array_keys($fields)), $fields));
        return self::wrapRecords($records, $className);
    }

    public static function all(string $className): array
    {
        $tableName = call_user_func(array($className, 'getTableName'));
        $records = Adapter::fetchAll("
            SELECT *
            FROM $tableName
        ");
        return self::wrapRecords($records, $className);
    }
}