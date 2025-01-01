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

use Override;

/**
 * Config für das Minify und das Hinzufügen der Revision
 */
final readonly class Config implements ConfigInterface
{
    /** @throws void */
    public function __construct(private ClientConfigInterface $config)
    {
        // nothing to do
    }

    /** @throws void */
    #[Override]
    public function getClientConfig(): ClientConfigInterface
    {
        return $this->config;
    }
}
