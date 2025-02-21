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
use Laminas\Http\Client\Exception\InvalidArgumentException;
use Laminas\Http\Header\CacheControl;
use Laminas\Http\Header\Connection;
use Laminas\Http\Header\Pragma;
use Laminas\Http\Headers;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

use function assert;

final class ClientAbstractFactoryTest extends TestCase
{
    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     */
    public function testCanCreateConfig(): void
    {
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::never())
            ->method('has');
        $container->expects(self::never())
            ->method('get');

        $factory = new ClientAbstractFactory();

        self::assertFalse($factory->canCreate($container, 'config'));
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     */
    public function testCanCreateFromEmptyString(): void
    {
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::never())
            ->method('has');
        $container->expects(self::never())
            ->method('get');

        $factory = new ClientAbstractFactory();

        self::assertFalse($factory->canCreate($container, ''));
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     */
    public function testCanCreate1(): void
    {
        $requestedName = 'test-client';

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(false);
        $container->expects(self::never())
            ->method('get');

        $factory = new ClientAbstractFactory();

        self::assertFalse($factory->canCreate($container, $requestedName));
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     */
    public function testCanCreate2(): void
    {
        $requestedName = 'test-client';

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(true);
        $container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn(true);

        $factory = new ClientAbstractFactory();

        self::assertFalse($factory->canCreate($container, $requestedName));
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     */
    public function testCanCreate3(): void
    {
        $requestedName = 'test-client';

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(true);
        $container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn([]);

        $factory = new ClientAbstractFactory();

        self::assertFalse($factory->canCreate($container, $requestedName));
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     */
    public function testCanCreate4(): void
    {
        $requestedName = 'test-client';

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(true);
        $container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn(['http-clients' => true]);

        $factory = new ClientAbstractFactory();

        self::assertFalse($factory->canCreate($container, $requestedName));
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     */
    public function testCanCreate5(): void
    {
        $requestedName = 'test-client';

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(true);
        $container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn(['http-clients' => []]);

        $factory = new ClientAbstractFactory();

        self::assertFalse($factory->canCreate($container, $requestedName));
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     */
    public function testCanCreate6(): void
    {
        $requestedName = 'test-client';

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(true);
        $container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn(['http-clients' => [$requestedName => true]]);

        $factory = new ClientAbstractFactory();

        self::assertFalse($factory->canCreate($container, $requestedName));
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     */
    public function testCanCreate7(): void
    {
        $requestedName = 'test-client';

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(true);
        $container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn(['http-clients' => [$requestedName => []]]);

        $factory = new ClientAbstractFactory();

        self::assertTrue($factory->canCreate($container, $requestedName));
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws \Laminas\Http\Header\Exception\InvalidArgumentException
     */
    public function testInvoke1(): void
    {
        $requestedName = 'test-client';

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(true);
        $container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn(['http-clients' => [$requestedName => true]]);

        $factory = new ClientAbstractFactory();

        $result = ($factory)($container, $requestedName);

        self::assertInstanceOf(HttpClient::class, $result);

        $headers = $result->getRequest()->getHeaders();

        self::assertInstanceOf(Headers::class, $headers);
        self::assertCount(3, $headers);
        self::assertTrue($headers->has('cache-control'));
        self::assertTrue($headers->has('pragma'));
        self::assertTrue($headers->has('connection'));

        $cc = $headers->get('cache-control');
        assert($cc instanceof CacheControl);

        self::assertTrue($cc->hasDirective('no-store'));
        self::assertTrue($cc->getDirective('no-store'));
        self::assertTrue($cc->hasDirective('no-cache'));
        self::assertTrue($cc->getDirective('no-cache'));
        self::assertTrue($cc->hasDirective('must-revalidate'));
        self::assertTrue($cc->getDirective('must-revalidate'));
        self::assertTrue($cc->hasDirective('post-check'));
        self::assertTrue($cc->getDirective('post-check'));
        self::assertTrue($cc->hasDirective('pre-check'));
        self::assertTrue($cc->getDirective('pre-check'));

        $p = $headers->get('pragma');
        assert($p instanceof Pragma);

        self::assertSame('no-cache', $p->getFieldValue());

        $c = $headers->get('connection');
        assert($c instanceof Connection);

        self::assertTrue($c->isPersistent());
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws \Laminas\Http\Header\Exception\InvalidArgumentException
     */
    public function testInvoke2(): void
    {
        $requestedName = 'test-client';

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(true);
        $container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn(['http-clients' => [$requestedName => []]]);

        $factory = new ClientAbstractFactory();

        $result = ($factory)($container, $requestedName);

        self::assertInstanceOf(HttpClient::class, $result);

        $headers = $result->getRequest()->getHeaders();

        self::assertInstanceOf(Headers::class, $headers);
        self::assertCount(3, $headers);
        self::assertTrue($headers->has('cache-control'));
        self::assertTrue($headers->has('pragma'));
        self::assertTrue($headers->has('connection'));

        $cc = $headers->get('cache-control');
        assert($cc instanceof CacheControl);

        self::assertTrue($cc->hasDirective('no-store'));
        self::assertTrue($cc->getDirective('no-store'));
        self::assertTrue($cc->hasDirective('no-cache'));
        self::assertTrue($cc->getDirective('no-cache'));
        self::assertTrue($cc->hasDirective('must-revalidate'));
        self::assertTrue($cc->getDirective('must-revalidate'));
        self::assertTrue($cc->hasDirective('post-check'));
        self::assertTrue($cc->getDirective('post-check'));
        self::assertTrue($cc->hasDirective('pre-check'));
        self::assertTrue($cc->getDirective('pre-check'));

        $p = $headers->get('pragma');
        assert($p instanceof Pragma);

        self::assertSame('no-cache', $p->getFieldValue());

        $c = $headers->get('connection');
        assert($c instanceof Connection);

        self::assertTrue($c->isPersistent());
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws \Laminas\Http\Header\Exception\InvalidArgumentException
     */
    public function testInvoke3(): void
    {
        $requestedName = 'test-client';

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(true);
        $container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn(
                ['http-clients' => [$requestedName => ['headers' => true, 'options' => true]]],
            );

        $factory = new ClientAbstractFactory();

        $result = ($factory)($container, $requestedName);

        self::assertInstanceOf(HttpClient::class, $result);

        $headers = $result->getRequest()->getHeaders();

        self::assertInstanceOf(Headers::class, $headers);
        self::assertCount(3, $headers);
        self::assertTrue($headers->has('cache-control'));
        self::assertTrue($headers->has('pragma'));
        self::assertTrue($headers->has('connection'));

        $cc = $headers->get('cache-control');
        assert($cc instanceof CacheControl);

        self::assertTrue($cc->hasDirective('no-store'));
        self::assertTrue($cc->getDirective('no-store'));
        self::assertTrue($cc->hasDirective('no-cache'));
        self::assertTrue($cc->getDirective('no-cache'));
        self::assertTrue($cc->hasDirective('must-revalidate'));
        self::assertTrue($cc->getDirective('must-revalidate'));
        self::assertTrue($cc->hasDirective('post-check'));
        self::assertTrue($cc->getDirective('post-check'));
        self::assertTrue($cc->hasDirective('pre-check'));
        self::assertTrue($cc->getDirective('pre-check'));

        $p = $headers->get('pragma');
        assert($p instanceof Pragma);

        self::assertSame('no-cache', $p->getFieldValue());

        $c = $headers->get('connection');
        assert($c instanceof Connection);

        self::assertTrue($c->isPersistent());
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws \Laminas\Http\Header\Exception\InvalidArgumentException
     */
    public function testInvoke4(): void
    {
        $requestedName = 'test-client';

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(true);
        $container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn(['http-clients' => [$requestedName => ['headers' => [], 'options' => []]]]);

        $factory = new ClientAbstractFactory();

        $result = ($factory)($container, $requestedName);

        self::assertInstanceOf(HttpClient::class, $result);

        $headers = $result->getRequest()->getHeaders();

        self::assertInstanceOf(Headers::class, $headers);
        self::assertCount(3, $headers);
        self::assertTrue($headers->has('cache-control'));
        self::assertTrue($headers->has('pragma'));
        self::assertTrue($headers->has('connection'));

        $cc = $headers->get('cache-control');
        assert($cc instanceof CacheControl);

        self::assertTrue($cc->hasDirective('no-store'));
        self::assertTrue($cc->getDirective('no-store'));
        self::assertTrue($cc->hasDirective('no-cache'));
        self::assertTrue($cc->getDirective('no-cache'));
        self::assertTrue($cc->hasDirective('must-revalidate'));
        self::assertTrue($cc->getDirective('must-revalidate'));
        self::assertTrue($cc->hasDirective('post-check'));
        self::assertTrue($cc->getDirective('post-check'));
        self::assertTrue($cc->hasDirective('pre-check'));
        self::assertTrue($cc->getDirective('pre-check'));

        $p = $headers->get('pragma');
        assert($p instanceof Pragma);

        self::assertSame('no-cache', $p->getFieldValue());

        $c = $headers->get('connection');
        assert($c instanceof Connection);

        self::assertTrue($c->isPersistent());
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws \Laminas\Http\Header\Exception\InvalidArgumentException
     */
    public function testInvoke5(): void
    {
        $requestedName = 'test-client';
        $separator     = '%';

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('has')
            ->with('config')
            ->willReturn(true);
        $container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn(
                [
                    'http-clients' => [
                        $requestedName => [
                            'headers' => ['cache-control' => 'no-cache', 'pragma' => 'cache', 'connection' => 'close', 'keep-alive' => '5'],
                            'options' => ['argseparator' => $separator],
                        ],
                    ],
                ],
            );

        $factory = new ClientAbstractFactory();

        self::assertTrue($factory->canCreate($container, $requestedName));

        $result = ($factory)($container, $requestedName);

        self::assertInstanceOf(HttpClient::class, $result);

        $headers = $result->getRequest()->getHeaders();

        self::assertInstanceOf(Headers::class, $headers);
        self::assertCount(4, $headers);
        self::assertTrue($headers->has('cache-control'));
        self::assertTrue($headers->has('pragma'));
        self::assertTrue($headers->has('connection'));
        self::assertTrue($headers->has('keep-alive'));

        $cc = $headers->get('cache-control');
        assert($cc instanceof CacheControl);

        self::assertFalse($cc->hasDirective('no-store'));
        self::assertNull($cc->getDirective('no-store'));
        self::assertTrue($cc->hasDirective('no-cache'));
        self::assertTrue($cc->getDirective('no-cache'));
        self::assertFalse($cc->hasDirective('must-revalidate'));
        self::assertNull($cc->getDirective('must-revalidate'));
        self::assertFalse($cc->hasDirective('post-check'));
        self::assertNull($cc->getDirective('post-check'));
        self::assertFalse($cc->hasDirective('pre-check'));
        self::assertNull($cc->getDirective('pre-check'));

        $p = $headers->get('pragma');
        assert($p instanceof Pragma);

        self::assertSame('cache', $p->getFieldValue());

        $c = $headers->get('connection');
        assert($c instanceof Connection);

        self::assertFalse($c->isPersistent());

        self::assertSame($separator, $result->getArgSeparator());
    }
}
