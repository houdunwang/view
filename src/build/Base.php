<?php namespace houdunwang\view\build;
/** .-------------------------------------------------------------------
 * |  Software: [HDCMS framework]
 * |      Site: www.hdcms.com
 * |-------------------------------------------------------------------
 * |    Author: 向军 <2300071698@qq.com>
 * |    WeChat: aihoudun
 * | Copyright (c) 2012-2019, www.houdunwang.com. All Rights Reserved.
 * '-------------------------------------------------------------------*/

use houdunwang\cache\Cache;
use houdunwang\config\Config;

class Base {
	use Compile;
	//模板变量集合
	protected $vars = [ ];
	//模版文件
	protected $file;
	//缓存目录
	protected $cacheDir;
	//缓存时间
	protected $expire;
	//配置项
	protected $config;

	//设置配置项
	public function config( $config, $value = null ) {
		if ( is_array( $config ) ) {
			$this->config = $config;

			return $this;
		} else if ( is_null( $value ) ) {
			return Arr::get( $this->config, $config );
		} else {
			$this->config = Arr::set( $this->config, $config, $value );

			return $this;
		}
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
		$this->file   = $this->template( $file );
		$this->expire = intval( $expire );

		return $this;
	}

	//解析编译文件,返回模板解析后的字符
	public function fetch( $file ) {
		$this->file  = $this->template( $file );
		$compileFile = $this->compile();
		ob_start();
		extract( $this->vars );
		include $compileFile;

		return ob_get_clean();
	}

	//显示模板
	public function __toString() {
		if ( $this->expire > 0 && $this->isCache( $this->file ) ) {
			//缓存有效时返回缓存数据
			return Cache::driver( 'file' )->dir( $this->config( 'cache_dir' ) )->get( $this->cacheName( $this->file ) );
		}
		$content = $this->fetch( $this->file );
		//创建缓存文件
		if ( $this->expire > 0 ) {
			Cache::driver( 'file' )->dir( $this->config( 'cache_dir' ) )->set( $this->cacheName( $this->file ), $content, $this->expire );
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

	/**
	 * 获取所有分配变量
	 * @return array
	 */
	public function vars() {
		return $this->vars;
	}

	//获取模板文件
	public function getTpl() {
		return $this->file;
	}

	//根据文件名获取模板文件
	protected function template( $file ) {
		//没有扩展名时添加上
		if ( $file && ! preg_match( '/\.[a-z]+$/i', $file ) ) {
			$file .= $this->config( 'prefix' );
		}
		if ( ! is_file( $file ) ) {
			if ( defined( 'MODULE' ) ) {
				//模块视图文件夹
				$file = Config::get( 'controller.app' ) . '/' . strtolower( MODULE . '/view/' . CONTROLLER ) . '/' . ( $file ?: ACTION . $this->config( 'prefix' ) );
				if ( ! is_file( $file ) ) {
					trigger_error( "模板不存在:$file", E_USER_ERROR );
				}
			} else {
				//路由访问时
				$file = $this->config( 'path' ) . '/' . $file;
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
		return Cache::driver( 'file' )->dir( $this->config( 'cache_dir' ) )->get( $this->cacheName( $file ) );
	}

	//删除模板缓存
	public function delCache( $file = '' ) {
		return Cache::driver( 'file' )->dir( $this->config( 'cache_dir' ) )->del( $this->cacheName( $file ) );
	}
}