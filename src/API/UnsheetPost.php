<?php

namespace Sheetpost\API;

use Exception;

class UnsheetPost extends Response
{
    public function __construct(string $host, string $dbname, string $user, string $password)
    {
        parent::__construct($host, $dbname, $user, $password);
        $this->parameters = ['username', 'password', 'post_id'];
    }

    /**
     * @throws Exception
     */
    protected function getQueryResponse(array $getParameters): array
    {
        $username = $getParameters['username'];
        $password = $getParameters['password'];
        $postId = $getParameters['post_id'];
        if (!ctype_digit($postId)) {
            return ['success' => false, 'error' => 'post id is not an integer'];
        }
        if ($this->db->isUserExists($username, $password)) {
            $this->db->query("
                DELETE FROM sheets
                    WHERE username='$username' AND post_id=$postId"
            );
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'invalid username or password'];
    }
}