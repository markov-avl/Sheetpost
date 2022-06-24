<?php

namespace Sheetpost\API;

use Exception;
use Sheetpost\Database\Post;
use Sheetpost\Database\User;
use Sheetpost\Models\APIResponse;
use Sheetpost\Models\StringParameter;

class EditPost extends APIResponse
{
    public function __construct()
    {
        parent::__construct(['username', 'password', 'post_id', 'message']);
    }

    /**
     * @throws Exception
     */
    protected function getQueryResponse(array $getParameters): array
    {
        $getParameters['message'] = trim($getParameters['message']);
        $message = new StringParameter($getParameters['message'], 'message', 4096, false);
        $messageError = $message->check();
        if ($messageError) {
            return ['success' => false, 'error' => $messageError];
        }

        if (User::getByFields(['username' => $getParameters['username'], 'password' => $getParameters['password']])) {
            $post = Post::getById($getParameters['post_id']);
            if ($post === null) {
                return ['success' => false, 'error' => 'post not found'];
            }
            if ($post->username !== $getParameters['username']) {
                return ['success' => false, 'error' => 'it is not a post created by this user'];
            }
            $post->message = $getParameters['message'];
            $post->save();
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'invalid username or password'];
    }
}