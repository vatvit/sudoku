<?php

namespace App\Infrastructure\Entity;

use App\Infrastructure\Repository\SudokuGameStateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SudokuGameStateRepository::class)]
class SudokuGameState extends AbstractEntity
{
    public const TYPE = 'sudoku';

    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    private ?Uuid $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameState $gameState = null;

    #[ORM\Column(type: Types::JSON, nullable: false)]
    private array $filledCells = [];

    #[ORM\Column(type: Types::JSON, nullable: false)]
    private array $notedCells = [];

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getGameState(): ?GameState
    {
        return $this->gameState;
    }

    public function setGameState(GameState $gameState): static
    {
        $this->gameState = $gameState;

        return $this;
    }

    public function getFilledCells(): array
    {
        return $this->filledCells;
    }

    public function setFilledCells(array $filledCells): static
    {
        $this->filledCells = $filledCells;

        return $this;
    }

    public function getNotedCells(): array
    {
        return $this->notedCells;
    }

    public function setNotedCells(array $notedCells): static
    {
        $this->notedCells = $notedCells;

        return $this;
    }
}
