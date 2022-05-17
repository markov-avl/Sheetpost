<?php

namespace Sheetpost\Models;

use Sheetpost\Database\Database;

abstract class APIResponse
{
    protected Database $db;
    protected array $parameters;
    protected string $query;

    public function __construct(string $host, string $dbname, string $user, string $password)
    {
        $this->db = new Database($host, $dbname, $user, $password);
    }

    private function hasAllGetParameters(array $getParameters): bool
    {
        foreach($this->parameters as $parameter) {
            if (!isset($getParameters[$parameter])) {
                return false;
            }
        }
        return true;
    }

    public function getResponse(array $getParameters): array
    {
        if ($this->hasAllGetParameters($getParameters)) {
            $this->db->connect();
            return $this->getQueryResponse($getParameters);
        }
        return ['success' => false, 'error' => 'missing parameters'];
    }

    protected abstract function getQueryResponse(array $getParameters): array;
}