<?php

namespace Sheetpost\Database;

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
    public static function all(): array
    {
        return parent::finaAll(self::class);
    }

    /**
     * @param string $username
     * @param int $postId
     * @return Sheet
     */
    public static function getById(string $username, int $postId): Sheet
    {
        return parent::getByPrimaryKeys(self::class, [
            'username' => $username,
            'post_id' => $postId
        ]);
    }

    /**
     * @return Sheet[]
     */
    public static function getByUsername(string $username): array
    {
        return parent::getByFields(self::class, [
            'username' => $username
        ]);
    }

    /**
     * @return Sheet[]
     */
    public static function getByPostId(int $postId): array
    {
        return parent::getByFields(self::class, [
            'post_id' => $postId
        ]);
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return int
     */
    public function getPostId(): int
    {
        return $this->postId;
    }
}