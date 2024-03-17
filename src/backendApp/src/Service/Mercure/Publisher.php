<?php

namespace App\Service\Mercure;

use Symfony\Component\Mercure\HubInterface;

class Publisher
{
    public function __construct(private HubInterface $mercureHub, private Factory $mercureFactory)
    {
    }


    public function publish(string $topic, mixed $data)
    {
        $jsonData = json_encode($data);

        $update = $this->mercureFactory->createUpdate($topic, $jsonData);

        $this->mercureHub->publish($update);
    }

}
