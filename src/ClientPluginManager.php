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
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\ServiceManager;
use Override;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

use function array_merge_recursive;
use function get_debug_type;
use function sprintf;

/**
 * PluginManager für Berechnungsdaten
 *
 * @template-extends AbstractPluginManager<HttpClient>
 * @phpstan-import-type ServiceManagerConfiguration from ServiceManager
 */
final class ClientPluginManager extends AbstractPluginManager
{
    private string $instanceOf = HttpClient::class;

    /**
     * @param array<mixed> $config
     * @phpstan-param ServiceManagerConfiguration $config
     *
     * @throws void
     */
    public function __construct(ContainerInterface $container, array $config = [])
    {
        $config = array_merge_recursive(
            [
                'abstract_factories' => [
                    ClientAbstractFactory::class,
                ],
            ],
            $config,
        );

        parent::__construct($container, $config);
    }

    /**
     * Validate an instance
     *
     * @throws InvalidServiceException if created instance does not respect the constraint on type imposed by the plugin manager
     * @throws ContainerExceptionInterface if any other error occurs
     *
     * @phpstan-assert HttpClient $instance
     */
    #[Override]
    public function validate(mixed $instance): void
    {
        if (!$instance instanceof $this->instanceOf) {
            throw new InvalidServiceException(
                sprintf(
                    '%s can only create instances of %s; %s is invalid',
                    self::class,
                    $this->instanceOf,
                    get_debug_type($instance),
                ),
            );
        }
    }
}
