<?php

namespace App\Interface\Controller\Sudoku;

use App\Application\Service\Sudoku\InstanceCreator;
use App\Interface\Controller\Sudoku\Dto\InstanceCreateResponseDto;
use App\Interface\Controller\Sudoku\Dto\InstanceGetResponseDto;
use App\Interface\Controller\Sudoku\Mapper\InstanceResponseMapper;
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
    public function __construct(private readonly InstanceResponseMapper $responseMapper)
    {}

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
    public function create(InstanceCreator $puzzleGenerator, CacheInterface $cache): JsonResponse
    {
        $puzzleStateDto = $puzzleGenerator->create();

        $gameCacheKey = $this->getGameCacheKey($puzzleStateDto->id);

        $cache->get($gameCacheKey, function (ItemInterface $item) use ($puzzleStateDto) {
            $item->expiresAfter(3600);

            return $puzzleStateDto->toArray();
        });

        $responseDto = $this->responseMapper->mapCreate($puzzleStateDto->id);
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

        $responseDto = $this->responseMapper->mapGet($table);
        return $this->json($responseDto);
    }

    private function getGameCacheKey(string $gameId): string
    {
        return 'game-sudoku-' . $gameId;
    }
}
