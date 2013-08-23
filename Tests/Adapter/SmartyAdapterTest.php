<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\TemplateRenderer\Tests\Adapter;

use Volcanus\TemplateRenderer\Adapter\SmartyAdapter;

/**
 * SmartyAdapterTest
 *
 * @author k.holy74@gmail.com
 */
class SmartyAdapterTest extends \PHPUnit_Framework_TestCase
{

	private $template_dir;
	private $compile_dir;

	public function setUp()
	{
		$this->template_dir = realpath(__DIR__ . '/../views');
		$this->compile_dir  = realpath(__DIR__ . '/../temp');
	}

	public function tearDown()
	{
		$it = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($this->template_dir)
		);
		foreach ($it as $file) {
			if ($file->isFile() && $file->getBaseName() !== '.gitignore') {
				unlink($file);
			}
		}
		$it = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($this->compile_dir)
		);
		foreach ($it as $file) {
			if ($file->isFile() && $file->getBaseName() !== '.gitignore') {
				unlink($file);
			}
		}
	}

	public function testFetch()
	{
		$adapter = new SmartyAdapter(null, array(
			'template_dir'    => $this->template_dir,
			'compile_dir'     => $this->compile_dir,
			'left_delimiter'  => '{{',
			'right_delimiter' => '}}',
			'caching'         => false,
			'force_compile'   => true,
			'use_sub_dirs'    => false,
			'escape_html'     => true,
		));

		$template = 'render.html';

		file_put_contents($this->template_dir . DIRECTORY_SEPARATOR . $template,
<<<'TEMPLATE'
<html>
<head>
<title>{{$title}}</title>
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
		$smarty = new \Smarty();
		$adapter = new SmartyAdapter($smarty);
		$smarty->escape_html = true;
		$this->assertTrue($adapter->getConfig('escape_html'));
	}

	public function testConfigureTemplateDir()
	{
		$adapter = new SmartyAdapter();
		$adapter->setConfig('template_dir', $this->template_dir);
		// 末尾のディレクトリセパレータがエンジンにより自動で付与される
		$this->assertContains($this->template_dir . DIRECTORY_SEPARATOR, $adapter->getConfig('template_dir'));
	}

	public function testConfigureCompileDir()
	{
		$adapter = new SmartyAdapter();
		$adapter->setConfig('compile_dir', $this->compile_dir);
		// 末尾のディレクトリセパレータがエンジンにより自動で付与される
		$this->assertEquals($this->compile_dir . DIRECTORY_SEPARATOR, $adapter->getConfig('compile_dir'));
	}

	public function testConfigureLeftDelimiter()
	{
		$adapter = new SmartyAdapter();
		$adapter->setConfig('left_delimiter', '{{');
		$this->assertEquals('{{', $adapter->getConfig('left_delimiter'));
	}

	public function testConfigureRightDelimiter()
	{
		$adapter = new SmartyAdapter();
		$adapter->setConfig('right_delimiter', '}}');
		$this->assertEquals('}}', $adapter->getConfig('right_delimiter'));
	}

	public function testConfigureCaching()
	{
		$adapter = new SmartyAdapter();
		$adapter->setConfig('caching', false);
		$this->assertFalse($adapter->getConfig('caching'));
	}

	public function testConfigureForceCompile()
	{
		$adapter = new SmartyAdapter();
		$adapter->setConfig('force_compile', true);
		$this->assertTrue($adapter->getConfig('force_compile'));
	}

	public function testConfigureUseSubDirs()
	{
		$adapter = new SmartyAdapter();
		$adapter->setConfig('use_sub_dirs', false);
		$this->assertFalse($adapter->getConfig('use_sub_dirs'));
	}

	public function testConfigureEscapeHtml()
	{
		$adapter = new SmartyAdapter();
		$adapter->setConfig('escape_html', true);
		$this->assertTrue($adapter->getConfig('escape_html'));
	}

}
