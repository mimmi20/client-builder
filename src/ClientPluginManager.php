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
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;

use function array_merge_recursive;

/**
 * PluginManager für Berechnungsdaten
 *
 * @template-extends AbstractPluginManager<HttpClient>
 * @phpstan-import-type ServiceManagerConfiguration from ServiceManager
 */
final class ClientPluginManager extends AbstractPluginManager
{
    /**
     * An object type that the created instance must be instanced of
     *
     * @var string|null
     * @phpstan-var class-string<HttpClient>|null
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $instanceOf = HttpClient::class;

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
}
