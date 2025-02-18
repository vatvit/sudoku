<?php

namespace App\CQRS\Command;

class CreateUserCommand
{
    public function __construct(
        public string $email,
        public string $password,
    )
    {
    }
}