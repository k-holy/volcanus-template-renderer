<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\TemplateRenderer\Adapter;

/**
 * Adapter for PHPTAL
 *
 * @author k.holy74@gmail.com
 */
class PhpTalAdapter implements AdapterInterface
{

	/**
	 * @var array 設定値
	 */
	protected $config;

	/**
	 * @var \PHPTAL
	 */
	public $phptal;

	/**
	 * @var array PHPTAL用オプション設定
	 */
	private static $phptal_options = array(
		'outputMode',
		'encoding',
		'templateRepository',
		'phpCodeDestination',
		'phpCodeExtension',
		'cacheLifetime',
		'forceReparse',
	);

	/**
	 * コンストラクタ
	 *
	 * @param \PHPTAL
	 * @param array 設定オプション
	 */
	public function __construct(\PHPTAL $phptal = null, array $configurations = array())
	{
		$this->initialize($phptal, $configurations);
	}

	/**
	 * オブジェクトを初期化します。
	 *
	 * @param \PHPTAL
	 * @param array 設定オプション
	 * @return self
	 */
	public function initialize($phptal = null, array $configurations = array())
	{
		$this->setPhpTal(isset($phptal) ? $phptal : new \PHPTAL());
		$this->config = array_fill_keys(static::$phptal_options, null);
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
		case 'templateRepository':
		case 'phpCodeDestination':
			if (!is_string($value) && !is_array($value)) {
				throw new \InvalidArgumentException(
					sprintf('The config parameter "%s" only accepts string.', $name));
			}
			break;
		case 'encoding':
		case 'phpCodeExtension':
			if (!is_string($value)) {
				throw new \InvalidArgumentException(
					sprintf('The config parameter "%s" only accepts string.', $name));
			}
			break;
		case 'outputMode':
		case 'cacheLifetime':
			if (!is_int($value) && !ctype_digit($value)) {
				throw new \InvalidArgumentException(
					sprintf('The config parameter "%s" only accepts bool.', $name));
			}
			$value = (int)$value;
			break;
		case 'forceReparse':
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
		if (isset($value) && in_array($name, static::$phptal_options)) {
			$method = 'set' . ucfirst($name);
			if (method_exists($this->phptal, $method)) {
				$this->phptal->{$method}($value);
			}
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
		foreach ($data as $name => $value) {
			$this->phptal->set($name, $value);
		}
		return $this->phptal->setTemplate($view)->execute();
	}

}
