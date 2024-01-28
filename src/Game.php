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
    private const FIRST_GENERATION_ALIVE_PROBABILITY = 0.3;
    private const GENERATION_DURATION                = 1; //Seconds

    private CellsGrid  $generation;
    private GridDrawer $drawer;

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

        if (empty($self->params[$paramName])) {
            throw new Exception('Undefined param "' . $paramName . '"');
        }

        return $self->params[$paramName];
    }

    /**
     * @return void
     */
    public function run(): void
    {
        $this->generation = $this->initFirstGeneration();
        $this->drawer     = new GridDrawer($this->generation);

        while (true) {
            $generationGrid = $this->drawer->draw();
            $this->replaceGeneration($generationGrid);
            $this->nextGeneration();
            sleep(self::getParam('gen_duration'));
        }
    }

    private function replaceGeneration(array $generation): void
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

    private function nextGeneration(): void
    {
        foreach ($this->generation->getGrid() as &$row) {
            /** @var Cell $cell */
            foreach ($row as &$cell) {
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
        /*print_r($this->params);
        die();*/
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