<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\TemplateRenderer\Test\Adapter;

use PHPUnit\Framework\TestCase;
use Volcanus\TemplateRenderer\Adapter\SmartyAdapter;

/**
 * SmartyAdapterTest
 *
 * @author k.holy74@gmail.com
 */
class SmartyAdapterTest extends TestCase
{

    private string $template_dir;
    private string $compile_dir;

    public function setUp(): void
    {
        $this->template_dir = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'views');
        $this->compile_dir = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'temp');
    }

    public function tearDown(): void
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
        $adapter = new SmartyAdapter(null, [
            'template_dir' => $this->template_dir,
            'compile_dir' => $this->compile_dir,
            'left_delimiter' => '{{',
            'right_delimiter' => '}}',
            'caching' => false,
            'force_compile' => true,
            'use_sub_dirs' => false,
            'escape_html' => true,
        ]);

        $template = 'render.tpl';

        file_put_contents($this->template_dir . DIRECTORY_SEPARATOR . $template,
            <<<'TEMPLATE'
<html lang="en">
<head>
<title>{{$title}}</title>
</head>
<body>
{{if count($items) }}
<ul>
{{foreach $items as $item}}
    <li id="item-{{$item@iteration}}">{{$item->value}}:{{$item->text}}</li>
{{/foreach}}
</ul>
{{/if}}
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

        /** @noinspection PhpUnhandledExceptionInspection */
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

    public function testConfigureCharset()
    {
        $adapter = new SmartyAdapter();
        $adapter->setConfig('charset', 'EUC-JP');
        $this->assertEquals('EUC-JP', $adapter->getConfig('charset'));
    }

    public function testConfigureDefaultLayout()
    {
        $adapter = new SmartyAdapter();
        $adapter->setConfig('defaultLayout', 'layout.html');
        $this->assertEquals('layout.html', $adapter->getConfig('defaultLayout'));
    }

    /**
     * @throws \SmartyException
     */
    public function testDefaultLayout()
    {
        $adapter = new SmartyAdapter(null, [
            'template_dir' => $this->template_dir,
            'compile_dir' => $this->compile_dir,
            'left_delimiter' => '{{',
            'right_delimiter' => '}}',
            'caching' => false,
            'force_compile' => true,
            'use_sub_dirs' => false,
            'escape_html' => true,
            'defaultLayout' => 'layout.html',
        ]);

        file_put_contents($this->template_dir . DIRECTORY_SEPARATOR . 'layout.html',
            <<<'TEMPLATE'
<html lang="en">
<head>
<title>{{$title}}</title>
</head>
<body>
{{block name="content"}}{{/block}}
</body>
</html>
TEMPLATE
        );

        file_put_contents($this->template_dir . DIRECTORY_SEPARATOR . 'content.html',
            <<<'TEMPLATE'
{{block name="content"}}
<h1>{{$title}}</h1>
{{/block}}
TEMPLATE
        );

        $xml = simplexml_load_string($adapter->fetch('content.html', ['title' => 'TITLE']));
        $titles = $xml->xpath('/html/head/title');
        $headings = $xml->xpath('/html/body/h1');
        $this->assertEquals('TITLE', (string)$titles[0]);
        $this->assertEquals('TITLE', (string)$headings[0]);
    }

}
