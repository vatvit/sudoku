<?php

namespace Acceptance\Sudoku;

use Acceptance\AbstractAcceptanceWebTestCase;
use Acceptance\Sudoku\HelperTrait\InstanceCreator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        // Configuration
        $this->client->catchExceptions(false);

        // Act
        try {
            $this->client->request('GET', '/api/games/sudoku/instances/nonexistent-id');
        } catch (\Exception $e) {
            // Assert
            $this->assertInstanceOf(NotFoundHttpException::class, $e);
            return;
        } finally {
            $this->client->catchExceptions(true);
        }

        $this->fail('Expected NotFoundHttpException not thrown.');
    }
}