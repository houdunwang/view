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

use houdunwang\cache\Cache;

class Base {
	//模板变量集合
	protected $vars = [ ];
	//模版文件
	protected $file;
	//编译目录
	protected $compileDir;
	//缓存目录
	protected $cacheDir;
	//缓存时间
	protected $expire;
	//外观类
	protected $facade;


	public function __construct( $facade ) {
		$this->facade     = $facade;
		$this->cacheDir   = $this->facade->config( 'cache_dir' );
		$this->compileDir = $this->facade->config( 'compile_dir' );
	}

	/**
	 * 解析模板
	 *
	 * @param string $file 模板文件
	 * @param int $expire 缓存时间
	 *
	 * @return $this
	 */
	public function make( $file = '', $expire = 0 ) {
		$this->file   = $file;
		$this->expire = intval( $expire );

		return $this;
	}

	/**
	 * 根据模板文件生成编译文件
	 *
	 * @param $file
	 *
	 * @return string
	 */
	public function compile( $file ) {
		$file        = $this->template( $file );
		$compileFile = $this->compileDir . '/' . preg_replace( '/[^\w]/', '_', $file ) . '_' . substr( md5( $file ), 0, 5 ) . '.php';
		$status      = $this->facade->config( 'compile_open' )
		               || ! is_file( $compileFile )
		               || ( filemtime( $file ) > filemtime( $compileFile ) );
		if ( $status ) {
			is_dir( dirname( $compileFile ) ) or mkdir( dirname( $compileFile ), 0755, true );
			//执行文件编译
			$compile = new Compile( $this, $this->facade );
			$content = $compile->run( $file );
			file_put_contents( $compileFile, $content );
		}

		return $compileFile;
	}

	//解析编译文件,返回模板解析后的字符
	public function fetch( $file ) {
		$compileFile = $this->compile( $file );
		ob_start();
		extract( $this->vars );
		include $compileFile;

		return ob_get_clean();
	}

	//显示模板
	public function __toString() {
		if ( ! $this->expire && $this->isCache( $this->file ) ) {
			//缓存有效时返回缓存数据
			return Cache::driver( 'file' )->dir( $this->cacheDir )->get( $this->cacheName( $this->file ) );
		}
		$content = $this->fetch( $this->file );
		//创建缓存文件
		if ( $this->expire ) {
			Cache::driver( 'file' )->dir( $this->cacheDir )->set( $this->cacheName( $this->file ), $content, $this->expire );
		}

		return $content;
	}

	/**
	 * 分配变量
	 *
	 * @param mixed $name 变量名
	 * @param string $value 值
	 *
	 * @return $this
	 */
	public function with( $name, $value = '' ) {
		if ( is_array( $name ) ) {
			foreach ( $name as $k => $v ) {
				$this->vars[ $k ] = $v;
			}
		} else {
			$this->vars[ $name ] = $value;
		}

		return $this;
	}

	//获取模板文件
	public function getTpl() {
		return $this->template( $this->file );
	}

	//根据文件名获取模板文件
	public function template( $file ) {
		//没有扩展名时添加上
		if ( $file && ! preg_match( '/\.[a-z]+$/i', $file ) ) {
			$file .= $this->facade->config( 'prefix' );
		}
		if ( ! is_file( $file ) ) {
			if ( defined( 'MODULE' ) ) {
				//模块视图文件夹
				$file = $this->facade->config( 'path' ) . '/' . strtolower( MODULE . '/view/' . CONTROLLER ) . '/' . ( $file ?: ACTION . $this->facade->config( 'prefix' ) );
				if ( ! is_file( $file ) ) {
					trigger_error( "模板不存在:$file", E_USER_ERROR );
				}
			} else {
				//路由访问时
				$file = $this->facade->config( 'path' ) . '/' . $file;
				if ( ! is_file( $file ) ) {
					trigger_error( "模板不存在:$file", E_USER_ERROR );
				}
			}
		}

		return $file;
	}

	//缓存标识
	protected function cacheName( $file ) {
		return md5( $_SERVER['REQUEST_URI'] . $this->template( $file ) );
	}

	//验证缓存文件
	public function isCache( $file = '' ) {
		return Cache::driver( 'file' )->dir( $this->cacheDir )->get( $this->cacheName( $file ) );
	}

	//删除模板缓存
	public function delCache( $file = '' ) {
		return Cache::driver( 'file' )->dir( $this->cacheDir )->del( $this->cacheName( $file ) );
	}
}