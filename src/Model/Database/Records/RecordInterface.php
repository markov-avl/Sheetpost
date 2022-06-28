<?php

namespace Sheetpost\Model\Database\Records;

interface RecordInterface
{
    public static function getTableName(): string;
}