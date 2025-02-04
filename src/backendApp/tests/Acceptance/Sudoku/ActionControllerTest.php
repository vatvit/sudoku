<?php

namespace Acceptance\Sudoku;

use Acceptance\AbstractAcceptanceWebTestCase;
use Acceptance\Sudoku\HelperTrait\InstanceCreator;

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
            'id' => 'some-action-id',
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
        // Act
        $this->client->jsonRequest('POST', '/api/games/sudoku/instances/nonexistent-game/actions', []);

        // Assert
        $this->assertResponseStatusCodeSame(404);
    }

}