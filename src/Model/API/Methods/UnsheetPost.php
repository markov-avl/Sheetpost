<?php

namespace Sheetpost\Model\API\Methods;

use Exception;
use Sheetpost\Model\API\Parameters\IntegerParameter;
use Sheetpost\Model\Database\Records\Sheet;
use Sheetpost\Model\Database\Repositories\SheetRepository;
use Sheetpost\Model\Database\Repositories\UserRepository;

class UnsheetPost extends APIMethodAbstract
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

        if (UserRepository::getByFields(['username' => $getParameters['username'], 'password' => $getParameters['password']])) {
            $sheet = new Sheet($getParameters['username'], $getParameters['post_id']);
            SheetRepository::remove($sheet);
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'invalid username or password'];
    }
}