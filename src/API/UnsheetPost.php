<?php

namespace Sheetpost\API;

use Exception;
use Sheetpost\Models\APIResponse;
use Sheetpost\Models\IntegerParameter;

class UnsheetPost extends APIResponse
{
    public function __construct(string $host, string $dbname, string $user, string $password)
    {
        parent::__construct($host, $dbname, $user, $password);
        $this->parameters = ['username', 'password', 'post_id'];
        $this->query = 'DELETE FROM sheets WHERE username = :username AND post_id = :post_id';
    }

    /**
     * @throws Exception
     */
    protected function getQueryResponse(array $getParameters): array
    {
        $postId = new IntegerParameter($getParameters['post_id'], 'post id', 0, 4294967295);
        $postIdError = $postId->check();
        if ($postIdError) {
            return ['success' => false, 'error' => $postIdError];
        }

        if ($this->db->isUserExists($getParameters['username'], $getParameters['password'])) {
            $this->db->query($this->query, [
                ':username' => $getParameters['username'],
                ':post_id' => $getParameters['post_id']
            ]);
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'invalid username or password'];
    }
}