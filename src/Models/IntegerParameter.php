<?php

namespace Sheetpost\Models;

class IntegerParameter
{
    public string $data;
    public string $name;
    public int $minValue;
    public int $maxValue;

    public function __construct(string $data, string $name, int $minValue, int $maxValue)
    {
        $this->data = $data;
        $this->name = $name;
        $this->minValue = $minValue;
        $this->maxValue = $maxValue;
    }

    public function check(): string
    {
        if (!ctype_digit($this->data)) {
            return "$this->name is not an integer";
        }
        if ($this->data < $this->minValue) {
            return "$this->name must be greater than or equal to $this->minValue";
        }
        if ($this->data > $this->maxValue) {
            return "$this->name must be less than or equal to $this->maxValue";
        }
        return "";
    }
}