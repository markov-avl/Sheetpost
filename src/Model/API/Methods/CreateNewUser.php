<?php

namespace Sheetpost\Model\API\Methods;

use Exception;
use Sheetpost\Model\API\Parameters\StringParameter;
use Sheetpost\Model\Database\Records\User;
use Sheetpost\Model\Database\Repositories\UserRepository;
use Sheetpost\Model\LoggerWrapper;


class CreateNewUser extends APIMethod
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