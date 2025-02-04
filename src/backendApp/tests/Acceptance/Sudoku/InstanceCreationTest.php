<?php

namespace Acceptance\Sudoku;

use Acceptance\AbstractAcceptanceWebTestCase;
use Acceptance\Sudoku\HelperTrait\InstanceCreator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class InstanceCreationTest extends AbstractAcceptanceWebTestCase
{
    use InstanceCreator;

    public function testCreateAndGetInstance(): void
    {
        // Arrange
        $responseContent = $this->createInstance($this->client);
        $gameId = $responseContent['id'];

        // Act
        $this->client->request('GET', sprintf('/api/games/sudoku/instances/%s', $gameId));

        // Assert
        $this->assertResponseIsSuccessful();

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);
        $this->assertArrayHasKey('id', $responseContent);
    }

    public function testGetInstance_NotFound(): void
    {
        // Act
        $this->client->request('GET', '/api/games/sudoku/instances/nonexistent-id');

        // Assert
        $this->assertResponseStatusCodeSame(404);
    }
}