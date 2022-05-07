<?php

namespace Sheetpost\API;

use Exception;
use JetBrains\PhpStorm\ArrayShape;
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
    #[ArrayShape(["success" => "bool"])]
    protected function getQueryResponse(array $getParameters): array
    {
        $username = $getParameters['username'];
        $password = $getParameters['password'];
        try {
            $this->db->query("
                INSERT INTO users (username, password)
                    VALUES ('$username', '$password')"
            );
            return ['success' => true];
        } catch (PDOException) {
            return ['success' => false];
        }
    }
}