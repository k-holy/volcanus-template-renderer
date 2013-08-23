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
	private $config;

	/**
	 * @var \Twig_Environment
	 */
	public $twig;

	/**
	 * コンストラクタ
	 *
	 * @param \Twig_Environment
	 * @param array 設定オプション
	 */
	public function __construct(\Twig_Environment $twig = null, array $configurations = array())
	{
		$this->initialize($twig, $configurations);
	}

	/**
	 * オブジェクトを初期化します。
	 *
	 * @param \Twig_Environment
	 * @param array 設定オプション
	 * @return self
	 */
	public function initialize($twig = null, array $configurations = array())
	{
		$this->setTwig(isset($twig) ? $twig : new \Twig_Environment());
		$this->config = array(
			'path' => null,
		);
		if (!empty($configurations)) {
			foreach ($configurations as $name => $value) {
				$this->setConfig($name, $value);
			}
		}
		return $this;
	}

	private function setTwig(\Twig_Environment $twig)
	{
		$this->twig = $twig;
	}

	/**
	 * 指定された設定値を返します。
	 *
	 * @param string 設定名
	 * @return mixed 設定値
	 */
	public function getConfig($name)
	{
		switch ($name) {
		case 'path':
			$loader = $this->twig->getLoader();
			return $loader->getPaths();
		case 'base_template_class':
			return $this->twig->getBaseTemplateClass();
		case 'charset':
			return $this->twig->getCharset();
		case 'cache':
			return $this->twig->getCache();
		case 'autoescape':
			if ($this->twig->hasExtension('escaper')) {
				$escaper = $this->twig->getExtension('escaper');
				return $escaper->getDefaultStrategy(null);
			}
			return false;
		case 'optimizations':
			return $this->config[$name];// Optimizerから値を取得する手段がないので…
		case 'debug':
			return $this->twig->isDebug();
		case 'strict_variables':
			return $this->twig->isStrictVariables();
		case 'auto_reload':
			return $this->twig->isAutoReload();
		}
		throw new \InvalidArgumentException(
			sprintf('The config parameter "%s" is not defined.', $name)
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
		case 'path':
			$this->twig->setLoader(new \Twig_Loader_Filesystem($value));
			break;
		case 'base_template_class':
			$this->twig->setBaseTemplateClass($value);
			break;
		case 'charset':
			$this->twig->setCharset($value);
			break;
		case 'cache':
			$this->twig->setCache($value);
			break;
		case 'autoescape':
			if ($this->twig->hasExtension('escaper')) {
				$escaper = $this->twig->getExtension('escaper');
				$escaper->setDefaultStrategy($value);
			} else {
				$this->twig->addExtension(new \Twig_Extension_Escaper($value));
			}
			break;
		case 'optimizations':
			$this->twig->addExtension(new \Twig_Extension_Optimizer($value));
			$this->config[$name] = $value; // Optimizerから値を取得する手段がないので…
			break;
		case 'debug':
			if ($value) {
				$this->twig->enableDebug();
				$this->twig->addExtension(new \Twig_Extension_Debug());
			} else {
				$this->twig->disableDebug();
			}
			break;
		case 'strict_variables':
			if ($value) {
				$this->twig->enableStrictVariables();
			} else {
				$this->twig->disableStrictVariables();
			}
			break;
		case 'auto_reload':
			if ($value) {
				$this->twig->enableAutoReload();
			} else {
				$this->twig->disableAutoReload();
			}
			break;
		default:
			throw new \InvalidArgumentException(
				sprintf('The config parameter "%s" is not defined.', $name)
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
		return $this->twig->render($view, $data);
	}

}
