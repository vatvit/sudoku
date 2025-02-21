<?php

namespace App\Application\CQRS\Query;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage]
class GetUsersQuery
{
}