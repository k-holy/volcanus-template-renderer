<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\TemplateRenderer\Adapter;

/**
 * Adapter for PHPTAL (https://phptal.org)
 *
 * @author k.holy74@gmail.com
 */
class PhpTalAdapter implements AdapterInterface
{

    /**
     * @var \PHPTAL
     */
    public \PHPTAL $phptal;

    /**
     * コンストラクタ
     *
     * @param \PHPTAL|null $phptal
     * @param array $configurations 設定オプション
     * @throws \PHPTAL_ConfigurationException
     */
    public function __construct(\PHPTAL $phptal = null, array $configurations = [])
    {
        $this->initialize($phptal, $configurations);
    }

    /**
     * オブジェクトを初期化します。
     *
     * @param mixed|null $engine
     * @param array $configurations 設定オプション
     * @return self
     * @throws \PHPTAL_ConfigurationException
     */
    public function initialize(mixed $engine = null, array $configurations = []): AdapterInterface
    {
        $this->setPhpTal($engine ?? new \PHPTAL());
        if (!empty($configurations)) {
            foreach ($configurations as $name => $value) {
                $this->setConfig($name, $value);
            }
        }
        return $this;
    }

    private function setPhpTal(\PHPTAL $phptal)
    {
        $this->phptal = $phptal;
    }

    /**
     * 指定された設定値を返します。
     *
     * @param string $name 設定名
     * @return mixed 設定値
     */
    public function getConfig(string $name): mixed
    {
        if (property_exists($this->phptal, $name)) {
            return $this->phptal->{$name};
        }
        switch ($name) {
            case 'templateRepository':
                return $this->phptal->getTemplateRepositories();
            case 'phpCodeDestination':
                return $this->phptal->getPhpCodeDestination();
            case 'encoding':
                return $this->phptal->getEncoding();
            case 'phpCodeExtension':
                return $this->phptal->getPhpCodeExtension();
            case 'outputMode':
                return $this->phptal->getOutputMode();
            case 'cacheLifetime':
                return $this->phptal->getCacheLifetime();
            case 'forceReparse':
                return $this->phptal->getForceReparse();
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
     * @return self
     * @throws \PHPTAL_ConfigurationException
     */
    public function setConfig(string $name, mixed $value): AdapterInterface
    {
        switch ($name) {
            case 'templateRepository':
                $this->phptal->setTemplateRepository($value);
                break;
            case 'phpCodeDestination':
                $this->phptal->setPhpCodeDestination($value);
                break;
            case 'encoding':
                $this->phptal->setEncoding($value);
                break;
            case 'phpCodeExtension':
                $this->phptal->setPhpCodeExtension($value);
                break;
            case 'outputMode':
                $this->phptal->setOutputMode($value);
                break;
            case 'cacheLifetime':
                $this->phptal->setCacheLifetime($value);
                break;
            case 'forceReparse':
                $this->phptal->setForceReparse($value);
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
        foreach ($data as $name => $value) {
            $this->phptal->set($name, $value);
        }
        return $this->phptal->setTemplate($view)->execute();
    }

}
