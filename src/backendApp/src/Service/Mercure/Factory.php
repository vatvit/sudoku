<?php

namespace App\Service\Mercure;

use Symfony\Component\Mercure\Update;

class Factory
{
    public function createUpdate($topics, string $data = '', bool $private = false, string $id = null, string $type = null, int $retry = null): Update
    {
        return new Update($topics, $data, $private, $id, $type, $retry);
    }
}
