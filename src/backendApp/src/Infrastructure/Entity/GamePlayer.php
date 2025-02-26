<?php

namespace App\Infrastructure\Entity;

use App\Infrastructure\Repository\GamePlayerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: GamePlayerRepository::class)]
class GamePlayer
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'gamePlayers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Player $playerId = null;

    #[ORM\ManyToOne(inversedBy: 'gamePlayers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameInstance $gameInstance = null;

    #[ORM\Column(options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getPlayerId(): ?Player
    {
        return $this->playerId;
    }

    public function setPlayerId(?Player $playerId): static
    {
        $this->playerId = $playerId;

        return $this;
    }

    public function getGameInstance(): ?GameInstance
    {
        return $this->gameInstance;
    }

    public function setGameInstance(?GameInstance $gameInstance): static
    {
        $this->gameInstance = $gameInstance;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
