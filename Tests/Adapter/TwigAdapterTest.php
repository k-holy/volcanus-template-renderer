<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\TemplateRenderer\Tests\Adapter;

use Volcanus\TemplateRenderer\Adapter\TwigAdapter;

/**
 * TwigAdapterTest
 *
 * @author k.holy74@gmail.com
 */
class TwigAdapterTest extends \PHPUnit_Framework_TestCase
{

	private $path;

	public function setUp()
	{
		$this->path = realpath(__DIR__ . '/../views');
	}

	public function tearDown()
	{
		$it = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($this->path)
		);
		foreach ($it as $file) {
			if ($file->isFile() && $file->getBaseName() !== '.gitignore') {
				unlink($file);
			}
		}
	}

	public function testSetConfig()
	{
		$adapter = new TwigAdapter();
		$adapter->setConfig('path'               , $this->path);
		$adapter->setConfig('debug'              , true);
		$adapter->setConfig('charset'            , 'EUC-JP');
		$adapter->setConfig('base_template_class', 'Twig_Template');
		$adapter->setConfig('strict_variables'   , true);
		$adapter->setConfig('autoescape'         , false);
		$adapter->setConfig('cache'              , true);
		$adapter->setConfig('auto_reload'        , true);
		$adapter->setConfig('optimizations'      , 0);
		$this->assertEquals($this->path   , $adapter->getConfig('path'));
		$this->assertTrue($adapter->getConfig('debug'));
		$this->assertEquals('EUC-JP'       , $adapter->getConfig('charset'));
		$this->assertEquals('Twig_Template', $adapter->getConfig('base_template_class'));
		$this->assertTrue($adapter->getConfig('strict_variables'));
		$this->assertFalse($adapter->getConfig('autoescape'));
		$this->assertTrue($adapter->getConfig('cache'));
		$this->assertTrue($adapter->getConfig('auto_reload'));
		$this->assertEquals(0, $adapter->getConfig('optimizations'));
	}

	public function testFetch()
	{
		$adapter = new TwigAdapter(array(
			'path' => $this->path,
		));

		$template = '/render.html';

		file_put_contents($this->path . $template,
<<<'TEMPLATE'
<html>
<head>
<title>{{title}}</title>
</head>
<body>
</body>
</html>
TEMPLATE
		);

		$xml = simplexml_load_string($adapter->fetch($template, array('title' => 'TITLE')));
		$titles = $xml->xpath('/html/head/title');
		$title = (string)$titles[0];

		$this->assertEquals('TITLE', $title);
	}


}
