<?php

namespace Sheetpost\Model\Database\Adapters;

interface DatabaseAdapterInterface
{
    public function fetch(string $query, array $values = []): array;

    public function fetchAll(string $query, array $values = []): array;

    public function execute(string $query, array $values = []): void;
}