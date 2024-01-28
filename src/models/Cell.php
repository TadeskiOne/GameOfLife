<?php

namespace Tadeskione\Glider\models;

use ArrayObject;
use Exception;
use Tadeskione\Glider\Game;

/**
 * Class Cell
 */
final class Cell
{
    private ArrayObject $neighboursPositions;

    /**
     * @param bool      $is_alive
     * @param array     $position
     * @param CellsGrid $cells
     *
     * @throws Exception
     */
    public function __construct(
        private bool               $is_alive = false,
        private readonly array     $position = [0, 0],
        private readonly CellsGrid $cells = new CellsGrid(),
    ) {
        $this->defineNeighbours();
    }

    public function setIsNotAlive(): void
    {
        $this->is_alive = false;
    }

    public function setIsAlive(): void
    {
        $this->is_alive = true;
    }

    public function isAlive(): bool
    {
        return $this->is_alive;
    }

    public function getCountOfAliveNeighbours(): int
    {
        $aliveNeighbours = 0;

        foreach ($this->neighboursPositions as $position) {
            [$x, $y] = $position;
            /** @var Cell|null $cell */
            $cell = $this->cells->getGrid()[$x][$y];

            if ($cell->isAlive()) {
                $aliveNeighbours++;
            }
        }

        return $aliveNeighbours;
    }

    /**
     * @return void
     * @throws Exception
     */
    private function defineNeighbours(): void
    {
        $gridSize = Game::getParam('grid_size');
        [$x, $y] = $this->position;
        $this->neighboursPositions = new ArrayObject([]);

        for ($i = -1; $i <= 1; $i++) {
            for ($j = -1; $j <= 1; $j++) {
                if ($i == 0 && $j == 0) {
                    continue;
                }
                $ny = $y + $j;
                $nx = $x + $i;
                if ($ny >= 0 && $ny < $gridSize && $nx >= 0 && $nx < $gridSize) {
                    $this->neighboursPositions->append([$nx, $ny]);
                }
            }
        }
    }
}