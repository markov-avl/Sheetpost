<?php

namespace Sheetpost\Model\Database\Repositories;

use Sheetpost\Model\Database\Mappers\RecordMapper;
use Sheetpost\Model\Database\Records\Sheet;

class SheetRepository
{
    public static function save(Sheet $sheet): void
    {
        RecordMapper::save($sheet);
    }

    public static function remove(Sheet $sheet): void
    {
        RecordMapper::remove($sheet);
    }

    public static function all(): array
    {
        return RecordMapper::all(Sheet::class);
    }

    public static function getByUsernameAndPostId(string $username, int $postId): ?Sheet
    {
        return RecordMapper::getByPrimaryKeys(Sheet::class, ['username' => $username, 'postId' => $postId]);
    }

    public static function getByFields(array $fields): array
    {
        return RecordMapper::getByFields(Sheet::class, $fields);
    }
}