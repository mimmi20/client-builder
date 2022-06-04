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

use RuntimeException;

/**
 * exception to show that something is missing in the config
 */
final class ConfigMissingException extends RuntimeException
{
    // nothing to do
}
