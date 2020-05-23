<?php
namespace home\controller;
use think\Controller;

/**
 * Class PublicController  公共控制器
 * @package Home\Controller
 */
class PublicController extends Controller
{

    //atWhoJson
    public function atWhoJson()
    {
        exit(json_encode($this->getAtWhoUsersCached()));
    }

    private function getAtWhoUsersCached()
    {
        $cacheKey = 'at_who_users';
        $atusers = S($cacheKey);
        if (empty($atusers[get_uid()])) {
            $atusers[get_uid()] = $this->getAtWhoUsers();
            S($cacheKey, $atusers, 600);
        }
        return $atusers[get_uid()];
    }

    /**
     * getAtWhoUsers  获取@列表
     */
    private function getAtWhoUsers()
    {
        //获取能AT的人，UID列表
        $uid = get_uid();
        $follows = D('Follow')->where(array('who_follow' => $uid, 'follow_who' => $uid, '_logic' => 'or'))->select();
        $uids = array();
        foreach ($follows as &$e) {
            $uids[] = $e['who_follow'];
            $uids[] = $e['follow_who'];
        }
        unset($e);
        $uids = array_unique($uids);

        //加入拼音检索
        $users = array();
        foreach ($uids as $uid) {
            $user = query_user(array('nickname', 'id', 'avatar32'), $uid);
            $user['search_key'] = $user['nickname'] . D('PinYin')->Pinyin($user['nickname']);
            $users[] = $user;
        }

        //返回at用户列表
        return $users;
    }


    public function getVideo(){
        $aLink = I('post.link');
        $this->ajaxReturn(array('data'=>D('ContentHandler')->getVideoInfo($aLink)));
    }
	
	public function getmsg(){
		$time = TIME();
		$map['create_time'] = array('egt',$time-10);
		$map['uid'] = is_login();
		$map['status'] = 1;

		$result = D('User/UserScoreLog')->where($map)->field('remark as msg')->order('id desc')->select();
		exit(json_encode(array('messages' => $result,'status'=>1)));
	}
	
    //检测消息返回新聊天状态和系统的消息
    public function getInformation()
    {
        $message = D('Common/Message');
        //取到所有没有提示过的信息
        $haventToastMessages = $message->getHaventToastMessage(is_login());
        $message->setAllToasted(is_login()); //消息中心推送
		
        $new_talks = D('TalkPush')->getAllPush(); //聊天推送
        D('TalkPush')->where(array('uid' => get_uid(), 'status' => 0))->setField('status', 1); //读取到推送之后，自动删除此推送来防止反复推送。
        $new_talk_messages = D('TalkMessagePush')->getAllPush(); //聊天消息推送
        D('TalkMessagePush')->where(array('uid' => get_uid(), 'status' => 0))->setField('status', 1); //读取到推送之后，自动删除此推送来防止反复推送。
        foreach($new_talk_messages as &$message){
            $message=D('TalkMessage')->find($message['source_id']);
            $message['user'] = query_user(array('avatar64', 'uid', 'username'), $message['uid']);
            $message['ctime'] = date('m-d h:i', $message['create_time']);
            $message['avatar64'] = $message['user']['avatar64'];
        }
        exit(json_encode(array('messages' => $haventToastMessages, 'new_talk_messages' => $new_talk_messages, 'new_talks' => $new_talks)));
    }
	
    //设置全部的系统消息为已读
    public function setAllMessageReaded()
    {
        D('Common/Message')->setAllReaded(is_login());
    }
	
    //设置某条系统消息为已读
    public function readMessage($message_id)
    {
        exit(json_encode(array('status' => D('Common/Message')->readMessage($message_id))));
    }

}
