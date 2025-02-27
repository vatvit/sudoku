<?php

namespace App\Infrastructure\Entity;

use App\Infrastructure\Repository\SudokuGameInstanceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SudokuGameInstanceRepository::class)]
class SudokuGameInstance
{
    public const TYPE = 'sudoku';

    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    private ?Uuid $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameInstance $gameInstance = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?SudokuGameInitialState $initialState = null;

    #[ORM\Column]
    private ?bool $solved = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getGameInstance(): ?GameInstance
    {
        return $this->gameInstance;
    }

    public function setGameInstance(GameInstance $gameInstance): static
    {
        $this->gameInstance = $gameInstance;

        return $this;
    }

    public function getInitialState(): ?SudokuGameInitialState
    {
        return $this->initialState;
    }

    public function setInitialState(?SudokuGameInitialState $initialState): static
    {
        $this->initialState = $initialState;

        return $this;
    }

    public function isSolved(): ?bool
    {
        return $this->solved;
    }

    public function setSolved(bool $solved): static
    {
        $this->solved = $solved;

        return $this;
    }
}
