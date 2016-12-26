<?php
require 'vendor/autoload.php';
$config = [
	/**
	 * 文件缓存
	 */
	'file'     => [
		'dir' => 'storage/cache'
	]
];
\houdunwang\config\Config::set( 'cache', $config );
$config = [
	//模板目录（只对路由调用有效）
	'path'         => 'view',
	//模板后缀
	'prefix'       => '.php',
	//标签
	'tags'         => [ ],
	//左标签
	'tag_left'     => '<',
	//右标签
	'tag_right'    => '>',
	//blade 模板功能开关
	'blade'        => true,
	//缓存目录
	'cache_dir'    => 'storage/view/cache',
	//编译目录
	'compile_dir'  => 'storage/view/compile',
	//开启编译
	'compile_open' => false,
];
\houdunwang\config\Config::set( 'view', $config );
\houdunwang\view\View::with( 'name', '后盾人' );
echo \houdunwang\view\View::make( 'a' );