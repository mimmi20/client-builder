<?php
/**
 * This file is part of the mimmi20/laminas-router-hostname package.
 *
 * Copyright (c) 2021, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\ClientBuilder;

use Mimmi20\ClientBuilder\ClientBuilder;
use Mimmi20\ClientBuilder\ClientBuilderInterface;
use Mimmi20\ClientBuilder\Module;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

final class ModuleTest extends TestCase
{
    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
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
        self::assertCount(1, $config['service_manager']['aliases']);
        self::assertArrayHasKey(ClientBuilderInterface::class, $config['service_manager']['aliases']);
    }
}
