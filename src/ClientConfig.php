<?php
/**
 * This file is part of the mimmi20/client-builder package.
 *
 * Copyright (c) 2022-2024, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\ClientBuilder;

use Laminas\Http\Header\HeaderInterface;
use Override;

/**
 * Config für das Minify und das Hinzufügen der Revision
 */
final readonly class ClientConfig implements ClientConfigInterface
{
    /**
     * @param array<(array|HeaderInterface|string)> $headers
     * @param array<bool|int|string>                $options
     * @phpstan-param array<int|string, HeaderInterface|string|array<int|string, string>> $headers
     * @phpstan-param array{timeout?: int, connecttimeout?: int, sslallowselfsigned?: bool, sslverifypeer?: bool, sslverifypeername?: bool, storeresponse?: bool, maxredirects?: int} $options
     *
     * @throws void
     */
    public function __construct(private array $headers = [], private array $options = [])
    {
        // nothing to do
    }

    /**
     * Get the http headers for the http client
     *
     * @return array<(array|HeaderInterface|string)>
     * @phpstan-return array<int|string, HeaderInterface|string|array<int|string, string>>
     *
     * @throws void
     */
    #[Override]
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get the config options for the http client
     *
     * @return array<bool|int|string>
     * @phpstan-return array{timeout?: int, connecttimeout?: int, sslallowselfsigned?: bool, sslverifypeer?: bool, sslverifypeername?: bool, storeresponse?: bool, maxredirects?: int}
     *
     * @throws void
     */
    #[Override]
    public function getOptions(): array
    {
        return $this->options;
    }
}
