<?php
namespace admin\controller;

use admin\builder\AdminConfigBuilder;
use admin\builder\AdminListBuilder;
use admin\builder\AdminSortBuilder;
use admin\builder\AdminTreeListBuilder;
/**
 * Class MessageController  消息控制器
 * @package Admin\Controller
 */
class MessageController extends AdminController
{
    public function userlist($page = 1, $r = 50)
    {
        $aUserGroup = I('get.user_group', 0, 'intval');
        $map = array();
        $aTitle=I('title','','op_t');
        if(!empty($aTitle)){
            if (is_numeric($aTitle)) {
        	   $map['uid|nickname'] = array(intval($aTitle), array('like', '%' . $aTitle . '%'), '_multi' => true);
            } else {
            	$map['nickname'] = array('like', '%' . (string)$aTitle . '%');
            }
        }
        if (!empty($aUserGroup)) {
            $uids = $this->getUids($aUserGroup);
            $map['id'] = array('in', $uids);
        }
        list($list,$totalCount)=$this->getListByPage('UcenterMember',$map,$page,'id desc, status desc','*',$r);
        // foreach($list as &$v){
        //     $v['id'] = $v['id'];
        // }
		//unset($v);
        $roption = array(array('id'=>50,'value'=>'显示50行'),array('id'=>100,'value'=>'显示100行'),array('id'=>200,'value'=>'显示200行'));

        $group = D('AuthGroup')->getGroups();

        $user_group = array(array('id' => 0, 'value' => '全部'));
        foreach ($group as $key => $v) {
            array_push($user_group, array('id' => $v['id'], 'value' => $v['title']));
        }

        $builder = new AdminListBuilder();
        $builder->title("群发用户列表");
        $builder->meta_title = '群发用户列表';

        $builder->setSelectPostUrl(U('Message/userList'))
        ->suggest('如果短信插件用的阿里云短信，不建议使用批量发送短信功能，因为阿里短信需要申请模板，自由度不够')
        ->setSearchPostUrl(U('Message/userList'))
        ->search('搜索', 'title', 'text', '请输入用户昵称或者用户ID')
        ->select('显示：','r','select','','','',$roption)
        ->select('用户组：', 'user_group', 'select', '根据用户组筛选', '', '', $user_group);
        $builder->buttonModal(U('Message/sendMessage',array('type'=>'shortmsg')), array('user_group' => $aUserGroup), '群发站内信', array('data-title' => '群发站内信', 'target-form' => 'ids', 'can_null' => 'true','class'=>'layui-btn layui-btn-xs fbutton ajax-post'));
        $builder->buttonModal(U('Message/sendMessage',array('type'=>'email')), array('user_group' => $aUserGroup), '群发邮件', array('data-title' => '群发邮件', 'target-form' => 'ids', 'can_null' => 'true','class'=>'layui-btn layui-btn-xs fbutton ajax-post'));
		$builder->buttonModal(U('Message/sendMessage',array('type'=>'mobile')), array('user_group' => $aUserGroup), '群发短信', array('data-title' => '群发短信', 'target-form' => 'ids', 'can_null' => 'true','class'=>'layui-btn layui-btn-xs fbutton ajax-post'));
        $builder->keyText('id', '用户ID')
		->keyText('username', "用户名")
		->keyText('email','邮箱')
		->keyText('mobile','手机号码')
        ->keyTime('reg_time', '注册时间')
        ->keyTime('last_login_time', '最后登录时间')
        ->keyText('login','累计登录次数')
        ->keyStatus('status','状态',array('1'=>'正常','0'=>'冻结','2'=>'未审核','3'=>'未激活','-1'=>'已删除'));
        $builder->data($list);
        $builder->pagination($totalCount, $r);
        $builder->display();
    }

    private function getUids($user_group = 0)
    {
        $uids = array();
        if (!empty($user_group)) {
            $users = D('auth_group_access')->where(array('group_id' => $user_group))->field('uid')->select();
            $group_uids = getSubByKey($users, 'uid');
            if ($group_uids) {
                $uids = $group_uids;
            }
        }
        return $uids;
    }

