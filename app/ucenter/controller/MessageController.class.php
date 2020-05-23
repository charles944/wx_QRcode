<?php
namespace ucenter\controller;
use think\Controller;
class MessageController extends BaseController
{
    public function _initialize()
    {
        parent::_initialize();
        $this->_assignSelf(is_login());
    }

    public function message($page = 1, $t = 'all')
    {
        //从条件里面获取Tab
        $map = $this->getMapByTab($t, $map);
        $map['to_uid'] = is_login();
        $messages = D('Common/Message')->where($map)->order('create_time desc')->page($page, 10)->select();
        $totalCount = D('Common/Message')->where($map)->order('create_time desc')->count(); //用于分页
        foreach ($messages as &$v) {
        	$v['content'] = D('Common/Message')->getContent($v['content_id']);
            if ($v['from_uid'] != 0) {
                $v['from_user'] = query_user(array('username', 'space_url', 'avatar64', 'space_link', 'nickname'), $v['from_uid']);
            }
        }
        $this->assign('totalCount', $totalCount);
        $this->assign('messages', $messages);
        //设置Tab
        $this->defaultTabHash('message');
		$this->_setTab('msg');
        $this->assign('t', $t);
        $this->display();
    }

    public function clearmessage(){
        $uid = is_login();
        $res = D('Common/Message')->where(array('to_uid'=>$uid))->delete();
        if($res){
            $this->success('清空站内信成功','refresh');
        }else{
            $this->error(D('Common/Message')->getError());
        }
    }

    public function setmessageread(){
        $uid = is_login();
        D('Common/Message')->setAllReaded($uid);
        $this->success('标记已读成功','refresh');
    }

    private function getMessageModel($message)
    {
        $appname = ucwords($message['appname']);
        $messageModel = D($appname . '/' . $appname . 'Message');
        return $messageModel;
    }

    private function getMapByTab($tab, $map)
    {
        switch ($tab) {
            case 'system':
                $map['type'] = 0;
                break;
            case 'user':
                $map['type'] = 1;
                break;
            case 'app':
                $map['type'] = 2;
                break;
            case 'all':
                break;
            default:
                $map['is_read'] = 0;
                break;
        }
        return $map;
    }
}