<?php
/** .-------------------------------------------------------------------
 * |  Software: [HDPHP framework]
 * |      Site: www.hdphp.com  www.hdcms.com
 * |-------------------------------------------------------------------
 * |    Author: 向军 <2300071698@qq.com>
 * |    WeChat: aihoudun
 * | Copyright (c) 2012-2019, www.houdunwang.com. All Rights Reserved.
 * '-------------------------------------------------------------------*/

namespace houdunwang\view\build;

/**
 * Trait Cache
 *
 * @package houdunwang\view\build
 */
trait Cache
{
    /**
     * 设置缓存时间
     *
     * @param int $expire 缓存时间
     *
     * @return $this
     */
    public function cache($expire)
    {
        $this->expire = $expire;

        return $this;
    }

    /**
     * 缓存标识
     *
     * @param $file
     *
     * @return string
     */
    protected function cacheName($file)
    {
        return md5($_SERVER['REQUEST_URI'].$this->template($file));
    }

    /**
     * 验证缓存文件
     *
     * @param string $file
     *
     * @return mixed
     */
    public function isCache($file = '')
    {
        return \houdunwang\cache\Cache::driver('file')
            ->dir(Config::get('view.cache_dir'))->get(
            $this->cacheName($file)
        );
    }

    /**
     * 删除模板缓存
     *
     * @param string $file
     *
     * @return mixed
     */
    public function delCache($file = '')
    {
        return \houdunwang\cache\Cache::driver('file')
            ->dir(Config::get('view.cache_dir'))->del(
            $this->cacheName($file)
        );
    }
}