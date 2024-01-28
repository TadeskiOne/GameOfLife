<?php

namespace Tadeskione\Glider\services;

use Tadeskione\Glider\models\Cell;
use Tadeskione\Glider\models\CellsGrid;

/**
 * Class GridDrawer
 */
 class GridDrawer
{
    public function __construct(private CellsGrid $grid) {}

    public function draw(): array
    {
        $grid = [];
        foreach ($this->grid->getGrid() as $row) {
            /** @var Cell $cell */
            $cells = [];
            foreach ($row as $cell) {
                $cells[] = $cell->isAlive() ? 'X' : '-';
            }
            $grid[] = implode(
                ' ',
                $cells
            );
        }

        return $grid;
    }
}