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

use Laminas\Http\Client as HttpClient;
use Laminas\Http\Header\CacheControl;
use Laminas\Http\Header\Connection;
use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\Pragma;
use Laminas\Http\Headers;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Override;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

use function array_key_exists;
use function is_array;

final class ClientAbstractFactory implements AbstractFactoryInterface
{
    /** @var array<string, array{headers: array<string, string>, options: array<string, string>}>|null */
    private array | null $config = null;

    /** @var string Top-level configuration key indicating forms configuration */
    private string $configKey = 'http-clients';

    /**
     * Create a http client
     *
     * @param array<mixed>|null $options
     *
     * @throws ContainerExceptionInterface
     * @throws HttpClient\Exception\InvalidArgumentException
     * @throws InvalidArgumentException
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    #[Override]
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        array | null $options = null,
    ): HttpClient {
        $config = $this->getConfig($container);
        $config = $config[$requestedName];

        $client  = new HttpClient();
        $headers = new Headers();

        if (is_array($config) && array_key_exists('headers', $config) && is_array($config['headers'])) {
            $headers->addHeaders($config['headers']);
        }

        if (!$headers->has('cache-control')) {
            $cacheControl = new CacheControl();
            $cacheControl->addDirective('no-store');
            $cacheControl->addDirective('no-cache');
            $cacheControl->addDirective('must-revalidate');
            $cacheControl->addDirective('post-check');
            $cacheControl->addDirective('pre-check');

            $headers->addHeader($cacheControl);
        }

        if (!$headers->has('pragma')) {
            $headers->addHeader(new Pragma('no-cache'));
        }

        if (!$headers->has('connection')) {
            $headers->addHeader((new Connection())->setPersistent(true));
        }

        $request = $client->getRequest();
        $request->setHeaders($headers);

        if (is_array($config) && array_key_exists('options', $config) && is_array($config['options'])) {
            $client->setOptions($config['options']);
        }

        return $client;
    }

    /**
     * Can we create the requested service?
     *
     * @throws ContainerExceptionInterface
     */
    #[Override]
    public function canCreate(ContainerInterface $container, string $requestedName): bool
    {
        // avoid infinite loops when looking up config
        if ($requestedName === 'config' || $requestedName === '') {
            return false;
        }

        $config = $this->getConfig($container);

        return array_key_exists($requestedName, $config)
            && is_array($config[$requestedName]);
    }

    /**
     * Get client configuration, if any
     *
     * @return array<string, array{headers: array<string, string>, options: array<string, string>}>
     *
     * @throws ContainerExceptionInterface
     */
    private function getConfig(ContainerInterface $container): array
    {
        if ($this->config !== null) {
            return $this->config;
        }

        if (!$container->has('config')) {
            $this->config = [];

            return $this->config;
        }

        $config = $container->get('config');

        if (
            !is_array($config)
            || !array_key_exists($this->configKey, $config)
            || !is_array($config[$this->configKey])
        ) {
            $this->config = [];

            return $this->config;
        }

        $this->config = $config[$this->configKey];

        return $this->config;
    }
}
