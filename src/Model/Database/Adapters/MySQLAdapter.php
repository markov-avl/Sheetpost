<?php

namespace Sheetpost\Model\Database\Adapters;

use PDO;
use PDOStatement;

class MySQLAdapter implements DatabaseAdapterInterface
{
    protected PDO $connection;

    public function __construct(string $dbHost, int $dbPort, string $dbName, string $dbUser, string $dbPassword)
    {
        $this->connection = new PDO("mysql:host=$dbHost;port=$dbPort;dbname=$dbName", $dbUser, $dbPassword);
    }

    public function getStatement(string $query, array $values): PDOStatement
    {
        $statement = $this->connection->prepare($query);
        $statement->execute($values);
        return $statement;
    }

    public function fetch(string $query, array $values = []): array
    {
        return $this->getStatement($query, $values)->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchAll(string $query, array $values = []): array
    {
        return $this->getStatement($query, $values)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function execute(string $query, array $values = []): void
    {
        $this->getStatement($query, $values);
    }
}