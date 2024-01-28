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

    public function draw(): void
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

        $this->display($grid);
    }

    private function display(array $generation): void
    {
        static $oldLines = 0;
        $numNewLines = count($generation) - 1;

        if ($oldLines == 0) {
            $oldLines = $numNewLines;
        }

        echo implode(PHP_EOL, $generation);
        echo chr(27) . "[0G";
        echo chr(27) . "[" . $oldLines . "A";
    }
}