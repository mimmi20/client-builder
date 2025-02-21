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

final class ConfigProvider
{
    /**
     * Returns configuration from file
     *
     * @return array<string, array<string, array<string, string>>>
     * @phpstan-return array{dependencies: array{factories: array<class-string, class-string>}}
     *
     * @throws void
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
     * @return array<string, array<string, string>>
     * @phpstan-return array{factories: array<class-string, class-string>}
     *
     * @throws void
     */
    public function getDependencyConfig(): array
    {
        return [
            'factories' => [
                ClientPluginManager::class => ClientPluginManagerFactory::class,
            ],
        ];
    }
}
