<?php

namespace App\Infrastructure\Serializer;

use App\Infrastructure\Entity\EntityFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Uid\Uuid;

readonly class EntityNormalizer implements DenormalizerInterface
{
    public function __construct(
        private ObjectNormalizer       $normalizer,
        private EntityManagerInterface $entityManager,
        private EntityFactory          $entityFactory,
    )
    {
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return $this->entityManager->getMetadataFactory()->isTransient($type) === false;
    }

    public function denormalize($data, $type, $format = null, array $context = []): mixed
    {
        if (!is_array($data)) {
            throw new UnexpectedValueException('Data expected to be an array for entity denormalization.');
        }

        $entity = $this->entityFactory->create($type);

        $result = $this->normalizer->denormalize($data, $type, $format, array_merge($context, [
            AbstractNormalizer::OBJECT_TO_POPULATE => $entity,
        ]));

        // Special handling for the `id` field because no setter
        if (!empty($data['id']) && property_exists($entity, 'id')) {
            $reflection = new \ReflectionProperty($entity, 'id');
            $reflection->setAccessible(true);

            $idType = $reflection->getType()->getName();
            $data['id'] = $this->normalizer->denormalize(['uuid' => $data['id']], $idType, $format);

            $reflection->setValue($entity, $data['id']);
        }
        return $result;
    }


    /**
     * Returns a list of supported entity types for denormalization.
     *
     * @param string|null $format The format being (de)normalized, or null for any format.
     *
     * @return array<string, bool> An associative array where the keys are entity class names and the values are always `true`.
     */
    public function getSupportedTypes(?string $format): array
    {
        $entityClasses = [];
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        foreach ($metadata as $classMetadata) {
            $entityClasses[$classMetadata->getName()] = true;
        }

        return $entityClasses;
    }
}