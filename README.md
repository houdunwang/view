#视图模板组件

##介绍
视图组件分开了逻辑程序和外在的内容 , 提供了一种易于管理的方法。可以描述为应用程序员和美工扮演了不同的角色 , 因为在大多数情况下 , 他们不可能是同一个人。例如 , 你正在创建一个用于浏览新闻的网页 , 新闻标题 , 标签栏 , 作者和内容等都是内容要素 , 他们并不包含应该怎样去呈现。模板设计者们编辑模板 , 组合使用 html 标签和模板标签去格式化这些要素的输出 (html 表格 , 背景色 , 字体大小 , 样式表 , 等等 )。
有一天程序员想要改变文章检索的方式 ( 也就是程序逻辑的改变 )。这个改变不影响模板设计者 , 内容仍将准确的输出到模板。同样的 , 哪天美工想要完全重做界面也不会影响到程序逻辑。因此 , 程序员可以改变逻辑而不需要重新构建模板 , 模板设计者可以改变模板而不影响到逻辑。 
模版组件引擎是编译型模版引擎，模版文件只编译一次，以后程序会直接采用编译文件，效率非常高。 

[TOC]
#开始使用

####安装组件
使用 composer 命令进行安装或下载源代码使用。
```
composer require houdunwang/view
```
> HDPHP 框架已经内置此组件，无需要安装

####配置缓存
组件使用了 [Cache组件](https://github.com/houdunwang/cache) 需要先行进行配置。

```
$config = [
	'file'     => [
	    //缓存目录
		'dir' => 'storage/cache'
	]
];
\houdunwang\config\Config::set( 'cache', $config );
```

####配置视图组件
```
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
```

##解析模板
模板就是视图界面,模板文件没有设置扩展名时将使用配置项 prefix 的值。

不添加后缀时使用配置项 prefix 设置的后缀。
```
View::make('add');
```

##分配数据
####以数组形式分配
```
View::with(['name'=>'后盾网','uri'=>'houdunwang.com']);
//模板中读取方式：{{$name}}
```

####分配变量并显示模板
```
View::with(['name'=>'后盾网','uri'=>'houdunwang.com'])->make();
```

##读取变量
通过View::with分配的变量在模板中使用{{变量名}}形式读取

```
{{$_GET['cid']}}          					读取 $_GET 中的值  
{{$_POST['cid']}}                 		读取 $_POST 中的值  
{{$_REQUEST['cid']}}               		读取 $_REQUEST 中的值
{{$_SESSION['cid']}}              		读取 $_SESSION 中的值  
{{$_COOKIE['cid']}}               		读取 $_COOKIE 中的值
{{$_SERVER['HTTP_HOST']}}         		读取 $_SERVER 中的值 
{{Config::get('database.user')}} 			读取配置项值  
```
> 提示：在{{}}中可以使用任意php函数

####忽略解析
```
@{{$name}}
```