<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\TemplateRenderer\Adapter;

/**
 * Adapter for Smarty (https://www.smarty.net)
 *
 * @author k.holy74@gmail.com
 */
class SmartyAdapter implements AdapterInterface
{

    /**
     * @var array 設定値
     */
    private array $config;

    /**
     * @var \Smarty
     */
    public \Smarty $smarty;

    /**
     * コンストラクタ
     *
     * @param \Smarty|null $smarty
     * @param array $configurations 設定オプション
     */
    public function __construct(\Smarty $smarty = null, array $configurations = [])
    {
        $this->initialize($smarty, $configurations);
    }

    /**
     * オブジェクトを初期化します。
     *
     * @param mixed|null $engine
     * @param array $configurations 設定オプション
     * @return self
     */
    public function initialize(mixed $engine = null, array $configurations = []): AdapterInterface
    {
        $this->setSmarty($engine ?? new \Smarty());
        $this->config = [
            'defaultLayout' => null,
        ];
        if (!empty($configurations)) {
            foreach ($configurations as $name => $value) {
                $this->setConfig($name, $value);
            }
        }
        return $this;
    }

    private function setSmarty(\Smarty $smarty)
    {
        $this->smarty = $smarty;
    }

    /**
     * 指定された設定値を返します。
     *
     * @param string $name 設定名
     * @return mixed 設定値
     */
    public function getConfig(string $name): mixed
    {
        if (property_exists($this->smarty, $name)) {
            return $this->smarty->{$name};
        }
        switch ($name) {
            case 'template_dir':
                return $this->smarty->getTemplateDir();
            case 'config_dir':
                return $this->smarty->getConfigDir();
            case 'plugins_dir':
                return $this->smarty->getPluginsDir();
            case 'compile_dir':
                return $this->smarty->getCompileDir();
            case 'cache_dir':
                return $this->smarty->getCacheDir();
            case 'charset':
                return \Smarty::$_CHARSET;
            case 'defaultLayout':
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
     * @return self
     */
    public function setConfig(string $name, mixed $value): AdapterInterface
    {
        switch ($name) {
            case 'template_dir':
                $this->smarty->setTemplateDir($value);
                break;
            case 'config_dir':
                $this->smarty->setConfigDir($value);
                break;
            case 'plugins_dir':
                $this->smarty->setPluginsDir($value);
                break;
            case 'compile_dir':
                $this->smarty->setCompileDir($value);
                break;
            case 'cache_dir':
                $this->smarty->setCacheDir($value);
                break;
            case 'left_delimiter':
            case 'right_delimiter':
            case 'default_modifiers':
                $this->smarty->{$name} = $value;
                break;
            case 'caching':
            case 'force_compile':
            case 'use_sub_dirs':
            case 'escape_html':
                $this->smarty->{$name} = (bool)$value;
                break;
            case 'charset':
                \Smarty::$_CHARSET = $value;
                break;
            case 'defaultLayout':
                $this->config[$name] = $value;
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
     * @throws \SmartyException
     */
    public function fetch(string $view, array $data = []): string
    {
        if (!preg_match('/\A[a-z_]+:/i', $view)) {
            $defaultLayout = $this->getConfig('defaultLayout');
            if (isset($defaultLayout)) {
                $view = sprintf('extends:%s|%s', $defaultLayout, $view);
            }
        }
        $template = $this->smarty->createTemplate($view);
        foreach ($data as $name => $value) {
            $template->assign($name, $value);
        }
        return $template->fetch();
    }

}
