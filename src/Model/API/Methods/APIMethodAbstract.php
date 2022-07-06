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

    private function getMissingParameters(array $getParameters): array
    {
        $missingParameters = [];
        foreach($this->parameters as $parameter) {
            if (!isset($getParameters[$parameter])) {
                $missingParameters[] = $parameter;
            }
        }
        return $missingParameters;
    }

    public function getResponse(array $getParameters): array
    {
        $missingParameters = $this->getMissingParameters($getParameters);
        if (empty($missingParameters)) {
            return $this->getQueryResponse($getParameters);
        }
        return ['success' => false, 'error' => 'missing parameters: ' . join(', ', $missingParameters)];
    }

    protected abstract function getQueryResponse(array $getParameters): array;
}