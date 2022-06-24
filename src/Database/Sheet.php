<?php

namespace Sheetpost\Database;

use PDO;
use PDOStatement;

class Sheet extends ActiveRecord
{
    public string $username;
    public int $postId;

    public function __construct(string $username, int $postId)
    {
        $this->username = $username;
        $this->postId = $postId;
    }

    /**
     * @return Sheet[]
     */
    protected static function wrapRecords(PDOStatement $statement): array
    {
        return array_map(function (array $record) {
            return new Sheet($record['username'], $record['post_id']);
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return Sheet[]
     */
    public static function all(): array
    {
        $statement = self::getConnection()->prepare("
            SELECT *
            FROM sheets;
        ");
        $statement->execute();
        return self::wrapRecords($statement);
    }

    public static function getById(string $username, int $postId): ?Sheet
    {
        $statement = self::getConnection()->prepare("
            SELECT *
            FROM sheets
            WHERE username = :username
              AND post_id = :post_id;
        ");
        $statement->execute([':username' => $username, ':post_id' => $postId]);
        return self::wrapRecords($statement)[0] ?? null;
    }

    public function save(): void
    {
        $statement = self::getConnection()->prepare("
            INSERT INTO sheets (username, post_id)
            VALUES (:username, :post_id)
            ON DUPLICATE KEY UPDATE username = :username, post_id = :post_id;
        ");
        $statement->execute([':username' => $this->username, ':post_id' => $this->postId]);
    }

    public function remove(): void
    {
        $statement = self::getConnection()->prepare("
            DELETE FROM sheets
            WHERE username = :username
              AND post_id = :post_id;
        ");
        $statement->execute([':username' => $this->username, ':post_id' => $this->postId]);
    }

    /**
     * @param array $fields
     * @return Sheet[]
     */
    public static function getByFields(array $fields): array
    {
        $array = join(' AND ', array_map(function ($column) {
            return "$column = :$column";
        }, array_keys($fields)));
        $statement = self::getConnection()->prepare("
            SELECT *
            FROM sheets
            WHERE $array
        ");
        $statement->execute(
            array_combine(array_map(function ($column) {
                return ":$column";
            }, array_keys($fields)), $fields)
        );
        return self::wrapRecords($statement);
    }
}