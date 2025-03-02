<?php

namespace Acceptance\Sudoku;

use Acceptance\AbstractAcceptanceWebTestCase;
use Acceptance\Sudoku\HelperTrait\InstanceCreator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ActionControllerTest extends AbstractAcceptanceWebTestCase
{
    use InstanceCreator;

    public function testAction(): void
    {
        // Arrange
        $responseContent = $this->createInstance($this->client);
        $gameId = $responseContent['id'] ?? null;

        $this->assertNotNull($gameId, "Failed to create a game instance during test setup.");

        $action = [
            'id' => $gameId,
            'timeDiff' => 123456,
            'effects' => [
                'id' => 2345,
                'coords' => 'some-coords',
                'value' => 3,
                'notes' => [1, 2, 3],
            ],
        ];

        // Act
        $this->client->jsonRequest('POST', sprintf('/api/games/sudoku/instances/%s/actions', $gameId), $action);

        // Assert
        $this->assertResponseIsSuccessful();

        $actionResponse = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($action, $actionResponse);
    }

    public function testAction_InstanceNotExists(): void
    {
        // Configuration
        $this->client->catchExceptions(false);

        $action = [
            'id' => 'action-' . uniqid(),
            'timeDiff' => time(),
            'effects' => [
                'id' => random_int(1, 9999),
                'coords' => 'A1',
                'value' => 5,
                'notes' => [2, 4, 6],
            ],
        ];

        // Act
        try {
            $this->client->jsonRequest('POST', '/api/games/sudoku/instances/nonexistent-game/actions', $action);
        } catch (\Exception $e) {
            // Assert
            $this->assertInstanceOf(NotFoundHttpException::class, $e);
            return;
        } finally {
            // revert configuration
            $this->client->catchExceptions(true);
        }

        $this->fail('Expected NotFoundHttpException not thrown.');
    }

}