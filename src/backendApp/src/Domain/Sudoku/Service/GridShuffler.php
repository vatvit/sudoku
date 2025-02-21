<?php

namespace App\Domain\Sudoku\Service;

class GridShuffler
{
    public const ACTION_TRANSPOSE = 'transposeTable';
    public const ACTION_SWITCH_COLS = 'switchCols';
    public const ACTION_SWITCH_ROWS = 'switchRows';
    public const ACTION_SWITCH_COLS_GROUP = 'switchColsGroup';
    public const ACTION_SWITCH_ROWS_GROUP = 'switchRowsGroup';

    private array $availableActions;

    public function __construct(?array $actions = null)
    {
        $this->availableActions = $actions ?? [
            self::ACTION_TRANSPOSE,
            self::ACTION_SWITCH_COLS,
            self::ACTION_SWITCH_ROWS,
            self::ACTION_SWITCH_COLS_GROUP,
            self::ACTION_SWITCH_ROWS_GROUP,
        ];

        if (empty($this->availableActions)) {
            throw new \InvalidArgumentException('At least one shuffle action must be provided');
        }

        foreach ($this->availableActions as $action) {
            if (!method_exists($this, $action)) {
                throw new \InvalidArgumentException(sprintf('Invalid shuffle action: %s', $action));
            }
        }
    }

    /**
     * @param array<mixed> $grid // TODO: use DTO
     * @param int $iterations
     * @return array<mixed> // TODO: use DTO
     */
    public function shuffle(array $grid, int $iterations = 10): array
    {
        $gridConfiguration = $this->getGridConfiguration($grid);

        for ($i = 0; $i < $iterations; $i++) {
            $randomAction = $this->availableActions[array_rand($this->availableActions)];
            $grid = $this->$randomAction($grid, $gridConfiguration);
        }

        return $grid;
    }

    private function getGridConfiguration($grid): array
    {
        $size = count($grid['cells']);

        if ($size <= 0 || (int)(floor(sqrt($size)) * floor(sqrt($size))) !== $size) {
            throw new \InvalidArgumentException(sprintf(
                'Grid size must be a perfect square number, "%d" provided.' . floor(sqrt($size)) * floor(sqrt($size)),
                $size
            ));
        }

        $boxSize = (int)sqrt($size);

        $gridConfiguration = [
            'size' => $size,
            'boxSize' => $boxSize,
        ];

        return $gridConfiguration;
    }

    /**
     * This method takes a grid array as input and returns its transpose.
     *
     * @param array<mixed> $grid // TODO: use DTO
     * @return array<mixed> // TODO: use DTO
     */
    private function transposeTable(array $grid, array $gridConfiguration): array
    {
        $transpose = [];

        for ($i = 0; $i < $gridConfiguration['size']; $i++) {
            for ($j = 0; $j < $gridConfiguration['size']; $j++) {
                $transpose[$j][$i] = $grid['cells'][$i][$j];
            }
        }

        $grid['cells'] = $transpose;

        return $grid;
    }

    /**
     * @param array<mixed> $grid // TODO: use DTO
     * @return array<mixed> // TODO: use DTO
     */
    private function switchColsGroup(array $grid, array $gridConfiguration): array
    {
        $groups = range(0, $gridConfiguration['size'] - 1, $gridConfiguration['boxSize']);
        shuffle($groups);
        $groupA = array_shift($groups);
        $groupB = array_shift($groups);

        for ($i = 0; $i < $gridConfiguration['size']; $i++) {
            for ($j = 0; $j < $gridConfiguration['boxSize']; $j++) {
                $temp = $grid['cells'][$i][$groupA + $j];
                $grid['cells'][$i][$groupA + $j] = $grid['cells'][$i][$groupB + $j];
                $grid['cells'][$i][$groupB + $j] = $temp;
            }
        }

        return $grid;
    }

    /**
     * @param array<mixed> $grid // TODO: use DTO
     * @return array<mixed> // TODO: use DTO
     */
    private function switchRowsGroup(array $grid, array $gridConfiguration): array
    {
        $groups = range(0, $gridConfiguration['size'] - 1, $gridConfiguration['boxSize']);
        shuffle($groups);
        $groupA = array_shift($groups);
        $groupB = array_shift($groups);

        for ($i = 0; $i < $gridConfiguration['boxSize']; $i++) {
            $temp = $grid['cells'][$groupA + $i];
            $grid['cells'][$groupA + $i] = $grid['cells'][$groupB + $i];
            $grid['cells'][$groupB + $i] = $temp;
        }

        return $grid;
    }

    /**
     * @param array<mixed> $grid // TODO: use DTO
     * @return array<mixed> // TODO: use DTO
     */
    private function switchRows(array $grid, array $gridConfiguration): array
    {
        $group = rand(0, $gridConfiguration['boxSize'] - 1);
        $rows = range(0, $gridConfiguration['boxSize'] - 1);
        shuffle($rows);
        $rowA = array_shift($rows) + ($group * $gridConfiguration['boxSize']);
        $rowB = array_shift($rows) + ($group * $gridConfiguration['boxSize']);

        $temp = $grid['cells'][$rowA];
        $grid['cells'][$rowA] = $grid['cells'][$rowB];
        $grid['cells'][$rowB] = $temp;

        return $grid;
    }

    /**
     * @param array<mixed> $grid // TODO: use DTO
     * @return array<mixed> // TODO: use DTO
     */
    private function switchCols(array $grid, array $gridConfiguration): array
    {
        $group = rand(0, $gridConfiguration['boxSize'] - 1);
        $cols = range(0, $gridConfiguration['boxSize'] - 1);
        shuffle($cols);
        $colA = array_shift($cols) + ($group * $gridConfiguration['boxSize']);
        $colB = array_shift($cols) + ($group * $gridConfiguration['boxSize']);

        for ($i = 0; $i < $gridConfiguration['size']; $i++) {
            $temp = $grid['cells'][$i][$colA];
            $grid['cells'][$i][$colA] = $grid['cells'][$i][$colB];
            $grid['cells'][$i][$colB] = $temp;
        }

        return $grid;
    }
}
