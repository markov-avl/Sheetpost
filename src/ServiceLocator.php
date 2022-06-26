<?php

namespace Sheetpost;

class ServiceLocator
{
    private array $services;

    public function contains(string $serviceName): bool
    {
        return isset($this->services[$serviceName]);
    }

    public function add(string $serviceName, object $service): void
    {
        $this->services[$serviceName] = $service;
    }

    public function get(string $serviceName): mixed
    {
        if (is_callable($this->services[$serviceName])) {
            $this->services[$serviceName] = $this->services[$serviceName]($this);
        }
        return $this->services[$serviceName];
    }
}