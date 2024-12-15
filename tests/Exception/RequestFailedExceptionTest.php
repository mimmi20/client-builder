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

namespace Mimmi20\ClientBuilder\Exception;

use Laminas\Http\Client as HttpClient;
use Laminas\Http\Response;
use Override;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

final class RequestFailedExceptionTest extends TestCase
{
    private RequestFailedException $object;

    /** @throws void */
    #[Override]
    protected function setUp(): void
    {
        $this->object = new RequestFailedException();
    }

    /** @throws ExpectationFailedException */
    public function testGetStatusCode(): void
    {
        self::assertSame(500, $this->object->getStatusCode());

        $this->object->setStatusCode(404);

        self::assertSame(404, $this->object->getStatusCode());
    }

    /** @throws ExpectationFailedException */
    public function testGetResponse(): void
    {
        self::assertNull($this->object->getResponse());

        $response = $this->createMock(Response::class);

        $this->object->setResponse($response);

        self::assertSame($response, $this->object->getResponse());
    }

    /** @throws ExpectationFailedException */
    public function testGetClient(): void
    {
        self::assertNull($this->object->getClient());

        $client = $this->createMock(HttpClient::class);

        $this->object->setClient($client);

        self::assertSame($client, $this->object->getClient());
    }
}
