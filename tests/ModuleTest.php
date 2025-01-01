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

use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

final class ModuleTest extends TestCase
{
    /** @throws Exception */
    public function testGetConfig(): void
    {
        $object = new Module();
        $config = $object->getConfig();

        self::assertIsArray($config);
        self::assertCount(1, $config);
        self::assertArrayHasKey('service_manager', $config);

        self::assertIsArray($config['service_manager']);
        self::assertCount(2, $config['service_manager']);
        self::assertArrayHasKey('factories', $config['service_manager']);

        self::assertIsArray($config['service_manager']['factories']);
        self::assertCount(1, $config['service_manager']['factories']);
        self::assertArrayHasKey(ClientBuilder::class, $config['service_manager']['factories']);

        self::assertArrayHasKey('aliases', $config['service_manager']);

        self::assertIsArray($config['service_manager']['aliases']);
        self::assertCount(3, $config['service_manager']['aliases']);
        self::assertArrayHasKey(ClientBuilderInterface::class, $config['service_manager']['aliases']);
        self::assertArrayHasKey(ClientConfigInterface::class, $config['service_manager']['aliases']);
        self::assertArrayHasKey(ConfigInterface::class, $config['service_manager']['aliases']);
    }
}
