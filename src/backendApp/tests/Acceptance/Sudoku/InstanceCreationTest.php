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

    public function testCreateInstanceWithCustomSize(): void
    {
        // Arrange
        $requestData = [
            'size' => 16
        ];

        // Act
        $this->client->jsonRequest('POST', '/api/games/sudoku/instances', $requestData);

        // Assert
        $this->assertResponseIsSuccessful();

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);
        $this->assertArrayHasKey('id', $responseContent);

        // Verify the instance was created (by trying to get it)
        $gameId = $responseContent['id'];
        $this->client->request('GET', sprintf('/api/games/sudoku/instances/%s', $gameId));
        $this->assertResponseIsSuccessful();
    }

    public function testCreateInstanceWithInvalidSize(): void
    {
        // Arrange
        $requestData = [
            'size' => 20 // Invalid size (out of range 4-16)
        ];

        // Act
        $this->client->jsonRequest('POST', '/api/games/sudoku/instances', $requestData);

        // Assert
        $this->assertResponseStatusCodeSame(422); // Validation error
    }

    public function testGetInstance_NotFound(): void
    {
        // Act
        $this->client->request('GET', '/api/games/sudoku/instances/nonexistent-id');

        // Assert
        $this->assertResponseStatusCodeSame(404);

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        // Verify error response structure includes error code
        $this->assertArrayHasKey('code', $responseContent, 'Error response should include error code');
        $this->assertArrayHasKey('type', $responseContent, 'Error response should include type');
        $this->assertArrayHasKey('title', $responseContent, 'Error response should include title');
        $this->assertArrayHasKey('status', $responseContent, 'Error response should include status');
        $this->assertArrayHasKey('detail', $responseContent, 'Error response should include detail');

        // Verify specific error code for game not found
        $this->assertEquals('GAME_NOT_FOUND', $responseContent['code']);
        $this->assertEquals(404, $responseContent['status']);
        $this->assertEquals('Game Not Found', $responseContent['title']);
        $this->assertStringContainsString('nonexistent-id', $responseContent['detail']);
    }
}
