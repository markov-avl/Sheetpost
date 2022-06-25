<?php

namespace Sheetpost\API;

use Exception;
use Sheetpost\Database\User;
use Sheetpost\Models\APIResponse;
use Sheetpost\Models\StringParameter;


class CreateNewUser extends APIResponse
{
    public function __construct()
    {
        parent::__construct(['username', 'password']);
    }

    /**
     * @throws Exception
     */
    protected function getQueryResponse(array $getParameters): array
    {
        foreach ([
                     new StringParameter($getParameters['username'], 'username', 32),
                     new StringParameter($getParameters['password'], 'password', 64)
                 ] as $parameter) {
            $parameterError = $parameter->check();
            if ($parameterError) {
                return ['success' => false, 'error' => $parameterError];
            }
        }

        if (User::getById($getParameters['username'])) {
            return ['success' => false, 'error' => 'this username is already taken'];
        }
        $newUser = new User($getParameters['username'], $getParameters['password']);
        $newUser->save();
        return ['success' => true];
    }
}