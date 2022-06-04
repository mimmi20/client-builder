<?php
/**
 * This file is part of the mimmi20/client-builder package.
 *
 * Copyright (c) 2022, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\ClientBuilder;

use Laminas\Http\Client as HttpClient;
use Laminas\Http\Header\CacheControl;
use Laminas\Http\Header\Connection;
use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\Pragma;
use Laminas\Http\Headers;

use function array_change_key_case;

use const CASE_LOWER;

final class ClientBuilder implements ClientBuilderInterface
{
    private ConfigInterface $config;
    private HttpClient $client;
    private Headers $headers;

    /**
     * @throws void
     */
    public function __construct(ConfigInterface $config, HttpClient $client, Headers $headers)
    {
        $this->config  = $config;
        $this->client  = $client;
        $this->headers = $headers;
    }

    /**
     * builds the client
     *
     * @param mixed[] $headers
     * @phpstan-param array<int|string, HeaderInterface|string|array<int|string, string>> $headers
     *
     * @throws Exception
     */
    public function build(string $uri, string $method, array $headers = []): HttpClient
    {
        $headers = array_change_key_case($headers, CASE_LOWER);

        $client = clone $this->client;

        $client->resetParameters();
        $client->clearCookies();
        $client->clearAuth();

        $client->setUri($uri);
        $client->setMethod($method);

        try {
            $config = $this->config->getClientConfig();
        } catch (ConfigMissingException $e) {
            throw new Exception(
                $e->getMessage(),
                0,
                $e
            );
        }

        $headers = $config->getHeaders() + $headers;

        $requestHeaders = clone $this->headers;

        try {
            $requestHeaders->addHeaders($headers);
        } catch (InvalidArgumentException $e) {
            throw new Exception(
                $e->getMessage(),
                0,
                $e
            );
        }

        if (!$requestHeaders->has('cache-control')) {
            $cacheControl = new CacheControl();
            $cacheControl->addDirective('no-store');
            $cacheControl->addDirective('no-cache');
            $cacheControl->addDirective('must-revalidate');
            $cacheControl->addDirective('post-check');
            $cacheControl->addDirective('pre-check');

            $requestHeaders->addHeader($cacheControl);
        }

        if (!$requestHeaders->has('pragma')) {
            $requestHeaders->addHeader(new Pragma('no-cache'));
        }

        if (!$requestHeaders->has('connection')) {
            $requestHeaders->addHeader((new Connection())->setPersistent(true));
        }

        $request = $client->getRequest();
        $request->setHeaders($requestHeaders);

        try {
            $client->setOptions($config->getOptions());
        } catch (\Laminas\Http\Client\Exception\InvalidArgumentException $e) {
            throw new Exception(
                $e->getMessage(),
                0,
                $e
            );
        }

        return $client;
    }
}
