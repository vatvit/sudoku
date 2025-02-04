<?php

namespace Acceptance;

class PublishControllerTest extends AbstractAcceptanceWebTestCase
{
    public function testPublish(): void
    {
        // Act
        $this->client->request('GET', '/api/mercure/publish');

        // Assert
        $this->assertResponseIsSuccessful();

        $responseContent = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('published to "my-private-topic"', $responseContent);

        $this->assertMatchesRegularExpression(
            '/\d{2}:\d{2}:\d{2}/', // Matches the "H:i:s" time format
            $responseContent
        );
    }
}