<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\TemplateRenderer\Adapter;

/**
 * Adapter for Twig
 *
 * @author k.holy74@gmail.com
 */
class TwigAdapter implements AdapterInterface
{

	/**
	 * @var array 設定値
	 */
	protected $config;

	/**
	 * @var \Twig_Environment
	 */
	public $twig;

	/**
	 * @var array Twig用オプション設定
	 */
	private static $twig_options = array(
		'debug',
		'charset',
		'base_template_class',
		'strict_variables',
		'autoescape',
		'cache',
		'auto_reload',
		'optimizations',
		'path',
	);

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
		$this->twig = new \Twig_Environment();
		$this->config = array_fill_keys(static::$twig_options, null);
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
		case 'path':
			if (!is_string($value) && !is_array($value)) {
				throw new \InvalidArgumentException(
					sprintf('The config parameter "%s" only accepts string.', $name));
			}
			break;
		case 'charset':
		case 'base_template_class':
			if (!is_string($value)) {
				throw new \InvalidArgumentException(
					sprintf('The config parameter "%s" only accepts string.', $name));
			}
			break;
		case 'cache':
			if (!is_bool($value) && !is_string($value)) {
				throw new \InvalidArgumentException(
					sprintf('The config parameter "%s" accepts bool or string.', $name));
			}
			break;
		case 'autoescape':
			if (!is_bool($value) && !is_string($value) && !is_callable($value)) {
				throw new \InvalidArgumentException(
					sprintf('The config parameter "%s" accepts bool or string or callable.', $name));
			}
			break;
		case 'optimizations':
			if (!is_int($value) && !ctype_digit($value)) {
				throw new \InvalidArgumentException(
					sprintf('The config parameter "%s" only accepts bool.', $name));
			}
			$value = (int)$value;
			break;
		case 'debug':
		case 'strict_variables':
		case 'auto_reload':
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
		foreach ($this->config as $name => $value) {
			if (isset($value) && in_array($name, static::$twig_options)) {
				switch ($name) {
				case 'debug':
					if ($value) {
						$this->twig->enableDebug();
						$this->twig->addExtension(new \Twig_Extension_Debug());
					} else {
						$this->twig->disableDebug();
					}
					break;
				case 'auto_reload':
					if ($value) {
						$this->twig->enableAutoReload();
					} else {
						$this->twig->disableAutoReload();
					}
					break;
				case 'strict_variables':
					if ($value) {
						$this->twig->enableStrictVariables();
					} else {
						$this->twig->disableStrictVariables();
					}
					break;
				case 'path':
					if ('\\' === DIRECTORY_SEPARATOR) {
						$value = (is_array($value))
							? array_map(function($val) {
								return str_replace('\\', '/', $val);
							}, $value)
							: str_replace('\\', '/', $value);
					}
					$this->twig->setLoader(new \Twig_Loader_Filesystem($value));
					break;
				case 'charset':
					$this->twig->setCharset($value);
					break;
				case 'base_template_class':
					$this->twig->setBaseTemplateClass($value);
					break;
				case 'cache':
					$this->twig->setCache($value);
					break;
				case 'autoescape':
					$this->twig->addExtension(new \Twig_Extension_Escaper($value));
					break;
				case 'optimizations':
					$this->twig->addExtension(new \Twig_Extension_Optimizer($value));
					break;
				}
			}
		}
		if (strpos($view, '/') === 0) {
			$view = substr($view, 1);
		}
		return $this->twig->render($view, $data);
	}

}
