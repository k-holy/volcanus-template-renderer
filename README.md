# Volcanus_TemplateRenderer

[![Latest Stable Version](https://poser.pugx.org/volcanus/template-renderer/v/stable.png)](https://packagist.org/packages/volcanus/template-renderer)
[![Build Status](https://travis-ci.org/k-holy/volcanus-template-renderer.png?branch=master)](https://travis-ci.org/k-holy/volcanus-template-renderer)
[![Coverage Status](https://coveralls.io/repos/k-holy/volcanus-template-renderer/badge.png?branch=master)](https://coveralls.io/r/k-holy/volcanus-template-renderer?branch=master)

各種テンプレートエンジンを共通のインタフェースで利用するためのPHPクラスライブラリです。

## 対応環境

* PHP 7.3以降（必須）
* Smarty（オプション）
* Latte (オプション)
* PHPTAL（オプション）

バージョンの下限は明確ではありません。ごめんなさい。

## 使い方

### Smarty

```php
<?php
include './vendor/autoload.php';

use Volcanus\TemplateRenderer\Renderer;
use Volcanus\TemplateRenderer\Adapter\SmartyAdapter;

// Smarty
$renderer = new Renderer(new SmartyAdapter(new \Smarty(), [
    'template_dir' => __DIR__,
    'compile_dir'  => sys_get_temp_dir(),
]));

$renderer->assign('suffix', '様');

file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'hello.tpl', 'Hello {$name}{$suffix} !!');

$renderer->render('hello.tpl', ['name' => 'Smarty']); // Hello Smarty様 !!

unlink(__DIR__ . DIRECTORY_SEPARATOR . 'hello.tpl');
```

### Latte

```php
<?php
include './vendor/autoload.php';

use Volcanus\TemplateRenderer\Renderer;
use Volcanus\TemplateRenderer\Adapter\LatteAdapter;

// Latte
$renderer = new Renderer(new LatteAdapter(new \Latte\Engine(), [
    'baseDir' => __DIR__,
    'tempDirectory' => sys_get_temp_dir(),
]));

$renderer->assign('suffix', '様');

file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'hello.latte', 'Hello {$name}{$suffix} !!');

$renderer->render('hello.latte',['name' => 'Latte']); // Hello Latte様 !!

unlink(__DIR__ . DIRECTORY_SEPARATOR . 'hello.latte');
```

### PHPTAL

```php
<?php
include './vendor/autoload.php';

use Volcanus\TemplateRenderer\Renderer;
use Volcanus\TemplateRenderer\Adapter\PhpTalAdapter;

// PHPTAL
$renderer = new Renderer(new PhpTalAdapter(new \PHPTAL(), [
    'templateRepository' => __DIR__,
    'phpCodeDestination' => sys_get_temp_dir(),
]));

$renderer->assign('suffix', '様');

file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'hello.tal', '<tal:block>Hello <span tal:replace="name">Anonymous</span><span tal:replace="suffix">氏</span> !!</tal:block>');

$renderer->render('hello.tal', ['name' => 'PHPTAL']); // Hello PHPTAL様 !!

unlink(__DIR__ . DIRECTORY_SEPARATOR . 'hello.tal');
```

## アダプタ別の注意点

基本的に、複雑な設定やプラグインのような独自機構へのアクセスは提供していません。

### Smarty

以下の設定値に対応しています。

* template_dir, config_dir, plugins_dir, compile_dir, cache_dir, left_delimiter, right_delimiter, default_modifiers,
  caching, force_compile, use_sub_dirs, escape_html

独自の設定値 charset により、テンプレートファイルのエンコーディングを指定できます。 （静的プロパティ Smarty::$_CHARSET を書き換えます）

独自の設定値 defaultLayout により、デフォルトの継承元テンプレートファイルを指定できます。

この設定は、出力メソッドで "extends:" や "string:" といったテンプレートリソースが指定されていない場合のみ動作します。

### Latte

以下の設定値に対応しています。

* tempDirectory, autoRefresh, strictTypes

設定値 baseDir は、 \Volcanus\TemplateRenderer\Renderer::render() および \Volcanus\TemplateRenderer\Renderer::fetch() 実行時に生成される \Latte\Loaders\FileLoader のコンストラクタ引数 $baseDir に利用されます。

### PHPTAL

以下の設定値に対応しています。

* templateRepository, phpCodeDestination, encoding, phpCodeExtension, outputMode, cacheLifetime, forceReparse

## 応用編

こんな感じでテンプレートエンジンを切り替えながら Renderer::assign() で出力結果を溜めつつ、最後に結合して出力することも可能です。

```php
<?php
include './vendor/autoload.php';

use Volcanus\TemplateRenderer\Renderer;
use Volcanus\TemplateRenderer\Adapter\SmartyAdapter;
use Volcanus\TemplateRenderer\Adapter\LatteAdapter;
use Volcanus\TemplateRenderer\Adapter\PhpTalAdapter;

// Smarty
$renderer = new Renderer(new SmartyAdapter(new \Smarty(), [
    'template_dir' => __DIR__,
    'compile_dir'  => sys_get_temp_dir(),
]));

$renderer->assign('suffix', '様');

file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'hello.tpl', 'Hello {$name}{$suffix} !!');

$renderer->assign('smarty', $renderer->fetch('hello.tpl', ['name' => 'Smarty'])); // Hello Smarty様 !!

unlink(__DIR__ . DIRECTORY_SEPARATOR . 'hello.tpl');

// Latte
$renderer->setAdapter(new LatteAdapter(new \Latte\Engine(), [
    'baseDir' => __DIR__,
    'tempDirectory' => sys_get_temp_dir(),
]));

file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'hello.latte', 'Hello {$name}{$suffix} !!');

$renderer->assign('latte', $renderer->fetch('hello.latte', ['name' => 'Latte'])); // Hello Latte様 !!

unlink(__DIR__ . DIRECTORY_SEPARATOR . 'hello.latte');

// PHPTAL
$renderer->setAdapter(new PhpTalAdapter(new \PHPTAL(),[
    'templateRepository' => __DIR__,
    'phpCodeDestination' => sys_get_temp_dir(),
]));

file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'outline.tal', <<<'TEMPLATE'
<html>
<head>
<title tal:content="title">タイトル</title>
</head>
<body>
<div tal:content="smarty">Smartyコンテンツ</div>
<div tal:content="latte">Latteコンテンツ</div>
<div>Hello <span tal:replace="name">Anonymous</span><span tal:replace="suffix">氏</span> !!</div>
</body>
</html>
TEMPLATE
);

$renderer->render('outline.tal', [
    'title' => 'PHPTAL + Smarty + Latte',
    'name'  => 'PHPTAL',
]);

unlink(__DIR__ . DIRECTORY_SEPARATOR . 'outline.tal');
```

