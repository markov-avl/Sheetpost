<?php

namespace Sheetpost\API;

use DateTime;
use Exception;
use Sheetpost\Models\APIResponse;
use Sheetpost\Models\StringParameter;

class CreateNewPost extends APIResponse
{
    public function __construct(string $host, string $dbname, string $user, string $password)
    {
        parent::__construct($host, $dbname, $user, $password);
        $this->parameters = ['username', 'password', 'message'];
        $this->query = 'INSERT INTO posts (username, date, message) VALUES (:username, :date, :message)';
    }

    /**
     * @throws Exception
     */
    protected function getQueryResponse(array $getParameters): array
    {
        $getParameters['message'] = trim($getParameters['message']);
        $message = new StringParameter($getParameters['message'], 'message', 4096, false);
        $messageError = $message->check();
        if ($messageError) {
            return ['success' => false, 'error' => $messageError];
        }

        if ($this->db->isUserExists($getParameters['username'], $getParameters['password'])) {
            $this->db->query($this->query, [
                ':username' => $getParameters['username'],
                ':date' => (new DateTime())->format('Y-m-d H:i:s'),
                ':message' => $getParameters['message']
            ]);
            return ['success' => true, 'post_id' => $this->db->getLastInsertedId()];
        }
        return ['success' => false, 'error' => 'invalid username or password'];
    }
}