    public function sendMessage()
    {
        if (IS_POST) {
            $aUids = I('post.uids');
            $aUserGroup = I('post.user_group');
            $aTitle = I('post.title', '', 'text');
            $aContent = I('post.content', '', 'html');
            $aUrl = I('post.url', '', 'text');
            $aArgs = I('post.args', '', 'text');
            $aMethod = I('post.method','','text');
            $args = array();
            // 转换成数组
            if ($aArgs) {
                $array = explode('/', $aArgs);
                while (count($array) > 0) {
                    $args[array_shift($array)] = array_shift($array);
                }
            }

            if (empty($aTitle)) {
                $this->error('请输入消息标题');
            }
            if (empty($aContent)) {
                $this->error('请输入消息内容');
            }
            // 以用户组或身份发送消息
            if(empty($aUids)){
                if (empty($aUserGroup)) {
                    $this->error('请选择用户组或身份组或者用户');
                }

                $group_count = M('AuthGroup')->where(array('status' => 1))->count();
                if ($group_count == count($aUserGroup)) {
                    $aUserGroup = 0;
                }
                if (!empty($aUserGroup)) {
                    //$uids = M('auth_group_access g')->join('__UCENTER_MEMBER__ u ON u.id = g.uid')->where(array('g.group_id' => array('in', $aUserGroup),'u.status'=>1))->field('g.uid,u.email,u.mobile')->select();
                    $uids = M('auth_group_access')->where(array('group_id' => array('in', $aUserGroup)))->field('uid')->select();
                    $to_uids = getSubByKey($uids, 'uid');
                }
                if (empty($aUserGroup)) {
                    //$uids = M('UcenterMember u')->join('__MEMBER__ m ON m.uid = u.id')->where(array('u.status' => 1))->field('m.uid,u.email,u.mobile,m.nickname')->select();
                    $uids = M('UcenterMember')->where(array('status' => 1))->field('id')->select();
                    $to_uids = getSubByKey($uids, 'id');
                }
                //$to_emials = getSubByKey($uids, 'email');
                //$to_mobiles = getSubByKey($uids, 'mobile');
                //$to_nicks = getSubByKey($uids, 'mobile');
            }else{
                // 用uid发送消息
                $to_uids = explode(',',$aUids);
            }
            switch($aMethod){
                case 'shortmsg':
                    D('Common/Message')->sendMessageWithoutCheckSelf($to_uids, $aTitle, $aContent, $aUrl, $args);
                    $result['status'] = 1;
                    $result['info'] = '发送成功';
                    $this->ajaxReturn($result);
                    break;
                case 'email':
                    $uids_data = M('UcenterMember u')->join('__MEMBER__ m ON m.uid = u.id')->where(array('u.id' => array('in', $to_uids),'u.email_ver'=>1,'u.status'=>1))->field('m.uid,u.email,u.mobile,m.nickname')->select();
                    $uids_to = getSubByKey($uids_data, 'uid');
                    $queue_data['title'] = $aTitle;
                    $queue_data['content'] = $aContent;
                    $queue_data['uids'] = implode(',',$uids_to);
                    $queue_data['user_groups'] = implode(',',$aUserGroup);
                    $queue_data['url'] = $aUrl;
                    $queue_data['status'] = 0;
                    $queue_data['method'] = $aMethod;
                    $queue_data['create_time'] = TIME();
                    $res = M('Queue')->add($queue_data);
                    foreach($uids_data as &$val){
                        $queue_detail_data['qid'] = $res;
                        $queue_detail_data['title'] = $aTitle;
                        $queue_detail_data['content'] = $aContent;
                        $queue_detail_data['url'] = $aUrl;
                        $queue_detail_data['uid'] = $val['uid'];
                        $queue_detail_data['email'] = $val['email'];
                        $queue_detail_data['mobile'] = '';
                        $queue_detail_data['status'] = 0;
                        $queue_detail_data['create_time'] = TIME();
                        $queue_detail_data['method'] = $aMethod;
                        M('QueueDetail')->add($queue_detail_data);
                    }
                    $result['status'] = 1;
                    $result['info'] = '提交群发任务成功，请注意配置好计划任务，否则无法完成群发！';
                    $this->ajaxReturn($result);
                    break;
                case 'mobile':
                    $uids_data = M('UcenterMember u')->join('__MEMBER__ m ON m.uid = u.id')->where(array('u.id' => array('in', $to_uids),'u.mobile_ver'=>1,'u.status'=>1))->field('m.uid,u.email,u.mobile,m.nickname')->select();
                    $uids_to = getSubByKey($uids_data, 'uid');
                    $queue_data['title'] = $aTitle;
                    $queue_data['content'] = $aContent;
                    $queue_data['uids'] = implode(',',$uids_to);
                    $queue_data['user_groups'] = implode(',',$aUserGroup);
                    $queue_data['url'] = $aUrl;
                    $queue_data['status'] = 0;
                    $queue_data['method'] = $aMethod;
                    $queue_data['create_time'] = TIME();
                    $res = M('Queue')->add($queue_data);
                    foreach($uids_data as &$val){
                        $queue_detail_data['qid'] = $res;
                        $queue_detail_data['title'] = $aTitle;
                        $queue_detail_data['content'] = $aContent;
                        $queue_detail_data['url'] = $aUrl;
                        $queue_detail_data['uid'] = $val['uid'];
                        $queue_detail_data['email'] = '';
                        $queue_detail_data['mobile'] = $val['mobile'];
                        $queue_detail_data['status'] = 0;
                        $queue_detail_data['create_time'] = TIME();
                        $queue_detail_data['method'] = $aMethod;
                        M('QueueDetail')->add($queue_detail_data);
                    }
                    $result['status'] = 1;
                    $result['info'] = '提交群发任务成功，请注意配置好计划任务，否则无法完成群发！';
                    $this->ajaxReturn($result);
                    break;
                default:

                    break;
            }
        } else {
            $aUids = I('get.ids');
            $aUserGroup = I('get.user_group', 0, 'intval');
            $aType = I('get.type','','text');
            if(empty($aType)){
                $this->error('参数错误！');
            }
            switch($aType){
                case 'shortmsg':
                    $this->assign('method',$aType);
                    break;
                case 'email':
                    $host = C('MAIL_SMTP_HOST');
                    $user = C('MAIL_SMTP_USER');
                    $pass = C('MAIL_SMTP_PASS');
                    if(!empty($host) && !empty($host) && !empty($host)){
                        $this->assign('method',$aType);
                    }else{
                        $this->error('请先在“系统配置”-“邮件配置“中设置相关发件信息，才可群发邮件！');
                    }
                    break;
                case 'mobile':
                    $sms_hook = modC('SMS_HOOK','none','CONFIG');
                    $sms_hook =  check_sms_hook_is_exist($sms_hook);

                    if($sms_hook == 'none'){
                        $this->error("还未配置短信服务商信息，请先安装相关短信发送插件，再打开”系统设置“-”网站基础配置”-“短信发送商配置”里选择对应插件");
                    }
                    $this->assign('method',$aType);
                    break;
                default:

                    break;
            }

            if (empty($aUids)) {
                $group = D('AuthGroup')->getGroups();
                $groups = array();
                foreach ($group as $key => $v) {
                    array_push($groups, array('id' => $v['id'], 'value' => $v['title']));
                }
                $this->assign('groups', $groups);
                $this->assign('aUserGroup', $aUserGroup);
            } else {
                $uids = implode(',',$aUids);
                $users = D('User/UcenterMember')->where(array('uid'=>array('in',$aUids)))->field('id,username,email,mobile')->select();
                $this->assign('users', $users);
                $this->assign('uids', $uids);
            }
            $this->display('sendmessage');
        }
    }

