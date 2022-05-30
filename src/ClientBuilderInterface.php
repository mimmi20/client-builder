<?php
/**
 * This file is part of the geldlib/interfaces package.
 *
 * Copyright (c) 2018-2022, JDC Geld.de GmbH
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
     * @param HeaderInterface[] $headers
     * @phpstan-param array<int|string, HeaderInterface|string|array<int|string, string>> $headers
     *
     * @throws void
     */
    public function build(string $uri, string $method, array $headers = []): HttpClient;
}
