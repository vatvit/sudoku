<?php

namespace App\Interface\Controller\Sudoku;

use App\Application\CQRS\Query\GetSudokuGameInstanceByIdHandler;
use App\Application\CQRS\Query\GetSudokuGameInstanceByIdQuery;
use App\Application\CQRS\Trait\HandleMultiplyTrait;
use App\Application\Service\Sudoku\InstanceCreator;
use App\Interface\Controller\Sudoku\Dto\InstanceCreateResponseDto;
use App\Interface\Controller\Sudoku\Dto\InstanceGetResponseDto;
use App\Interface\Controller\Sudoku\Mapper\InstanceResponseMapper;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class InstanceController extends AbstractController
{
    use HandleMultiplyTrait;

    public function __construct(
        private readonly InstanceResponseMapper $responseMapper,
        MessageBusInterface                     $messageBus
    )
    {
        $this->messageBus = $messageBus;
    }

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
    public function create(InstanceCreator $puzzleGenerator): JsonResponse
    {
        $puzzleStateDto = $puzzleGenerator->create();

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
    public function get(string $gameId): JsonResponse
    {
        // Fetch the Sudoku game instance using the CQRS query
        $query = new GetSudokuGameInstanceByIdQuery(Uuid::fromString($gameId));
        $table = $this->handleAndGetResultByHandlerName($query, GetSudokuGameInstanceByIdHandler::class);

        // Check if the table was returned
        if (!$table) {
            return $this->json(['error' => 'Game instance not found'], 404);
        }
        $responseDto = $this->responseMapper->mapGet($table);
        return $this->json($responseDto);
    }
}
