<?php

namespace App\Controller\Sudoku;

use App\Service\Sudoku\Table;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class InstanceController extends AbstractController
{
    #[Route('/api/games/sudoku/instances', name: 'game-sudoku-instance-create', options: ['cache' => false], methods: ['POST'])]
    public function create(Table $table, CacheInterface $cache): JsonResponse
    {
        $tableStateDto = $table->generate();

        $table = $tableStateDto->toArray();

        $cacheKey = $this->getCacheKey($tableStateDto->id);

        $cache->get($cacheKey, function (ItemInterface $item) use ($table) {
            $item->expiresAfter(3600);

            return $table;
        });

        return $this->json([
            'id' => $tableStateDto->id,
        ]);
    }

    #[Route('/api/games/sudoku/instances/{id}', name: 'game-sudoku-instance-get', options: ['cache' => false], methods: ['GET'])]
    public function get(string $id, CacheInterface $cache): JsonResponse
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
