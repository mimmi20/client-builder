<?php
/**
 * This file is part of the mimmi20/client-builder package.
 *
 * Copyright (c) 2022-2023, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\ClientBuilder;

interface ConfigInterface
{
    /**
     * Get the config options for Cleverreach
     *
     * @throws ConfigMissingException
     */
    public function getClientConfig(): ClientConfigInterface;
}
