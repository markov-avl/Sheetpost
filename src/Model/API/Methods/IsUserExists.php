<?php

namespace Sheetpost\Model\API\Methods;

use Exception;
use Sheetpost\Model\Database\Repositories\UserRepository;

class IsUserExists extends APIMethod
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