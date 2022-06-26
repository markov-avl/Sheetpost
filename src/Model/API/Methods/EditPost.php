<?php

namespace Sheetpost\Model\API\Methods;

use Exception;
use Sheetpost\Model\API\Parameters\StringParameter;
use Sheetpost\Model\Database\Repositories\PostRepository;
use Sheetpost\Model\Database\Repositories\UserRepository;

class EditPost extends APIMethod
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

        if (UserRepository::getByFields(['username' => $getParameters['username'], 'password' => $getParameters['password']])) {
            $post = PostRepository::getById($getParameters['post_id']);
            if ($post === null) {
                return ['success' => false, 'error' => 'post not found'];
            }
            if ($post->username !== $getParameters['username']) {
                return ['success' => false, 'error' => 'it is not a post created by this user'];
            }
            $post->message = $getParameters['message'];
            PostRepository::save($post);
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'invalid username or password'];
    }
}