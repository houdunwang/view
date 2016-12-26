<?php
/** .-------------------------------------------------------------------
 * |  Software: [HDCMS framework]
 * |      Site: www.hdcms.com
 * |-------------------------------------------------------------------
 * |    Author: 向军 <2300071698@qq.com>
 * |    WeChat: aihoudun
 * | Copyright (c) 2012-2019, www.houdunwang.com. All Rights Reserved.
 * '-------------------------------------------------------------------*/
namespace houdunwang\view\build;
use houdunwang\config\Config;

/**
 * 模板编译
 * Class Compile
 * @package hdphp\view
 * @author 向军
 */
class Compile {
	//视图对象
	protected $view;
	//模板编译内容
	protected $content;
	//外观类
	protected $facade;

	//构造函数
	function __construct( $view, $facade ) {
		$this->view   = $view;
		$this->facade = $facade;
	}

	/**
	 * 运行编译
	 *
	 * @param $tpl
	 *
	 * @return string
	 */
	public function run( $tpl ) {
		//模板内容
		$this->content = file_get_contents( $tpl );
		//解析标签
		$this->tags();
		//解析全局变量与常量
		$this->globalParse();

		//保存编译文件
		return $this->content;
	}

	/**
	 * 解析全局变量与常量
	 */
	private function globalParse() {
		//处理{{}}
		$this->content = preg_replace( '/(?<!@)\{\{(.*?)\}\}/i', '<?php echo \1?>', $this->content );
		//处理@{{}}
		$this->content = preg_replace( '/@(\{\{.*?\}\})/i', '\1', $this->content );
	}

	/**
	 * 解析标签
	 */
	private function tags() {
		//标签库
		$tags   = $this->facade->config( 'tags' );
		$tags[] = 'houdunwang\view\build\Tag';
		//解析标签
		foreach ( $tags as $class ) {
			$obj           = new $class( $this->content, $this->view, $this->facade );
			$this->content = $obj->parse();
		}
	}
}