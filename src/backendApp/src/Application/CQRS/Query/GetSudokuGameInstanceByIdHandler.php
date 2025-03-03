<?php

namespace App\Application\CQRS\Query;

use App\Infrastructure\Entity\SudokuGameInstance;
use App\Infrastructure\Repository\SudokuGameInstanceRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[AsMessageHandler]
readonly class GetSudokuGameInstanceByIdHandler
{
    public function __construct(
        private SudokuGameInstanceRepository $repository,
        private CacheInterface               $cache,
        private SerializerInterface          $serializer,
    )
    {
    }

    public function __invoke(GetSudokuGameInstanceByIdQuery $query): ?SudokuGameInstance
    {
        // TODO: Extract it
        $cacheKey = 'game|instance|sudoku|' . $query->id;

        $entityJson = $this->cache->get($cacheKey, function () use ($query) {
            $entity = $this->repository->find($query->id);

            if ($entity === null) {
                return null;
            }

            $entityJson = $this->serializer->serialize($entity, 'json');

            return $entityJson;
        });

        if ($entityJson === null) {
            return null;
        }

        $entity = $this->serializer->deserialize($entityJson, SudokuGameInstance::class, 'json');

        return $entity;
    }
}