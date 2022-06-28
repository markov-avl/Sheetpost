<?php

namespace Sheetpost\Model\Database\Records;

class User implements RecordInterface
{
    public string $username;
    public string $password;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public static function getTableName(): string
    {
        return 'users';
    }
}