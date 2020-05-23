<?php
namespace user\model;
use think\Model;

class MemberModel extends Model
{
    /* 用户模型自动完成 */
    protected $_auto = array(
        array('update_time', NOW_TIME),
        array('money', 0, self::MODEL_INSERT),
        array('score', 0, self::MODEL_INSERT),
        array('gold_coin', 0, self::MODEL_INSERT),
        array('pos_province', 0, self::MODEL_INSERT),
        array('pos_city', 0, self::MODEL_INSERT),
        array('pos_district', 0, self::MODEL_INSERT),
        array('pos_community', 0, self::MODEL_INSERT),
    );

    protected $_validate = array(
        array('signature', '0,100', -1, self::EXISTS_VALIDATE, 'length'),

        array('nickname', 'checkNickname', -33, self::EXISTS_VALIDATE, 'callback'), //昵称长度不合法
        array('nickname', 'checkDenyNickname', -31, self::EXISTS_VALIDATE, 'callback'), //昵称禁止注册
        array('nickname', 'checkNickname', -32, self::EXISTS_VALIDATE, 'callback'),
        array('nickname', '', -30, self::EXISTS_VALIDATE, 'unique'), //昵称被占用
    );

    protected $insertField = 'nickname,sex,birthday,qq,signature'; //新增数据时允许操作的字段
    protected $updateField = 'nickname,sex,birthday,qq,signature,money,score,gold_coin,pos_province,pos_city,pos_district,pos_community'; //编辑数据时允许操作的字段

