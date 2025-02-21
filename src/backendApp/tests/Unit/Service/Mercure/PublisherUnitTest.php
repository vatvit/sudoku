<?php

namespace App\Tests\Unit\Service\Mercure;

use App\Application\Service\Mercure\Factory;
use App\Application\Service\Mercure\Publisher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mercure\Hub;
use Symfony\Component\Mercure\Update;

class PublisherUnitTest extends TestCase
{

    public function testPublish(): void
    {
        $mercureFactoryStub = $this->createStub(Factory::class);
        $mercureHubStub = $this->createMock(Hub::class);
        $mercureUpdateStub = $this->createStub(Update::class);

        $mercureFactoryStub
            ->method('createUpdate')
            ->willReturn($mercureUpdateStub);

        $mercureHubStub->expects($this->any())->method('publish')->with($mercureUpdateStub);

        $publisher = new Publisher($mercureHubStub, $mercureFactoryStub);

        $publisher->publish('test-topic', ['key' => 'data']);

        $this->assertTrue(true);
    }

}
