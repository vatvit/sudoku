<?php

namespace App\Interface\Controller\Sudoku;

use App\Application\Service\Sudoku\InstanceCreator;
use App\Application\Service\Sudoku\InstanceGetter;
use App\Interface\Controller\Sudoku\Dto\InstanceCreateResponseDto;
use App\Interface\Controller\Sudoku\Dto\InstanceGetResponseDto;
use App\Interface\Controller\Sudoku\Mapper\InstanceResponseMapper;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class InstanceController extends AbstractController
{
    public function __construct(
        private readonly InstanceResponseMapper $responseMapper,
    )
    {
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
    public function create(InstanceCreator $instanceCreator): JsonResponse
    {
        $puzzleStateDto = $instanceCreator->create();

        $responseDto = $this->responseMapper->mapCreateResponse($puzzleStateDto->id);
        return $this->json($responseDto);
    }

    #[Route(
        '/api/games/sudoku/instances/{instanceId}',
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
    public function get(string $instanceId, InstanceGetter $instanceGetter): JsonResponse
    {
        try {
            $instanceId = Uuid::fromString($instanceId);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException();
        }

        $sudokuGameInstanceDto = $instanceGetter->getById($instanceId);

        if (!$sudokuGameInstanceDto) {
            throw $this->createNotFoundException();
        }

        $responseDto = $this->responseMapper->mapGetResponse($sudokuGameInstanceDto);
        return $this->json($responseDto);
    }
}
