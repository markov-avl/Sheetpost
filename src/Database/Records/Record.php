<?php

namespace Sheetpost\Database\Records;

abstract class Record
{
    public static abstract function getTableName(): string;
}