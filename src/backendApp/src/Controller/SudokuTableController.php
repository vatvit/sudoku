<?php

namespace App\Controller;

use App\Service\Sudoku\Table;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class SudokuTableController extends AbstractController
{
    #[Route('/api/sudoku/puzzle', name: 'sudoku-puzzle-create', options: ['cache' => false], methods: ['POST'])]
    public function create(Table $table, CacheInterface $cache): JsonResponse
    {
        $tableStateDto = $table->generate();

        $table = $tableStateDto->toArray();

        $gameId = sha1(json_encode($table));

        $cacheKey = $this->getCacheKey($gameId);

        $cache->get($cacheKey, function (ItemInterface $item) use ($table) {
            $item->expiresAfter(3600);

            return $table;
        });

        return $this->json([
            'puzzleId' => $gameId,
        ]);
    }

    #[Route('/api/sudoku/puzzle/{id}', name: 'sudoku-puzzle-get', options: ['cache' => false], methods: ['GET'])]
    public function load(string $id, CacheInterface $cache): JsonResponse
    {
        $cacheKey = $this->getCacheKey($id);
        $tableCacheItem = $cache->getItem($cacheKey);
        if (!$tableCacheItem->isHit()) {
            throw $this->createNotFoundException();
        }
        $table = $tableCacheItem->get();

        return $this->json($table);
    }

    private function getCacheKey(string $gameId): string
    {
        return 'game-sudoku-' . $gameId;
    }

}
