<?php

namespace Sheetpost\API;

use Exception;
use Sheetpost\Database\Records\Post;
use Sheetpost\Database\Records\User;
use Sheetpost\Database\Repositories\PostRepository;
use Sheetpost\Database\Repositories\UserRepository;
use Sheetpost\Models\APIResponse;

class DeletePost extends APIResponse
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
        if (UserRepository::getByFields(['username' => $getParameters['username'], 'password' => $getParameters['password']])) {
            $post = PostRepository::getById($getParameters['post_id']);
            if ($post === null) {
                return ['success' => false, 'error' => 'post not found'];
            }
            if ($post->username !== $getParameters['username']) {
                return ['success' => false, 'error' => 'it is not a post created by this user'];
            }
            PostRepository::remove($post);
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'invalid username or password'];
    }
}