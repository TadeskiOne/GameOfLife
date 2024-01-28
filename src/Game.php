<?php

namespace Tadeskione\Glider;

use Exception;
use Tadeskione\Glider\models\Cell;
use Tadeskione\Glider\models\CellsGrid;
use Tadeskione\Glider\models\CellsRow;
use Tadeskione\Glider\services\GridDrawer;

/**
 * Class Game
 */
final class Game
{
    private static array $instances = [];
    private CellsGrid    $generation;
    private GridDrawer   $drawer;

    /**
     * @param array $params
     *
     * @return self
     * @throws Exception
     */
    public static function instance(array $params = []): self
    {
        $cls = self::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new self($params);
        }

        return self::$instances[$cls];
    }

    private function __construct(private readonly array $params = []) {}

    /**
     * @param string $paramName
     *
     * @return mixed
     * @throws Exception
     */
    public static function getParam(string $paramName): mixed
    {
        $self = self::instance();

        if (!isset($self->params[$paramName])) {
            throw new Exception('Undefined param "' . $paramName . '"');
        }

        return $self->params[$paramName];
    }

    /**
     * @return void
     * @throws Exception
     */
    public function run(): void
    {
        $this->generation = $this->initFirstGeneration();
        $this->drawer     = new GridDrawer($this->generation);
        $gameCycle        = (int)self::getParam('game_cycle');
        $game             = function () {
            $this->drawer->draw();
            $this->nextGeneration();
            sleep(self::getParam('gen_duration'));
        };

        if ($gameCycle > 0) {
            for ($i = 0; $i < $gameCycle; $i++) {
                $game();
            }
        } else {
            while (true) {
                $game();
            }
        }
    }

    private function nextGeneration(): void
    {
        foreach ($this->generation->getGrid() as $row) {
            /** @var Cell $cell */
            foreach ($row as $cell) {
                $aliveNeighborsCount = $cell->getCountOfAliveNeighbours();
                if ($cell->isAlive() && ($aliveNeighborsCount < 2 || $aliveNeighborsCount > 3)) {
                    $cell->setIsNotAlive();
                } elseif ($cell->isAlive() && ($aliveNeighborsCount == 2 || $aliveNeighborsCount == 3)) {
                    $cell->setIsAlive();
                } elseif (!$cell->isAlive() && $aliveNeighborsCount == 3) {
                    $cell->setIsAlive();
                }
            }
        }
    }

    /**
     * @return CellsGrid
     * @throws Exception
     */
    private function initFirstGeneration(): CellsGrid
    {
        srand((double)microtime() * 1000000);
        $i         = 0;
        $cellsGrid = new CellsGrid();
        do {
            $j        = 0;
            $cellsRow = new CellsRow();

            do {
                $cellsRow->addCell(
                    new Cell(
                        (rand(0, 100) / 100.0) < Game::getParam('first_gen_alive_probability'),
                        [$i, $j],
                        $cellsGrid
                    )
                );
                $j++;
            } while ($j < Game::getParam('grid_size'));

            $cellsGrid->addRow($cellsRow);

            $i++;
        } while ($i < Game::getParam('grid_size'));

        return $cellsGrid;
    }
}