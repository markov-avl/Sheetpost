<?php

namespace Sheetpost\Model\Database\Records;

class Sheet extends Record
{
    public string $username;
    public int $postId;

    public function __construct(string $username, int $postId)
    {
        $this->username = $username;
        $this->postId = $postId;
    }

    public static function getTableName(): string
    {
        return 'sheets';
    }
}