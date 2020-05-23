<?php
namespace common\api;
use user\model\UcenterMemberModel;

class UserApi {

    /**
     * 注：appname及之后的参数，一般情况下无需填写
     * @param array  $to_uid 接受消息的用户ID
     * @param int    $type 消息类型，0系统，1用户，2应用
     * @param string $mod 模型名称
     * @param number $aAction inc，dec，to
     * @param string $aMark gold_coin
     * @param string $aValue 0
     * @param string $content 内容
     * @param string $title 标题，默认为  您有新的消息
     * @param        $url 链接地址，不提供则默认进入消息中心
     * @param int    $from_uid 发起消息的用户，根据用户自动确定左侧图标，如果为用户，则左侧显示头像
     
     * @param string $appname 应用名，默认不需填写，如果填写了就必须实现对应的消息处理模型，例如贴吧里面可以基于某个回复开启聊天
     * @param string $apptype 同上，应用里面的一个标识符
     * @param int    $source_id 来源ID，通过来源ID获取基于XX聊天的来源信息
     * @param int    $find_id 查找ID，通过查找ID获得标识ID
     */
    public function sendReward($to_uid, $type = 0, $mod, $aAction = 'inc', $aMark, $aValue = 0, $title = '您有新的短消息', $content = '', $from_uid = 0,  $url = '',  $appname = '', $apptype = '', $source_id = 0, $find_id = 0)
    {
        $action_ip = get_client_ip(1);
        //$to_uid = intval($to_uid);

        $res = D('User/Score')->setUserScore($to_uid, $aAction, $aMark, $aValue);
        if($res){
            D('User/UserScoreLog')->sendmingxiWithoutCheckSelf($to_uid, $type , $mod, $aAction, $aMark, $aValue, $content);
            D('Common/Message')->sendMessageWithoutCheckSelf($to_uid, $title, $content);
            $return = true;
            return $return;
        }
        //返回成功结果
        //return 1;
    }

