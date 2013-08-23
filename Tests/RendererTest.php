<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\TemplateRenderer\Tests;

use Volcanus\TemplateRenderer\Renderer;

/**
 * RendererTest
 *
 * @author k.holy74@gmail.com
 */
class RendererTest extends \PHPUnit_Framework_TestCase
{

	public function testConfig()
	{
		$adapter = $this->getMock('Volcanus\TemplateRenderer\Adapter\AdapterInterface');
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

	public function testFetch()
	{
		$adapter = $this->getMock('Volcanus\TemplateRenderer\Adapter\AdapterInterface');
		$adapter->expects($this->any())
			->method('fetch')
			->will($this->returnCallback(function($view, $data) {
				return $data['name'];
			}));
		$renderer = new Renderer($adapter);
		$this->assertEquals('foo', $renderer->fetch('/path/to/template', array('name' => 'foo')));
	}

	public function testAssignAndFetch()
	{
		$adapter = $this->getMock('Volcanus\TemplateRenderer\Adapter\AdapterInterface');
		$adapter->expects($this->any())
			->method('fetch')
			->will($this->returnCallback(function($view, $data) {
				return $data['name'];
			}));
		$renderer = new Renderer($adapter);
		$renderer->assign('name', 'foo');
		$this->assertEquals('foo', $renderer->fetch('/path/to/template'));
		$this->assertEquals('bar', $renderer->fetch('/path/to/template', array('name' => 'bar')));
	}

	public function testRender()
	{
		$adapter = $this->getMock('Volcanus\TemplateRenderer\Adapter\AdapterInterface');
		$adapter->expects($this->any())
			->method('fetch')
			->will($this->returnCallback(function($view, $data) {
				return $data['name'];
			}));
		$renderer = new Renderer($adapter);
		ob_start();
		$renderer->render('/path/to/template', array('name' => 'foo'));
		$this->assertEquals('foo', ob_get_contents());
		ob_end_clean();
	}

}
