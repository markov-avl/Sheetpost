<?php

namespace Sheetpost\Database\Repositories;

use Sheetpost\Database\Mappers\RecordMapper;
use Sheetpost\Database\Records\User;

class UserRepository
{
    public static function save(User $user): void
    {
        RecordMapper::save($user);
    }

    public static function remove(User $post): void
    {
        RecordMapper::remove($post);
    }

    public static function all(): array
    {
        return RecordMapper::all(User::class);
    }

    public static function getByUsername(string $username): ?User
    {
        return RecordMapper::getByPrimaryKeys(User::class, ['username' => $username]);
    }

    public static function getByFields(array $fields): array
    {
        return RecordMapper::getByFields(User::class, $fields);
    }
}