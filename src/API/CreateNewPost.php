<?php

namespace Sheetpost\API;

use DateTime;
use Exception;
use Sheetpost\Database\Post;
use Sheetpost\Database\User;
use Sheetpost\Models\APIResponse;
use Sheetpost\Models\StringParameter;

class CreateNewPost extends APIResponse
{
    public function __construct()
    {
        parent::__construct(['username', 'password', 'message']);
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
            $newPost = new Post(
                null,
                $getParameters['username'],
                (new DateTime())->format('Y-m-d H:i:s'),
                $getParameters['message']
            );
            $newPost->save();
            return ['success' => true, 'post_id' => $newPost->id];
        }
        return ['success' => false, 'error' => 'invalid username or password'];
    }
}