<?php

namespace Sheetpost\Database;

use PDO;
use PDOStatement;

class User extends ActiveRecord
{

    public string $username;
    public string $password;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return User[]
     */
    protected static function wrapRecords(PDOStatement $statement): array
    {
        return array_map(function (array $record) {
            return new User($record['username'], $record['password']);
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return User[]
     */
    public static function all(): array
    {
        $statement = self::getConnection()->prepare("
            SELECT *
            FROM users;
        ");
        $statement->execute();
        return self::wrapRecords($statement);
    }

    public static function getById(string $username): ?User
    {
        $statement = self::getConnection()->prepare("
            SELECT *
            FROM users
            WHERE username = :username;
        ");
        $statement->execute([':username' => $username]);
        return self::wrapRecords($statement)[0] ?? null;
    }

    public function save(): void
    {
        $statement = self::getConnection()->prepare("
            INSERT INTO users (username, password)
            VALUES (:username, :password)
            ON DUPLICATE KEY UPDATE username = :username, password = :password;
        ");
        $statement->execute([':username' => $this->username, ':password' => $this->password]);
    }

    public function remove(): void
    {
        $statement = self::getConnection()->prepare("
            DELETE FROM users
            WHERE username = :username;
        ");
        $statement->execute([':username' => $this->username]);
    }

    /**
     * @param array $fields
     * @return User[]
     */
    public static function getByFields(array $fields): array
    {
        $array = join(' AND ', array_map(function ($column) {
            return "$column = :$column";
        }, array_keys($fields)));
        $statement = self::getConnection()->prepare("
            SELECT *
            FROM users
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