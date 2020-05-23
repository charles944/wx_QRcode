<?php
namespace admin\model;
use think\Model;

/**
 * 角色模型
 * Class RoleModel
 * @package Admin\Model
 * @郑钟良
 */
class RoleModel extends Model
{

    protected $_validate = array(
        array('name', 'require', '标识不能为空。', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '', '角色标识已经存在。', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        array('name', 'checkName', '角色标识只能由字母和下滑线组成。', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH),

        array('title', 'require', '角色名不能为空。', self::EXISTS_VALIDATE , 'regex', self::MODEL_BOTH),
        array('title', '', '角色名已经存在。', self::VALUE_VALIDATE , 'unique', self::MODEL_BOTH),
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_UPDATE),
        array('status', '1', self::MODEL_BOTH),
    );
    protected $insertFields='group_id,name,title,description,user_groups,levelmod,audit,sort,status,create_time,canlevel,buyvalue,levelup_number,levelup_mark,day';
    protected $updateFields='id,group_id,name,title,description,user_groups,levelmod,audit,sort,status,update_time,canlevel,buyvalue,levelup_number,levelup_mark,day';
    /**
     * 插入数据
     * @param $data
     * @return mixed
     * 
     */
    public function insert($data=array()){
        $data=$this->create($data);
        if($data){
            $result=$this->add($data);
        }else{
            $result=false;
        }
        return $result;
    }

    /**
     * 修改数据
     * @param $data
     * @return mixed
     * 
     */
    public function update($data=array()){
        $data=$this->create($data);
        if($data){
            $result=$this->save($data);
        }else{
            $result=false;
        }
        return $result;
    }

    /**
     * 分页按照$map获取列表
     * @param array $map 查询条件
     * @param int $page 页码
     * @param $order 排序
     * @param null $fields 查询字段，null表示全部字段
     * @param int $r 每页条数
     * @return mixed 一页结果列表
     * 
     */
    public function selectPageByMap($map=array(),$page=1,$r=20,$order,$fields=null){
        $order=$order?$order:"id asc";
        if($fields==null){
            $list=$this->where($map)->order($order)->page($page,$r)->select();
        }else{
            $list=$this->where($map)->order($order)->field($fields)->page($page,$r)->select();
        }
        $totalCount=$this->where($map)->count();
        return array($list,$totalCount);
    }

    /**
     * 通过$map获取列表
     * @param array $map 查询条件
     * @param $order 排序
     * @param null $fields 查询字段，null表示全部字段
     * @return mixed 结果列表
     * 
     */
    public function selectByMap($map=array(),$order,$fields=null){
        $order=$order?$order:"id asc";
        if($fields==null){
            $list=$this->where($map)->order($order)->select();
        }else{
            $list=$this->where($map)->order($order)->field($fields)->select();
        }
        return $list;
    }

    /**
     * * 通过$map获取单条值
     * @param array $map 查询条件
     * @param string $order 排序
     * @param null $fields 查询字段，null表示全部字段
     * @return mixed 结果
     * 
     */
    public function getByMap($map=array(),$order,$fields=null){
        $order=$order?$order:"id asc";
        if($fields==null){
            $data=$this->where($map)->order($order)->find();
        }else{
            $data=$this->where($map)->order($order)->field($fields)->find();
        }
        return $data;
    }

    /**
     * 验证角色名(只能有字母和下划线组成)
     * @param $name
     * @return bool
     * 
     */
    public function checkName($name){
        if(!preg_match('/^[_a-z]*$/i',$name)){
            return false;
        }
        return true;
    }
} 