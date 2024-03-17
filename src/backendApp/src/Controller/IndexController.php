<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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
    public function index(HubInterface $hub, UserRepository $userRepository, CacheInterface $cache)
    {
        // DB
        $allUsers = $userRepository->findAll(); // no Exception? and good
        foreach ($allUsers as $key => $user) {
            $allUsers[$key] = ['email' => $user->getEmail()];
        }

        // Cache
        $cachedDatetime = $cache->get('cachedDatetime', function (ItemInterface $item) {
            $item->expiresAfter(10);
            return date('Y-m-d H:i:s');
        });

        // Mercure
        $jwt = 'eyJhbGciOiJIUzI1NiJ9.eyJtZXJjdXJlIjp7InN1YnNjcmliZSI6WyIqIl19fQ.dTeuPHTe_h_4E_D6xOJerk4__cG2YmhfI3BfyaGsHQ0';
        $mercureAuthCookie = new Cookie(
            'mercureAuthorization',
            $jwt,
            (new \DateTime('now'))->modify("+1 day"),
            '/.well-known/mercure',
            parse_url($hub->getPublicUrl(), PHP_URL_HOST),
            null,
            true,
            true,
            Cookie::SAMESITE_STRICT
        );

        //
        $config = [
            'mercurePublicUrl' => $hub->getPublicUrl(),
            'allUsers' => $allUsers,
            'cachedDatetime' => $cachedDatetime,
        ];
        $response = $this->render(
            'index.html.twig',
            [
                'config' => $config,
            ],
        );
        $response->headers->setCookie($mercureAuthCookie);

        return $response;
    }
}
