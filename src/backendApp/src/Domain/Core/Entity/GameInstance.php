<?php

namespace App\Domain\Core\Entity;

use App\Domain\Sudoku\Entity\SudokuGameInstance;
use App\Infrastructure\Repository\GameInstanceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: GameInstanceRepository::class)]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: "game_instance_type", type: "string")]
#[ORM\DiscriminatorMap([
    SudokuGameInstance::TYPE => SudokuGameInstance::class,
])]
class GameInstance
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    private ?Uuid $id = null;

    #[ORM\Column(options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $finishedAt = null;

    /**
     * @var Collection<int, GamePlayer>
     */
    #[ORM\OneToMany(mappedBy: 'gameInstance', targetEntity: GamePlayer::class)]
    private Collection $gamePlayers;

    /**
     * @var Collection<int, GameInstanceAction>
     */
    #[ORM\OneToMany(mappedBy: 'gameInstanceAction', targetEntity: GameInstanceAction::class)]
    private Collection $gameInstanceActions;

    public function __construct()
    {
        $this->gamePlayers = new ArrayCollection();
        $this->gameInstanceActions = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
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

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTimeImmutable $startedAt): static
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getFinishedAt(): ?\DateTimeImmutable
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(?\DateTimeImmutable $finishedAt): static
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    /**
     * @return Collection<int, GamePlayer>
     */
    public function getGamePlayers(): Collection
    {
        return $this->gamePlayers;
    }

    public function addGamePlayer(GamePlayer $gamePlayer): static
    {
        if (!$this->gamePlayers->contains($gamePlayer)) {
            $this->gamePlayers->add($gamePlayer);
            $gamePlayer->setGameInstance($this);
        }

        return $this;
    }

    public function removeGamePlayer(GamePlayer $gamePlayer): static
    {
        if ($this->gamePlayers->removeElement($gamePlayer)) {
            // set the owning side to null (unless already changed)
            if ($gamePlayer->getGameInstance() === $this) {
                $gamePlayer->setGameInstance(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GameInstanceAction>
     */
    public function getGameInstanceActions(): Collection
    {
        return $this->gameInstanceActions;
    }

    public function addGameInstanceAction(GameInstanceAction $gameInstanceAction): static
    {
        if (!$this->gameInstanceActions->contains($gameInstanceAction)) {
            $this->gameInstanceActions->add($gameInstanceAction);
            $gameInstanceAction->setGameInstance($this);
        }

        return $this;
    }

    public function removeGameInstanceAction(GameInstanceAction $gameInstanceAction): static
    {
        if ($this->gameInstanceActions->removeElement($gameInstanceAction)) {
            // set the owning side to null (unless already changed)
            if ($gameInstanceAction->getGameInstance() === $this) {
                $gameInstanceAction->setGameInstance(null);
            }
        }

        return $this;
    }
}
