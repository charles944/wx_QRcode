<?php
/**
 * 控制器
 * Created by 朝兮夕兮.
 * Date: 15.5.25
 */
namespace admin\controller;

use admin\builder\AdminConfigBuilder;
use admin\builder\AdminListBuilder;
use admin\builder\AdminTreeListBuilder;
use vendor\PHPExcel;

class StationmanageController extends AdminController
{

    protected $memberModel;
    protected $ucentermemberModel;

    function _initialize()
    {
        $this->memberModel = D('User/Member');
        $this->ucentermemberModel = D('User/UcenterMember');
        parent::_initialize();
    }
    
    public function online(){
        $map['status'] = array('egt', -1);
        $map['last_login_time'] = array('gt',TIME()-1800);
        $model = $this->ucentermemberModel;
        $list = $model->where($map)->select();
        
        // unset($list);
        $totalCount = $model->where($map)->count();
        
        // 显示页面
        $builder = new AdminListBuilder();
        $builder->meta_title = '在线会员列表';
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';
        
        foreach ($list as &$val) {
            $val['uid'] = $val['uid'].'|'.$val['nickname'];
        }
        // unset($val);
        
        $builder->title('在线会员列表')
        ->keyText('uid', '会员')
        ->keyTime('last_login_time', '最后登录时间')
        ->data($list)
        ->display();
    }
    
    public function cleardata(){
        if(!IS_POST){
            //获取所有的数据表
            $tables = D('Model')->getTables();
        
            $this->assign('tables', $tables);
            $this->meta_title = '清理数据';
            $this->display();
        }else{
            $table = I('post.table');
            empty($table) && $this->error('请选择要生成的数据表！');
            $dat = I('post.create_time');
            $dat = strtotime($dat);
            empty($dat) && $this->error('请选择要清理的时间！');
            $res =  M()->execute("delete FROM ".$table." WHERE create_time < '".$dat."'");
            if($res){
                $this->success('清理数据成功！', $_SERVER['HTTP_REFERER']);
            }else{
                $this->error('没有数据可以清理', $_SERVER['HTTP_REFERER']);
            }
        }
    }
    
    public function excelreport(){
        $this->display();
    }
    
    public function cheatdetect(){
        $this->display();
    }
    

}
?>