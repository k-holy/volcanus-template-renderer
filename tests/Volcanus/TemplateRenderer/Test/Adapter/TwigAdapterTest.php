<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
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

	public function testFetch()
	{
		$adapter = new TwigAdapter(null, array(
			'path' => $this->path,
		));

		$template = 'render.html';

		file_put_contents($this->path . DIRECTORY_SEPARATOR . $template,
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

	public function testConstructWithEngineAndGetChangedConfiguration()
	{
		$twig = new \Twig_Environment();
		$adapter = new TwigAdapter($twig);
		$twig->setCharset('EUC-JP');
		$this->assertEquals('EUC-JP', $adapter->getConfig('charset'));
	}

	public function testConfigurePath()
	{
		$adapter = new TwigAdapter();
		$adapter->setConfig('path', $this->path);
		$this->assertContains($this->path, $adapter->getConfig('path'));
	}

	public function testConfigureDebug()
	{
		$adapter = new TwigAdapter();
		$adapter->setConfig('debug', true);
		$this->assertTrue($adapter->getConfig('debug'));
	}

	public function testConfigureCharset()
	{
		$adapter = new TwigAdapter();
		$adapter->setConfig('charset', 'EUC-JP');
		$this->assertEquals('EUC-JP', $adapter->getConfig('charset'));
	}

	public function testConfigureBaseTemplateClass()
	{
		$adapter = new TwigAdapter();
		$adapter->setConfig('base_template_class', 'Twig_Template');
		$this->assertEquals('Twig_Template', $adapter->getConfig('base_template_class'));
	}

	public function testConfigureStrictVariables()
	{
		$adapter = new TwigAdapter();
		$adapter->setConfig('strict_variables', true);
		$this->assertTrue($adapter->getConfig('strict_variables'));
	}

	public function testConfigureAutoescape()
	{
		$adapter = new TwigAdapter();
		$adapter->setConfig('autoescape', 'html');
		$this->assertEquals('html', $adapter->getConfig('autoescape'));
	}

	public function testConfigureCache()
	{
		$adapter = new TwigAdapter();
		$adapter->setConfig('cache', true);
		$this->assertTrue($adapter->getConfig('cache'));
	}

	public function testConfigureAutoReload()
	{
		$adapter = new TwigAdapter();
		$adapter->setConfig('auto_reload', true);
		$this->assertTrue($adapter->getConfig('auto_reload'));
	}

	public function testConfigureOptimizations()
	{
		$adapter = new TwigAdapter();
		$adapter->setConfig('optimizations', 0);
		$this->assertEquals(0, $adapter->getConfig('optimizations'));
	}

}
