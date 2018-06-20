<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\TemplateRenderer\Test;

use Volcanus\TemplateRenderer\Renderer;

/**
 * RendererTest
 *
 * @author k.holy74@gmail.com
 */
class RendererTest extends \PHPUnit\Framework\TestCase
{

    public function testConfig()
    {
        /** @var $adapter \Volcanus\TemplateRenderer\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject */
        $adapter = $this->createMock('\Volcanus\TemplateRenderer\Adapter\AdapterInterface');
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
        /** @var $adapter \Volcanus\TemplateRenderer\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject */
        $adapter = $this->createMock('\Volcanus\TemplateRenderer\Adapter\AdapterInterface');
        $adapter->expects($this->once())
            ->method('setConfig');

        /** @noinspection PhpUnusedLocalVariableInspection */
        $renderer = new Renderer($adapter, ['foo' => 'value']);
    }

    public function testFetch()
    {
        /** @var $adapter \Volcanus\TemplateRenderer\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject */
        $adapter = $this->createMock('\Volcanus\TemplateRenderer\Adapter\AdapterInterface');
        /** @noinspection PhpUnusedParameterInspection */
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
        /** @var $adapter \Volcanus\TemplateRenderer\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject */
        $adapter = $this->createMock('\Volcanus\TemplateRenderer\Adapter\AdapterInterface');
        /** @noinspection PhpUnusedParameterInspection */
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
        /** @var $adapter \Volcanus\TemplateRenderer\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject */
        $adapter = $this->createMock('\Volcanus\TemplateRenderer\Adapter\AdapterInterface');
        $renderer = new Renderer($adapter);
        $renderer->assign('name', 'foo');
        $this->assertTrue($renderer->assigned('name'));
        $this->assertFalse($renderer->assigned('age'));
    }

    public function testRender()
    {
        /** @var $adapter \Volcanus\TemplateRenderer\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject */
        $adapter = $this->createMock('\Volcanus\TemplateRenderer\Adapter\AdapterInterface');
        /** @noinspection PhpUnusedParameterInspection */
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

}
