<?php
/**
 * 显示模板
 */
if ( ! function_exists( 'view' ) ) {
	function view( $tpl = '', $expire = 0 ) {
		echo  View::make( $tpl, $expire );
	}
}