<?php

namespace Sheetpost\API;

use Exception;
use Sheetpost\Database\Repositories\PostRepository;
use Sheetpost\Database\Repositories\SheetRepository;
use Sheetpost\Database\Repositories\UserRepository;
use Sheetpost\Database\Records\Sheet;
use Sheetpost\Models\APIResponse;
use Sheetpost\Models\IntegerParameter;

class SheetPost extends APIResponse
{
    public function __construct()
    {
        parent::__construct(['username', 'password', 'post_id']);
    }

    /**
     * @throws Exception
     */
    protected function getQueryResponse(array $getParameters): array
    {
        $postId = new IntegerParameter($getParameters['post_id'], 'post id', 0, 4294967295);
        $postIdError = $postId->check();
        if ($postIdError) {
            return ['success' => false, 'error' => $postIdError];
        }

        if (PostRepository::getById($getParameters['post_id']) === null) {
            return ['success' => false, 'error' => 'post id not found'];
        }
        if (UserRepository::getByFields(['username' => $getParameters['username'], 'password' => $getParameters['password']])) {
            $sheet = new Sheet($getParameters['username'], $getParameters['post_id']);
            SheetRepository::save($sheet);
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'invalid username or password'];
    }
}