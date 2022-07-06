<?php

namespace Sheetpost\Model\Database\Connections;

use ReflectionClass;

abstract class ConnectionAbstract
{
    protected string $driver;
    protected ?string $host;
    protected ?int $port;
    protected ?string $dbname;
    protected ?string $user;
    protected ?string $password;
    protected ?string $path;

    public function __construct(string  $driver,
                                ?string $host = null,
                                ?int    $port = null,
                                ?string $name = null,
                                ?string $user = null,
                                ?string $password = null,
                                ?string $path = null)
    {
        $this->driver = $driver;
        $this->host = $host;
        $this->port = $port;
        $this->dbname = $name;
        $this->user = $user;
        $this->password = $password;
        $this->path = $path;
    }

    public function toArray(): array
    {
        $reflection = new ReflectionClass($this);
        $configuration = [];
        foreach ($reflection->getProperties() as $var) {
            if (isset($this->{$var->name})) {
                $configuration[$var->name] = $this->{$var->name};
            }
        }
        return $configuration;
    }
}