<?php

namespace App\Service\Mercure;

use Symfony\Component\Mercure\Update;

class Factory
{
    /**
     * @param string|array<string> $topics
     * @param string $data
     * @param bool $private
     * @param string|null $id
     * @param string|null $type
     * @param int|null $retry
     * @return Update
     */
    public function createUpdate(
        string|array $topics,
        string $data = '',
        bool $private = false,
        string $id = null,
        string $type = null,
        int $retry = null
    ): Update {
        return new Update($topics, $data, $private, $id, $type, $retry);
    }
}
