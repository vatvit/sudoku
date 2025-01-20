<?php

namespace App\tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PublishControllerTest extends WebTestCase
{
    public function testPublish(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('GET', '/api/mercure/publish');

        // Assert
        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $this->assertStringContainsString('published to "my-private-topic"', $responseContent);

        $this->assertMatchesRegularExpression(
            '/\d{2}:\d{2}:\d{2}/', // Matches the "H:i:s" time format
            $responseContent
        );
    }
}