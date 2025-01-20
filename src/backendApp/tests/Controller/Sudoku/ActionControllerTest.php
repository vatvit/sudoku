<?php

namespace App\tests\Controller\Sudoku;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ActionControllerTest extends WebTestCase
{
    public function testActionNotFound(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->jsonRequest('POST', '/api/games/sudoku/instances/nonexistent-game/actions', []);

        // Assert
        $this->assertResponseStatusCodeSame(404);
    }

    public function testSuccessfulAction(): void
    {
        // Arrange
        $client = static::createClient();

        $client->request('POST', '/api/games/sudoku/instances');
        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $gameId = $responseContent['id'] ?? null;

        $this->assertNotNull($gameId, "Failed to create a game instance during test setup.");

        // Act
        $client->jsonRequest('POST', sprintf('/api/games/sudoku/instances/%s/actions', $gameId), [
            'x' => 0,
            'y' => 0,
            'value' => 1,
        ]);

        // Assert
        $this->assertResponseIsSuccessful();

        $actionResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('x', $actionResponse);
        $this->assertArrayHasKey('y', $actionResponse);
        $this->assertArrayHasKey('value', $actionResponse);
        $this->assertEquals(0, $actionResponse['x']);
        $this->assertEquals(0, $actionResponse['y']);
        $this->assertEquals(1, $actionResponse['value']);
    }
}