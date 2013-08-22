<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\TemplateRenderer\Tests\Adapter;

use Volcanus\TemplateRenderer\Adapter\PhpTalAdapter;

/**
 * PhpTalAdapterTest
 *
 * @author k.holy74@gmail.com
 */
class PhpTalAdapterTest extends \PHPUnit_Framework_TestCase
{

	private $templateRepository;
	private $phpCodeDestination;

	public function setUp()
	{
		$this->templateRepository = realpath(__DIR__ . '/../views');
		$this->phpCodeDestination  = realpath(__DIR__ . '/../temp');
	}

	public function tearDown()
	{
		$it = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($this->templateRepository)
		);
		foreach ($it as $file) {
			if ($file->isFile() && $file->getBaseName() !== '.gitignore') {
				unlink($file);
			}
		}
		$it = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($this->phpCodeDestination)
		);
		foreach ($it as $file) {
			if ($file->isFile() && $file->getBaseName() !== '.gitignore') {
				unlink($file);
			}
		}
	}

	public function testSetConfig()
	{
		$adapter = new PhpTalAdapter();
		$adapter->setConfig('outputMode', \PHPTAL::XHTML);
		$adapter->setConfig('encoding', 'UTF-8');
		$adapter->setConfig('templateRepository', $this->templateRepository);
		$adapter->setConfig('phpCodeDestination', $this->phpCodeDestination);
		$adapter->setConfig('phpCodeExtension', 'php');
		$adapter->setConfig('cacheLifetime', 0);
		$adapter->setConfig('forceReparse', true);
		$this->assertEquals(\PHPTAL::XHTML, $adapter->getConfig('outputMode'));
		$this->assertEquals('UTF-8', $adapter->getConfig('encoding'));
		$this->assertEquals($this->templateRepository, $adapter->getConfig('templateRepository'));
		$this->assertEquals($this->phpCodeDestination, $adapter->getConfig('phpCodeDestination'));
		$this->assertEquals('php', $adapter->getConfig('phpCodeExtension'));
		$this->assertEquals(0, $adapter->getConfig('cacheLifetime'));
		$this->assertTrue($adapter->getConfig('forceReparse'));
	}

	public function testFetch()
	{
		$adapter = new PhpTalAdapter(array(
			'templateRepository' => $this->templateRepository,
			'phpCodeDestination' => $this->phpCodeDestination,
			'cacheLifetime'      => 0,
			'forceReparse'       => true,
		));

		$template = '/render.html';

		file_put_contents($this->templateRepository . $template,
<<<'TEMPLATE'
<html>
<head>
<title tal:content="title">Title is here.</title>
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
