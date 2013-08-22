<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\TemplateRenderer\Tests\Adapter;

use Volcanus\TemplateRenderer\Adapter\PhpAdapter;

/**
 * PhpAdapterTest
 *
 * @author k.holy74@gmail.com
 */
class PhpAdapterTest extends \PHPUnit_Framework_TestCase
{

	private $template_dir;

	public function setUp()
	{
		$this->template_dir = realpath(__DIR__ . '/../views');
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
	}

	public function testSetConfig()
	{
		$adapter = new PhpAdapter();
		$adapter->setConfig('template_dir', $this->template_dir);
		$this->assertEquals($this->template_dir, $adapter->getConfig('template_dir'));
	}

	public function testFetch()
	{
		$adapter = new PhpAdapter(array(
			'template_dir' => $this->template_dir,
		));

		$template = '/render.php';

		file_put_contents($this->template_dir . $template,
<<<'TEMPLATE'
<html>
<head>
<title><?=$title?></title>
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
