<?php

namespace App\Controller;

use App\Repository\UserRepository;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

class IndexController extends AbstractController
{
    #[Route(
        '/',
        name: 'index-page',
        methods: ['GET']
    )]
    public function index(HubInterface $hub, UserRepository $userRepository, CacheInterface $cache): Response
    {
        //
        $config = [
            // omitted
        ];
        $response = $this->render(
            'index.html.twig',
            [
                'config' => $config,
            ],
        );

        return $response;
    }
}
