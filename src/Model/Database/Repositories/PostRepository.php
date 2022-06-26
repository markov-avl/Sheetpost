<?php

namespace Sheetpost\Model\Database\Repositories;

use Sheetpost\Model\Database\Mappers\RecordMapper;
use Sheetpost\Model\Database\Records\Post;

class PostRepository
{
    public static function save(Post $post): void
    {
        RecordMapper::save($post);
    }

    public static function remove(Post $post): void
    {
        RecordMapper::remove($post);
    }

    public static function all(): array
    {
        return RecordMapper::all(Post::class);
    }

    public static function getById(int $id): ?Post
    {
        return RecordMapper::getByPrimaryKeys(Post::class, ['id' => $id]);
    }

    public static function getByFields(array $fields): array
    {
        return RecordMapper::getByFields(Post::class, $fields);
    }
}