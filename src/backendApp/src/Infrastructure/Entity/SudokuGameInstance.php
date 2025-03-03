<?php

namespace App\Infrastructure\Entity;

use App\Infrastructure\Repository\SudokuGameInstanceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SudokuGameInstanceRepository::class)]
class SudokuGameInstance extends GameInstance
{
    public const TYPE = 'sudoku';

    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SudokuPuzzle $sudokuPuzzle = null;

    #[ORM\Column]
    private ?bool $solved = false;

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
