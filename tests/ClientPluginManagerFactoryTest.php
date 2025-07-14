<?php

/**
 * This file is part of the mimmi20/client-builder package.
 *
 * Copyright (c) 2022-2025, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\ClientBuilder;

use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use PHPUnit\Event\NoPreviousThrowableException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class ClientPluginManagerFactoryTest extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws NoPreviousThrowableException
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testInvokeWithServiceListener(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::never())
            ->method('get');
        $container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(false);

        $result = (new ClientPluginManagerFactory())($container, '');

        self::assertInstanceOf(ClientPluginManager::class, $result);

        $this->expectException(ServiceNotFoundException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'A plugin by the name "test" was not found in the plugin manager Mimmi20\ClientBuilder\ClientPluginManager',
        );

        $result->get('test');
    }
}
