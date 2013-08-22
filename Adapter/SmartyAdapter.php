<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\TemplateRenderer\Adapter;

/**
 * Adapter for Smarty3
 *
 * @author k.holy74@gmail.com
 */
class SmartyAdapter implements AdapterInterface
{

	/**
	 * @var array 設定値
	 */
	protected $config;

	/**
	 * @var \Smarty
	 */
	public $smarty;

	/**
	 * @var array Smarty用オプション設定
	 */
	private static $smarty_options = array(
		'template_dir',
		'config_dir',
		'plugins_dir',
		'compile_dir',
		'cache_dir',
		'left_delimiter',
		'right_delimiter',
		'default_modifiers',
		'caching',
		'force_compile',
		'use_sub_dirs',
		'escape_html',
	);

	/**
	 * コンストラクタ
	 *
	 * @param \Smarty
	 * @param array 設定オプション
	 */
	public function __construct(\Smarty $smarty = null, array $configurations = array())
	{
		$this->initialize($smarty, $configurations);
	}

	/**
	 * オブジェクトを初期化します。
	 *
	 * @param \Smarty
	 * @param array 設定オプション
	 * @return self
	 */
	public function initialize($smarty = null, array $configurations = array())
	{
		$this->setSmarty(isset($smarty) ? $smarty : new \Smarty());
		$this->config = array_fill_keys(static::$smarty_options, null) + array(
			'defaultLayout' => null,
		);
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
		case 'config_dir':
		case 'plugins_dir':
		case 'compile_dir':
		case 'cache_dir':
			if (!is_string($value) && !is_array($value)) {
				throw new \InvalidArgumentException(
					sprintf('The config parameter "%s" only accepts string.', $name));
			}
			break;
		case 'left_delimiter':
		case 'right_delimiter':
		case 'default_modifiers':
		case 'defaultLayout':
			if (!is_string($value)) {
				throw new \InvalidArgumentException(
					sprintf('The config parameter "%s" only accepts string.', $name));
			}
			break;
		case 'caching':
		case 'force_compile':
		case 'use_sub_dirs':
		case 'escape_html':
			if (!is_bool($value) && !is_int($value) && !ctype_digit($value)) {
				throw new \InvalidArgumentException(
					sprintf('The config parameter "%s" only accepts bool.', $name));
			}
			$value = (bool)$value;
			break;
		default:
			throw new \InvalidArgumentException(
				sprintf('The config parameter "%s" is not defined.', $name)
			);
		}
		if (isset($value) && in_array($name, static::$smarty_options)) {
			$this->smarty->{$name} = $value;
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
		if (strpos($view, '/') === 0) {
			$view = substr($view, 1);
		}
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
