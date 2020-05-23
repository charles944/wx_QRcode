<?php

namespace ucenter\controller;
use think\Controller;

class MyController extends BaseController
{
    public function _initialize()
    {
        parent::_initialize();
        $uid = is_login();
        $this->_tab_menu();
        $this->_assignSelf($uid);
    }

    public function index($page=1)
    {
        $str = '{$self.nickname|text}';
        $this->setTitle($str . "的个人中心");
        $this->setKeywords($str . "的个人中心");
        $this->setDescription($str . "的个人中心");
        $this->display();
    }

    public function appList($page = 1)
    {
        $appArr = $this->_tab_menu();

        $type = op_t($_GET['type']);
		$control = op_t($_GET['control']);
        if (!isset ($appArr [$type])) {
            $this->error('参数出错！！');
        }
        $this->assign('type', $type);
		$this->assign('control', $control);
        $this->assign('module',$appArr[$type]['data-id']);
        $this->assign('page',$page);

        $str = '{$self.nickname|op_t}';
        $str_app = '{$appArr.'.$type.'.title|op_t}';
        $this->setTitle($str . "的个人" . $str_app . "页");
        $this->setKeywords($str . "，个人主页，个人" . $str_app);
        $this->setDescription($str . "的个人" . $str_app . "页");
        $this->display();
    }

  //   public function fans($page = 1)
  //   {
  //       $this->assign('tabs', 'fans');
  //       // $fans = D('Common/Follow')->getFans(is_login(), $page, array('avatar128', 'uid', 'nickname', 'fans', 'following', 'space_url', 'title'), $totalCount);
  //       // $this->assign('fans', $fans);
  //       // $this->assign('totalCount', $totalCount);

  //       $str = '{$self.nickname|op_t}';
  //       $this->setTitle($str . "的个人粉丝页");
  //       $this->setKeywords($str . "，个人粉丝");
  //       $this->setDescription($str . "的个人粉丝页");
		// $this->_setTab('following');
  //       $this->display();
  //   }

  //   public function following($page = 1)
  //   {
  //       $following = D('Common/Follow')->getFollowing(is_login(), $page, array('avatar128', 'uid', 'nickname', 'fans', 'following', 'space_url', 'title'), $totalCount);
  //       $this->assign('following', $following);
  //       $this->assign('totalCount', $totalCount);
  //       $this->assign('tabs', 'following');

  //       $str = '{$self.nickname|op_t}';
  //       $this->setTitle($str . "的个人关注页");
  //       $this->setKeywords($str . "，个人关注");
  //       $this->setDescription($str . "的个人关注页");
		// $this->_setTab('following');
  //       $this->display();
  //   }

    public function rank()
    {
        $rankList = D('rank_user')->where(array('uid' => is_login(), 'status' => 1))->field('rank_id,reason,create_time')->select();
        foreach ($rankList as &$val) {
            $rank = D('rank')->where('id=' . $val['rank_id'])->find();
            $val['title'] = $rank['title'];
            $val['logo_url'] = get_pic_src(M('picture')->where('id=' . $rank['logo'])->field('path')->getField('path'));
            $val['label_content']=$rank['label_content'];
            $val['label_bg']=$rank['label_bg'];
            $val['label_color']=$rank['label_color'];
        }
        unset($val);
        $this->assign('rankList', $rankList);
        $this->assign('tabs', 'rank');

        $str = '{$self.nickname|op_t}';
        $this->setTitle($str . "的头衔列表页");
        $this->setKeywords($str . "，个人头衔");
        $this->setDescription($str . "的头衔列表页");
		$this->_setTab('rank');
        $this->display('rank');
    }

    public function rankVerifyFailure()
    {
        $rankList = D('rank_user')->where(array('uid' => is_login(), 'status' => -1))->field('id,rank_id,reason,create_time')->select();
        foreach ($rankList as &$val) {
            $rank = D('rank')->where('id=' . $val['rank_id'])->find();
            $val['title'] = $rank['title'];
            $val['logo_url'] = get_pic_src(M('picture')->where('id=' . $rank['logo'])->field('path')->getField('path'));
            $val['label_content']=$rank['label_content'];
            $val['label_bg']=$rank['label_bg'];
            $val['label_color']=$rank['label_color'];
        }
        unset($val);
        $this->assign('rankList', $rankList);
        $this->assign('tabs', 'rankVerifyFailure');

        $str = '{$self.nickname|op_t}';
        $this->setTitle($str . "的被驳回头衔申请列表页");
        $this->setKeywords($str . "，个人头衔");
        $this->setDescription($str . "的被驳回头衔申请列表页");
		$this->_setTab('rank');
        $this->display('rank');
    }

    public function rankVerifyWait()
    {
        $rankList = D('rank_user')->where(array('uid' => is_login(), 'status' => 0))->field('rank_id,reason,create_time')->select();
        foreach ($rankList as &$val) {
            $rank = D('rank')->where('id=' . $val['rank_id'])->find();
            $val['title'] = $rank['title'];
            $val['logo_url'] = get_pic_src(M('picture')->where('id=' . $rank['logo'])->field('path')->getField('path'));
            $val['label_content']=$rank['label_content'];
            $val['label_bg']=$rank['label_bg'];
            $val['label_color']=$rank['label_color'];
        }
        unset($val);
        $this->assign('rankList', $rankList);
        $this->assign('tabs', 'rankVerifyWait');

        $str = '{$self.nickname|op_t}';
        $this->setTitle($str . "的待审核头衔申请列表页");
        $this->setKeywords($str . "，个人头衔");
        $this->setDescription($str . "的待审核头衔申请列表页");
		$this->_setTab('rank');
        $this->display('rank');
    }

