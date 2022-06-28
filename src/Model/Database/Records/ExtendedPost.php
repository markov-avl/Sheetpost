<?php

namespace Sheetpost\Model\Database\Records;

class ExtendedPost implements RecordInterface
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

    public static function getTableName(): string
    {
        return 'posts';
    }
}