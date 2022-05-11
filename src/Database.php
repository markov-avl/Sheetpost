<?php

namespace Sheetpost;

use Exception;
use PDO;
use PDOStatement;

class Database
{
    public string $host;
    public string $dbname;
    public string $user;
    public string $password;
    private PDO|null $connection;

    public function __construct(string $host, string $dbname, string $user, string $password)
    {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->user = $user;
        $this->password = $password;
        $this->connection = null;
    }

    public function connect(): void
    {
        $this->connection = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->user, $this->password);
    }

    /**
     * @throws Exception
     */
    public function query(string $query): PDOStatement
    {
        if ($this->connection !== null) {
            return $this->connection->query($query);
        }
        throw new Exception("No BD connection created");
    }

    /**
     * @throws Exception
     */
    public function isUserExists(string $username, string $password): bool
    {
        return $this->query("
            SELECT COUNT(*) as count
            FROM users
            WHERE username='$username' AND password='$password'"
        )->fetch(PDO::FETCH_ASSOC)['count'] === 1;
    }

    /**
     * @throws Exception
     */
    public function getAllPosts(string|null $authorizedUser = null): array
    {
        $sheeted = $authorizedUser
            ? "(SELECT COUNT(*) FROM sheets WHERE posts.id=sheets.post_id AND sheets.username='$authorizedUser')" : "1";
        return $this->query("
            SELECT *,
                   (SELECT COUNT(*) FROM sheets WHERE posts.id=sheets.post_id) as sheet_count,
                   $sheeted as sheeted
            FROM posts
            ORDER BY date DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @throws Exception
     */
    public function getUserPosts(string $username): array
    {
        return $this->query("
            SELECT *,
                   (SELECT COUNT(*) FROM sheets
                                    WHERE posts.id = sheets.post_id) as sheet_count,
                   (SELECT COUNT(*) FROM sheets
                                    WHERE posts.id = sheets.post_id AND sheets.username='$username') as sheeted
            FROM posts
            WHERE username='$username'
            ORDER BY date DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }
}