    public function rankVerifyCancel($rank_id = null)
    {
        $rank_id = intval($rank_id);
        if (is_login() && $rank_id) {
            $map['rank_id'] = $rank_id;
            $map['uid'] = is_login();
            $map['status'] = 0;
            $result = D('rank_user')->where($map)->delete();
            if ($result) {
                D('Common/Message')->sendMessageWithoutCheckSelf(is_login(), '头衔申请取消成功', '取消头衔申请', U('Ucenter/Message/message', array('tabs' => 'system')));
                $this->success('取消成功', U('Ucenter/My/rankVerifyWait'));
            } else {
                $this->error('取消失败');
            }
        }
    }

    public function rankVerify($rank_user_id = null)
    {
        $rank_user_id = intval($rank_user_id);
        $map_already['uid'] = is_login();
        //重新申请头衔
        if ($rank_user_id) {
            $model = D('rank_user')->where(array('id' => $rank_user_id));
            $old_rank_user = $model->field('id,rank_id,reason')->find();
            if (!$old_rank_user) {
                $this->error('请正确选择要重新申请的头衔');
            }
            $this->assign('old_rank_user', $old_rank_user);
            $map_already['id'] = array('neq', $rank_user_id);
            D('Common/Message')->sendMessageWithoutCheckSelf(is_login(), '你将进行头衔的重新申请', '头衔重新申请', 'Ucenter/Message/message', array('tabs' => 'system'));
        }
        $alreadyRank = D('rank_user')->where($map_already)->field('rank_id')->select();
        $alreadyRank = array_column($alreadyRank, 'rank_id');
        if ($alreadyRank) {
            $map['id'] = array('not in', $alreadyRank);
        }
        $map['types'] = 1;
        $rankList = D('rank')->where($map)->select();
        foreach($rankList as &$rank){
            $rank['logo_url'] = get_pic_src(M('picture')->where('id=' . $rank['logo'])->field('path')->getField('path'));
        }
        unset($rank);
        $this->assign('rankList', $rankList);
        $this->assign('tabs', 'rankVerify');

        $str = '{$self.nickname|op_t}';
        $this->setTitle($str . "的头衔申请页");
        $this->setKeywords($str . "，个人头衔，头衔申请");
        $this->setDescription($str . "的头衔申请页");
		$this->_setTab('rank');
        $this->display('rank_verify');
    }

    public function verify($rank_id = null, $reason = null, $rank_user_id = 0)
    {
        $rank_id = intval($rank_id);
        $reason = op_t($reason);
        $rank_user_id = intval($rank_user_id);
        if (!$rank_id) {
            $this->error('请选择要申请的头衔');
        }
        if ($reason == null || $reason == '') {
            $this->error('请填写申请理由');
        }
        $data['rank_id'] = $rank_id;
        $data['reason'] = $reason;
        $data['uid'] = is_login();
        $data['is_show'] = 1;
        $data['create_time'] = time();
        $data['status'] = 0;
        if ($rank_user_id) {
            $model = D('rank_user')->where(array('id' => $rank_user_id));
            if (!$model->select()) {
                $this->error('请正确选择要重新申请的头衔');
            }
            $result = D('rank_user')->where(array('id' => $rank_user_id))->save($data);
        } else {
            $result = D('rank_user')->add($data);
        }
        if ($result) {
            D('Common/Message')->sendMessageWithoutCheckSelf(is_login(), '头衔申请成功,等待管理员审核', '头衔申请', 'Ucenter/Message/message', array('tabs' => 'system'));
            $this->success('申请成功,等待管理员审核', U('Ucenter/My/rankVerify'));
        } else {
            $this->error('申请失败');
        }
    }
	
	public function detail($page = 1,$s = 0,$r = 20)
    {
		$uid = is_login();
    	switch($s){
    		case 1:
    			$map['model'] = 'game';
    			break;
    		case 2:
    			$map['model'] = 'task';
    			break;
    		case 3:
    			$map['model'] = 'tmall';
    			break;
    		case 4:
    			$map['model'] = 'dama';
    			break;
    		default:
    			break;
    	}
    	$map['uid'] = $uid;
    	$map['status'] = 1;
    	$useraccount = D('User/UserScoreLog')->where($map)->order('id desc')->page($page, $r)->select();
    	$totalCount = D('User/UserScoreLog')->where($map)->count();
    	
		$scoreModel = D('User/Score');
		$scores = $scoreModel->getTypeList(array('status'=>1));
			$this->assign('scoretypelist', $scores);
			foreach ($scores as &$v) {
				$v['value'] = $scoreModel->getUserScore(is_login(), $v['id']);
			}
			unset($v);
		$this->assign('scores', $scores);
		
    	$this->assign('r', $r);
    	$this->assign('accountdetail', $useraccount);
    	$this->assign('totalPageCount', $totalCount);
		$this->_setTab('account');
		$this->setTitle('收入明细');
    	$this->assign('tab_s',$s);
        $this->display();
    }
    

}