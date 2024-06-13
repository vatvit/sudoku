<?php

namespace App\Controller;

use App\Service\Mercure\Publisher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Routing\Annotation\Route;

class PublishController extends AbstractController
{
    #[Route('/mercure/publish')]
    public function publish(HubInterface $hub, Publisher $publisher): Response
    {
        $data = ['time' => date("H:i:s")];

        $publisher->publish('my-private-topic', $data);

        return new Response('published to "my-private-topic"! ' . json_encode($data));
    }
}
