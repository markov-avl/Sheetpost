<?php

namespace Sheetpost\Model\Database\Records;

abstract class Record
{
    public static abstract function getTableName(): string;
}