<?php

namespace App\tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConfigControllerTest extends WebTestCase
{
    public function testIndex()
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('GET', '/api/config');

        // Assert
        $this->assertResponseIsSuccessful();

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('mercurePublicUrl', $responseData);
        $this->assertArrayHasKey('allUsers', $responseData);
        $this->assertArrayHasKey('cachedDatetime', $responseData);

        $this->assertNotEmpty($responseData['mercurePublicUrl']);

        $this->assertIsArray($responseData['allUsers']);

        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/',
            $responseData['cachedDatetime']
        );

        $cookie = $client->getResponse()->headers->getCookies()[0]; // Retrieve the first cookie
        $this->assertEquals('mercureAuthorization', $cookie->getName());
        $this->assertNotEmpty($cookie->getValue());
        $this->assertEquals('/.well-known/mercure', $cookie->getPath());
        $this->assertTrue($cookie->isHttpOnly());
        $this->assertTrue($cookie->isSecure());
    }
}