<?php

namespace App\Application\CQRS\Command;

class CreateUserCommand
{
    public function __construct(
        public string $email,
        public string $password,
    )
    {
    }
}