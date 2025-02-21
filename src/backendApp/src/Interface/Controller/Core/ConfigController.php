<?php

namespace App\Interface\Controller\Core;

use App\Application\CQRS\Command\CreateUserCommand;
use App\Application\CQRS\Query\GetUsersQuery;
use App\Interface\Controller\Core\Dto\ConfigResponseDto;
use App\Interface\Controller\Core\Mapper\ConfigResponseMapper;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ConfigController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    #[Route(
        '/api/config',
        name: 'get-config',
        methods: ['GET']
    )]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: ConfigResponseDto::class)
    )]
    #[OA\Tag(name: 'get-data')]
    #[OA\Tag(name: 'config')]
    public function index(ConfigResponseMapper $responseMapper, HubInterface $hub, CacheInterface $cache): Response
    {
        // DB
        $this->messageBus->dispatch(new CreateUserCommand(
            'John',
            'some-password',
        ));
        $allUsers = (array)$this->handle(new GetUsersQuery());

        // Cache
        $cachedDatetime = (string)$cache->get('cachedDatetime', function (ItemInterface $item) {
            $item->expiresAfter(10);
            return date('Y-m-d H:i:s');
        });

        // Mercure
        // phpcs:ignore Generic.Files.LineLength.TooLong
        $jwt = 'eyJhbGciOiJIUzI1NiJ9.eyJtZXJjdXJlIjp7InN1YnNjcmliZSI6WyIqIl19fQ.dTeuPHTe_h_4E_D6xOJerk4__cG2YmhfI3BfyaGsHQ0';
        $mercureAuthCookie = $this->createMercureAuthorizationCookie($jwt, $hub);

        // Preparing Response
        $responseDto = $responseMapper->mapIndex($hub->getPublicUrl(), $allUsers, $cachedDatetime);

        $response = $this->json($responseDto);
        $response->headers->setCookie($mercureAuthCookie);

        return $response;
    }

    private function createMercureAuthorizationCookie(string $jwt, HubInterface $hub): Cookie
    {
        return new Cookie(
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
    }
}
