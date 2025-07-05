<?php

namespace App\Application\Service\Sudoku;

use App\Application\CQRS\Command\CreateSudokuGameInstanceCommand;
use App\Application\CQRS\Command\CreateSudokuGameInstanceHandler;
use App\Application\CQRS\Command\CreateSudokuGridCommand;
use App\Application\CQRS\Command\CreateSudokuGridHandler;
use App\Application\CQRS\Command\CreateSudokuPuzzleCommand;
use App\Application\CQRS\Command\CreateSudokuPuzzleHandler;
use App\Application\CQRS\Trait\HandleMultiplyTrait;
use App\Application\Service\Converter\JsonStringToArrayConverter;
use App\Application\Service\Sudoku\Dto\SudokuGameInstanceDto;
use App\Application\Service\Sudoku\Mapper\SudokuGameInstanceEntityToDtoMapper;
use App\Domain\Sudoku\Service\Dto\CellDto;
use App\Domain\Sudoku\Service\Dto\CellGroupDto;
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
        private readonly GridGenerator                       $gridGenerator,
        private readonly GridCellHider                       $gridCellHider,
        private readonly EntityManagerInterface              $entityManager,
        private readonly SerializerInterface                 $serializer,
        private readonly CacheInterface                      $cache,
        private readonly SudokuGameInstanceEntityToDtoMapper $sudokuGameInstanceEntityToDtoMapper,
        MessageBusInterface                                  $messageBus,
    )
    {
        $this->messageBus = $messageBus;
    }

    public function create(int $size = 9): SudokuGameInstanceDto
    {
        $sudokuGameInstanceEntity = $this->createSudokuGameInstanceEntity($size);

        $this->cacheTheEntity($sudokuGameInstanceEntity);

        $puzzleStateDto = $this->sudokuGameInstanceEntityToDtoMapper->map($sudokuGameInstanceEntity);

        return $puzzleStateDto;
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

    private function getHiddenCellsCount(int $size, float $ratio): int
    {
        $hiddenCellsCount = (int)ceil($size * $size * $ratio);
        return $hiddenCellsCount;
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
