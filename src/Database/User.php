<?php

namespace Sheetpost\Database;

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
    public static function all(): array
    {
        return parent::finaAll(self::class);
    }

    public static function getById(string $username): User
    {
        return parent::getByPrimaryKeys(self::class, [
            'username' => $username
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
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}