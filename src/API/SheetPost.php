<?php

namespace Sheetpost\API;

use Exception;
use JetBrains\PhpStorm\ArrayShape;

class SheetPost extends Response
{
    public function __construct(string $host, string $dbname, string $user, string $password)
    {
        parent::__construct($host, $dbname, $user, $password);
        $this->parameters = ['username', 'password', 'post_id'];
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
            $postId = $getParameters['post_id'];
            $this->db->query("
                INSERT IGNORE INTO sheets (username, post_id)
                    VALUES ('$username', $postId)"
            );
            return ['success' => true];
        }
        return ['success' => false];
    }
}