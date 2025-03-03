<?php

namespace App\Infrastructure\Entity;

use App\Application\Traits\WithValidator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
abstract class AbstractEntity
{
    use WithValidator;

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function validateObjectOnPrePersist(): void
    {
        $this->assertValid();
    }
}