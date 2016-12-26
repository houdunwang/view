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


#系统标签
模板标签是使用预先定义好的tag快速读取数据。开发者也可以根据项目需要扩展标签库。

[TOC]

##运算符
可以在属性中使用以下运算符：
```
eq		==
neq		!=
lt		<
gt		>
lte		<=
gte		>=
```

####使用
```
<if value="$a gt 2">
</if>
```

##foreach 标签 
foreach标签与 PHP 中的 foreach 使用方法一致
```
语法
<foreach from='变量' key='键名' value='键值'>
	内容 
</foreach>
```

####基本使用
```
<foreach from='$user' key='$key' value='$value'>
 {{strtoupper($value)}} 
</foreach>
```

####多重嵌套
```
<foreach from='$user' key='$key' value='$value'>
	<foreach from='$value' key='$n'value='$m'>
		{{$m}}
	</foreach>
</foreach>
```

##list 标签
####语法
```
<list from='变量' name='值' row='显示行数' empty='为空时显示内容'>
	内容 
</list>
```

####基本使用
```
<list from='$data' name='$d' row='10' start='0' empty='没有数据'>
	{{$d['cname']}}
</list>
```

####间隔读取
表示每次间隔 2 条数据输出
```
<list from='$row' name='$n' step='2'>
	{{$n['title']}}
</list>
```

####起始记录
从第 2 条数据开始显示
```
<list from='$row' name='$n' start='2'>
	{{$n.title}}
</list>
```

####高级使用
```
<list from='$data' name='$n'>
    <if value="$hd['list']['n']['first']">
        {{$hd['list']['n']['index']}}: 这是第一条数据<br/>
    <elseif value="$hd['list']['n']['last']"/>
        {{$hd['list']['n']['index']}}: 最后一条记录<br/>
    <else/>
        {{$hd['list']['n']['index']}}:{{$n['title']}}<br/>
	</if>
</list>
{{$hd['list']['n']['total']}} 	部记录数 
{{$hd['list']['n']['first']}} 	是否为第 1 条记录 
{{$hd['list']['n']['last']}} 		是否为最后一条记录 
{{$hd['list']['n']['total']}} 	总记录数 
{{$hd['list']['n']['index']}} 	当前循环是第几条 
```

##if 标签 
```
<if value="$webname eq 'houdunwang'">
	后盾网 
</if>
```

##else 标签
```
<if value='$webname == "houdunwang"'>
    后盾网 
<elseif value='$webname == "baidu"'/>
    百度 
<else/>
	其他网站 
</if>
```

##include导入模板
```
<include file="header"/>
```

可以在include标签中使用任意的路径常量
```
<include file="header"/>
```

导入指定的具体文件
```
<include file="template/index.html"/>
```

##php标签
用于生成php代码
```
<php>if(true){</php>
后盾网
<php>}</php>
```

##引入CSS文件
可以在标签中使用系统提供的url常量
```
<css file="css/common.css"/>
```

##引入JavaScript文件
可以在标签中使用系统提供的url常量
```
<js file="view/css/common.js/>
```


#扩展标签

框架提供了方便快速的标签定义，大大减少代码量，实现快速网站开发。 设置自定义标签简单、快速，下面我们来学习掌握框架自定义标签的使用方法。 

[TOC]

##文件
####设置配置
在配置项 tags 添加标签类即可。
```
'tags'=> ['system\tag\Common']
```

##创建
标签代码可以放在任何目录中，只需要配置项中正确指定类即可。
####代码
```
<?php namespace system/tag;
use hdphp\view\TagBase;
class Common extends TagBase{
    //标签声明
    public $tags = [
    	//block说明 1：块标签  0：行标签
        'test' => ['block' => 1, 'level' => 4]
    ];
    /**
     * 测试标签
     * @param $attr 标签属性集合
     * @param $content 标签嵌套内容，块标签才有值
     * @param $view 视图服务对象
     */
    public function _test($attr, $content, &$view){
        return '33';
    }
}
```

####说明
1. 块标签设置level 用于定义系统解析标签嵌套层数
2. 行标签不需要设置level

#缓存模板
缓存可以增加网站加载速度，减少数据库服务器的压力，结合路由操作可以实例与全站静态化相同的效果，并且操作更加便捷。

[TOC]
##创建
生成缓存文件，第二个参数为缓存时间，0(默认)为不缓存
```
View::make('article',100);
//将article缓存100秒
```

##验证
验证当缓存是否有效
```
View::isCache('article');
```

##删除
删除缓存必须在 make 与 isCache 等方法前执行
```
View::delCache('article');
```

#模板继承
##介绍
模板继承类似于PHP中的类继承，有两个角色一个是“布局模板”用于定义相应的blade（区块)，然后是继承“布局模板”的“视图模板”，视图模板 定义块内容替换 布局模板 中相应的blade区域。

[TOC]
####特点
* 布局模板用于定义区块
* 视图模板用于定义替换布局模板的内容
* 布局模板可以被多个 视图模板 继承

####开关
修改配置 blade 可关闭模板继承功能，即所有模板标签全部失效

##使用
####布局模板(父模板)
模板文件master.php
```
<html>
<head>
    <title>Blade 页面布局</title>
</head>
<body>
<blade name="content"/>
<widget name="header">
头部内容(这是要被子页面调用的) {{title}}
</widget>
<widget name="header">
	底部内容
</widget>
</body>
</html>
```

####视图模板(子模板)
```
<extend file='master'/>
<block name="content">
	<parent name="header" title="这是标题">
  这是主体内容  
	<parent name="footer">
</block>
```
####说明
* extend用于继承 布局模板（父级)，必须放在 parent/block 等标签前面调用
* 使用block标签定义视图内容，block替换“父级模板"中相同name属性的blade标签
* parent标签用于将父级模板 widget标签内容显示到此处
* parent标签支持向父级传递内容如上例中的title，父级中使用{{title}}方式调用