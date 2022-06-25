<?php

namespace Sheetpost\Database;

use BadMethodCallException;
use PDO;
use PDOStatement;

class PostExtended extends ActiveRecord
{
    public int $id;
    public string $username;
    public string $date;
    public string $message;
    public int $sheetCount;
    public int $sheeted;

    public function __construct(int $id, string $username, string $date, string $message, int $sheetCount, int $sheeted)
    {
        $this->id = $id;
        $this->username = $username;
        $this->date = $date;
        $this->message = $message;
        $this->sheetCount = $sheetCount;
        $this->sheeted = $sheeted;
    }

    /**
     * @return PostExtended[]
     */
    protected static function wrapRecords(PDOStatement $statement): array
    {
        return array_map(function (array $record) {
            return new PostExtended(
                $record['id'],
                $record['username'],
                $record['date'],
                $record['message'],
                $record['sheet_count'],
                $record['sheeted']
            );
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return PostExtended[]
     */
    public static function all(): array
    {
        $statement = parent::getConnection()->prepare("
            SELECT *,
                   (SELECT COUNT(*)
                    FROM sheets
                    WHERE id = post_id) AS sheet_count,
                   TRUE AS sheeted
            FROM posts
            ORDER BY date DESC
        ");
        $statement->execute();
        return self::wrapRecords($statement);
    }

    /**
     * @return PostExtended[]
     */
    public static function allAuthorized(string $authorizedUser): array
    {
        $statement = parent::getConnection()->prepare("
            SELECT *,
                   (SELECT COUNT(*)
                    FROM sheets
                    WHERE id = post_id) AS sheet_count,
                   EXISTS(SELECT 1
                          FROM sheets
                          WHERE id = post_id
                            AND sheets.username = :username) AS sheeted
            FROM posts
            ORDER BY date DESC
        ");
        $statement->execute([':username' => $authorizedUser]);
        return self::wrapRecords($statement);
    }

    /**
     * @return PostExtended[]
     */
    public static function getByUsername(string $username): array
    {
        $statement = parent::getConnection()->prepare("
            SELECT *,
                   (SELECT COUNT(*)
                    FROM sheets
                    WHERE id = post_id) AS sheet_count,
                   EXISTS(SELECT 1
                          FROM sheets
                          WHERE id = post_id
                            AND sheets.username = :username) AS sheeted
            FROM posts
            WHERE username = :username
            ORDER BY date DESC
        ");
        $statement->execute([':username' => $username]);
        return self::wrapRecords($statement);
    }

    public function save(): void
    {
        throw new BadMethodCallException();
    }

    public function remove(): void
    {
        throw new BadMethodCallException();
    }

    public static function getByFields(array $fields): array
    {
        $array = join(' AND ', array_map(function ($column) {
            return "$column = :$column";
        }, array_keys($fields)));
        $statement = parent::getConnection()->prepare("
            SELECT *,
                   (SELECT COUNT(*)
                    FROM sheets
                    WHERE id = post_id) AS sheet_count,
                   EXISTS(SELECT 1
                          FROM sheets
                          WHERE id = post_id) AS sheeted
            FROM posts
            WHERE $array
            ORDER BY date DESC
        ");
        $statement->execute(
            array_combine(array_map(function ($column) {
                return ":$column";
            }, array_keys($fields)), $fields)
        );
        return self::wrapRecords($statement);
    }
}