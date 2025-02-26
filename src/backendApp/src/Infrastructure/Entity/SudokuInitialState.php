<?php

namespace App\Infrastructure\Entity;

use App\Infrastructure\Repository\SudokuInitialStateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SudokuInitialStateRepository::class)]
class SudokuInitialState
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    private ?Uuid $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?SudokuGrid $grid = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $hiddenCells = null;

    #[ORM\Column(options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getGrid(): ?SudokuGrid
    {
        return $this->grid;
    }

    public function setGrid(?SudokuGrid $grid): static
    {
        $this->grid = $grid;

        return $this;
    }

    public function getHiddenCells(): ?string
    {
        return $this->hiddenCells;
    }

    public function setHiddenCells(string $hiddenCells): static
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
