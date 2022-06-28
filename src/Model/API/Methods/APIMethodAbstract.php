<?php

namespace Sheetpost\Model\API\Methods;

abstract class APIMethodAbstract
{
    protected array $parameters;

    public function __construct(array $parameters)
    {
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