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
	private $config;

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
		$this->config = array();
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
	 * @param string 設定名
	 * @param mixed 設定値
	 * @return self
	 */
	public function setConfig($name, $value)
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
	 * @param string テンプレートファイルのパス
	 * @param array テンプレート変数の配列
	 * @return string
	 */
	public function fetch($view, array $data = array())
	{
		foreach ($data as $name => $value) {
			$this->phptal->set($name, $value);
		}
		return $this->phptal->setTemplate($view)->execute();
	}

}