    /**
     * 处理上线提成方法
     * @param unknown $owner_id 下线基础用户ID
     * @param unknown $model 数据表名 或 模块名 或 模型名
     * 
     * @param unknown $tage_id 任务ID
     * @param unknown $aMark 奖励积分字段类型
     * @param number $aValue 奖励积分数量
     * @param string $title 标题备注
     * @param string $content 内容备注
     * @auth 朝兮夕兮
     */
    public function processMember($owner_id, $model, $is_model, $tage_id, $aMark, $aValue = 0, $title = '', $content =''){
        $action_ip = get_client_ip(1);
        $ucenterMemberModel = D('User/UcenterMember');
        if(!is_numeric($owner_id)){
            return false;
        }
        switch($is_model){
            case "module":
                $union_rules = M('AuthGroup')->where(array('status'=>1,'type' => 1))->Field('union_rules,id,title')->select();
                foreach($union_rules as &$v){
                    $union_new = json_decode($v['union_rules'], true);
                    $union_rule[$v['id']] = $union_new[$model];
                }

                break;
            case "table":
                $tage = D($model)->where('id = '.$tage_id)->getField('tage');
                $union_rule = json_decode($tage,true);
                break;
            default:
                break;
        }
        $mem = D('UcenterMember')->where('id = '.$owner_id .' and status = 1')->find();
        $ups_type = modC('UPS_TYPE_MODE','id','USERCONFIG');
        
        if($mem){
            switch($ups_type){
                case 'id':
                    $mem_one = $mem["ups_one"];
                    $mem_two = $mem["ups_two"];
                    $mem_three = $mem["ups_three"];
                    $mem_four = $mem["ups_four"];
                    $mem_five = $mem["ups_five"];
                    $mem_six = $mem["ups_six"];
                    $mem_seven = $mem["ups_seven"];
                    break;
                case 'username':
                    $mem_one = $ucenterMemberModel->where(array('username'=>$mem['ups_one']))->getfield('id');
                    $mem_two = $ucenterMemberModel->where(array('username'=>$mem['ups_two']))->getfield('id');
                    $mem_three = $ucenterMemberModel->where(array('username'=>$mem['ups_three']))->getfield('id');
                    $mem_four = $ucenterMemberModel->where(array('username'=>$mem['ups_four']))->getfield('id');
                    $mem_five = $ucenterMemberModel->where(array('username'=>$mem['ups_five']))->getfield('id');
                    $mem_six = $ucenterMemberModel->where(array('username'=>$mem['ups_six']))->getfield('id');
                    $mem_seven = $ucenterMemberModel->where(array('username'=>$mem['ups_seven']))->getfield('id');
                    break;
            }
            
            if($mem_one){
                $authgroup_info = AuthGroupModel::getUserGroup($mem_one);
                $group_id = $authgroup_info[0]['group_id'];
                $new_gold = ($union_rule[$group_id][child][1]/100) * $aValue;
                $title_one = $title."，一级上线提成奖励：".$new_gold;
                $content_one = $content."，一级上线提成奖励：".$new_gold;
                $this->sendReward($mem_one,0, $model, 'inc', $aMark, $new_gold, $title_one, $content_one);
                unset($title_one);
                unset($content_one);
                unset($new_gold);
                unset($group_id);
                unset($authgroup_info);
            }
            if($mem_two){
                $authgroup_info = AuthGroupModel::getUserGroup($mem_two);
                $group_id = $authgroup_info[0]['group_id'];
                $new_gold = ($union_rule[$group_id][child][2]/100) * $aValue;
                $title_two = $title."，二级上线提成奖励：".$new_gold;
                $content_two = $content."，二级上线提成奖励：".$new_gold;
                $this->sendReward($mem_two,0, $model, 'inc', $aMark, $new_gold, $title_two, $content_two);
                unset($title_two);
                unset($content_two);
                unset($new_gold);
                unset($group_id);
                unset($authgroup_info);
            }
            if($mem_three){
                $authgroup_info = AuthGroupModel::getUserGroup($mem_three);
                $group_id = $authgroup_info[0]['group_id'];
                $new_gold = ($union_rule[$group_id][child][3]/100) * $aValue;
                $title_three = $title."，三级上线提成奖励：".$new_gold;
                $content_three = $content."，三级上线提成奖励：".$new_gold;
                $this->sendReward($mem_three,0, $model, 'inc', $aMark, $new_gold, $title_three, $content_three);
                unset($title_three);
                unset($content_three);
                unset($new_gold);
                unset($group_id);
                unset($authgroup_info);
            }
            if($mem_four){
                $authgroup_info = AuthGroupModel::getUserGroup($mem_four);
                $group_id = $authgroup_info[0]['group_id'];
                $new_gold = ($union_rule[$group_id][child][4]/100) * $aValue;
                $title_four = $title."，四级上线提成奖励：".$new_gold;
                $content_four = $content."，四级上线提成奖励：".$new_gold;
                $this->sendReward($mem_four,0, $model, 'inc', $aMark, $new_gold, $title_four, $content_four);
                unset($title_four);
                unset($content_four);
                unset($new_gold);
                unset($group_id);
                unset($authgroup_info);
            }
            if($mem_five){
                $authgroup_info = AuthGroupModel::getUserGroup($mem_five);
                $group_id = $authgroup_info[0]['group_id'];
                $new_gold = ($union_rule[$group_id][child][5]/100) * $aValue;
                $title_five = $title."，五级上线提成奖励：".$new_gold;
                $content_five = $content."，五级上线提成奖励：".$new_gold;
                $this->sendReward($mem_five,0, $model, 'inc', $aMark, $new_gold, $title_five, $content_five);
                unset($title_five);
                unset($content_five);
                unset($new_gold);
                unset($group_id);
                unset($authgroup_info);
            }
            if($mem_six){
                $authgroup_info = AuthGroupModel::getUserGroup($mem_six);
                $group_id = $authgroup_info[0]['group_id'];
                $new_gold = ($union_rule[$group_id][child][6]/100) * $aValue;
                $title_six = $title."，六级上线提成奖励：".$new_gold;
                $content_six = $content."，六级上线提成奖励：".$new_gold;
                $this->sendReward($mem_six,0, $model, 'inc', $aMark, $new_gold, $title_six, $content_six);
                unset($title_six);
                unset($content_six);
                unset($new_gold);
                unset($group_id);
                unset($authgroup_info);
            }
            if($mem_seven){
                $authgroup_info = AuthGroupModel::getUserGroup($mem_seven);
                $group_id = $authgroup_info[0]['group_id'];
                $new_gold = ($union_rule[$group_id][child][7]/100) * $aValue;
                $title_seven = $title."，七级上线提成奖励：".$new_gold;
                $content_seven = $content."，七级上线提成奖励：".$new_gold;
                $this->sendReward($mem_seven,0, $model, 'inc', $aMark, $new_gold, $title_seven, $content_seven);
                unset($title_seven);
                unset($content_seven);
                unset($new_gold);
                unset($group_id);
                unset($authgroup_info);
            }
            return true;
        }else{
            return false;
        }
    }
    /**
     * 
     * @param unknown $to_uid
     * @param unknown $pay_type
     * @param number $pay_num
     * @param unknown $content
     * @param unknown $title
     * @param number $action_ip
     * @param number $type
     * @param number $purse
     * @param string $purse_type
     * @param string $appname
     * @param number $apptype
     * @return unknown
     */
    public function sendPayLog($to_uid, $type = 0, $pay_action, $pay_type, $pay_num = 0, $title, $content, $purse = 0, $purse_type = NULL, $appname ='', $apptype = 0){
        $r = D('User/UserPayLog')->sendPayLogWithoutCheckSelf($to_uid, $type, $pay_action, $pay_type, $pay_num, $content, $title, $purse, $purse_type, $appname, $apptype);
        return $r;
    }
}