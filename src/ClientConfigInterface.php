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

use Laminas\Http\Header\HeaderInterface;

interface ClientConfigInterface
{
    /**
     * Get the http headers for the http client
     *
     * @return array<(HeaderInterface|string|array)> $headers
     * @phpstan-return array<int|string, HeaderInterface|string|array<int|string, string>> $headers
     *
     * @throws void
     */
    public function getHeaders(): array;

    /**
     * Get the config options for the http client
     *
     * @return array<bool>|array<int>|array<string>
     * @phpstan-return array{timeout: int, connecttimeout: int, sslallowselfsigned: bool, sslverifypeer: bool, sslverifypeername: bool, storeresponse: bool, maxredirects: int}
     *
     * @throws void
     */
    public function getOptions(): array;
}
