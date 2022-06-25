<?php

namespace Sheetpost\Database;

use PDO;
use PDOStatement;

class Post extends ActiveRecord
{
    public ?int $id;
    public string $username;
    public string $date;
    public string $message;

    public function __construct(?int $id, string $username, string $date, string $message)
    {
        $this->id = $id;
        $this->username = $username;
        $this->date = $date;
        $this->message = $message;
    }

    /**
     * @return Post[]
     */
    protected static function wrapRecords(PDOStatement $statement): array
    {
        return array_map(function (array $record) {
            return new Post($record['id'], $record['username'], $record['date'], $record['message']);
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return Post[]
     */
    public static function all(): array
    {
        $statement = self::getConnection()->prepare("
            SELECT *
            FROM posts;
        ");
        $statement->execute();
        return self::wrapRecords($statement);
    }

    public static function getById(int $id): ?Post
    {
        $statement = self::getConnection()->prepare("
            SELECT *
            FROM posts
            WHERE id = :id;
        ");
        $statement->execute([':id' => $id]);
        return self::wrapRecords($statement)[0] ?? null;
    }

    public function save(): void
    {
        $statement = self::getConnection()->prepare("
            INSERT INTO posts (id, username, date, message)
            VALUES (:id, :username, :date, :message)
            ON DUPLICATE KEY UPDATE id = :id, username = :username, date = :date, message = :message;
        ");
        $statement->execute([
            ':id' => $this->id,
            ':username' => $this->username,
            ':date' => $this->date,
            ':message' => $this->message
        ]);
        if (!isset($this->id)) {
            $this->id = self::getConnection()->lastInsertId();
        }
    }

    public function remove(): void
    {
        $statement = self::getConnection()->prepare("
            DELETE FROM posts
            WHERE id = :id;
        ");
        $statement->execute([':id' => $this->id]);
    }

    /**
     * @param array $fields
     * @return Post[]
     */
    public static function getByFields(array $fields): array
    {
        $array = join(' AND ', array_map(function ($column) {
            return "$column = :$column";
        }, array_keys($fields)));
        $statement = self::getConnection()->prepare("
            SELECT *
            FROM posts
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