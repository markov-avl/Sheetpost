<?php

namespace Sheetpost\API;

use Exception;
use Sheetpost\Database\Records\User;
use Sheetpost\Database\Repositories\UserRepository;
use Sheetpost\Models\APIResponse;
use Sheetpost\Models\LoggerWrapper;
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

        $logger = new LoggerWrapper('logger');
        $logger->debug('Тут');
        if (UserRepository::getByUsername($getParameters['username']) !== null) {
            return ['success' => false, 'error' => 'this username is already taken'];
        }
        $newUser = new User($getParameters['username'], $getParameters['password']);
        UserRepository::save($newUser);
        return ['success' => true];
    }
}