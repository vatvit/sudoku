<?php

namespace App\Controller\Sudoku;

use App\Controller\Sudoku\Dto\InstanceCreateResponseDto;
use App\Controller\Sudoku\Dto\InstanceGetResponseDto;
use App\Service\Sudoku\PuzzleGenerator;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class InstanceController extends AbstractController
{
    #[Route(
        '/api/games/sudoku/instances',
        name: 'create-game-sudoku-instance',
        options: ['cache' => false],
        methods: ['POST']
    )]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: InstanceCreateResponseDto::class)
    )]
    #[OA\Tag(name: 'post-data')]
    #[OA\Tag(name: 'game-instances')]
    #[OA\Tag(name: 'game-sudoku')]
    #[OA\Tag(name: 'game-sudoku-instances')]
    public function create(PuzzleGenerator $puzzleGenerator, CacheInterface $cache): JsonResponse
    {
        $puzzleStateDto = $puzzleGenerator->generate();

        $gameCacheKey = $this->getGameCacheKey($puzzleStateDto->id);

        $cache->get($gameCacheKey, function (ItemInterface $item) use ($puzzleStateDto) {
            $item->expiresAfter(3600);

            return $puzzleStateDto->toArray();
        });

        $responseDto = InstanceCreateResponseDto::hydrate([
            'id' => $puzzleStateDto->id,
        ]);

        return $this->json($responseDto);
    }

    #[Route(
        '/api/games/sudoku/instances/{gameId}',
        name: 'get-game-sudoku-instance',
        options: ['cache' => false],
        methods: ['GET']
    )]
    #[OA\Parameter(
        name: 'gameId',
        description: 'Unique identifier for the Sudoku game instance',
        in: 'path',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: InstanceGetResponseDto::class)
    )]
    #[OA\Tag(name: 'get-data')]
    #[OA\Tag(name: 'game-instances')]
    #[OA\Tag(name: 'game-sudoku')]
    #[OA\Tag(name: 'game-sudoku-instances')]
    public function get(string $gameId, CacheInterface $cache): JsonResponse
    {
        /** @var TagAwareAdapter $cache */
        $cacheKey = $this->getGameCacheKey($gameId);
        $tableCacheItem = $cache->getItem($cacheKey);
        if (!$tableCacheItem->isHit()) {
            throw $this->createNotFoundException();
        }
        $table = $tableCacheItem->get();

        $responseDto = InstanceGetResponseDto::hydrate($table);

        return $this->json($responseDto);
    }

    private function getGameCacheKey(string $gameId): string
    {
        return 'game-sudoku-' . $gameId;
    }
}
