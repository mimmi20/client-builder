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

use Override;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class ClientPluginManagerFactoryTest extends TestCase
{
    private ClientPluginManagerFactory $object;

    /** @throws void */
    #[Override]
    protected function setUp(): void
    {
        $this->object = new ClientPluginManagerFactory();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvokeWithServiceListener(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::never())
            ->method('get');
        $container->expects(self::never())
            ->method('has');

        $result = ($this->object)($container, '');

        self::assertInstanceOf(ClientPluginManager::class, $result);
    }
}
