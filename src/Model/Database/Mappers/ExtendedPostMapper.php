<?php

namespace Sheetpost\Model\Database\Mappers;

use Sheetpost\Model\Database\Adapter;

abstract class ExtendedPostMapper extends RecordMapper
{
    public static function all(string $className): array
    {
        $tableName = call_user_func(array($className, 'getTableName'));
        $records = Adapter::fetchAll("
            SELECT *,
                   (SELECT COUNT(*)
                    FROM sheets
                    WHERE id = post_id) AS sheet_count,
                   TRUE                 AS sheeted
            FROM $tableName
            ORDER BY date DESC
        ");
        return self::wrapRecords($records, $className);
    }

    public static function allAuthorized(string $className, string $authorizedUser): array
    {
        $tableName = call_user_func(array($className, 'getTableName'));
        $records = Adapter::fetchAll("
            SELECT *,
                   (SELECT COUNT(*)
                    FROM sheets
                    WHERE id = post_id) AS sheet_count,
                   EXISTS(SELECT 1
                          FROM sheets
                          WHERE id = post_id
                            AND sheets.username = :username) AS sheeted
            FROM $tableName
            ORDER BY date DESC
        ", [
            ':username' => $authorizedUser
        ]);
        return self::wrapRecords($records, $className);
    }

    public static function allUserPosts(string $className, string $username): array
    {
        $tableName = call_user_func(array($className, 'getTableName'));
        $records = Adapter::fetchAll("
            SELECT *,
                   (SELECT COUNT(*)
                    FROM sheets
                    WHERE id = post_id)                      AS sheet_count,
                   EXISTS(SELECT 1
                          FROM sheets
                          WHERE id = post_id
                            AND sheets.username = :username) AS sheeted
            FROM $tableName
            WHERE username = :username
            ORDER BY date DESC
        ", [
            ':username' => $username
        ]);
        return self::wrapRecords($records, $className);
    }

    public static function allUserSheets(string $className, string $username): array
    {
        $tableName = call_user_func(array($className, 'getTableName'));
        $records = Adapter::fetchAll("
            SELECT *,
                   (SELECT COUNT(*)
                    FROM sheets
                    WHERE id = post_id) AS sheet_count,
                   TRUE                 AS sheeted
            FROM $tableName
            WHERE EXISTS(SELECT 1
                         FROM sheets
                         WHERE id = post_id
                           AND sheets.username = :username)
            ORDER BY date DESC
        ", [
            ':username' => $username
        ]);
        return self::wrapRecords($records, $className);
    }

    public static function getByFields(string $className, array $fields): array
    {
        $tableName = call_user_func(array($className, 'getTableName'));
        $array = join(' AND ', array_map(function ($column) {
            return "$column = :$column";
        }, array_keys($fields)));
        $records = Adapter::fetchAll("
            SELECT *,
                   (SELECT COUNT(*)
                    FROM sheets
                    WHERE id = post_id) AS sheet_count,
                   TRUE                 AS sheeted
            FROM $tableName
            WHERE $array
            ORDER BY date DESC
        ", array_combine(array_map(function ($column) {
            return ":$column";
        }, array_keys($fields)), $fields));
        return self::wrapRecords($records, $className);
    }
}