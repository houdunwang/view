<?php namespace houdunwang\view\build;
/**
 * 模板编译
 * Class Compile
 * @package houdunwang\view\build
 */
trait Compile {
	//模板编译内容
	protected $content;

	/**
	 * 根据模板文件生成编译文件
	 *
	 * @return string
	 */
	final protected function compile() {
		$compileFile = $this->config( 'compile_dir' ) . '/' . preg_replace( '/[^\w]/', '_', $this->file ) . '_' . substr( md5( $this->file ), 0, 5 ) . '.php';
		$status      = $this->config( 'compile_open' )
		               || ! is_file( $compileFile )
		               || ( filemtime( $this->file ) > filemtime( $compileFile ) );
		if ( $status ) {
			is_dir( dirname( $compileFile ) ) or mkdir( dirname( $compileFile ), 0755, true );
			//模板内容
			$this->content = file_get_contents( $this->file );
			//解析标签
			$this->tags();
			//解析全局变量与常量
			$this->globalParse();
			file_put_contents( $compileFile, $this->content );
		}

		return $compileFile;
	}

	/**
	 * 解析全局变量与常量
	 */
	final protected function globalParse() {
		//处理{{}}
		$this->content = preg_replace( '/(?<!@)\{\{(.*?)\}\}/i', '<?php echo \1?>', $this->content );
		//处理@{{}}
		$this->content = preg_replace( '/@(\{\{.*?\}\})/i', '\1', $this->content );
	}

	/**
	 * 解析标签
	 */
	final protected function tags() {
		//标签库
		$tags   = $this->config( 'tags' );
		$tags[] = 'houdunwang\view\build\Tag';
		//解析标签
		foreach ( $tags as $class ) {
			$obj           = new $class( $this->content, $this );
			$this->content = $obj->parse();
		}
	}
}