<?php

namespace Tests\Unit\Domain\Sudoku\Service;

use App\Domain\Sudoku\Service\GridCellHider;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class GridCellHiderTest extends TestCase
{
    private GridCellHider $gridCellHider;

    protected function setUp(): void
    {
        $this->gridCellHider = new GridCellHider();
    }

    #[DataProvider('gridSizeProvider')]
    public function testHideCells(int $size): void
    {
        // Arrange
        $grid = $this->createValidGrid($size);
        $hiddenCount = (int)($size * $size * 0.3); // Hide 30% of cells

        // Act
        $result = $this->gridCellHider->hideCells($grid, $hiddenCount);

        // Assert
        $this->assertGridStructureValid($result, $size);
        $this->assertEquals($hiddenCount, $this->countHiddenCells($result));
        $this->assertOriginalValuesPreserved($grid, $result);
    }

    #[DataProvider('gridSizeProvider')]
    public function testHideAllCells(int $size): void
    {
        // Arrange
        $grid = $this->createValidGrid($size);
        $hiddenCount = $size * $size; // All cells

        // Act
        $result = $this->gridCellHider->hideCells($grid, $hiddenCount);

        // Assert
        $this->assertGridStructureValid($result, $size);
        $this->assertEquals($hiddenCount, $this->countHiddenCells($result));
    }

    #[DataProvider('gridSizeProvider')]
    public function testHideNoCells(int $size): void
    {
        // Arrange
        $grid = $this->createValidGrid($size);
        $hiddenCount = 0;

        // Act
        $result = $this->gridCellHider->hideCells($grid, $hiddenCount);

        // Assert
        $this->assertGridStructureValid($result, $size);
        $this->assertEquals($hiddenCount, $this->countHiddenCells($result));
        $this->assertEquals($grid, $result);
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

    private function assertGridStructureValid(array $grid, int $size): void
    {
        $this->assertArrayHasKey('cells', $grid);
        $this->assertCount($size, $grid['cells']);
        foreach ($grid['cells'] as $row) {
            $this->assertCount($size, $row);
            foreach ($row as $cell) {
                $this->assertArrayHasKey('value', $cell);
                $this->assertIsInt($cell['value']);
                $this->assertGreaterThanOrEqual(0, $cell['value']);
                $this->assertLessThanOrEqual($size, $cell['value']);
            }
        }
    }

    private function countHiddenCells(array $grid): int
    {
        $count = 0;
        foreach ($grid['cells'] as $row) {
            foreach ($row as $cell) {
                if ($cell['value'] === 0) {
                    $count++;
                }
            }
        }
        return $count;
    }

    private function assertOriginalValuesPreserved(array $original, array $result): void
    {
        $size = count($original['cells']);
        for ($row = 0; $row < $size; $row++) {
            for ($col = 0; $col < $size; $col++) {
                if ($result['cells'][$row][$col]['value'] !== 0) {
                    $this->assertEquals(
                        $original['cells'][$row][$col]['value'],
                        $result['cells'][$row][$col]['value']
                    );
                }
            }
        }
    }

    public static function gridSizeProvider(): array
    {
        return [
            '4x4 grid' => [4],
            '9x9 grid' => [9],
            '16x16 grid' => [16],
        ];
    }
}
