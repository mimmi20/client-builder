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

use Laminas\Http\Client as HttpClient;
use Laminas\Http\Response;
use RuntimeException;

/**
 * exception to show that a request was not successful
 */
final class RequestFailedException extends RuntimeException
{
    private int $statusCode           = 500;
    private Response | null $response = null;
    private HttpClient | null $client = null;

    /** @throws void */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /** @throws void */
    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    /** @throws void */
    public function getResponse(): Response | null
    {
        return $this->response;
    }

    /** @throws void */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    /** @throws void */
    public function getClient(): HttpClient | null
    {
        return $this->client;
    }

    /** @throws void */
    public function setClient(HttpClient $client): void
    {
        $this->client = $client;
    }
}
