<?php

namespace Sheetpost\API;

use DateTime;
use Exception;

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
    protected function getQueryResponse(array $getParameters): array
    {
        $username = $getParameters['username'];
        $password = $getParameters['password'];
        $message = addcslashes($getParameters['message'], "'");

        if (strlen($message) > 4096) {
            return ['success' => false, 'error' => 'message is too long (maximum 4096 characters)'];
        }
        if ($this->db->isUserExists($username, $password)) {
            $date = (new DateTime())->format('Y-m-d H:i:s');
            $this->db->query("
                INSERT INTO posts (username, date, message)
                    VALUES ('$username', STR_TO_DATE('$date', '%Y-%m-%d %H:%i:%s'), '$message')"
            );
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'invalid username or password'];
    }
}