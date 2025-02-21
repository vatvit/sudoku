<?php

namespace Tests\Unit\Domain\Sudoku\Service;

use App\Domain\Sudoku\Service\GridGenerator;
use App\Domain\Sudoku\Service\GridShuffler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class GridGeneratorTest extends TestCase
{
    private GridShuffler $gridShuffler;
    private GridGenerator $gridGenerator;

    protected function setUp(): void
    {
        $this->gridShuffler = $this->createMock(GridShuffler::class);
        $this->gridGenerator = new GridGenerator($this->gridShuffler);
    }

    #[DataProvider('gridSizeProvider')]
    public function testGenerate(int $size): void
    {
        // Arrange
        $boxSize = (int)sqrt($size);
        $expectedGrid = ['cells' => []];
        for ($row = 0; $row < $size; $row++) {
            for ($col = 0; $col < $size; $col++) {
                $expectedGrid['cells'][$row][$col] = [
                    'value' => $this->getCellValue($row, $col, $size, $boxSize)
                ];
            }
        }

        $shuffledGrid = ['cells' => array_reverse($expectedGrid['cells'])]; // Mock shuffled result
        $this->gridShuffler->expects($this->once())
            ->method('shuffle')
            ->with($expectedGrid, 10)
            ->willReturn($shuffledGrid);

        // Act
        $result = $this->gridGenerator->generate($size);

        // Assert
        $this->assertEquals($shuffledGrid, $result);
    }

    #[DataProvider('invalidGridSizeProvider')]
    public function testGenerateWithInvalidSize(int $size): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Grid size must be a perfect square number');

        $this->gridGenerator->generate($size);
    }

    public function testGeneratedGridIsValid(): void
    {
        // Arrange
        $this->gridShuffler->method('shuffle')->willReturnArgument(0);

        // Act
        $result = $this->gridGenerator->generate();

        // Assert
        $this->assertGridIsValid($result, 9);
    }

    public static function gridSizeProvider(): array
    {
        return [
            'Standard 9x9 grid' => [9],
            'Small 4x4 grid' => [4],
            'Large 16x16 grid' => [16],
        ];
    }

    public static function invalidGridSizeProvider(): array
    {
        return [
            'Zero size' => [0],
            'Negative size' => [-1],
            'Non-perfect-square size' => [10],
        ];
    }

    private function assertGridIsValid(array $grid, int $size): void
    {
        $boxSize = (int)sqrt($size);

        // Check grid structure
        $this->assertArrayHasKey('cells', $grid);
        $this->assertCount($size, $grid['cells']);

        // Check each row
        foreach ($grid['cells'] as $row) {
            $this->assertCount($size, $row);
            $values = array_column($row, 'value');
            $this->assertEquals(range(1, $size), array_values(array_unique($values)));
        }

        // Check each column
        for ($col = 0; $col < $size; $col++) {
            $values = [];
            for ($row = 0; $row < $size; $row++) {
                $values[] = $grid['cells'][$row][$col]['value'];
            }
            $this->assertEquals(range(1, $size), array_values(array_unique($values)));
        }

        // Check each box
        for ($boxRow = 0; $boxRow < $boxSize; $boxRow++) {
            for ($boxCol = 0; $boxCol < $boxSize; $boxCol++) {
                $values = [];
                for ($row = $boxRow * $boxSize; $row < ($boxRow + 1) * $boxSize; $row++) {
                    for ($col = $boxCol * $boxSize; $col < ($boxCol + 1) * $boxSize; $col++) {
                        $values[] = $grid['cells'][$row][$col]['value'];
                    }
                }
                $this->assertEquals(range(1, $size), array_values(array_unique($values)));
            }
        }
    }

    private function getCellValue(int $row, int $col, int $size, int $boxSize): int
    {
        $value = $col;
        $value = ($value + ($row * $boxSize) * 2);
        $value = $value + (floor($row / $boxSize) * ($size - 1));

        $value = ($value % $size) + 1;
        return (int)$value;
    }
}
