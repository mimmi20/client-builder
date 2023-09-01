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
final class ClientConfigTest extends TestCase
{
    /** @throws ExpectationFailedException */
    public function testGetClientConfig(): void
    {
        $headers = ['abc'];
        $options = ['timeout' => 10];

        $clientConfig = new ClientConfig($headers, $options);

        self::assertSame($headers, $clientConfig->getHeaders());
        self::assertSame($options, $clientConfig->getOptions());
    }
}
