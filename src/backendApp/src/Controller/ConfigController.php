<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\Dto\ConfigDto;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ConfigController extends AbstractController
{
    #[Route('/api/config', name: 'config', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: ConfigDto::class)
    )]
    public function index(HubInterface $hub, UserRepository $userRepository, CacheInterface $cache): Response
    {
        // DB
//        $allUsers = $userRepository->findAll(); // no Exception? and good
//        foreach ($allUsers as $key => $user) {
//            $allUsers[$key] = ['email' => $user->getEmail()];
//        }
        $allUsers = [];

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
            true,
            true,
            true,
            Cookie::SAMESITE_STRICT
        );

        //
        $config = new ConfigDto([
            'mercurePublicUrl' => $hub->getPublicUrl(),
            'allUsers' => $allUsers,
            'cachedDatetime' => $cachedDatetime,
        ]);
        $response = $this->json($config, 200);
        $response->headers->setCookie($mercureAuthCookie);

        return $response;
    }
}
