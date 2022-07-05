<?php

namespace Sheetpost\Model\API\Methods;

use Doctrine\ORM\EntityManagerInterface;

abstract class APIMethodAbstract
{
    protected EntityManagerInterface $entityManager;
    protected array $parameters;

    public function __construct(EntityManagerInterface $entityManager, array $parameters)
    {
        $this->entityManager = $entityManager;
        $this->parameters = $parameters;
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
            return $this->getQueryResponse($getParameters);
        }
        return ['success' => false, 'error' => 'missing parameters'];
    }

    protected abstract function getQueryResponse(array $getParameters): array;
}