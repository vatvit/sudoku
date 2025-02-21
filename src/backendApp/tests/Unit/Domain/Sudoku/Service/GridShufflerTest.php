<?php

namespace Tests\Unit\Domain\Sudoku\Service;

use App\Domain\Sudoku\Service\GridShuffler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class GridShufflerTest extends TestCase
{
    private GridShuffler $gridShuffler;

    protected function setUp(): void
    {
        $this->gridShuffler = new GridShuffler();
    }

    #[DataProvider('gridSizeProvider')]
    public function testShuffle(int $size): void
    {
        // Arrange
        $originalGrid = $this->createValidGrid($size);

        // Act
        $result = $this->gridShuffler->shuffle($originalGrid);

        // Assert
        $this->assertNotEquals($originalGrid, $result);
        $this->assertGridIsValid($result);
    }

    #[DataProvider('gridSizeProvider')]
    public function testTransposeTable(int $size): void
    {
        // Arrange
        $grid = $this->createValidGrid($size);
        
        // Act
        $result = $this->gridShuffler->shuffle($grid, 1); // Ensure at least one transpose
        
        // Assert
        $this->assertNotEquals($grid, $result);
        $this->assertGridIsValid($result);
    }

    #[DataProvider('gridSizeProvider')]
    public function testSwitchColsGroup(int $size): void
    {
        // Arrange
        $grid = $this->createValidGrid($size);
        $originalGrid = $grid;

        // Act
        $result = $this->gridShuffler->shuffle($grid, 1); // Ensure at least one column group switch

        // Assert
        $this->assertNotEquals($originalGrid, $result);
        $this->assertGridIsValid($result);
    }

    #[DataProvider('gridSizeProvider')]
    public function testSwitchRowsGroup(int $size): void
    {
        // Arrange
        $grid = $this->createValidGrid($size);
        $originalGrid = $grid;

        // Act
        $result = $this->gridShuffler->shuffle($grid, 1); // Ensure at least one row group switch

        // Assert
        $this->assertNotEquals($originalGrid, $result);
        $this->assertGridIsValid($result);
    }

    #[DataProvider('gridSizeProvider')]
    public function testSwitchRows(int $size): void
    {
        // Arrange
        $grid = $this->createValidGrid($size);
        $originalGrid = $grid;

        // Act
        $result = $this->gridShuffler->shuffle($grid, 1); // Ensure at least one row switch

        // Assert
        $this->assertNotEquals($originalGrid, $result);
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

    private function createValidGrid(int $size = 9): array
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
