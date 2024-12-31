<?php

namespace App\Controller\Sudoku;

use App\Service\Sudoku\Dto\ActionDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

class ActionController extends AbstractController
{
    #[Route('/api/games/sudoku/instances/{id}/actions', name: 'game-sudoku-instance-action-create', options: ['cache' => false], methods: ['POST'])]
    public function action(string $id, Request $request, CacheInterface $cache): JsonResponse
    {
        $dto = new ActionDto(json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR));
        return $this->json($dto);
    }

    private function getCacheKey(string $gameId): string
    {
        return 'game-sudoku-' . $gameId;
    }

}
