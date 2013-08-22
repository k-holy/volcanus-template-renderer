<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\TemplateRenderer\Adapter;

/**
 * Adapter for plain PHP template.
 *
 * @author k.holy74@gmail.com
 */
class PhpAdapter implements AdapterInterface
{

	/**
	 * @var array 設定値
	 */
	protected $config;

	/**
	 * コンストラクタ
	 *
	 * @param array 設定オプション
	 */
	public function __construct(array $configurations = array())
	{
		$this->initialize($configurations);
	}

	/**
	 * オブジェクトを初期化します。
	 *
	 * @param array 設定オプション
	 * @return self
	 */
	public function initialize(array $configurations = array())
	{
		$this->config = array(
			'template_dir' => null,
		);
		if (!empty($configurations)) {
			foreach ($configurations as $name => $value) {
				$this->setConfig($name, $value);
			}
		}
		return $this;
	}

	/**
	 * 指定された設定値を返します。
	 *
	 * @param string 設定名
	 * @return mixed 設定値
	 */
	public function getConfig($name)
	{
		return $this->config[$name];
	}

	/**
	 * 指定された設定値をセットします。
	 *
	 * @param string 設定名
	 * @param mixed 設定値
	 * @return self
	 */
	public function setConfig($name, $value)
	{
		switch ($name) {
		case 'template_dir':
			if (!is_string($value)) {
				throw new \InvalidArgumentException(
					sprintf('The config parameter "%s" only accepts string.', $name));
			}
			break;
		default:
			throw new \InvalidArgumentException(
				sprintf('The config parameter "%s" is not defined.', $name)
			);
		}
		$this->config[$name] = $value;
		return $this;
	}

	/**
	 * テンプレート処理結果を返します。
	 *
	 * @param string テンプレートファイルのパス
	 * @param array テンプレート変数の配列
	 * @return string
	 */
	public function fetch($view, array $data = array())
	{
		$dir = $this->getConfig('template_dir');
		if (isset($dir)) {
			$dir = rtrim($dir, '/');
		}
		$template = (isset($dir)) ? $dir . DIRECTORY_SEPARATOR . $view : $view;
		if ('\\' === DIRECTORY_SEPARATOR) {
			$template = str_replace('\\', '/', $template);
		}
		if (false !== realpath($template)) {
			ob_start();
			extract($data);
			include $template;
			$contents = ob_get_contents();
			ob_end_clean();
			return $contents;
		}
	}

}
