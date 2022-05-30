<?php
/**
 * This file is part of the mimmi20/laminas-router-hostname package.
 *
 * Copyright (c) 2021, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\ClientBuilder;

use Mimmi20\Routing\Router\HostName;
use Mimmi20\Routing\Router\HostNameFactory;

final class ConfigProvider
{
    /**
     * Returns configuration from file
     *
     * @return array<string, array<string, array<string, string>>>
     * @phpstan-return array{dependencies: array{aliases: array<string|class-string, class-string>, abstract_factories: array<int, class-string>, factories: array<class-string, class-string>}}
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    /**
     * Return application-level dependency configuration.
     *
     * @return array<string, array<int|string, string>>
     * @phpstan-return array{aliases: array<string|class-string, class-string>, abstract_factories: array<int, class-string>, factories: array<class-string, class-string>}
     */
    public function getDependencyConfig(): array
    {
        return [
            'aliases' => [
                ClientBuilderInterface::class => ClientBuilder::class,
            ],
            'factories' => [
                ClientBuilder::class => ClientBuilderFactory::class,
            ],
        ];
    }
}
