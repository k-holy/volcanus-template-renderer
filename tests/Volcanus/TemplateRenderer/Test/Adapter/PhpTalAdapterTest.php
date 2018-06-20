<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\TemplateRenderer\Tests\Adapter;

use Volcanus\TemplateRenderer\Adapter\PhpTalAdapter;

/**
 * PhpTalAdapterTest
 *
 * @author k.holy74@gmail.com
 */
class PhpTalAdapterTest extends \PHPUnit\Framework\TestCase
{

    private $templateRepository;
    private $phpCodeDestination;

    public function setUp()
    {
        $this->templateRepository = realpath(__DIR__ . '/../views');
        $this->phpCodeDestination = realpath(__DIR__ . '/../temp');
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
<html>
<head>
<title tal:content="title">Title is here.</title>
</head>
<body>
</body>
</html>
TEMPLATE
        );

        $xml = simplexml_load_string($adapter->fetch($template, ['title' => 'TITLE']));
        $titles = $xml->xpath('/html/head/title');
        $title = (string)$titles[0];

        $this->assertEquals('TITLE', $title);
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
        /** @noinspection PhpUnhandledExceptionInspection */
        $adapter = new PhpTalAdapter();
        /** @noinspection PhpUnhandledExceptionInspection */
        $adapter->setConfig('outputMode', \PHPTAL::XHTML);
        $this->assertEquals(\PHPTAL::XHTML, $adapter->getConfig('outputMode'));
    }

    public function testConfigureEncoding()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $adapter = new PhpTalAdapter();
        /** @noinspection PhpUnhandledExceptionInspection */
        $adapter->setConfig('encoding', 'UTF-8');
        $this->assertEquals('UTF-8', $adapter->getConfig('encoding'));
    }

    public function testConfigureTemplateRepository()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $adapter = new PhpTalAdapter();
        /** @noinspection PhpUnhandledExceptionInspection */
        $adapter->setConfig('templateRepository', $this->templateRepository);
        $this->assertContains($this->templateRepository, $adapter->getConfig('templateRepository'));
    }

    public function testConfigurePhpCodeDestination()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $adapter = new PhpTalAdapter();
        /** @noinspection PhpUnhandledExceptionInspection */
        $adapter->setConfig('phpCodeDestination', $this->phpCodeDestination);
        // 末尾のディレクトリセパレータがエンジンにより自動で付与される
        $this->assertEquals($this->phpCodeDestination . DIRECTORY_SEPARATOR, $adapter->getConfig('phpCodeDestination'));
    }

    public function testConfigureCacheLifetime()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $adapter = new PhpTalAdapter();
        /** @noinspection PhpUnhandledExceptionInspection */
        $adapter->setConfig('cacheLifetime', 1);
        $this->assertEquals(1, $adapter->getConfig('cacheLifetime'));
    }

    public function testConfigureForceReparse()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $adapter = new PhpTalAdapter();
        /** @noinspection PhpUnhandledExceptionInspection */
        $adapter->setConfig('forceReparse', true);
        $this->assertTrue($adapter->getConfig('forceReparse'));
    }

}
