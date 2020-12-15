<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\TemplateRenderer;

use Psr\Http\Message\ResponseInterface;
use Volcanus\TemplateRenderer\Adapter\AdapterInterface;

/**
 * テンプレートレンダラ
 *
 * @author k.holy74@gmail.com
 */
class Renderer
{

    /**
     * @var AdapterInterface アダプタ
     */
    private $adapter;

    /**
     * @var array 出力データ
     */
    private $data;

    /**
     * コンストラクタ
     *
     * @param AdapterInterface $adapter $adapter
     * @param array $configurations 設定オプション
     */
    public function __construct(AdapterInterface $adapter, array $configurations = [])
    {
        $this->initialize($adapter, $configurations);
    }

    /**
     * オブジェクトを初期化します。
     *
     * @param AdapterInterface $adapter
     * @param array $configurations 設定オプション
     * @return $this
     */
    public function initialize(AdapterInterface $adapter, array $configurations = []): Renderer
    {
        $this->data = [];
        $this->setAdapter($adapter, $configurations);
        return $this;
    }

    /**
     * アダプタをセットします。
     *
     * @param AdapterInterface $adapter
     * @param array $configurations 設定オプション
     * @return $this
     */
    public function setAdapter(AdapterInterface $adapter, array $configurations = []): Renderer
    {
        $this->adapter = $adapter;
        if (!empty($configurations)) {
            foreach ($configurations as $name => $value) {
                $this->adapter->setConfig($name, $value);
            }
        }
        return $this;
    }

    /**
     * 引数1の場合は指定された設定の値を返します。
     * 引数2の場合は指定された設置の値をセットして$thisを返します。
     *
     * @param string $name 設定名
     * @return mixed 設定値 または $this
     */
    public function config(string $name)
    {
        switch (func_num_args()) {
            case 1:
                return $this->adapter->getConfig($name);
            case 2:
                $value = func_get_arg(1);
                $this->adapter->setConfig($name, $value);
                return $this;
        }
        throw new \InvalidArgumentException('Invalid argument count.');
    }

    /**
     * 出力データに値を追加します。
     *
     * @param string $name 名前
     * @param mixed $value 値
     */
    public function assign(string $name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * 指定された名前で値がアサインされているかどうかを返します。
     *
     * @param string $name 名前
     * @return bool
     */
    public function assigned(string $name): bool
    {
        return array_key_exists($name, $this->data);
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
        return $this->adapter->fetch(
            $view,
            array_merge($this->data, $data)
        );
    }

    /**
     * テンプレート処理結果を出力します。
     *
     * @param string $view テンプレートファイルのパス
     * @param array $data テンプレート変数の配列
     */
    public function render(string $view, array $data = [])
    {
        echo $this->fetch($view, $data);
    }

    /**
     * テンプレート処理結果をレスポンスボディに書き込んで返します。
     *
     * @param ResponseInterface $response
     * @param string $view テンプレートファイルのパス
     * @param array $data テンプレート変数の配列
     * @return ResponseInterface
     */
    public function writeResponse(ResponseInterface $response, string $view, array $data = []): ResponseInterface
    {
        $output = $this->fetch($view, $data);
        $response->getBody()->write($output);
        return $response;
    }

}
