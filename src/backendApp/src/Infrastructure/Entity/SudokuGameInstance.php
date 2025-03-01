<?php

namespace App\Infrastructure\Entity;

use App\Infrastructure\Repository\SudokuGameInstanceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SudokuGameInstanceRepository::class)]
class SudokuGameInstance extends AbstractEntity
{
    public const TYPE = 'sudoku';

    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    private ?Uuid $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?SudokuPuzzle $sudokuPuzzle = null;

    #[ORM\Column]
    private ?bool $solved = false;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getSudokuPuzzle(): ?SudokuPuzzle
    {
        return $this->sudokuPuzzle;
    }

    public function setSudokuPuzzle(SudokuPuzzle $sudokuPuzzle): static
    {
        $this->sudokuPuzzle = $sudokuPuzzle;

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
