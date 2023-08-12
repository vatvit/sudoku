<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class PublishController extends AbstractController
{
    #[Route('/mercure/publish')]
    public function publish(HubInterface $hub): Response
    {
        $data = json_encode(['time' => date("H:i:s")]);

        $update = new Update(
            'my-private-topic',
            $data
        );

        $hub->publish($update);

        return new Response('published to "my-private-topic"! ' . $data);
    }
}
