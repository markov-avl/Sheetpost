<?php

namespace Sheetpost\Model\API\Parameters;

interface ParameterInterface
{
    public function check(): ?string;
}