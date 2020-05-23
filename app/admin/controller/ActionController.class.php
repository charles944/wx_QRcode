<?php
namespace admin\controller;

use admin\builder\AdminConfigBuilder;
use admin\builder\AdminListBuilder;
use admin\builder\AdminSortBuilder;
use admin\builder\AdminTreeListBuilder;

/**
 * 行为控制器
 * @author 乾坤网络有限公司
 */
class ActionController extends AdminController {

    /**
     * 行为日志列表
     * @author 乾坤网络有限公司
     */
    public function log($page = 1, $r = 50){
        //获取列表数据
    	$aTitle=I('title','','intval');
    	if($aTitle){
    		$map['user_id'] = $aTitle;
    	}
        $roption = array(array('id'=>50,'value'=>'显示50行'),array('id'=>100,'value'=>'显示100行'),array('id'=>200,'value'=>'显示200行'));
        list($list,$totalCount)=$this->getListByPage('ActionLog', $map,$page,'id desc','*',$r);
        foreach ($list as $key=>$value){
            $model_id                  =   get_document_field($value['model'],"name","id");
            $list[$key]['model_id']    =   $model_id ? $model_id : 0;
            $list[$key]['action_name'] =   get_action($value['action_id'],'title');
        }
        // 显示页面
        $builder = new AdminListBuilder();
        $builder->title('行为日志')
        ->buttonClear(U('clear'),'清空记录')
        ->buttonDelete(U('remove'),'批量删除',array('target-form'=>'ids'))
        ->setSelectPostUrl(U('Action/actionlog'))
        ->setSearchPostUrl(U('Action/actionlog'))
        ->search('搜索', 'title', 'text', '请输入用户ID')
        ->select('显示：','r','select','','','',$roption)
        ->keyId()
        ->keyText('action_name', '行为名称')
        ->keyUid('user_id','操作会员')
        ->keyText('action_id', '行为ID')
        ->keyText('remark', '备注')
        ->keyTime('create_time', '操作时间')
        ->keyDoActionView('Action/view?id=###','查看')
        ->keyDoActionRealDel('Action/remove?ids=###','删除');
        $builder->data($list)
        ->pagination($totalCount, $r)
        ->display();
    }

    /**
     * 查看行为日志
     * @author 乾坤网络有限公司
     */
    public function viewlog($id = 0){
        empty($id) && $this->error('参数错误！');

        $info = M('ActionLog')->field(true)->find($id);

        $this->assign('info', $info);
        $this->meta_title = '查看行为日志';
        $this->display();
    }

    /**
     * 删除日志
     * @param mixed $ids
     * @author 乾坤网络有限公司
     */
    public function removelog($ids = 0){
        empty($ids) && $this->error('参数错误！');
        if(is_array($ids)){
            $map['id'] = array('in', $ids);
        }elseif (is_numeric($ids)){
            $map['id'] = $ids;
        }
        $res = M('ActionLog')->where($map)->delete();
        if($res !== false){
            $this->success('删除成功！');
        }else {
            $this->error('删除失败！');
        }
    }

    /**
     * 清空日志
     * @author 乾坤网络有限公司
     */
    public function clearlog(){
        $res = M('ActionLog')->where(array('type'=>array('neq',2)))->delete();
        if($res !== false){
            $this->success('日志清空成功！');
        }else {
            $this->error('日志清空失败！');
        }
    }

}
