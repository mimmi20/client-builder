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
use Laminas\Http\Headers;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LogicException;
use Override;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

use function assert;

final class ClientBuilderFactory implements FactoryInterface
{
    /**
     * create http client using config
     *
     * @param string            $requestedName
     * @param array<mixed>|null $options
     *
     * @throws LogicException
     * @throws ContainerExceptionInterface
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    #[Override]
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array | null $options = null,
    ): ClientBuilder {
        $config = $container->get(ConfigInterface::class);
        assert($config instanceof ConfigInterface);

        return new ClientBuilder($config, new HttpClient(), new Headers());
    }
}
