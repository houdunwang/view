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

class Tag extends TagBase {

	//blockshow模板(父级)
	protected $widget = [ ];
	/**
	 * block 块标签
	 * level 嵌套层次
	 */
	public $tags
		= [
			'foreach' => [ 'block' => true, 'level' => 5 ],
			'list'    => [ 'block' => true, 'level' => 5 ],
			'if'      => [ 'block' => true, 'level' => 5 ],
			'elseif'  => [ 'block' => false ],
			'else'    => [ 'block' => false ],
			'js'      => [ 'block' => false ],
			'css'     => [ 'block' => false ],
			'include' => [ 'block' => false ],
			'extend'  => [ 'block' => false ],
			'blade'   => [ 'block' => false ],
			'parent'  => [ 'block' => false ],
			'block'   => [ 'block' => true, 'level' => 5 ],
			'widget'  => [ 'block' => true, 'level' => 5 ],
			'php'     => [ 'block' => true, 'level' => 5 ],
		];

	//加载模板文件
	public function _include( $attr, $content, &$view ) {
		return file_get_contents( $view->compile( $attr['file'] ) );
	}

	//引入CSS文件
	public function _css( $attr, $content, &$view ) {
		$attr['file'] = $this->replaceConst( $attr['file'] );

		return "<link type=\"text/css\" rel=\"stylesheet\" href=\"{$attr['file']}\"/>";
	}

	//引入JavaScript文件
	public function _js( $attr, $content, &$hd ) {
		$attr['file'] = $this->replaceConst( $attr['file'] );

		return "<script type=\"text/javascript\" src=\"{$attr['file']}\"></script>";
	}

	//list标签
	public function _list( $attr, $content, &$view ) {
		$from  = $attr['from']; //变量
		$name  = $attr['name'];//name名去除$
		$empty = isset( $attr['empty'] ) ? $attr['empty'] : '';//默认值
		$row   = isset( $attr['row'] ) ? $attr['row'] : 100;//显示条数
		$step  = isset( $attr['step'] ) && $attr['step'] > 0 ? $attr['step'] : 1;//间隔
		$start = isset( $attr['start'] ) ? max( 0, $attr['start'] - 1 ) : 0;//开始数
		$php
		       = <<<php
        <?php
        if (empty($from)) {
            echo '$empty';
        }else{
            //初始化
            \$_name= substr('$name',1);
            \$hd['list'][\$_name]['first']=false;
            \$hd['list'][\$_name]['last'] =false;
            \$hd['list'][\$_name]['index']=0;
            \$hd['list'][\$_name]['total']=0;
            \$id=0;\$key=$start;\$_tmp=$from;
            for(\$index=$start;\$index<count($from);\$index++){
                $name=\$_tmp[\$key];\$key +=$step; 
                \$hd['list'][\$_name]['first'] = \$index==$start;
                \$hd['list'][\$_name]['index'] = ++\$id;
				\$hd['list'][\$_name]['last']  = \$id>=$row || !isset(\$_tmp[\$key]);
            ?>
php;
		$php .= $content;
		$php
			.= "<?php 
					if(\$hd['list'][\$_name]['last']){break;}
				}}?>";

		return $php;
	}

	//标签处理
	public function _foreach( $attr, $content ) {
		if ( isset( $attr['key'] ) ) {
			$php = "<?php if(!empty({$attr['from']})){ foreach ({$attr['from']} as {$attr['key']}=>{$attr['value']}){?>";
		} else {
			$php = "<?php if(!empty({$attr['from']})){foreach ({$attr['from']} as {$attr['value']}){?>";
		}
		$php .= $content;
		$php .= '<?php }}?>';

		return $php;
	}

	//if标签
	public function _if( $attr, $content, &$hd ) {
		$php
			= "<?php if({$attr['value']}){?>
                $content
               <?php }?>";

		return $php;
	}

	//elseif标签
	public function _elseif( $attr, $content, &$view ) {
		return "<?php }else if({$attr['value']}){?>";
	}

	//else标签
	public function _else( $attr, $content, &$view ) {
		return "<?php }else{?>";
	}

	//php标签
	public function _php( $attr, $content, &$view ) {
		return "<?php $content;?>";
	}

	//块布局时引入布局页的bladeshow块
	public function _extend( $attr, $content, &$view ) {
		//开启blade模板功能
		if ( $this->facade->config( 'blade' ) ) {
			return $view->make( $this->replaceConst( $attr['file'] ) );
		}
	}

	//布局模板定义的块(父级)
	public function _blade( $attr, $content, &$view ) {
		return "<!--blade_{$attr['name']}-->";
	}

	//视图模板定义的内容(子级)
	public function _block( $attr, $content, &$view ) {
		if ( $this->facade->config( 'blade' ) ) {
			$this->content = str_replace( "<!--blade_{$attr['name']}-->", $content, $this->content );
		} else {
			return $content;
		}
	}

	//布局模板定义用于显示在视图模板的内容(父级)
	public function _widget( $attr, $content, &$view ) {
		if ( $this->facade->config( 'blade' ) ) {
			$this->widget[ $attr['name'] ] = $content;
		}
	}

	//视图模板引用布局模板(子级)
	public function _parent( $attr, $content, &$view ) {
		if ( $this->facade->config( 'blade' ) ) {
			$content = $this->widget[ $attr['name'] ];
			foreach ( $attr as $k => $v ) {
				$content = str_replace( '{{' . $k . '}}', $v, $content );
			}

			return $content;
		}
	}
}