    public function queuelist($page = 1, $r = 50)
    {
        $map = array();
        $aTitle=I('title','','op_t');
        $aMethod=I('method','','op_t');
        if(!empty($aTitle)){
            if (is_numeric($aTitle)) {
               $map['uid'] = intval($aTitle);
            }
        }
        if(!empty($aMethod)){
            switch($aMethod){
                case 1:
                    $map['method'] = 'mobile';
                    break;
                case 2:
                    $map['method'] = 'email';
                    break;
                default:
                    break;
            }
        }
        list($list,$totalCount)=$this->getListByPage('Queue',$map,$page,'id desc, status desc','*',$r);
        $roption = array(array('id'=>50,'value'=>'显示50行'),array('id'=>100,'value'=>'显示100行'),array('id'=>200,'value'=>'显示200行'));

        $method = array(array('id' => 0, 'value' => '全部'), array('id' => 1, 'value' => '手机'), array('id' => 2, 'value' => '邮箱'));

        $builder = new AdminListBuilder();
        $builder->title("群发任务列表");
        $builder->meta_title = '群发任务列表';

        $builder->setSelectPostUrl(U('Message/queuelist'))
        ->setSearchPostUrl(U('Message/queuelist'))
        ->search('搜索', 'title', 'text', '请输入用户ID')
        ->select('显示：','r','select','','','',$roption)
        ->select('类型：', 'method', 'select', '根据用户组筛选', '', '', $method);
        $builder->keyText('id', '用户ID')
        ->keyText('title', "发送标题")
        ->keyText('content','发送内容')
        ->keyText('method','发送类型')
        ->keyText('uids', '用户ID')
        ->keyText('user_groups','用户组')
        ->keyTime('create_time', '创建时间')
        ->keyText('has_queue','已完成任务数')
        ->keyStatus('status','状态',array('1'=>'正在处理','0'=>'未开始','2'=>'已完成'))
        ->keyDoActionView('Message/queueview?id=###','查看详情')
        ->keyDoAction('Message/queueenable?id=###','开启',array('class'=>'layui-btn layui-btn-xs ajax-get'))
        ->keyDoAction('Message/queueparse?id=###','暂停',array('class'=>'layui-btn layui-btn-xs ajax-get'))
        ->keyDoActionDel('Message/queuedel?id=###','删除');
        $builder->data($list);
        $builder->pagination($totalCount, $r);
        $builder->display();
    }

