<?php
/**
 * This file is part of the geldlib/interfaces package.
 *
 * Copyright (c) 2018-2022, JDC Geld.de GmbH
 */

declare(strict_types = 1);

namespace Mimmi20\ClientBuilder;

use Laminas\Http\Client as HttpClient;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LogicException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

final class ClientBuilderFactory implements FactoryInterface
{
    /**
     * create http client using config
     *
     * @param string       $requestedName
     * @param mixed[]|null $options
     *
     * @throws LogicException
     * @throws ContainerExceptionInterface
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ClientBuilder
    {
        return new ClientBuilder(
            $container->get(ConfigInterface::class),
            new HttpClient()
        );
    }
}
