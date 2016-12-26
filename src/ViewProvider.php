<?php
/** .-------------------------------------------------------------------
 * |  Software: [HDCMS framework]
 * |      Site: www.hdcms.com
 * |-------------------------------------------------------------------
 * |    Author: 向军 <2300071698@qq.com>
 * |    WeChat: aihoudun
 * | Copyright (c) 2012-2019, www.houdunwang.com. All Rights Reserved.
 * '-------------------------------------------------------------------*/
namespace houdunwang\view;

use hdphp\kernel\ServiceProvider;

class ViewProvider extends ServiceProvider {

	//延迟加载
	public $defer = true;

	public function boot() {
		if ( defined( 'MODULE' ) ) {
			//模块视图文件夹
			$file = c( 'app.path' ) . '/' . strtolower( MODULE . '/view/' . CONTROLLER ) . '/' . ( $file ?: ACTION . c( 'view.prefix' ) );
			if ( ! is_file( $file ) ) {
				trigger_error( "模板不存在:$file", E_USER_ERROR );
			}
		} else {
			//路由访问时
			$file = Config::get( 'view.path' ) . '/' . $file;
			if ( ! is_file( $file ) ) {
				trigger_error( "模板不存在:$file", E_USER_ERROR );
			}
		}
	}

	public function register() {
		$this->app->single( 'View', function ( $app ) {
			return new View( $app );
		} );
	}
}