<?php
/**
 * This file is part of the mimmi20/client-builder package.
 *
 * Copyright (c) 2022-2023, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\ClientBuilder;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

/** @psalm-suppress InternalMethod */
final class ConfigTest extends TestCase
{
    /** @throws ExpectationFailedException */
    public function testGetClientConfig(): void
    {
        $clientConfig = $this->getMockBuilder(ClientConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $config = new Config($clientConfig);

        self::assertSame($clientConfig, $config->getClientConfig());
    }
}
