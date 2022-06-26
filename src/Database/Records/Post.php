<?php

namespace Sheetpost\Database\Records;

class Post extends Record
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

    public static function getTableName(): string
    {
        return 'posts';
    }
}