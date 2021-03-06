<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\TemplateRenderer\Adapter;

/**
 * Interface of adapter for template engines.
 *
 * @author k.holy74@gmail.com
 */
interface AdapterInterface
{

    /**
     * オブジェクトを初期化します。
     *
     * @param mixed $engine テンプレートエンジンのインスタンス
     * @param array $configurations 設定オプション
     */
    public function initialize($engine = null, array $configurations = []);

    /**
     * 指定された設定値をセットします。
     *
     * @param string $name 設定名
     * @param mixed $value 設定値
     * @return $this
     */
    public function setConfig($name, $value);

    /**
     * 指定された設定値を返します。
     *
     * @param string $name 設定名
     * @return mixed 設定値
     */
    public function getConfig($name);

    /**
     * テンプレート処理結果を返します。
     *
     * @param string $view テンプレートファイルのパス
     * @param array $data テンプレート変数の配列
     * @return string
     */
    public function fetch($view, array $data = []);

}