    public function queueview($id = 0, $page = 1, $r = 50){
        if(!empty($id)){
            $map['qid'] = $id;
        }else{
            $this->error('参数错误！');
        }
        $data = M('Queue')->where(array('id'=>$id))->find();
        list($list,$totalCount)=$this->getListByPage('QueueDetail',$map,$page,'id desc, status desc','*',$r);
        $roption = array(array('id'=>50,'value'=>'显示50行'),array('id'=>100,'value'=>'显示100行'),array('id'=>200,'value'=>'显示200行'));

        $builder = new AdminListBuilder();
        $builder->title("群发任务列表");
        $builder->meta_title = '群发任务列表';

        $builder->suggest('发送标题：'.$data['title'].'<br>发送内容：'.$data['content'])
        ->setSelectPostUrl(U('Message/queueview'))
        ->select('显示：','r','select','','','',$roption);
        $builder->keyText('uid', '用户ID')
        ->keyText('method','发送类型')
        ->keyText('email','邮箱')
        ->keyText('mobile','手机')
        ->keyTime('create_time', '创建时间')
        ->keyTime('update_time','发送时间')
        ->keyStatus('status','状态',array('1'=>'成功','0'=>'未发送','2'=>'失败'))
        ->keyText('status_msg','回调文本');
        $builder->data($list);
        $builder->pagination($totalCount, $r);
        $builder->display();
    }

    public function queueparse($id = 0){
        if(!empty($id)){
            $map['id'] = $id;
        }else{
            $this->error('参数错误！');
        }
        $data = M('Queue')->where($map)->find();
        if(empty($data)){
            $this->error('无此数据！');
        }
        if($data['status'] == 2){
            $this->error('此任务已完成，无需暂停！');
        }
        $res = M('Queue')->where($map)->save(array('status'=>3));
        if($res){
            $this->success('暂停群发任务成功！');
        }else{
            $this->error('暂停群发任务失败！');
        }
    }

    public function queueenable($id = 0){
        if(!empty($id)){
            $map['id'] = $id;
        }else{
            $this->error('参数错误！');
        }
        $data = M('Queue')->where($map)->find();
        if(empty($data)){
            $this->error('无此数据！');
        }
        if($data['status'] == 2){
            $this->error('此任务已完成，无需开启！');
        }
        $res = M('Queue')->where($map)->save(array('status'=>0));
        if($res){
            $this->success('开启群发任务成功！');
        }else{
            $this->error('开启群发任务失败！');
        }
    }

    public function queuedel($id = 0){
        if(!empty($id)){
            $map['id'] = $id;
        }else{
            $this->error('参数错误！');
        }
        $data = M('Queue')->where($map)->find();
        if(empty($data)){
            $this->error('无此数据！');
        }
        $res = M('Queue')->where($map)->delete();
        if($res){
            $res = M('QueueDetail')->where(array('qid'=>$id))->delete();
            $this->success('删除群发任务成功！');
        }else{
            $this->error('删除群发任务失败！');
        }
    }
}