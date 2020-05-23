<?php
namespace admin\controller;

use admin\builder\AdminConfigBuilder;
use admin\builder\AdminListBuilder;
use admin\builder\AdminSortBuilder;
use user\model\MemberModel;
use vendor\PHPExcel;

/**
 * 后台用户控制器
 * 
 */
class UserlogController extends AdminController
{
    
    public function index($page  = 1, $r = 50)
    {
    	
    	$nickname = I('nickname','','text');
    	if(!empty($nickname)){
	    	if (is_numeric($nickname)) {
	        	$map['uid|nickname'] = array(intval($nickname), array('like', '%' . $nickname . '%'), '_multi' => true);
	        } else {
	        	$map['nickname'] = array('like', '%' . (string)$nickname . '%');
	        }
    	}
    	
    	$ctime = I('create_time','','text');
    	$etime = I('end_time','','text');
    	if(!empty($ctime)){
    		$map['create_time'] = array(array('egt',  $ctime), array('elt',  $etime ), 'AND');
    		
    	}
    	$aTitle=I('remark','','op_t');
    	if($aTitle){
    		$map['remark'] = array('like','%'.$aTitle.'%');
    	}
    	$aDead=I('dead',0,'intval');
    	switch($aDead){
    		case 0:
    			$map['status'] = '1';
    			break;
    		case 1:
    			$map['status'] = '0';
    			break;
    		case 2:
    			$map['status'] = '-1';
    			break;
    		default:
    			$map['status'] = '1';
    			break;
    	}
    	list($list,$totalCount)=$this->getListByPage('UserOutLog',$map,$page,'id desc','*',$r);

    	$roption = array(array('id'=>50,'value'=>'显示50行'),array('id'=>100,'value'=>'显示100行'),array('id'=>200,'value'=>'显示200行'));

    	// 显示页面
    	$builder = new AdminListBuilder();
    	$builder->meta_title = '违规记录';
    	
    	//foreach ($list as &$val) {
    	//}
    	//$score_option=D('User/Score')->getTypeList(array('status = 1'));
    	
    	$builder->title('违规记录');
        $builder->buttonDelete(U('Userlog/deloutlog'),'清空记录');
        $builder->button('查看登录记录',array('href' => U('Userlog/loginlog'),'class'=>'layui-btn layui-btn-xs fbutton'));
    	$builder->setSearchPostUrl(U('Userlog/outlog'))
    	->setSelectPostUrl(U('Userlog/outlog'))
    	->search('昵称或ID', 'nickname', 'text', '请输入用户昵称或者ID')
    	->search('说明','remark','text','输入说明关键词')
    	->search('开始时间','create_time','time','开始时间','')
    	->search('结束时间','end_time','time','开始时间','')
    	->select('显示：','r','select','','','',$roption);
    	$builder->keyId()
    	->keyText('uid', '用户ID')
        ->keyText('username', '用户名')
    	->keyText('nickname', '昵称');
    	$builder->keyText('remark','记录')
    	->keyCreateTime()
    	->data($list)
    	->pagination($totalCount, $r)
    	->display();
    }
    
    public function loginlog($page  = 1, $r = 50)
    {
    	
    	$nickname = I('nickname','','text');
    	if(!empty($nickname)){
	    	if (is_numeric($nickname)) {
	        	$map['uid|nickname'] = array(intval($nickname), array('like', '%' . $nickname . '%'), '_multi' => true);
	        } else {
	        	$map['nickname'] = array('like', '%' . (string)$nickname . '%');
	        }
    	}
    	
    	$ctime = I('create_time','','text');
    	$etime = I('end_time','','text');
    	if(!empty($ctime)){
    		$map['create_time'] = array(array('egt',  $ctime), array('elt',  $etime ), 'AND');
    		
    	}
    	list($list,$totalCount)=$this->getListByPage('UserLoginLog',$map,$page,'id desc','*',$r);

    	$roption = array(array('id'=>50,'value'=>'显示50行'),array('id'=>100,'value'=>'显示100行'),array('id'=>200,'value'=>'显示200行'));

    	// 显示页面
    	$builder = new AdminListBuilder();
    	$builder->meta_title = '会员登录记录';
    	
    	foreach ($list as &$val) {
    	   $val['login_ip'] = long2ip($val['login_ip']);
    	}
    	//$score_option=D('User/Score')->getTypeList(array('status = 1'));
    	
    	$builder->title('会员登录记录');
        $builder->buttonDelete(U('User/delloginlog'),'清空记录');
        $builder->button('查看违规记录',array('href' => U('Userlog/index'),'class'=>'layui-btn layui-btn-xs fbutton'));
    	$builder->setSearchPostUrl(U('Userlog/loginlog'))
    	->setSelectPostUrl(U('Userlog/loginlog'))
    	->search('昵称或ID', 'nickname', 'text', '请输入用户昵称或者ID')
    	->search('说明','remark','text','输入说明关键词')
    	->search('开始时间','create_time','time','开始时间','')
    	->search('结束时间','end_time','time','开始时间','')
    	->select('显示：','r','select','','','',$roption);
    	$builder->keyId()
        ->keyDoAction('Userlog/index?nickname=[uid]','查看违规记录',array('class'=>'layui-btn layui-btn-xs hbutton btn_see'))
    	->keyText('uid', '用户ID')
        ->keyText('username', '用户名')
    	->keyText('nickname', '昵称');
    	$builder->keyText('login_ip','最后登录IP')
    	->keyCreateTime()
    	->data($list)
    	->pagination($totalCount, $r)
    	->display();
    }
    
    
}