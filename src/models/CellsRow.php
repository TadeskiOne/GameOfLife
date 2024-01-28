<?php

namespace Tadeskione\Glider\models;

use ArrayObject;

/**
 * Class CellsRow
 */
final class CellsRow
{
    private int $lastCellIndex = 0;

    public function __construct(private ArrayObject $row = new ArrayObject([])) {}

    public function addCell(Cell $cell): void
    {
        $this->row[$this->lastCellIndex] = $cell;
        $this->lastCellIndex++;
    }

    public function getRow(): ArrayObject
    {
        return $this->row;
    }
}