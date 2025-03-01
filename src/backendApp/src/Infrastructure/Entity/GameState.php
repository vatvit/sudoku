<?php

namespace App\Infrastructure\Entity;

use App\Infrastructure\Repository\GameStateRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: GameStateRepository::class)]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: "game_state_type", type: "string")]
#[ORM\DiscriminatorMap([
    SudokuGameInstance::TYPE => SudokuGameInstance::class,
])]
class GameState extends AbstractEntity
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'gameStates')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameInstance $gameInstance = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?GameInstanceAction $lastGameInstanceAction = null;

    #[ORM\Column(options: ["default" => "CURRENT_TIMESTAMP"])]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?Uuid
    {
        return $this->id;
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

    public function getLastGameInstanceAction(): ?GameInstanceAction
    {
        return $this->lastGameInstanceAction;
    }

    public function setLastGameInstanceAction(?GameInstanceAction $lastGameInstanceAction): static
    {
        $this->lastGameInstanceAction = $lastGameInstanceAction;

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
