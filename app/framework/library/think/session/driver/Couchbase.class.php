<?php 
// +----------------------------------------------------------------------
// | Author: spring <sujian009@gmail.com>
// +----------------------------------------------------------------------
namespace think\session\driver;
/**
 * Couchbase方式Session驱动
 * @category   Extend
 * @package  Extend
 * @subpackage  Driver.Session
 * @author   spring
 */
class Couchbase {

    /**
     * Session有效时间
     */
    private static $lifeTime      = '';

    /**
     * session保存的数据桶名
     */
   protected $sessionBuckets  = '';

    /**
     * 数据库句柄
     */
    private static $hander;

    /**
     * 打开Session 
     * @access public 
     * @param string $savePath 
     * @param mixed $sessName  
     */
    public function open($savePath, $sessName) {
       self::$lifeTime = C('SESSION_EXPIRE')?C('SESSION_EXPIRE'):ini_get('session.gc_maxlifetime');
       $this->sessionBuckets  =   C('COUCH_BUCKETS')?C('COUCH_BUCKETS'):'default';
       self::$hander  = new \Couchbase(C('COUCH_HOST').':'.C('COUCH_PORT'), C('COUCH_USER'),C('COUCH_PASS'),$this->sessionBuckets);
       if(!self::$hander)
           return false;
       return true; 
    }

    // read接口
//C('SESSION_PREFIX');
    public static function read($id) {
        return self::$hander->get(C('SESSION_PREFIX').$id);

    }

    // write接口

    public static function write($id, $data)  {
        return self::$hander->set(C('SESSION_PREFIX').$id, $data, self::$lifeTime);

    }

    // destory接口

    public static function destroy($id)  {

        return self::$hander->delete(C('SESSION_PREFIX').$id);

    }

    // gc接口

    public static function gc(){ return true; }

    // close接口

    public static function close(){    return true; }

    public function __destruct()  {

        session_write_close();

    }

    public function execute() {
        session_set_save_handler(array(&$this,"open"),
            array(&$this,"close"),
            array(&$this,"read"),
            array(&$this,"write"),
            array(&$this,"destroy"),
            array(&$this,"gc"));

    }
}