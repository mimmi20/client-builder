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

final class ConfigProviderTest extends TestCase
{
    /** @throws Exception */
    public function testGetDependencyConfig(): void
    {
        $object = new ConfigProvider();
        $config = $object->getDependencyConfig();

        self::assertIsArray($config);
        self::assertCount(1, $config);
        self::assertArrayHasKey('factories', $config);

        self::assertIsArray($config['factories']);
        self::assertCount(1, $config['factories']);
        self::assertArrayHasKey(ClientPluginManager::class, $config['factories']);
    }

    /** @throws Exception */
    public function testInvoke(): void
    {
        $object = new ConfigProvider();
        $config = $object();

        self::assertIsArray($config);
        self::assertCount(1, $config);
        self::assertArrayHasKey('dependencies', $config);

        self::assertIsArray($config['dependencies']);
        self::assertCount(1, $config['dependencies']);
        self::assertArrayHasKey('factories', $config['dependencies']);

        self::assertIsArray($config['dependencies']['factories']);
        self::assertCount(1, $config['dependencies']['factories']);
        self::assertArrayHasKey(ClientPluginManager::class, $config['dependencies']['factories']);
    }
}
