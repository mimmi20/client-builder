<?php
/**
 * This file is part of the geldlib/interfaces package.
 *
 * Copyright (c) 2018-2022, JDC Geld.de GmbH
 */

declare(strict_types = 1);

namespace Mimmi20\ClientBuilder;

use GeldLib\Interfaces\Cleverreach\Entity\Form;
use GeldLib\Interfaces\Cleverreach\Entity\Group;
use GeldLib\Interfaces\Generic\ConfigMissingException;
use GeldLib\Interfaces\Generic\RequestFailedException;
use GeldLib\Interfaces\Generic\ServiceTrait;
use Laminas\Http\Client as HttpClient;
use Laminas\Http\Client\Exception\InvalidArgumentException;
use Laminas\Http\Header\Authorization;
use Laminas\Http\Header\CacheControl;
use Laminas\Http\Header\Connection;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\Pragma;
use Laminas\Http\Header\Referer;
use Laminas\Http\Header\UserAgent;
use Laminas\Http\Headers;

use function is_array;
use function mb_strpos;
use function sprintf;
use function var_export;

final class ClientBuilder implements ClientBuilderInterface
{
    private ConfigInterface $config;
    private HttpClient $client;

    /**
     * @throws void
     */
    public function __construct(ConfigInterface $config, HttpClient $client)
    {
        $this->config = $config;
        $this->client = $client;
    }

    /**
     * builds the client
     *
     * @param HeaderInterface[] $headers
     * @phpstan-param array<int|string, HeaderInterface|string|array<int|string, string>> $headers
     *
     * @throws void
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

        $configHeaders = $config->getHeaders();

        if (null !== $configHeaders) {
            $headers = $configHeaders + $headers;
        }

        $requestHeaders = new Headers();

        try {
            $requestHeaders->addHeaders($headers);
        } catch (\Laminas\Http\Header\Exception\InvalidArgumentException $e) {
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
