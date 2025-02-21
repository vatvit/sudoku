<?php

namespace App\Interface\Controller\Core;

use App\Application\Service\Mercure\Publisher;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Routing\Annotation\Route;

class PublishController extends AbstractController
{
    #[Route(
        '/api/mercure/publish',
        name: 'mercure-publish',
        methods: ['GET']
    )]
    #[OA\Tag(name: 'testing')]
    #[OA\Tag(name: 'get-data')]
    #[OA\Tag(name: 'mercure')]
    #[OA\Tag(name: 'mercure-publish')]
    public function publish(HubInterface $hub, Publisher $publisher): Response
    {
        $data = ['time' => date("H:i:s")];

        $publisher->publish('my-private-topic', $data);

        return $this->getPublishResponse($data);
    }

    /**
     * @param array $data
     * @return Response
     */
    public function getPublishResponse(array $data): Response
    {
        return new Response('published to "my-private-topic"! ' . json_encode($data));
    }
}
