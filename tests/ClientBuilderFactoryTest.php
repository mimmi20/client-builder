<?php

namespace Mimmi20\ClientBuilder;

use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Mimmi20\ClientBuilder\ClientBuilderFactory;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class ClientBuilderFactoryTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testInvoke(): void
    {
        $config = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::never())
            ->method('has');
        $container->expects(self::once())
            ->method('get')
            ->with(ConfigInterface::class)
            ->willReturn($config);

        $factory = new ClientBuilderFactory();

        $client = $factory($container, '');

        self::assertInstanceOf(ClientBuilder::class, $client);
    }
}
