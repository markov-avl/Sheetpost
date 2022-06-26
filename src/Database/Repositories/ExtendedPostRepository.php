<?php

namespace Sheetpost\Database\Repositories;

use Sheetpost\Database\Mappers\ExtendedPostMapper;
use Sheetpost\Database\Records\ExtendedPost;

class ExtendedPostRepository
{
    public static function all(): array
    {
        return ExtendedPostMapper::all(ExtendedPost::class);
    }

    public static function allAuthorized(string $authorizedUser): array
    {
        return ExtendedPostMapper::allAuthorized(ExtendedPost::class, $authorizedUser);
    }

    public static function allUserPosts(string $username): array
    {
        return ExtendedPostMapper::allUserPosts(ExtendedPost::class, $username);
    }

    public static function allUserSheets(string $username): array
    {
        return ExtendedPostMapper::allUserSheets(ExtendedPost::class, $username);
    }

    public static function getByFields(array $fields): array
    {
        return ExtendedPostMapper::getByFields(ExtendedPost::class, $fields);
    }
}