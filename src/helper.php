<?php
/**
 * 显示模板
 */
if ( ! function_exists( 'view' ) ) {
	function view( $tpl = '', $expire = 0 ) {
		return View::make( $tpl, $expire );
	}
}
if ( ! function_exists( 'widget' ) ) {
	//解析页面组件
	function widget() {
		$vars = func_get_args();
		$info = preg_split( '@[\./]@', array_shift( $vars ) );
		//方法名
		$method = array_pop( $info );
		//类名
		$className = array_pop( $info );
		$class     = implode( '\\', $info ) . '\\' . ucfirst( $className );
		return call_user_func_array( [ new $class, $method ], $vars );
	}
}