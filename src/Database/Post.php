<?php

namespace Sheetpost\Database;

class Post extends ActiveRecord
{
    public int $id;
    public string $username;
    public string $date;
    public string $message;

    public function __construct(int $id, string $username, string $date, string $message)
    {
        $this->id = $id;
        $this->username = $username;
        $this->date = $date;
        $this->message = $message;
    }

    /**
     * @return Post[]
     */
    public static function all(): array
    {
        return parent::finaAll(self::class);
    }

    public static function getById(int $id): Post
    {
        return parent::getByPrimaryKeys(self::class, [
            'id' => $id
        ]);
    }

    /**
     * @return Post[]
     */
    public static function getByUsername(string $username): array
    {
        return parent::getByFields(self::class, [
            'username' => $username
        ]);
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
}