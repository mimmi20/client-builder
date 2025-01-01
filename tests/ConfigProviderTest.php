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
        self::assertCount(2, $config);
        self::assertArrayHasKey('factories', $config);

        self::assertIsArray($config['factories']);
        self::assertCount(1, $config['factories']);
        self::assertArrayHasKey(ClientBuilder::class, $config['factories']);

        self::assertArrayHasKey('aliases', $config);

        self::assertIsArray($config['aliases']);
        self::assertCount(3, $config['aliases']);
        self::assertArrayHasKey(ClientBuilderInterface::class, $config['aliases']);
        self::assertArrayHasKey(ClientConfigInterface::class, $config['aliases']);
        self::assertArrayHasKey(ConfigInterface::class, $config['aliases']);
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
        self::assertCount(2, $config['dependencies']);
        self::assertArrayHasKey('factories', $config['dependencies']);

        self::assertIsArray($config['dependencies']['factories']);
        self::assertCount(1, $config['dependencies']['factories']);
        self::assertArrayHasKey(ClientBuilder::class, $config['dependencies']['factories']);

        self::assertArrayHasKey('aliases', $config['dependencies']);

        self::assertIsArray($config['dependencies']['aliases']);
        self::assertCount(3, $config['dependencies']['aliases']);
        self::assertArrayHasKey(ClientBuilderInterface::class, $config['dependencies']['aliases']);
        self::assertArrayHasKey(ClientConfigInterface::class, $config['dependencies']['aliases']);
        self::assertArrayHasKey(ConfigInterface::class, $config['dependencies']['aliases']);
    }
}
