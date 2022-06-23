<?php

namespace Sheetpost\Database;

use BadMethodCallException;

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
    public static function all(?string $authorizedUser = null): array
    {
        $sheeted = $authorizedUser
            ? '(SELECT COUNT(*) FROM sheets WHERE id = post_id AND sheets.username = :username)' : 1;
        $statement = parent::getConnection()->prepare("
            SELECT *,
                   (SELECT COUNT(*) FROM sheets WHERE id = post_id) AS sheet_count,
                   $sheeted AS sheeted
            FROM posts
            ORDER BY date DESC
        ");
        $statement->execute($authorizedUser ? [':username' => $authorizedUser] : []);
        return parent::wrapRecords($statement, self::class);
    }

    /**
     * @return Post[]
     */
    public static function getByUsername(string $username): array
    {
        $statement = parent::getConnection()->prepare("
            SELECT *,
                   (SELECT COUNT(*) FROM sheets WHERE id = post_id) as sheet_count,
                   (SELECT COUNT(*) FROM sheets WHERE id = post_id AND sheets.username = :username) as sheeted
            FROM posts
            WHERE username = :username
            ORDER BY date DESC
        ");
        $statement->execute([':username' => $username]);
        return parent::wrapRecords($statement, self::class);
    }

    public function save(): void
    {
        throw new BadMethodCallException();
    }

    public function remove(): void
    {
        throw new BadMethodCallException();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getSheetCount(): int
    {
        return $this->sheetCount;
    }

    /**
     * @return int
     */
    public function getSheeted(): int
    {
        return $this->sheeted;
    }
}