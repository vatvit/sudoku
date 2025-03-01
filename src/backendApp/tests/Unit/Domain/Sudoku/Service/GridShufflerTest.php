<?php

namespace Tests\Unit\Domain\Sudoku\Service;

use App\Domain\Sudoku\Service\GridShuffler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class GridShufflerTest extends TestCase
{
    private const VALID_ACTIONS = [
        GridShuffler::ACTION_TRANSPOSE,
        GridShuffler::ACTION_SWITCH_COLS,
        GridShuffler::ACTION_SWITCH_ROWS,
        GridShuffler::ACTION_SWITCH_COLS_GROUP,
        GridShuffler::ACTION_SWITCH_ROWS_GROUP,
    ];

    public function testConstructorShouldValidateActions(): void
    {
        // Invalid action
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid shuffle action: invalidAction');
        new GridShuffler(['invalidAction']);
    }

    public function testConstructorShouldRequireAtLeastOneAction(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('At least one shuffle action must be provided');
        new GridShuffler([]);
    }

    #[DataProvider('gridSizeAndActionProvider')]
    public function testEachShuffleActionShouldModifyGrid(int $size, string $action): void
    {
        // Arrange
        $grid = $this->createValidGrid($size);
        $shuffler = new GridShuffler([$action]);

        // Act
        $result = $shuffler->shuffle($grid, 1);

        // Assert
        $this->assertNotEquals(
            $grid,
            $result,
            sprintf('Action %s did not modify the grid', $action)
        );
        $this->assertGridIsValid($result);
    }

    #[DataProvider('gridSizeProvider')]
    public function testMultipleShufflesShouldMaintainValidGrid(int $size): void
    {
        // Arrange
        $grid = $this->createValidGrid($size);
        $shuffler = new GridShuffler(self::VALID_ACTIONS);

        // Act
        $result = $shuffler->shuffle($grid, 4);

        // Assert
        $this->assertNotEquals($grid, $result);
        $this->assertGridIsValid($result);
    }

    public static function gridSizeProvider(): array
    {
        return [
            '4x4 grid' => [4],
            '9x9 grid' => [9],
            '16x16 grid' => [16],
        ];
    }

    public static function gridSizeAndActionProvider(): array
    {
        $testCases = [];
        $sizes = [4, 9, 16];
        
        foreach ($sizes as $size) {
            foreach (self::VALID_ACTIONS as $action) {
                $testCases[sprintf('%dx%d grid with %s', $size, $size, $action)] = [$size, $action];
            }
        }
        
        return $testCases;
    }

    private function createValidGrid(int $size): array
    {
        $grid = ['cells' => []];
        $blockSize = (int)sqrt($size);
        for ($row = 0; $row < $size; $row++) {
            for ($col = 0; $col < $size; $col++) {
                $value = (($row * $blockSize + floor($row / $blockSize) + $col) % $size) + 1;
                $grid['cells'][$row][$col] = ['value' => $value];
            }
        }
        return $grid;
    }

    private function assertGridIsValid(array $grid): void
    {
        // Check grid structure
        $this->assertArrayHasKey('cells', $grid);
        $size = count($grid['cells']);
        $blockSize = (int)sqrt($size);
        
        // Check grid size is valid
        $this->assertEquals($blockSize * $blockSize, $size, 'Grid size must be a perfect square');

        // Check each row
        foreach ($grid['cells'] as $row) {
            $this->assertCount($size, $row);
            $values = array_column($row, 'value');
            sort($values);
            $this->assertEquals(range(1, $size), $values);
        }

        // Check each column
        for ($col = 0; $col < $size; $col++) {
            $values = [];
            for ($row = 0; $row < $size; $row++) {
                $values[] = $grid['cells'][$row][$col]['value'];
            }
            sort($values);
            $this->assertEquals(range(1, $size), $values);
        }

        // Check each block
        for ($boxRow = 0; $boxRow < $blockSize; $boxRow++) {
            for ($boxCol = 0; $boxCol < $blockSize; $boxCol++) {
                $values = [];
                for ($row = $boxRow * $blockSize; $row < ($boxRow + 1) * $blockSize; $row++) {
                    for ($col = $boxCol * $blockSize; $col < ($boxCol + 1) * $blockSize; $col++) {
                        $values[] = $grid['cells'][$row][$col]['value'];
                    }
                }
                sort($values);
                $this->assertEquals(range(1, $size), $values);
            }
        }
    }
}
