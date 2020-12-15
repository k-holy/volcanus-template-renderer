<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\TemplateRenderer\Test;

use Laminas\Diactoros\Response\HtmlResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Volcanus\TemplateRenderer\Adapter\AdapterInterface;
use Volcanus\TemplateRenderer\Renderer;

/**
 * RendererTest
 *
 * @author k.holy74@gmail.com
 */
class RendererTest extends TestCase
{

    public function testConfig()
    {
        /** @var $adapter AdapterInterface|MockObject */
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->expects($this->any())
            ->method('setConfig')
            ->will($this->returnValue('set foo value'));
        $adapter->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue('get foo value'));

        $renderer = new Renderer($adapter);
        $this->assertSame($renderer, $renderer->config('foo', 'value'));
        $this->assertEquals('get foo value', $renderer->config('foo'));
    }

    public function testSetAdapterWithConfig()
    {
        /** @var $adapter AdapterInterface|MockObject */
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->expects($this->once())
            ->method('setConfig');

        /** @noinspection PhpUnusedLocalVariableInspection */
        $renderer = new Renderer($adapter, ['foo' => 'value']);
    }

    public function testFetch()
    {
        /** @var $adapter AdapterInterface|MockObject */
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->expects($this->any())
            ->method('fetch')
            ->will($this->returnCallback(function (
                /** @noinspection PhpUnusedParameterInspection */
                $view, $data
            ) {
                return $data['name'];
            }));
        $renderer = new Renderer($adapter);
        $this->assertEquals('foo', $renderer->fetch('/path/to/template', ['name' => 'foo']));
    }

    public function testAssignAndFetch()
    {
        /** @var $adapter AdapterInterface|MockObject */
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->expects($this->any())
            ->method('fetch')
            ->will($this->returnCallback(function (
                /** @noinspection PhpUnusedParameterInspection */
                $view, $data
            ) {
                return $data['name'];
            }));
        $renderer = new Renderer($adapter);
        $renderer->assign('name', 'foo');
        $this->assertEquals('foo', $renderer->fetch('/path/to/template'));
        $this->assertEquals('bar', $renderer->fetch('/path/to/template', ['name' => 'bar']));
    }

    public function testAssigned()
    {
        /** @var $adapter AdapterInterface|MockObject */
        $adapter = $this->createMock(AdapterInterface::class);
        $renderer = new Renderer($adapter);
        $renderer->assign('name', 'foo');
        $this->assertTrue($renderer->assigned('name'));
        $this->assertFalse($renderer->assigned('age'));
    }

    public function testRender()
    {
        /** @var $adapter AdapterInterface|MockObject */
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->expects($this->any())
            ->method('fetch')
            ->will($this->returnCallback(function (
                /** @noinspection PhpUnusedParameterInspection */
                $view, $data
            ) {
                return $data['name'];
            }));
        $renderer = new Renderer($adapter);
        ob_start();
        $renderer->render('/path/to/template', ['name' => 'foo']);
        $this->assertEquals('foo', ob_get_contents());
        ob_end_clean();
    }

    public function testWriteResponse()
    {
        /** @var $adapter AdapterInterface|MockObject */
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->expects($this->any())
            ->method('fetch')
            ->will($this->returnCallback(function (
                /** @noinspection PhpUnusedParameterInspection */
                $view, $data
            ) {
                return $data['name'];
            }));
        $renderer = new Renderer($adapter);
        $renderer->assign('name', 'foo');
        $response = new HtmlResponse('');
        $renderedResponse = $renderer->writeResponse($response, '/path/to/template');
        $this->assertInstanceOf(HtmlResponse::class, $renderedResponse);
        $this->assertSame($response, $renderedResponse);
        $this->assertEquals('foo', $renderedResponse->getBody()->__toString());
    }

}
