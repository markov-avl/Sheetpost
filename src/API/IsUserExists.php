<?php

namespace Sheetpost\API;

use Exception;
use Sheetpost\Database\Records\User;
use Sheetpost\Database\Repositories\UserRepository;
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
            "exists" => isset(UserRepository::getByFields([
                    'username' => $getParameters['username'],
                    'password' => $getParameters['password']
                ])[0])
        ];
    }
}