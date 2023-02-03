<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
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
     * @param mixed|null $engine テンプレートエンジンのインスタンス
     * @param array $configurations 設定オプション
     * @return self
     */
    public function initialize(mixed $engine = null, array $configurations = []): AdapterInterface;

    /**
     * 指定された設定値をセットします。
     *
     * @param string $name 設定名
     * @param mixed $value 設定値
     * @return self
     */
    public function setConfig(string $name, mixed $value): AdapterInterface;

    /**
     * 指定された設定値を返します。
     *
     * @param string $name 設定名
     * @return mixed 設定値
     */
    public function getConfig(string $name): mixed;

    /**
     * テンプレート処理結果を返します。
     *
     * @param string $view テンプレートファイルのパス
     * @param array $data テンプレート変数の配列
     * @return string
     */
    public function fetch(string $view, array $data = []): string;

}
