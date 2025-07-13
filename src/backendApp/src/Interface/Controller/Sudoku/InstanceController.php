<?php

namespace App\Interface\Controller\Sudoku;

use App\Application\UseCase\Sudoku\CreateInstanceUseCase;
use App\Application\UseCase\Sudoku\GetInstanceUseCase;
use App\Interface\Controller\Sudoku\Dto\CreateInstanceRequestDto;
use App\Interface\Controller\Sudoku\Dto\GetInstanceRequestDto;
use App\Interface\Controller\Sudoku\Dto\InstanceCreateResponseDto;
use App\Interface\Controller\Sudoku\Dto\InstanceGetResponseDto;
use App\Interface\Controller\Sudoku\Handler\ExceptionHandler;
use App\Interface\Controller\Sudoku\Mapper\InstanceResponseMapper;
use App\Interface\Controller\Sudoku\Mapper\RequestToUseCaseMapper;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class InstanceController extends AbstractController
{
    public function __construct(
        private readonly InstanceResponseMapper $responseMapper,
        private readonly RequestToUseCaseMapper $requestToUseCaseMapper,
        private readonly CreateInstanceUseCase $createInstanceUseCase,
        private readonly GetInstanceUseCase $getInstanceUseCase,
        private readonly ExceptionHandler $exceptionHandler,
    )
    {
    }

    #[Route(
        '/api/games/sudoku/instances',
        name: 'create-game-sudoku-instance',
        options: ['cache' => false],
        methods: ['POST']
    )]
    #[OA\RequestBody(
        description: 'Sudoku instance creation parameters',
        content: new Model(type: CreateInstanceRequestDto::class)
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
    public function create(
        #[MapRequestPayload] CreateInstanceRequestDto $requestDto
    ): JsonResponse {
        $inputDto = $this->requestToUseCaseMapper->mapToCreateInstanceInput($requestDto);
        $outputDto = $this->createInstanceUseCase->execute($inputDto);

        $responseDto = $this->responseMapper->mapCreateResponse($outputDto->instance);
        return $this->json($responseDto);
    }

    #[Route(
        '/api/games/sudoku/instances/{instanceId}',
        name: 'get-game-sudoku-instance',
        options: ['cache' => false],
        methods: ['GET']
    )]
    #[OA\Parameter(
        name: 'instanceId',
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
    public function get(
        #[MapRequestPayload] GetInstanceRequestDto $requestDto
    ): JsonResponse {
        $inputDto = $this->requestToUseCaseMapper->mapToGetInstanceInput($requestDto->instanceId);
        $outputDto = $this->getInstanceUseCase->execute($inputDto);

        $this->exceptionHandler->handleInstanceNotFound($requestDto->instanceId, $outputDto);

        $responseDto = $this->responseMapper->mapGetResponse($outputDto->instance);
        return $this->json($responseDto);
    }
}
