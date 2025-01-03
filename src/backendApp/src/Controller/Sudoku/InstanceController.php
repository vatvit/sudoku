<?php

namespace App\Controller\Sudoku;

use App\Service\Sudoku\PuzzleGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class InstanceController extends AbstractController
{
    #[Route('/api/games/sudoku/instances', name: 'game-sudoku-instance-create', options: ['cache' => false], methods: ['POST'])]
    public function create(PuzzleGenerator $puzzleGenerator, CacheInterface $cache): JsonResponse
    {
        $puzzleStateDto = $puzzleGenerator->generate();

        $gameCacheKey = $this->getGameCacheKey($puzzleStateDto->id);

        $cache->get($gameCacheKey, function (ItemInterface $item) use ($puzzleStateDto) {
            $item->expiresAfter(3600);

            return $puzzleStateDto->toArray();
        });

        return $this->json([
            'id' => $puzzleStateDto->id,
        ]);
    }

    #[Route('/api/games/sudoku/instances/{gameId}', name: 'game-sudoku-instance-get', options: ['cache' => false], methods: ['GET'])]
    public function get(string $gameId, CacheInterface $cache): JsonResponse
    {
        $cacheKey = $this->getGameCacheKey($gameId);
        $tableCacheItem = $cache->getItem($cacheKey);
        if (!$tableCacheItem->isHit()) {
            throw $this->createNotFoundException();
        }
        $table = $tableCacheItem->get();

        return $this->json($table);
    }

    private function getGameCacheKey(string $gameId): string
    {
        return 'game-sudoku-' . $gameId;
    }

}
