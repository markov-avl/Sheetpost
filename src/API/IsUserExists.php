<?php

namespace Sheetpost\API;

use Exception;
use Sheetpost\Models\APIResponse;

class IsUserExists extends APIResponse
{
    public function __construct(string $host, string $dbname, string $user, string $password)
    {
        parent::__construct($host, $dbname, $user, $password);
        $this->parameters = ['username', 'password'];
        $this->query = '';
    }

    /**
     * @throws Exception
     */
    protected function getQueryResponse(array $getParameters): array
    {
        return [
            "success" => true,
            "exists" => $this->db->isUserExists($getParameters['username'], $getParameters['password'])
        ];
    }
}