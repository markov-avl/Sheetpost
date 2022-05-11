<?php

namespace Sheetpost\API;

use Exception;
use PDOException;

class CreateNewUser extends Response
{
    public function __construct(string $host, string $dbname, string $user, string $password)
    {
        parent::__construct($host, $dbname, $user, $password);
        $this->parameters = ['username', 'password'];
    }

    /**
     * @throws Exception
     */
    protected function getQueryResponse(array $getParameters): array
    {
        $username = $getParameters['username'];
        $password = $getParameters['password'];
        if (strlen($username) > 32) {
            return ['success' => false, 'error' => 'username is too long (maximum 32 characters)'];
        }
        if (strlen($password) > 64) {
            return ['success' => false, 'error' => 'password is too long (maximum 64 characters)'];
        }
        try {
            $this->db->query("
                INSERT INTO users (username, password)
                    VALUES ('$username', '$password')"
            );
            return ['success' => true];
        } catch (PDOException) {
            return ['success' => false, 'error' => 'this username is already taken'];
        }
    }
}