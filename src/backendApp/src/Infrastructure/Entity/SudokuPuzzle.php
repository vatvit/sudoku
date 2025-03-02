<?php

namespace App\Infrastructure\Entity;

use App\Infrastructure\Repository\SudokuPuzzleRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SudokuPuzzleRepository::class)]
class SudokuPuzzle extends AbstractEntity
{
    #[Groups(['entity'])]
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    private ?Uuid $id = null;

    #[Groups(['entity'])]
    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SudokuGrid $sudokuGrid = null;

    #[Groups(['entity'])]
    #[ORM\Column]
    private array $hiddenCells = [];

    #[Groups(['entity'])]
    #[ORM\Column(options: ["default" => "CURRENT_TIMESTAMP"])]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getSudokuGrid(): ?SudokuGrid
    {
        return $this->sudokuGrid;
    }

    public function setSudokuGrid(?SudokuGrid $sudokuGrid): static
    {
        $this->sudokuGrid = $sudokuGrid;

        return $this;
    }

    public function getHiddenCells(): array
    {
        return $this->hiddenCells;
    }

    public function setHiddenCells(array $hiddenCells): static
    {
        $this->hiddenCells = $hiddenCells;

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
