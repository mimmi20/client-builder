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

namespace Mimmi20\ClientBuilder\Exception;

use Laminas\Http\Client as HttpClient;
use Laminas\Http\Response;
use PHPUnit\Event\NoPreviousThrowableException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

final class RequestFailedExceptionTest extends TestCase
{
    /** @throws ExpectationFailedException */
    public function testGetStatusCode(): void
    {
        $object = new RequestFailedException();

        self::assertSame(500, $object->getStatusCode());

        $object->setStatusCode(404);

        self::assertSame(404, $object->getStatusCode());
    }

    /**
     * @throws ExpectationFailedException
     * @throws NoPreviousThrowableException
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testGetResponse(): void
    {
        $object = new RequestFailedException();

        self::assertNull($object->getResponse());

        $response = $this->createMock(Response::class);

        $object->setResponse($response);

        self::assertSame($response, $object->getResponse());
    }

    /**
     * @throws ExpectationFailedException
     * @throws NoPreviousThrowableException
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testGetClient(): void
    {
        $object = new RequestFailedException();

        self::assertNull($object->getClient());

        $client = $this->createMock(HttpClient::class);

        $object->setClient($client);

        self::assertSame($client, $object->getClient());
    }
}
