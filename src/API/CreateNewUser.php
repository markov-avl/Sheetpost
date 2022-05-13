<?php

namespace Sheetpost\API;

use Exception;
use PDOException;
use Sheetpost\Models\APIResponse;
use Sheetpost\Models\StringParameter;


class CreateNewUser extends APIResponse
{
    public function __construct(string $host, string $dbname, string $user, string $password)
    {
        parent::__construct($host, $dbname, $user, $password);
        $this->parameters = ['username', 'password'];
        $this->query = 'INSERT INTO users (username, password) VALUES (:username, :password)';
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

        try {
            $this->db->query($this->query, [
                ':username' => $getParameters['username'],
                ':password' => $getParameters['password']
            ]);
            return ['success' => true];
        } catch (PDOException) {
            return ['success' => false, 'error' => 'this username is already taken'];
        }
    }
}