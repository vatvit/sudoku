<?php

namespace App\Application\Service\Mercure;

use Symfony\Component\Mercure\HubInterface;

readonly class Publisher
{
    public function __construct(private HubInterface $mercureHub, private Factory $mercureFactory)
    {
    }


    public function publish(string $topic, mixed $data): void
    {
        $jsonData = json_encode($data);

        $update = $this->mercureFactory->createUpdate($topic, $jsonData);

        $this->mercureHub->publish($update);
    }
}
