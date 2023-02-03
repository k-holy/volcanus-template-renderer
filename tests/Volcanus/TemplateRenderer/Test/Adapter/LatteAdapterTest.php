<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\TemplateRenderer\Test\Adapter;

use PHPUnit\Framework\TestCase;
use Volcanus\TemplateRenderer\Adapter\LatteAdapter;

/**
 * LatteAdapterTest
 *
 * @author k.holy74@gmail.com
 */
class LatteAdapterTest extends TestCase
{

    private string $baseDir;
    private string $tempDirectory;

    public function setUp(): void
    {
        $this->baseDir = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'views');
        $this->tempDirectory = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'temp');
    }

    public function tearDown(): void
    {
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->baseDir)
        );
        foreach ($it as $file) {
            if ($file->isFile() && $file->getBaseName() !== '.gitignore') {
                unlink($file);
            }
        }
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->tempDirectory)
        );
        foreach ($it as $file) {
            if ($file->isFile() && $file->getBaseName() !== '.gitignore') {
                unlink($file);
            }
        }
    }

    public function testFetch()
    {
        $adapter = new LatteAdapter(null, [
            'baseDir' => $this->baseDir,
            'tempDirectory' => $this->tempDirectory,
            'autoRefresh' => true,
            'strictTypes' => true,
        ]);

        $template = 'render.latte';

        file_put_contents($this->baseDir . DIRECTORY_SEPARATOR . $template,
            <<<'TEMPLATE'
<html lang="en">
<head>
<title>{$title}</title>
</head>
<body>
<ul n:if="count($items)">
    <li n:foreach="$items as $item" id="item-{$iterator->counter}">{$item->value}:{$item->text}</li>
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

    public function testConfigureBaseDir()
    {
        $adapter = new LatteAdapter();
        $adapter->setConfig('baseDir', $this->baseDir);
        $this->assertEquals($this->baseDir, $adapter->getConfig('baseDir'));
    }

    public function testConfigureTempDirectory()
    {
        $adapter = new LatteAdapter();
        $adapter->setConfig('tempDirectory', $this->tempDirectory);
        $this->assertEquals($this->tempDirectory, $adapter->getConfig('tempDirectory'));
    }

    public function testConfigureAutoRefresh()
    {
        $adapter = new LatteAdapter();
        $adapter->setConfig('autoRefresh', true);
        $this->assertTrue($adapter->getConfig('autoRefresh'));
    }

    public function testConfigureStrictTypes()
    {
        $adapter = new LatteAdapter();
        $adapter->setConfig('strictTypes', true);
        $this->assertTrue($adapter->getConfig('strictTypes'));
    }

}
