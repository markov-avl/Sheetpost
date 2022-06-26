<?php

namespace Sheetpost\Model\API\Parameters;

class StringParameter
{
    public string $data;
    public string $name;
    public int $maxLength;
    public bool $noSpaces;

    public function __construct(string $data, string $name, int $maxLength, bool $noSpaces = true)
    {
        $this->data = $data;
        $this->name = $name;
        $this->maxLength = $maxLength;
        $this->noSpaces = $noSpaces;
    }

    public function check(): string
    {
        if (!$this->data) {
            return "$this->name is empty";
        }
        if (strlen($this->data) > $this->maxLength) {
            return "$this->name is too long (maximum $this->maxLength characters)";
        }
        if ($this->noSpaces && str_contains($this->data, ' ')) {
            return "$this->name must not contain spaces";
        }
        return "";
    }
}