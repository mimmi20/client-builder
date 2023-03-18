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

use Laminas\Http\Client as HttpClient;
use Laminas\Http\Header\HeaderInterface;

interface ClientBuilderInterface
{
    /**
     * builds the client
     *
     * @param array<mixed> $headers
     * @phpstan-param array<int|string, HeaderInterface|string|array<int|string, string>> $headers
     *
     * @throws Exception
     */
    public function build(
        string $uri,
        string $method,
        array $headers = [],
    ): HttpClient;
}
