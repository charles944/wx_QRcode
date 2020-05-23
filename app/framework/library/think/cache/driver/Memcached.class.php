<?php
namespace think\cache\driver;

use Memcached as MemcachedResource;
use think\Cache;
defined('THINK_PATH') or exit();
/**
 * Aliyun OCS (Memcached)缓存驱动
 */
class Memcached extends Cache
{

    /**
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        if ( !extension_loaded('memcached') ) {
            E(L('_NOT_SUPPERT_').':memcached');
        }

        $options = array_merge(array(
            'servers' => C('MEMCACHED_SERVER') ? C('MEMCACHED_SERVER') : null,
            'port' => C('MEMCACHED_PORT') ? C('MEMCACHED_PORT') : 11211,
            'lib_options' => C('MEMCACHED_LIB') ? C('MEMCACHED_LIB') : null,
            'username'        =>  C('MEMCACHED_USERNAME') ? C('MEMCACHED_USERNAME') : 'username',
            'password'        =>  C('MEMCACHED_PASSWORD') ? C('MEMCACHED_PASSWORD') : 'password',
        ), $options);

        $this->options      =   $options;
        $this->options['expire'] =  isset($options['expire'])?  $options['expire']  :   C('DATA_CACHE_TIME');
        $this->options['prefix'] =  isset($options['prefix'])?  $options['prefix']  :   C('DATA_CACHE_PREFIX');
        $this->options['length'] =  isset($options['length'])?  $options['length']  :   0;

        $this->handler      =   new MemcachedResource;
        $this->handler->setOption(MemcachedResource::OPT_COMPRESSION, false);
        $this->handler->setOption(MemcachedResource::OPT_BINARY_PROTOCOL, true);
        $options['servers'] && $this->handler->addServer($options['servers'],$options['port']);
        $options['username'] && $options['password'] && $this->handler->setSaslAuthData($options['username'],$options['password']);
        $options['lib_options'] && $this->handler->setOptions($options['lib_options']);
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function get($name)
    {
        N('cache_read',1);        
        return $this->handler->get($this->options['prefix'].$name);
    }

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param integer $expire  有效时间（秒）
     * @return boolean
     */
    public function set($name, $value, $expire = null)
    {
        N('cache_write',1);
        if(is_null($expire)) {
            $expire  =  $this->options['expire'];
        }
        $name   =   $this->options['prefix'].$name;
		$results =$this->handler->set($name, $value, $expire);
        if($results) {
            if($this->options['length']>0) {
                // 记录缓存队列
                $this->queue($name);
            }
            return $results;
        }
        return false;
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function rm($name, $ttl = false) {
        $name   =   $this->options['prefix'].$name;
        return $ttl === false ?
            $this->handler->delete($name) :
            $this->handler->delete($name, $ttl);
    }

    /**
     * 清除缓存
     * @access public
     * @return boolean
     */
    public function clear() {
        return $this->handler->flush();
    }
}
