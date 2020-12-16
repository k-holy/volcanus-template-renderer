<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\TemplateRenderer\Adapter;

use Latte\Engine;
use Latte\Loaders\FileLoader;

/**
 * Adapter for Latte (https://latte.nette.org/en/)
 *
 * @author k.holy74@gmail.com
 */
class LatteAdapter implements AdapterInterface
{

    /**
     * @var array 設定値
     */
    private $config = [
        'baseDir' => null,
        'tempDirectory' => null,
        'autoRefresh' => null,
        'strictTypes' => null,
    ];

    /**
     * @var \Latte\Engine
     */
    public $latte;

    /**
     * @var \Latte\Loaders\FileLoader
     */
    private $fileLoader;

    /**
     * コンストラクタ
     *
     * @param \Latte\Engine|null $latte
     * @param array $configurations 設定オプション
     */
    public function __construct(Engine $latte = null, array $configurations = [])
    {
        $this->initialize($latte, $configurations);
    }

    /**
     * オブジェクトを初期化します。
     *
     * @param \Latte\Engine|null $latte
     * @param array $configurations 設定オプション
     * @return $this
     */
    public function initialize($latte = null, array $configurations = []): AdapterInterface
    {
        $this->setLatte(isset($latte) ? $latte : new \Latte\Engine());
        foreach (array_keys($this->config) as $name) {
            $this->config[$name] = null;
        }
        if (!empty($configurations)) {
            foreach ($configurations as $name => $value) {
                $this->setConfig($name, $value);
            }
        }
        return $this;
    }

    private function setLatte(\Latte\Engine $latte)
    {
        $this->latte = $latte;
    }

    /**
     * 指定された設定値を返します。
     *
     * @param string $name 設定名
     * @return mixed 設定値
     */
    public function getConfig(string $name)
    {
        switch ($name) {
            // \Latte\Engine は設定値の取得用メソッドを提供していないので…
            case 'baseDir':
            case 'tempDirectory':
            case 'autoRefresh':
            case 'strictTypes':
                return $this->config[$name];
        }
        throw new \InvalidArgumentException(
            sprintf('The config parameter "%s" is not support.', $name)
        );
    }

    /**
     * 指定された設定値をセットします。
     *
     * @param string $name 設定名
     * @param mixed $value 設定値
     * @return $this
     */
    public function setConfig(string $name, $value): AdapterInterface
    {
        switch ($name) {
            case 'baseDir':
                $this->config[$name] = $value;
                break;
            case 'tempDirectory':
                $this->config[$name] = $value;
                $this->latte->setTempDirectory($value);
                break;
            case 'autoRefresh':
                $this->config[$name] = $value;
                $this->latte->setAutoRefresh($value);
                break;
            case 'strictTypes':
                $this->config[$name] = $value;
                $this->latte->setStrictTypes($value);
                break;
            default:
                throw new \InvalidArgumentException(
                    sprintf('The config parameter "%s" is not support.', $name)
                );
        }
        return $this;
    }

    /**
     * テンプレート処理結果を返します。
     *
     * @param string $view テンプレートファイルのパス
     * @param array $data テンプレート変数の配列
     * @return string
     */
    public function fetch(string $view, array $data = []): string
    {
        if ($this->fileLoader === null) {
            $this->fileLoader = new FileLoader(
                $this->getConfig('baseDir')
            );
        }
        $this->latte->setLoader($this->fileLoader);
        return $this->latte->renderToString($view, $data);
    }

}
