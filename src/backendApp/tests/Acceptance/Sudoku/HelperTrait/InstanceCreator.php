<?php

namespace Acceptance\Sudoku\HelperTrait;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait InstanceCreator
{

    private function createInstance(KernelBrowser $client): array
    {
        $client->request('POST', '/api/games/sudoku/instances');
        $responseContent = json_decode($client->getResponse()->getContent(), true);
        return $responseContent;
    }

}