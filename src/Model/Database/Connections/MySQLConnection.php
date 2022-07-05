<?php

namespace Sheetpost\Model\Database\Connections;

class MySQLConnection extends ConnectionAbstract
{
    public function __construct(string $host, int $port, string $dbname, string $user, string $password)
    {
        parent::__construct('pdo_mysql', $host, $port, $dbname, $user, $password);
    }
}