    /**
     * 检测用户名是不是被禁止注册
     * @param  string $nickname 昵称
     * @return boolean          ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyNickname($nickname)
    {
        /* 检查系统禁止注册词*/
    	$denyName = M("Config")->where(array('name' => 'USER_NAME_BAOLIU'))->getField('value');
        if ($denyName != '') {
            $denyName = explode(',', $denyName);
            foreach ($denyName as $val) {
                if (!is_bool(strpos($nickname, $val))) {
                    return false;
                }
            }
        }
        return true;
    }

    protected function checkNickname($nickname)
    {
        //如果用户名中有空格，不允许注册
        if (strpos($nickname, ' ') !== false) {
            return false;
        }
        preg_match('/^(?!_|\s\')[A-Za-z0-9_\x80-\xff\s\']+$/', $nickname, $result);

        if (!$result) {
            return false;
        }
        return true;
    }
	
	/**
     * 验证昵称长度
     * @param $nickname
     * @return bool
     */
    protected function checkNicknameLength($nickname)
    {
        $length = mb_strlen($nickname, 'utf-8'); // 当前数据长度
        if ($length < modC('NICKNAME_MIN_LENGTH',2,'USERCONFIG') || $length > modC('NICKNAME_MAX_LENGTH',32,'USERCONFIG')) {
            return false;
        }
        return true;
    }

    public function registerMember($nickname = '')
    {
        //在当前应用中注册用户
        if ($user = $this->create(array('nickname' => $nickname))) {
            $uid = $this->add($user);
            if (!$uid) {
                $this->error = '前台用户信息注册失败，请重试！';
                return false;
            }
            //$this->initFollow($uid);
            return $uid;
        } else {
            return $this->getError(); //错误详情见自动验证注释
        }
    }

    /**
     * 注销当前用户
     * @return void
     */
    public function logout()
    {
        session('_AUTH_LIST_'.get_uid().'1',null);
        session('_AUTH_LIST_'.get_uid().'2',null);
        session('user_auth', null);
        session('user_auth_sign', null);
        cookie('QN_LOGGED_USER_'.C('DATA_AUTH_KEY'), NULL);
    }

    /**
     * 设置用户默认基本信息
     * @param $group_id
     * @param $uid
     */
    public function initUser($group_id, $uid)
    {
        $authGroupModel = D('AuthGroup');
        $authGroupAccessModel = D('AuthGroupAccess');
        $have_user_group_id = $authGroupAccessModel->where(array('uid' => $uid,'group_id'=>$group_id))->find();
        if(!empty($have_user_group_id)){
            return false;
        }
        $authGroup = $authGroupModel->where(array('id' => $group_id))->find();
        $user_group = array('uid' => $uid, 'group_id' => $group_id,'start_time'=>time(),'end_time'=>time()+$authGroup['day']*3600*24);
        if ($authGroup['audit']) { //该角色需要审核
            $user_group['status'] = 2; //未审核
        } else {
            $user_group['status'] = 1;
        }
        $result = $authGroupAccessModel->add($user_group);
        if (empty($authGroup['audit']) && $result) { //该角色不需要审核且已新增成功加载默认配置
            $authGroupConfigModel = D('AuthGroupConfig');
            $map['group_id'] = $group_id;
            $map['name'] = array('in', array('score', 'rank'));
            $config = $authGroupConfigModel->where($map)->select();
            $config = array_combine(array_column($config, 'name'), $config);

            //默认积分设置
            if (isset($config['score']['value'])) {
                $value = json_decode($config['score']['value'], true);
                $user = $this->where(array('uid' => $uid))->find();
                foreach ($value as $key => $val) {
                    if ($val > 0) {
                        if (isset($user[$key])) {
                            $this->where(array('uid' => $uid))->setInc($key, $val);
                        } else {
                            $this->where(array('uid' => $uid))->setField($key, $val);
                        }
                    }
                }
                unset($val);
            }
            //默认积分设置 end

            //默认头衔设置
            if (isset($config['rank']['value']) && $config['rank']['value'] != '') {
                $ranks = explode(',', $config['rank']['value']);
                if (count($ranks)) {
                    //查询已拥有头衔
                    $rankUserModel = D('RankUser');
                    $have_rank_ids = $rankUserModel->where(array('uid' => $uid))->select();
                    $have_rank_ids = array_column($have_rank_ids, 'rank_id');
                    //查询已拥有头衔 end

                    $reason = json_decode($config['rank']['data'], true);
                    $rank_user['uid'] = $uid;
                    $rank_user['create_time'] = time();
                    $rank_user['status'] = 1;
                    $rank_user['is_show'] = 1;
                    $rank_user['reason'] = $reason['reason'];
                    $rank_user_list = array();
                    foreach ($ranks as $val) {
                        if ($val != '' && !in_array($val, $have_rank_ids)) { //去除已拥有头衔
                            $rank_user['rank_id'] = $val;
                            $rank_user_list[] = $rank_user;
                        }
                    }
                    unset($val);
                    $rankUserModel->addAll($rank_user_list);
                }
            }
            //默认头衔设置 end
        }
        return $result;
    }

    // private function initFollow($uid=0)
    // {
    //     if($uid!=0){
    //         $followModel=D('Common/Follow');
    //         $follow=modC('NEW_USER_FOLLOW','','USERCONFIG');
    //         $fans=modC('NEW_USER_FANS','','USERCONFIG');
    //         $friends=modC('NEW_USER_FRIENDS','','USERCONFIG');
    //         if($follow!=''){
    //             $follow=explode(',',$follow);
    //             foreach($follow as $val){
    //                 if(query_user('uid',$val)){
    //                     $followModel->addFollow($uid,$val);
    //                 }
    //             }
    //             unset($val);
    //         }
    //         if($fans!=''){
    //             $fans=explode(',',$fans);
    //             foreach($fans as $val){
    //                 if(query_user('uid',$val)){
    //                     $followModel->addFollow($val,$uid);
    //                 }
    //             }
    //             unset($val);
    //         }
    //         if($friends!=''){
    //             $friends=explode(',',$friends);
    //             foreach($friends as $val){
    //                 if(query_user('uid',$val)){
    //                     $followModel->addFollow($val,$uid);
    //                     $followModel->addFollow($uid,$val);
    //                 }
    //             }
    //             unset($val);
    //         }
    //     }
    //     return true;
    // }
    /**
     * 同步登陆时添加用户信息
     */
    public function addSyncData($uid, $info)
    {
        //去除特殊字符。
        $data['nickname'] = preg_replace('/[^A-Za-z0-9_\x80-\xff\s\']/', '', $info['nick']);
        // 截取字数
        $data['nickname'] = mb_substr($data['nickname'], 0, 32, 'utf-8');
        // 为空则随机生成
        if (empty($data['nickname'])) {
            $data['nickname'] = $this->rand_nickname();
        } else {
            if ($this->where(array('nickname' => $data['nickname']))->select()) {
                $data['nickname'] .= '_' . $uid;
            }
        }
        $data['sex'] = $info['sex'];
        $data = $this->validate(
            array('signature', '0,100', -1, self::EXISTS_VALIDATE, 'length'),
            /* 验证昵称 */
            array('nickname', 'checkDenyNickname', -31, self::EXISTS_VALIDATE, 'callback'), //昵称禁止注册
            array('nickname', 'checkNickname', -32, self::EXISTS_VALIDATE, 'callback'),
            array('nickname', '', -30, self::EXISTS_VALIDATE, 'unique'))->create($data);
        $data['uid'] = $uid;
        $res = $this->add($data);
        return $res;
    }

    public function rand_nickname()
    {
        $nickname = create_rand(4);
        if ($this->where(array('nickname' => $nickname))->select()) {
            $this->rand_nickname();
        } else {
            return $nickname;
        }
    }
}
