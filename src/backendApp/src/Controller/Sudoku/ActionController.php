<?php

namespace App\Controller\Sudoku;

use App\Service\Sudoku\Dto\ActionDto;
use App\Service\Sudoku\Dto\PuzzleStateDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

class ActionController extends AbstractController
{
    #[Route('/api/games/sudoku/instances/{gameId}/actions', name: 'game-sudoku-instance-action-create', options: ['cache' => false], methods: ['POST'])]
    public function action(string $gameId, #[MapRequestPayload] ActionDto $actionDto, CacheInterface $cache): JsonResponse
    {
        $cacheKey = $this->getGameCacheKey($gameId);
        $tableCacheItem = $cache->getItem($cacheKey);
        if (!$tableCacheItem->isHit()) {
            throw $this->createNotFoundException();
        }
        $table = $tableCacheItem->get();
        $puzzleStateDto = new PuzzleStateDto($table);

        // TODO: do something

        $tableCacheItem->set($table);
        $cache->save($tableCacheItem);

        /*
         * TODO: Write Application/API tests
         * TODO: Use AI to generate tests/logic
         * TODO: Use proto lib to communicate Front-Back
         * TODO: Applying Game actions
         * store info about started game into the session on the game creation
         * store info about finished game into persistent storage on the game finish
         * clean up the started game info in the session if the started game not found
         * create Sudoku Puzzle service(?)
         * use Sudoku Puzzle service to validate Action and State
         * update SudokuDto cache object accordingly to Action
         *
         * TODO: Storing finished games stats storing
         * use persistent storage to store the finished games stats
         * show finished games stats on the game page
         *
         * TODO: Undo/Redo
         * store ActionDto objects (before and after states) to cache
         *  one cache object? what size limitation? alternatives?
         * store ActionDto objects to persistent storage on the game finish
         * Create Undo/Redo UI buttons
         * Load finished game from persistent storage to cache to enable Undo/Redo feature
         * Apply Undo/Redo actions based on ActionDto from cache
         */
        return $this->json($actionDto);
    }

    private function getGameCacheKey(string $gameId): string
    {
        return 'game-sudoku-' . $gameId;
    }

}
