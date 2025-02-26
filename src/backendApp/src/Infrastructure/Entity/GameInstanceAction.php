<?php

namespace App\Infrastructure\Entity;

use App\Infrastructure\Repository\GameInstanceActionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: GameInstanceActionRepository::class)]
class GameInstanceAction
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'gameInstanceActions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameInstance $gameInstance = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GamePlayer $gamePlayer = null;

    #[ORM\Column(options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, GameInstanceActionAffect>
     */
    #[ORM\OneToMany(mappedBy: 'gameInstanceAction', targetEntity: GameInstanceActionAffect::class)]
    private Collection $gameInstanceActionAffects;

    public function __construct()
    {
        $this->gameInstanceActionAffects = new ArrayCollection();
    }

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

    public function getGamePlayer(): ?GamePlayer
    {
        return $this->gamePlayer;
    }

    public function setGamePlayer(?GamePlayer $gamePlayer): static
    {
        $this->gamePlayer = $gamePlayer;

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

    /**
     * @return Collection<int, GameInstanceActionAffect>
     */
    public function getGameInstanceActionAffects(): Collection
    {
        return $this->gameInstanceActionAffects;
    }

    public function addGameInstanceActionAffect(GameInstanceActionAffect $gameInstanceActionAffect): static
    {
        if (!$this->gameInstanceActionAffects->contains($gameInstanceActionAffect)) {
            $this->gameInstanceActionAffects->add($gameInstanceActionAffect);
            $gameInstanceActionAffect->setGameInstanceAction($this);
        }

        return $this;
    }

    public function removeGameInstanceActionAffect(GameInstanceActionAffect $gameInstanceActionAffect): static
    {
        if ($this->gameInstanceActionAffects->removeElement($gameInstanceActionAffect)) {
            // set the owning side to null (unless already changed)
            if ($gameInstanceActionAffect->getGameInstanceAction() === $this) {
                $gameInstanceActionAffect->setGameInstanceAction(null);
            }
        }

        return $this;
    }
}
