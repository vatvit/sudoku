<?php

namespace App\Infrastructure\Entity;

use App\Infrastructure\Repository\SudokuGridRepository;
use App\Infrastructure\Validator\Constraint\ValidSudokuGridEntity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Uid\Uuid;

#[ValidSudokuGridEntity]
#[ORM\Entity(repositoryClass: SudokuGridRepository::class)]
#[ORM\HasLifecycleCallbacks]
class SudokuGrid extends AbstractEntity
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    private ?Uuid $id = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    private ?int $size = null;

    #[ORM\Column(type: Types::JSON, nullable: false)]
    private ?string $grid = null;

    #[ORM\Column(type: Types::JSON, nullable: false)]
    private ?string $cellGroups = null;

    #[ORM\Column(options: ["default" => "CURRENT_TIMESTAMP"])]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): void
    {
        $this->size = $size;
    }

    public function getGrid(): ?string
    {
        return $this->grid;
    }

    public function setGrid(string $grid): static
    {
        $this->grid = $grid;

        return $this;
    }

    public function getCellGroups(): ?string
    {
        return $this->cellGroups;
    }

    public function setCellGroups(string $cellGroups): static
    {
        $this->cellGroups = $cellGroups;

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
