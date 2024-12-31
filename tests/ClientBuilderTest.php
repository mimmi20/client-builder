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

use Laminas\Http\Client as HttpClient;
use Laminas\Http\Header\CacheControl;
use Laminas\Http\Header\Connection;
use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\Pragma;
use Laminas\Http\Headers;
use Laminas\Http\Request;
use Mimmi20\ClientBuilder\Exception\ConfigMissingException;
use Mimmi20\ClientBuilder\Exception\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

use function array_change_key_case;
use function assert;

use const CASE_LOWER;

final class ClientBuilderTest extends TestCase
{
    /** @throws ExpectationFailedException */
    public function testBuild(): void
    {
        $uri       = 'https://test.uri';
        $method    = 'Test-Method';
        $headers   = [];
        $exception = new ConfigMissingException('test');

        $config = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $config->expects(self::once())
            ->method('getClientConfig')
            ->willThrowException($exception);

        $client = $this->getMockBuilder(HttpClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $client->expects(self::once())
            ->method('resetParameters')
            ->with(false);
        $client->expects(self::once())
            ->method('clearCookies');
        $client->expects(self::once())
            ->method('clearAuth');
        $client->expects(self::once())
            ->method('setUri')
            ->with($uri);
        $client->expects(self::once())
            ->method('setMethod')
            ->with($method);
        $client->expects(self::never())
            ->method('getRequest');
        $client->expects(self::never())
            ->method('setOptions');

        $headersObj = $this->getMockBuilder(Headers::class)
            ->disableOriginalConstructor()
            ->getMock();
        $headersObj->expects(self::never())
            ->method('addHeaders');
        $headersObj->expects(self::never())
            ->method('addHeader');
        $headersObj->expects(self::never())
            ->method('has');

        $object = new ClientBuilder($config, $client, $headersObj);

        try {
            $object->build($uri, $method, $headers);

            self::fail('Exception expected');
        } catch (Exception $e) {
            self::assertSame('test', $e->getMessage());
            self::assertSame(0, $e->getCode());

            self::assertSame($exception, $e->getPrevious());
        }
    }

    /**
     * @throws ExpectationFailedException
     * @throws Exception
     */
    public function testBuild2(): void
    {
        $uri           = 'https://test.uri';
        $method        = 'Test-Method';
        $headers       = [];
        $configHeaders = ['a' => 'b'];
        $options       = ['timeout' => null];

        $clientConfig = $this->getMockBuilder(ClientConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $clientConfig->expects(self::once())
            ->method('getHeaders')
            ->willReturn($configHeaders);
        $clientConfig->expects(self::once())
            ->method('getOptions')
            ->willReturn($options);

        $config = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $config->expects(self::once())
            ->method('getClientConfig')
            ->willReturn($clientConfig);

        $headersObj = $this->getMockBuilder(Headers::class)
            ->disableOriginalConstructor()
            ->getMock();
        $headersObj->expects(self::once())
            ->method('addHeaders')
            ->with($configHeaders + $headers);
        $matcher = self::exactly(3);
        $headersObj->expects($matcher)
            ->method('addHeader')
            ->willReturnCallback(static function (HeaderInterface $header) use ($matcher): void {
                $invocation = $matcher->numberOfInvocations();

                switch ($invocation) {
                    case 1:
                        self::assertInstanceOf(CacheControl::class, $header);
                        assert($header instanceof CacheControl);
                        self::assertTrue($header->hasDirective('no-store'));
                        self::assertTrue($header->getDirective('no-store'));
                        self::assertTrue($header->hasDirective('no-cache'));
                        self::assertTrue($header->getDirective('no-cache'));
                        self::assertTrue($header->hasDirective('must-revalidate'));
                        self::assertTrue($header->getDirective('must-revalidate'));
                        self::assertTrue($header->hasDirective('post-check'));
                        self::assertTrue($header->getDirective('post-check'));
                        self::assertTrue($header->hasDirective('pre-check'));
                        self::assertTrue($header->getDirective('pre-check'));

                        break;
                    case 2:
                        self::assertInstanceOf(Pragma::class, $header);
                        assert($header instanceof Pragma);
                        self::assertSame('no-cache', $header->getFieldValue());

                        break;
                    default:
                        self::assertInstanceOf(Connection::class, $header);
                        assert($header instanceof Connection);
                        self::assertTrue($header->isPersistent());
                }
            });
        $matcher = self::exactly(3);
        $headersObj->expects($matcher)
            ->method('has')
            ->willReturnCallback(static function (string $param) use ($matcher): bool {
                match ($matcher->numberOfInvocations()) {
                    1 => self::assertSame('cache-control', $param),
                    2 => self::assertSame('pragma', $param),
                    default => self::assertSame('connection', $param),
                };

                return false;
            });

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::once())
            ->method('setHeaders')
            ->with($headersObj);

        $client = $this->getMockBuilder(HttpClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $client->expects(self::once())
            ->method('resetParameters')
            ->with(false);
        $client->expects(self::once())
            ->method('clearCookies');
        $client->expects(self::once())
            ->method('clearAuth');
        $client->expects(self::once())
            ->method('setUri')
            ->with($uri);
        $client->expects(self::once())
            ->method('setMethod')
            ->with($method);
        $client->expects(self::once())
            ->method('getRequest')
            ->willReturn($request);
        $client->expects(self::once())
            ->method('setOptions')
            ->with($options);

        $object = new ClientBuilder($config, $client, $headersObj);

        $result = $object->build($uri, $method, $headers);

        self::assertInstanceOf(HttpClient::class, $result);
    }

    /** @throws ExpectationFailedException */
    public function testBuild3(): void
    {
        $uri           = 'https://test.uri';
        $method        = 'Test-Method';
        $headers       = ['test-header' => null];
        $configHeaders = ['a' => 'b'];
        $exception     = new InvalidArgumentException('wrong header');

        $clientConfig = $this->getMockBuilder(ClientConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $clientConfig->expects(self::once())
            ->method('getHeaders')
            ->willReturn($configHeaders);
        $clientConfig->expects(self::never())
            ->method('getOptions');

        $config = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $config->expects(self::once())
            ->method('getClientConfig')
            ->willReturn($clientConfig);

        $client = $this->getMockBuilder(HttpClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $client->expects(self::once())
            ->method('resetParameters')
            ->with(false);
        $client->expects(self::once())
            ->method('clearCookies');
        $client->expects(self::once())
            ->method('clearAuth');
        $client->expects(self::once())
            ->method('setUri')
            ->with($uri);
        $client->expects(self::once())
            ->method('setMethod')
            ->with($method);
        $client->expects(self::never())
            ->method('getRequest');
        $client->expects(self::never())
            ->method('setOptions');

        $headersObj = $this->getMockBuilder(Headers::class)
            ->disableOriginalConstructor()
            ->getMock();
        $headersObj->expects(self::once())
            ->method('addHeaders')
            ->with($configHeaders + $headers)
            ->willThrowException($exception);
        $headersObj->expects(self::never())
            ->method('addHeader');
        $headersObj->expects(self::never())
            ->method('has');

        $object = new ClientBuilder($config, $client, $headersObj);

        try {
            $object->build($uri, $method, $headers);

            self::fail('Exception expected');
        } catch (Exception $e) {
            self::assertSame('wrong header', $e->getMessage());
            self::assertSame(0, $e->getCode());

            self::assertSame($exception, $e->getPrevious());
        }
    }

    /**
     * @throws ExpectationFailedException
     * @throws Exception
     */
    public function testBuild4(): void
    {
        $uri           = 'https://test.uri';
        $method        = 'Test-Method';
        $headers       = ['cache-control' => 'no-cache'];
        $configHeaders = ['a' => 'b'];
        $options       = ['timeout' => 10];

        $clientConfig = $this->getMockBuilder(ClientConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $clientConfig->expects(self::once())
            ->method('getHeaders')
            ->willReturn($configHeaders);
        $clientConfig->expects(self::once())
            ->method('getOptions')
            ->willReturn($options);

        $config = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $config->expects(self::once())
            ->method('getClientConfig')
            ->willReturn($clientConfig);

        $headersObj = $this->getMockBuilder(Headers::class)
            ->disableOriginalConstructor()
            ->getMock();
        $headersObj->expects(self::once())
            ->method('addHeaders')
            ->with($configHeaders + $headers);
        $headersObj->expects(self::never())
            ->method('addHeader');
        $matcher = self::exactly(3);
        $headersObj->expects($matcher)
            ->method('has')
            ->willReturnCallback(static function (string $param) use ($matcher): bool {
                match ($matcher->numberOfInvocations()) {
                    1 => self::assertSame('cache-control', $param),
                    2 => self::assertSame('pragma', $param),
                    default => self::assertSame('connection', $param),
                };

                return true;
            });

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::once())
            ->method('setHeaders')
            ->with($headersObj);

        $client = $this->getMockBuilder(HttpClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $client->expects(self::once())
            ->method('resetParameters')
            ->with(false);
        $client->expects(self::once())
            ->method('clearCookies');
        $client->expects(self::once())
            ->method('clearAuth');
        $client->expects(self::once())
            ->method('setUri')
            ->with($uri);
        $client->expects(self::once())
            ->method('setMethod')
            ->with($method);
        $client->expects(self::once())
            ->method('getRequest')
            ->willReturn($request);
        $client->expects(self::once())
            ->method('setOptions')
            ->with($options);

        $object = new ClientBuilder($config, $client, $headersObj);

        self::assertInstanceOf(HttpClient::class, $object->build($uri, $method, $headers));
    }

    /** @throws ExpectationFailedException */
    public function testBuild5(): void
    {
        $uri           = 'https://test.uri';
        $method        = 'Test-Method';
        $headers       = ['cache-control' => 'no-cache'];
        $configHeaders = ['a' => 'b'];
        $options       = ['timeout' => 10];
        $exception     = new HttpClient\Exception\InvalidArgumentException('wrong option');

        $clientConfig = $this->getMockBuilder(ClientConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $clientConfig->expects(self::once())
            ->method('getHeaders')
            ->willReturn($configHeaders);
        $clientConfig->expects(self::once())
            ->method('getOptions')
            ->willReturn($options);

        $config = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $config->expects(self::once())
            ->method('getClientConfig')
            ->willReturn($clientConfig);

        $headersObj = $this->getMockBuilder(Headers::class)
            ->disableOriginalConstructor()
            ->getMock();
        $headersObj->expects(self::once())
            ->method('addHeaders')
            ->with($configHeaders + $headers);
        $headersObj->expects(self::never())
            ->method('addHeader');
        $matcher = self::exactly(3);
        $headersObj->expects($matcher)
            ->method('has')
            ->willReturnCallback(static function (string $param) use ($matcher): bool {
                match ($matcher->numberOfInvocations()) {
                    1 => self::assertSame('cache-control', $param),
                    2 => self::assertSame('pragma', $param),
                    default => self::assertSame('connection', $param),
                };

                return true;
            });

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::once())
            ->method('setHeaders')
            ->with($headersObj);

        $client = $this->getMockBuilder(HttpClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $client->expects(self::once())
            ->method('resetParameters')
            ->with(false);
        $client->expects(self::once())
            ->method('clearCookies');
        $client->expects(self::once())
            ->method('clearAuth');
        $client->expects(self::once())
            ->method('setUri')
            ->with($uri);
        $client->expects(self::once())
            ->method('setMethod')
            ->with($method);
        $client->expects(self::once())
            ->method('getRequest')
            ->willReturn($request);
        $client->expects(self::once())
            ->method('setOptions')
            ->with($options)
            ->willThrowException($exception);

        $object = new ClientBuilder($config, $client, $headersObj);

        try {
            $object->build($uri, $method, $headers);

            self::fail('Exception expected');
        } catch (Exception $e) {
            self::assertSame('wrong option', $e->getMessage());
            self::assertSame(0, $e->getCode());

            self::assertSame($exception, $e->getPrevious());
        }
    }

    /**
     * @throws ExpectationFailedException
     * @throws Exception
     */
    public function testBuild6(): void
    {
        $uri           = 'https://test.uri';
        $method        = 'Test-Method';
        $headers       = ['Cache-Control' => 'no-cache'];
        $configHeaders = ['a' => 'b'];
        $options       = ['timeout' => 10];

        $clientConfig = $this->getMockBuilder(ClientConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $clientConfig->expects(self::once())
            ->method('getHeaders')
            ->willReturn($configHeaders);
        $clientConfig->expects(self::once())
            ->method('getOptions')
            ->willReturn($options);

        $config = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $config->expects(self::once())
            ->method('getClientConfig')
            ->willReturn($clientConfig);

        $headersObj = $this->getMockBuilder(Headers::class)
            ->disableOriginalConstructor()
            ->getMock();
        $headersObj->expects(self::once())
            ->method('addHeaders')
            ->with($configHeaders + array_change_key_case($headers, CASE_LOWER));
        $headersObj->expects(self::never())
            ->method('addHeader');
        $matcher = self::exactly(3);
        $headersObj->expects($matcher)
            ->method('has')
            ->willReturnCallback(static function (string $param) use ($matcher): bool {
                match ($matcher->numberOfInvocations()) {
                    1 => self::assertSame('cache-control', $param),
                    2 => self::assertSame('pragma', $param),
                    default => self::assertSame('connection', $param),
                };

                return true;
            });

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::once())
            ->method('setHeaders')
            ->with($headersObj);

        $client = $this->getMockBuilder(HttpClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $client->expects(self::once())
            ->method('resetParameters')
            ->with(false);
        $client->expects(self::once())
            ->method('clearCookies');
        $client->expects(self::once())
            ->method('clearAuth');
        $client->expects(self::once())
            ->method('setUri')
            ->with($uri);
        $client->expects(self::once())
            ->method('setMethod')
            ->with($method);
        $client->expects(self::once())
            ->method('getRequest')
            ->willReturn($request);
        $client->expects(self::once())
            ->method('setOptions')
            ->with($options);

        $object = new ClientBuilder($config, $client, $headersObj);

        self::assertInstanceOf(HttpClient::class, $object->build($uri, $method, $headers));
    }

    /**
     * @throws ExpectationFailedException
     * @throws Exception
     */
    public function testBuild7(): void
    {
        $uri           = 'https://test.uri';
        $method        = 'GET';
        $headers       = ['Cache-Control' => 'no-cache'];
        $configHeaders = ['a' => 'b'];
        $options       = ['timeout' => 10];

        $clientConfig = $this->getMockBuilder(ClientConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $clientConfig->expects(self::once())
            ->method('getHeaders')
            ->willReturn($configHeaders);
        $clientConfig->expects(self::once())
            ->method('getOptions')
            ->willReturn($options);

        $config = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $config->expects(self::once())
            ->method('getClientConfig')
            ->willReturn($clientConfig);

        $headersObj = new class () extends Headers {
            /** @phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable */
            private bool $cloned = false;

            /**
             * @throws void
             *
             * @api
             */
            public function isCloned(): bool
            {
                return $this->cloned;
            }

            /** @throws void */
            public function __clone(): void
            {
                $this->cloned = true;
            }
        };

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::never())
            ->method('setHeaders');

        $client = new class () extends HttpClient {
            /** @phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable */
            private bool $cloned = false;

            /**
             * @throws void
             *
             * @api
             */
            public function isCloned(): bool
            {
                return $this->cloned;
            }

            /** @throws void */
            public function __clone(): void
            {
                $this->cloned = true;
            }
        };

        $client->setRequest($request);

        $object = new ClientBuilder($config, $client, $headersObj);

        $buildClient = $object->build($uri, $method, $headers);

        self::assertInstanceOf(HttpClient::class, $buildClient);
        self::assertTrue($buildClient->isCloned());
        self::assertInstanceOf(Headers::class, $buildClient->getRequest()->getHeaders());
        self::assertTrue($buildClient->getRequest()->getHeaders()->isCloned());
    }
}
