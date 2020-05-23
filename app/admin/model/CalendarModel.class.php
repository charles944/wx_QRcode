<?php
namespace admin\model;
use think\Model;

/**
 * 日程模型
 * @author 乾坤网络有限公司
 */

class CalendarModel extends Model {

    /* 自动验证规则 */
    protected $_validate = array(
        array('title', 'require', '标题不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', '1,80', '标题长度不能超过80个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('status', 1, self::MODEL_INSERT, 'string'),
    	array('start_time', 'time', self::MODEL_BOTH, 'function'),
        array('update_time', 'time', self::MODEL_BOTH, 'function'),
    );

    /**
     * 新增或更新一个日程
     * @return boolean fasle 失败 ， int  成功 返回完整的数据
     * @author 乾坤网络有限公司
     */
    public function update(){
        /* 获取数据对象 */
        $data = $this->create($_POST);
        if(empty($data)){
            return false;
        }

        /* 添加或新增日程 */
        if(empty($data['id'])){ //新增数据
            $id = $this->add(); //添加日程
            if(!$id){
                $this->error = '新增日程出错！';
                return false;
            }
        } else { //更新数据
            $status = $this->save(); //更新基础内容
            if(false === $status){
                $this->error = '更新日程出错！';
                return false;
            }
        }
        //删除缓存
        S('calendar_list', null);

        //内容添加或更新完成
        return $data;

    }

}
