<?php

namespace App\Application\Service\Sudoku;

use App\Application\CQRS\Command\CreateSudokuGameInstanceCommand;
use App\Application\CQRS\Command\CreateSudokuGameInstanceHandler;
use App\Application\CQRS\Command\CreateSudokuGridCommand;
use App\Application\CQRS\Command\CreateSudokuGridHandler;
use App\Application\CQRS\Command\CreateSudokuPuzzleCommand;
use App\Application\CQRS\Command\CreateSudokuPuzzleHandler;
use App\Application\CQRS\Trait\HandleMultiplyTrait;
use App\Domain\Sudoku\Service\Dto\CellDto;
use App\Domain\Sudoku\Service\Dto\CellGroupDto;
use App\Domain\Sudoku\Service\Dto\SudokuGameInstanceDto;
use App\Domain\Sudoku\Service\GridCellHider;
use App\Domain\Sudoku\Service\GridGenerator;
use App\Infrastructure\Entity\SudokuGameInstance;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class InstanceCreator
{
    use HandleMultiplyTrait;

    private const DEFAULT_HIDDEN_CELLS_RATIO = 0.1;

    public function __construct(
        private readonly GridGenerator          $gridGenerator,
        private readonly GridCellHider          $gridCellHider,
        private readonly EntityManagerInterface $entityManager,
        private readonly SerializerInterface    $serializer,
        private readonly CacheInterface         $cache,
        MessageBusInterface                     $messageBus,
    )
    {
        $this->messageBus = $messageBus;
    }

    public function create(int $size = 9): SudokuGameInstanceDto
    {
        $sudokuGameInstanceEntity = $this->createSudokuGameInstanceEntity($size);

        $this->cacheTheEntity($sudokuGameInstanceEntity);

        $puzzleStateDto = $this->hydratePuzzleStateDto($sudokuGameInstanceEntity);

        return $puzzleStateDto;
    }

    private function applyHiddenCells(array $grid, array $hiddenCells): array
    {
        foreach ($hiddenCells as $hiddenCell) {
            [$rowIndex, $colIndex] = explode(':', $hiddenCell);

            if (isset($grid['cells'][$rowIndex][$colIndex])) {
                $grid['cells'][$rowIndex][$colIndex]['value'] = 0;
            }
        }

        return $grid;
    }

    private function hydratePuzzleStateDto(SudokuGameInstance $sudokuGameInstance): SudokuGameInstanceDto
    {
        $sudokuGridJson = $sudokuGameInstance->getSudokuPuzzle()->getSudokuGrid()->getGrid();
        $size = $sudokuGameInstance->getSudokuPuzzle()->getSudokuGrid()->getSize();

        try {
            $sudokuGrid = json_decode($sudokuGridJson, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \RuntimeException(sprintf('Failed to decode Sudoku grid JSON for Sudoku Game Instance ID: %s. Error: %s', $sudokuGameInstance->getId()->toString() ?? 'unknown', $e->getMessage()), 0, $e);
        }

        $sudokuGrid['cells'] = $sudokuGrid; // TODO: fix it

        $hiddenCells = $sudokuGameInstance->getSudokuPuzzle()->getHiddenCells();
        $puzzleGrid = $this->applyHiddenCells($sudokuGrid, $hiddenCells);

        $cellGroups = [];
        foreach ($puzzleGrid['cells'] as $rowIndex => $rowArray) {
            foreach ($rowArray as $colIndex => $cell) {
                $cellDto = $this->hydrateCellDto($rowIndex, $colIndex, $cell);
                $puzzleGrid['cells'][$rowIndex][$colIndex] = $cellDto;

                $cellGroups = $this->hydrateCellGroups($cellGroups, $cellDto, $size);
            }
        }
        $puzzleGrid['cellGroups'] = array_values($cellGroups);

        $puzzleGrid['id'] = $sudokuGameInstance->getId()->toString();

        return SudokuGameInstanceDto::hydrate($puzzleGrid);
    }

    /**
     * @param int $rowIndex
     * @param int $colIndex
     * @param array<mixed> $cell // TODO: use DTO
     * @return CellDto
     */
    private function hydrateCellDto(int $rowIndex, int $colIndex, array $cell): CellDto
    {
        $cell['coords'] = $this->getCellCoords($rowIndex, $colIndex);
        $cell['protected'] = (bool)$cell['value'];

        $cellDto = CellDto::hydrate($cell);
        return $cellDto;
    }

    /**
     * @param array<int, array{id: int, type: string, cells: array<string, CellDto>}> $groups // TODO: use DTO
     * @param CellDto $cellDto
     * @return array<int, array{id: int, type: string, cells: array<string, CellDto>}> // TODO: use DTO
     */
    private function hydrateCellGroups(array $groups, CellDto $cellDto, int $size): array
    {
        $cellGroups = $this->getCellGroups($cellDto, $size);

        foreach ($cellGroups as $group) {
            $groupId = $group['id'] . ':' . $group['type'];
            if (!isset($groups[$groupId])) {
                $groups[$groupId] = $group;
            }
            $groups[$groupId]['cells'][(string)$cellDto->coords] = $cellDto;
        }
        return $groups;
    }

    private function getCellCoords(int $rowIndex, int $colIndex): string
    {
        return ($rowIndex + 1) . ':' . ($colIndex + 1);
    }

    /**
     * @param CellDto $cellDto
     * @return array<int, array{id: int, type: string, cells: array{}}> // TODO: use DTO
     */
    private function getCellGroups(CellDto $cellDto, int $size): array
    {
        $rowIndex = (int)explode(':', $cellDto->coords)[0] - 1;
        $colIndex = (int)explode(':', $cellDto->coords)[1] - 1;

        $squareId = $this->getBlockId($rowIndex, $colIndex, $size);

        $cellGroupRowDto = ['id' => $rowIndex + 1, 'type' => CellGroupDto::TYPE_ROW, 'cells' => []];
        $cellGroupColDto = ['id' => $colIndex + 1, 'type' => CellGroupDto::TYPE_COLUMN, 'cells' => []];
        $cellGroupSqrDto = ['id' => $squareId, 'type' => CellGroupDto::TYPE_BLOCK, 'cells' => []];

        return [
            $cellGroupRowDto,
            $cellGroupColDto,
            $cellGroupSqrDto,
        ];
    }

    private function getBlockId(int $rowIndex, int $colIndex, int $size): int
    {
        $blockSize = (int)sqrt($size);
        return (int)((floor($colIndex / $blockSize)) + (floor($rowIndex / $blockSize) * $blockSize) + 1);
    }

    public function getHiddenCellsCount(int $size, float $ratio): int
    {
        $hiddenCellsCount = (int)ceil($size * $size * $ratio);
        return $hiddenCellsCount;
    }

    private function createSudokuGameInstanceEntity(int $size): SudokuGameInstance
    {
        $grid = $this->gridGenerator->generate($size);

        $sudokuGridEntity = $this->handleAndGetResultByHandlerName(
            new CreateSudokuGridCommand($size, $grid['cells'], $grid['cellGroups']),
            CreateSudokuGridHandler::class
        );

        $hiddenCellsCount = $this->getHiddenCellsCount($size, self::DEFAULT_HIDDEN_CELLS_RATIO);
        $hiddenCells = $this->gridCellHider->generateHiddenCells($grid, $hiddenCellsCount);

        $sudokuPuzzleEntity = $this->handleAndGetResultByHandlerName(
            new CreateSudokuPuzzleCommand($sudokuGridEntity, $hiddenCells),
            CreateSudokuPuzzleHandler::class
        );

        $sudokuGameInstanceEntity = $this->handleAndGetResultByHandlerName(
            new CreateSudokuGameInstanceCommand($sudokuPuzzleEntity),
            CreateSudokuGameInstanceHandler::class
        );

        $this->entityManager->flush();
        return $sudokuGameInstanceEntity; // commit unit of work
    }

    /**
     * @param SudokuGameInstance $sudokuGameInstanceEntity
     * @return void
     */
    public function cacheTheEntity(SudokuGameInstance $sudokuGameInstanceEntity): void
    {
        // TODO: Extract it
        $cacheKey = 'game|instance|sudoku|' . $sudokuGameInstanceEntity->getId()->toString();

        /** @var CacheItemInterface $cacheItem */
        $cacheItem = $this->cache->getItem($cacheKey);
        if ($cacheItem->isHit()) {
            throw new \RuntimeException(sprintf('Cache key "%s" must be unique. Duplicate detected.', $cacheKey));
        }

        $cacheItem->set($this->serializer->serialize($sudokuGameInstanceEntity, 'json'));

        $cacheItem->expiresAfter(3600);
        $this->cache->save($cacheItem);
    }
}
