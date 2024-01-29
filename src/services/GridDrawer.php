<?php

namespace Tadeskione\Glider\services;

use Tadeskione\Glider\models\Cell;
use Tadeskione\Glider\models\CellsGrid;

/**
 * Class GridDrawer
 */
final class GridDrawer
{
    private static int $oldLines = 0;

    public function __construct(private readonly CellsGrid $grid) {}

    public function drawBadEnd(): void
    {
        echo implode(PHP_EOL, $this->getGrid());
        echo PHP_EOL, "\033[31mAll cells are dead :(";
        die(PHP_EOL);
    }

    public function draw(): void
    {
        $generation = $this->getGrid();

        $numNewLines = count($generation) - 1;

        if (self::$oldLines == 0) {
            self::$oldLines = $numNewLines;
        }

        echo implode(PHP_EOL, $generation);
        echo chr(27) . "[0G";
        echo chr(27) . "[" . self::$oldLines . "A";
    }

    private function getGrid(): array
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
