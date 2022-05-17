<?php

namespace Sheetpost\Database;

use Exception;
use PDO;
use PDOStatement;

class Database
{
    public string $host;
    public string $name;
    public string $user;
    public string $password;
    private ?PDO $connection;

    public function __construct(string $host, string $name, string $user, string $password)
    {
        $this->host = $host;
        $this->name = $name;
        $this->user = $user;
        $this->password = $password;
        $this->connection = null;
    }

    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            $this->connection = new PDO("mysql:host=$this->host;dbname=$this->name", $this->user, $this->password);
        }
        return $this->connection;
    }

    public function createUser(string $username, string $password): User
    {
        return new User($username, $password);
    }

    public function connect(): void
    {
        $this->connection = new PDO("mysql:host=$this->host;dbname=$this->name", $this->user, $this->password);
    }

    /**
     * @throws Exception
     */
    public function query(string $query, array $options = []): PDOStatement
    {
        if ($this->connection !== null) {
            $statement = $this->connection->prepare($query);
            $statement->execute($options);
            return $statement;
        }
        throw new Exception("No BD connection created");
    }

    /**
     * @throws Exception
     */
    public function getLastInsertedId(): int
    {
        if ($this->connection !== null) {
            return $this->connection->lastInsertId();
        }
        throw new Exception("No BD connection created");
    }

    /**
     * @throws Exception
     */
    public function isUserExists(string $username, string $password): bool
    {
        return $this->query('
            SELECT * FROM users
            WHERE username = :username AND password = :password
        ', [':username' => $username, ':password' => $password])->rowCount() === 1;
    }

    /**
     * @throws Exception
     */
    public function getAllPosts(string|null $authorizedUser = null): array
    {
        $sheeted = $authorizedUser
            ? '(SELECT COUNT(*) FROM sheets WHERE posts.id = sheets.post_id AND sheets.username = :username)' : 1;
        return $this->query("
            SELECT *,
                   (SELECT COUNT(*) FROM sheets WHERE posts.id=sheets.post_id) as sheet_count,
                   $sheeted as sheeted
            FROM posts
            ORDER BY date DESC
        ", $authorizedUser ? [':username' => $authorizedUser] : [])->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @throws Exception
     */
    public function getUserPosts(string $username): array
    {
        return $this->query('
            SELECT *,
                   (SELECT COUNT(*) FROM sheets
                                    WHERE posts.id = sheets.post_id) as sheet_count,
                   (SELECT COUNT(*) FROM sheets
                                    WHERE posts.id = sheets.post_id AND sheets.username = :username) as sheeted
            FROM posts
            WHERE username = :username
            ORDER BY date DESC
        ', [':username' => $username])->fetchAll(PDO::FETCH_ASSOC);
    }
}