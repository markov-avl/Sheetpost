<?php

namespace Sheetpost\API;

use DateTime;
use Exception;
use JetBrains\PhpStorm\ArrayShape;

class CreateNewPost extends Response
{
    public function __construct(string $host, string $dbname, string $user, string $password)
    {
        parent::__construct($host, $dbname, $user, $password);
        $this->parameters = ['username', 'password', 'message'];
    }

    /**
     * @throws Exception
     */
    #[ArrayShape(["success" => "bool"])]
    protected function getQueryResponse(array $getParameters): array
    {
        $username = $getParameters['username'];
        $password = $getParameters['password'];
        if ($this->db->isUserExists($username, $password)) {
            $message = $getParameters['message'];
            $date = (new DateTime())->format('Y-m-d H:i:s');
            $this->db->query("
                INSERT INTO posts (username, date, message)
                    VALUES ('$username', STR_TO_DATE('$date', '%Y-%m-%d %H:%i:%s'), '$message')"
            );
            return ['success' => true];
        }
        return ['success' => false];
    }
}