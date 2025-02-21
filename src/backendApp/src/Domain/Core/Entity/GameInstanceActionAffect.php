<?php

namespace App\Domain\Core\Entity;

use App\Infrastructure\Repository\GameInstanceActionAffectRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: GameInstanceActionAffectRepository::class)]
class GameInstanceActionAffect
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'gameInstanceActionAffects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameInstanceAction $gameInstanceAction = null;

    #[ORM\Column(length: 255)]
    private ?string $gameInstanceType = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getGameInstanceAction(): ?GameInstanceAction
    {
        return $this->gameInstanceAction;
    }

    public function setGameInstanceAction(?GameInstanceAction $gameInstanceAction): static
    {
        $this->gameInstanceAction = $gameInstanceAction;

        return $this;
    }

    public function getGameInstanceType(): ?string
    {
        return $this->gameInstanceType;
    }

    public function setGameInstanceType(string $gameInstanceType): static
    {
        $this->gameInstanceType = $gameInstanceType;

        return $this;
    }
}
