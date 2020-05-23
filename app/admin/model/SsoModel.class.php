<?php
namespace admin\model;
use think\Model;

/**
 * Class SsoModel  单点登录模型
 * @package Admin\Model
 */
class SsoModel extends Model{
    /**
     * 自动完成
     * @var array
     */

    private $appModel =null;
    protected $_auto = array(
        array('status', 1, self::MODEL_INSERT),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
    );

    protected function _initialize()
    {
        parent::_initialize();
        $this->appModel =  M('sso_app');
    }

    public function getApp($map){
        $app = $this->appModel->where($map)->find();
        return $app;
    }

    public function addApp($data){
        $res = $this->appModel->add($data);
        return $res;
    }

    public function delApp($ids){
        $res = $this->appModel->where(array('id'=>array(array('in',$ids))))->delete();
        return $res;
    }

    public function editApp($data){
        $res = $this->appModel->save($data);
        return $res;
    }


}
