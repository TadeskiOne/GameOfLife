<?php

namespace Tadeskione\Glider\models;

use ArrayObject;

/**
 * Class CellsCollection
 */
final class CellsGrid
{
    private int $lastRowIndex = 0;

    public function __construct(private ArrayObject $rows = new ArrayObject([])) {}

    public function addRow(CellsRow $row): void
    {
        $this->rows[$this->lastRowIndex] = $row->getRow();
        $this->lastRowIndex++;
    }

    public function getGrid(): ArrayObject
    {
        return $this->rows;
    }
}