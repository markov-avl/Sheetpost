<?php

namespace Sheetpost\API;

use Exception;
use Sheetpost\Database\User;
use Sheetpost\Models\APIResponse;

class IsUserExists extends APIResponse
{
    public function __construct()
    {
        parent::__construct(['username', 'password']);
    }

    /**
     * @throws Exception
     */
    protected function getQueryResponse(array $getParameters): array
    {
        return [
            "success" => true,
            "exists" => isset(User::getByFields([
                    'username' => $getParameters['username'],
                    'password' => $getParameters['password']
                ])[0])
        ];
    }
}