<?php

namespace Sheetpost\API;

use Exception;
use Sheetpost\Database\Post;
use Sheetpost\Database\User;
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
        if (User::getByFields(['username' => $getParameters['username'], 'password' => $getParameters['password']])) {
            $post = Post::getById($getParameters['post_id']);
            if ($post === null) {
                return ['success' => false, 'error' => 'post not found'];
            }
            if ($post->username !== $getParameters['username']) {
                return ['success' => false, 'error' => 'it is not a post created by this user'];
            }
            $post->remove();
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'invalid username or password'];
    }
}