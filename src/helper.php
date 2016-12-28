<?php
/**
 * 显示模板
 */
if ( ! function_exists( 'view' ) ) {
	function view( $tpl = '', $expire = null ) {
		return View::make( $tpl, $expire );
	}
}