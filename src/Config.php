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

/**
 * Config für das Minify und das Hinzufügen der Revision
 */
final class Config implements ConfigInterface
{
    /** @throws void */
    public function __construct(private readonly ClientConfigInterface $config)
    {
        // nothing to do
    }

    /** @throws void */
    public function getClientConfig(): ClientConfigInterface
    {
        return $this->config;
    }
}
