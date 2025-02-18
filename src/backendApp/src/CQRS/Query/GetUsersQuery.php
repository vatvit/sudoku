<?php

namespace App\CQRS\Query;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage]
class GetUsersQuery
{
}