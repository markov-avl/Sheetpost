<?php

namespace Sheetpost\Model\API\Methods;

use DateTime;
use Exception;
use Sheetpost\Model\API\Parameters\StringParameter;
use Sheetpost\Model\Database\Records\Post;
use Sheetpost\Model\Database\Repositories\PostRepository;
use Sheetpost\Model\Database\Repositories\UserRepository;

class CreateNewPost extends APIMethodAbstract
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

        if (UserRepository::getByFields(['username' => $getParameters['username'], 'password' => $getParameters['password']])) {
            $newPost = new Post(
                null,
                $getParameters['username'],
                (new DateTime())->format('Y-m-d H:i:s'),
                $getParameters['message']
            );
            PostRepository::save($newPost);
            return ['success' => true, 'post_id' => $newPost->id];
        }
        return ['success' => false, 'error' => 'invalid username or password'];
    }
}