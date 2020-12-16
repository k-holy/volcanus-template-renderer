<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\TemplateRenderer\Test\Adapter;

use PHPUnit\Framework\TestCase;
use Volcanus\TemplateRenderer\Adapter\PhpTalAdapter;

/**
 * PhpTalAdapterTest
 *
 * @author k.holy74@gmail.com
 */
class PhpTalAdapterTest extends TestCase
{

    private $templateRepository;
    private $phpCodeDestination;

    public function setUp(): void
    {
        $this->templateRepository = realpath(__DIR__ . '/../views');
        $this->phpCodeDestination = realpath(__DIR__ . '/../temp');
    }

    public function tearDown(): void
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

    public function testFetch()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $adapter = new PhpTalAdapter(null, [
            'templateRepository' => $this->templateRepository,
            'phpCodeDestination' => $this->phpCodeDestination,
            'cacheLifetime' => 0,
            'forceReparse' => true,
        ]);

        $template = 'render.html';

        file_put_contents($this->templateRepository . DIRECTORY_SEPARATOR . $template,
            <<<'TEMPLATE'
<html lang="en">
<head>
<title tal:content="title">Title is here.</title>
</head>
<body>
<ul tal:condition="php:count(items)">
    <li tal:repeat="item items" id="item-${repeat/item/number}">${item/value}:${item/text}</li>
</ul>
</body>
</html>
TEMPLATE
        );

        $items = [];
        $items[0] = new \stdClass();
        $items[0]->value = '1';
        $items[0]->text = 'first text';
        $items[1] = new \stdClass();
        $items[1]->value = '2';
        $items[1]->text = 'second text';
        $items[2] = new \stdClass();
        $items[2]->value = '3';
        $items[2]->text = 'third text';

        $params = [
            'title' => '<TITLE>',
            'items' => $items,
        ];

        $xml = simplexml_load_string($adapter->fetch($template, $params));

        $elements = $xml->xpath('/html/head/title');
        $title = (string)$elements[0];
        $this->assertEquals('<TITLE>', $title);

        $elements = $xml->xpath('/html/body/ul/li[1]/text()');
        $text = (string)$elements[0];
        $this->assertEquals('1:first text', $text);

        $elements = $xml->xpath('/html/body/ul/li[3]/@id');
        $id = (string)$elements[0];
        $this->assertEquals('item-3', $id);

    }

    public function testConstructWithEngineAndGetChangedConfiguration()
    {
        $phptal = new \PHPTAL();
        /** @noinspection PhpUnhandledExceptionInspection */
        $adapter = new PhpTalAdapter($phptal);
        $phptal->setEncoding('EUC-JP');
        $this->assertEquals('EUC-JP', $adapter->getConfig('encoding'));
    }

    public function testConfigureOutputMode()
    {
        $adapter = new PhpTalAdapter();
        /** @noinspection PhpUnhandledExceptionInspection */
        $adapter->setConfig('outputMode', \PHPTAL::XHTML);
        $this->assertEquals(\PHPTAL::XHTML, $adapter->getConfig('outputMode'));
    }

    public function testConfigureEncoding()
    {
        $adapter = new PhpTalAdapter();
        /** @noinspection PhpUnhandledExceptionInspection */
        $adapter->setConfig('encoding', 'UTF-8');
        $this->assertEquals('UTF-8', $adapter->getConfig('encoding'));
    }

    public function testConfigureTemplateRepository()
    {
        $adapter = new PhpTalAdapter();
        /** @noinspection PhpUnhandledExceptionInspection */
        $adapter->setConfig('templateRepository', $this->templateRepository);
        $this->assertContains($this->templateRepository, $adapter->getConfig('templateRepository'));
    }

    public function testConfigurePhpCodeDestination()
    {
        $adapter = new PhpTalAdapter();
        /** @noinspection PhpUnhandledExceptionInspection */
        $adapter->setConfig('phpCodeDestination', $this->phpCodeDestination);
        // 末尾のディレクトリセパレータがエンジンにより自動で付与される
        $this->assertEquals($this->phpCodeDestination . DIRECTORY_SEPARATOR, $adapter->getConfig('phpCodeDestination'));
    }

    public function testConfigureCacheLifetime()
    {
        $adapter = new PhpTalAdapter();
        /** @noinspection PhpUnhandledExceptionInspection */
        $adapter->setConfig('cacheLifetime', 1);
        $this->assertEquals(1, $adapter->getConfig('cacheLifetime'));
    }

    public function testConfigureForceReparse()
    {
        $adapter = new PhpTalAdapter();
        /** @noinspection PhpUnhandledExceptionInspection */
        $adapter->setConfig('forceReparse', true);
        $this->assertTrue($adapter->getConfig('forceReparse'));
    }

}
