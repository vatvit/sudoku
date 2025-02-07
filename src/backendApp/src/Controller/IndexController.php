<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class IndexController extends AbstractController
{
    #[Route('/')]
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
