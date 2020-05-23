<?php
// +----------------------------------------------------------------------
// | Author: spring <sujian009@gmail.com>
// +----------------------------------------------------------------------
namespace think\cache\driver;
use think\Cache;
defined('THINK_PATH') or exit();
/**
 * Couchbase缓存驱动
 * @category   Extend
 * @package  Extend
 * @subpackage  Driver.Cache
 * @author   spring
 */
class Couchbase extends Cache {

    /**
     * 架构函数
     * @param array $options 缓存参数
     * @access public
     */
    function __construct($options=array()) {
        if ( !extension_loaded('couchbase') ) {
            throw_exception(L('_NOT_SUPPERT_').':couchbase');
        }
            $options = array_merge(array (
                'host'			=>	C('COUCH_HOST') ? C('COUCH_HOST') : '127.0.0.1',
                'port'			=>	C('COUCH_PORT') ? C('COUCH_PORT') : 8091,
                'timeout'		=>	C('COUCH_EXPIRE') ? C('COUCH_EXPIRE') : false,
                'user'         =>  	C('COUCH_USER') ? C('COUCH_USER') : '',
                'pass'         =>  C('COUCH_PASS') ? C('COUCH_PASS') : '',
                'expire'       => C('COUCH_EXPIRE') ? C('COUCH_EXPIRE') :'',
                'buckets'      => C('COUCH_BUCKETS') ? C('COUCH_BUCKETS') : '',
             
            ),$options);




        $this->options				=	$options;
        $this->options['expire']	=	isset($options['expire'])?  $options['expire']  :   C('DATA_CACHE_TIME');
        $this->options['prefix']	=	isset($options['prefix'])?  $options['prefix']  :   C('DATA_CACHE_PREFIX');        
        $func               =   $options['persistent'] ? 'pconnect' : 'connect';
        $this->handler= new \Couchbase($this->options['host'].':'.$this->options['port'],$this->options['user'],$this->options['pass'], $this->options['buckets']);
    }



    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function get($name) {
        N('cache_read',1);
        return $this->handler->get($this->options['prefix'].$name);
    }

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param integer $expire  有效时间（秒）
     * @return boolen
     */
    public function set($name, $value, $expire = null) {
        N('cache_write',1);
        if(is_null($expire)) {
            $expire  =  time() + $this->options['expire'];
        }else if($expire > 0){
            $expire  =  time() + $expire;
		}
        if($this->handler->set($this->options['prefix'].$name, $value,$expire)) {
            return true;
        }
        return false;
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolen
     */
    public function rm($name) {
            $this->handler->delete($name);
    }

//    /**
//     * 清除缓存
//     * @access public
//     * @return boolen
//     */
//    public function clear() {
//        return $this->handler->flush();
//    }
}