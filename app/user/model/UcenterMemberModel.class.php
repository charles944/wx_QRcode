<?php
namespace user\model;
use think\Model;
use user\model\MemberModel;
require_once(APP_PATH . 'user/conf/config.php');
require_once(APP_PATH . 'user/common/common.php');

//会员模型

class UcenterMemberModel extends Model
{
    /**
     * 数据表前缀
     */
    protected $tablePrefix = UC_TABLE_PREFIX;
    /**
     * 数据库连接
     * @var string
     */
    protected $connection = UC_DB_DSN;
     /* 用户模型自动验证 */
    protected $_validate = array(
        /* 验证用户名 */
        array('username', 'checkUsernameLength', -1, self::EXISTS_VALIDATE,'callback'), //用户名长度不合法
        array('username', 'checkDenyMember', -2, self::EXISTS_VALIDATE, 'callback'), //用户名禁止注册
        array('username', 'checkUsername', -20, self::EXISTS_VALIDATE, 'callback'),
        array('username', '', -3, self::EXISTS_VALIDATE, 'unique'), //用户名被占用
        /* 验证密码 */
        array('password', '6,30', -4, self::EXISTS_VALIDATE, 'length'), //密码长度不合法
        /* 验证邮箱 */
        array('email', 'email', -5, self::EXISTS_VALIDATE), //邮箱格式不正确
        array('email', '4,32', -6, self::EXISTS_VALIDATE, 'length'), //邮箱长度不合法
        array('email', 'checkDenyEmail', -7, self::EXISTS_VALIDATE, 'callback'), //邮箱禁止注册
        array('email', '', -8, self::EXISTS_VALIDATE, 'unique'), //邮箱被占用
        /* 验证手机号码 */
        array('mobile', '/^(1[0-9])[0-9]{9}$/', -9, self::EXISTS_VALIDATE), //手机格式不正确 TODO:
        array('mobile', 'checkDenyMobile', -10, self::EXISTS_VALIDATE, 'callback'), //手机禁止注册
        array('mobile', '', -11, self::EXISTS_VALIDATE, 'unique'), //手机号被占用
    );
    /* 用户模型自动完成 */
    protected $_auto = array(
        array('login', 0, self::MODEL_INSERT),
        array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
        array('reg_time', NOW_TIME, self::MODEL_INSERT),
        array('last_login_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
        array('last_login_time', NOW_TIME, self::MODEL_INSERT),
        array('password', 'think_ucenter_md5', self::MODEL_BOTH, 'function', UC_AUTH_KEY),
        array('update_time', NOW_TIME),
        array('status', 1, self::MODEL_INSERT),
        //array('status', 'getStatus', self::MODEL_BOTH, 'callback'),
    );
    /**
     * 检测用户名是不是被禁止注册
     * @param  string $username 用户名
     * @return boolean          ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyMember($username)
    {
        $denyName=M("Config")->where(array('name' => 'USER_NAME_BAOLIU'))->getField('value');
        if($denyName!=''){
            $denyName=explode(',',$denyName);
            foreach($denyName as $val){
                if(!is_bool(strpos($username,$val))){
                    return false;
                }
            }
        }
        return true;
    }
    /**
     * 检测邮箱是不是被禁止注册
     * @param  string $email 邮箱
     * @return boolean       ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyEmail($email)
    {
        $denyEmail=M("Config")->where(array('name' => 'USER_EMAIL_BAOLIU'))->getField('value');
        if($denyEmail!=''){
            $denyEmail=explode(',',$denyEmail);
            foreach($denyEmail as $val){
                if(!is_bool(strpos($email,$val))){
                    return false;
                }
            }
        }
        return true;
    }
    protected function checkUsername($username)
    {
        //如果用户名中有空格，不允许注册
        if (strpos($username, ' ') !== false) {
            return false;
        }
        preg_match("/^[a-zA-Z0-9_]{4,64}$/", $username, $result);
        if (!$result) {
            return false;
        }
        return true;
    }
	
	/**
     * 验证用户名长度
     * @param $username
     * @return bool
     * @author
     */
    protected function checkUsernameLength($username)
    {
        $length = mb_strlen($username, 'utf-8'); // 当前数据长度
        if ($length < modC('USERNAME_MIN_LENGTH',2,'USERCONFIG') || $length > modC('USERNAME_MAX_LENGTH',32,'USERCONFIG')) {
            return false;
        }
        return true;
    }
	
    /**
     * 检测手机是不是被禁止注册
     * @param  string $mobile 手机
     * @return boolean        ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyMobile($mobile)
    {
        $denyMobile=M("Config")->where(array('name' => 'USER_MOBILE_BAOLIU'))->getField('value');
        if($denyMobile!=''){
            $denyMobile=explode(',',$denyMobile);
            foreach($denyMobile as $val){
                if(!is_bool(strpos($mobile,$val))){
                    return false;
                }
            }
        }
        return true;
    }
    /**
     * 返回用户状态
     * @return integer 用户状态
     */
    public function getStatus($uid)
    {
         $user = $this->field(true)->find($uid);
        if(empty($user)){
            return -2;
        }else{
            return $user['status'];
        }
    }

    /**
     * 注册一个新用户
     * @param  string $username 用户名
     * @param  string $nickname 昵称
     * @param  string $password 用户密码
     * @param  string $email 用户邮箱
     * @param  string $mobile 用户手机号码
     * @return integer          注册成功-用户信息，注册失败-错误编号
     */
    public function register($username, $nickname, $password, $email=null, $mobile=null, $type=1, $ups=null)
    {
    	if(empty($ups)){
	    	$union = cookie('ups_one_invite');
	    	if(!empty($union)){
		    	$TG_UPS = modC('UPS_TYPE_MODE','id','USERCONFIG');
		    	switch($TG_UPS){
		    	    case 'id':
		    	        $map['id'] = $union;
		    	        $user_ups = $this->where($map)->find();
		    	        if (is_array($user_ups) && $user_ups['status']) {
		    	            $ups['ups_one'] = $user_ups['id'];
		    	            $ups['ups_two'] = $user_ups['ups_one'];
		    	            $ups['ups_three'] = $user_ups['ups_two'];
		    	            $ups['ups_four'] = $user_ups['ups_three'];
		    	            $ups['ups_five'] = $user_ups['ups_four'];
		    	            $ups['ups_six'] = $user_ups['ups_five'];
		    	            $ups['ups_seven'] = $user_ups['ups_six'];
		    	        }
		    	        break;
		    	    case 'username':
		    	        $map['username'] = $union;
		    	        $user_ups = $this->where($map)->find();
		    	        if (is_array($user_ups) && $user_ups['status']) {
		    	            $ups['ups_one'] = $user_ups['username'];
		    	            $ups['ups_two'] = $user_ups['ups_one'];
		    	            $ups['ups_three'] = $user_ups['ups_two'];
		    	            $ups['ups_four'] = $user_ups['ups_three'];
		    	            $ups['ups_five'] = $user_ups['ups_four'];
		    	            $ups['ups_six'] = $user_ups['ups_five'];
		    	            $ups['ups_seven'] = $user_ups['ups_six'];
		    	        }
		    	        break;
		    	}
	    	}
    	}
		//默认邮箱以及手机认证
		$ver['email_ver'] = 0;
		$ver['mobile_ver'] = 0;
		
		if (modC('EMAIL_VERIFY_TYPE', 0, 'USERCONFIG') == 2 && $type == 2) {
			$ver['email_ver'] = 1;
		}else{
			$ver['email_ver'] = 0;
		}
		if (modC('MOBILE_VERIFY_TYPE', 0, 'USERCONFIG') == 1 && $type == 3) {
			$ver['mobile_ver'] = 1;
		}else{
			$ver['mobile_ver'] = 0;
		}
    	
    	if(is_array($ups) && $ups != null){
    		$data = array(
				'username'  => $username,
				'password'  => $password,
				'email'     => $email,
				'email_ver' => $ver['email_ver'],
				'mobile'    => $mobile,
				'mobile_ver'=> $ver['mobile_ver'],
				'type' 		=> $type,
				'ups_one'   => $ups['ups_one'],
				'ups_two'   => $ups['ups_two'],
				'ups_three' => $ups['ups_three'],
				'ups_four'  => $ups['ups_four'],
				'ups_five'  => $ups['ups_five'],
				'ups_six'   => $ups['ups_six'],
				'ups_seven' => $ups['ups_seven'],
    		);
    	}else{
    		$data = array(
				'username'  => $username,
				'password'  => $password,
				'email'     => $email,
				'email_ver' => $ver['email_ver'],
				'mobile'    => $mobile,
				'mobile_ver'=> $ver['mobile_ver'],
				'type'      => $type,
    		);
    	}
		
    	//验证手机
    	if (empty($data['mobile'])) unset($data['mobile']);
    	if (empty($data['username'])) $data['username'] = $this->rand_username();
    	if (empty($data['email'])) unset($data['email']);
		if (empty($nickname)) $nickname = $this->rand_username();
		
    	// 添加用户
    	$usercenter_member = $this->create($data);
    	if ($usercenter_member) {
    		$result = D('User/Member')->registerMember($nickname);
    		if ($result > 0) {
    			$usercenter_member['id'] = $result;
    			$uid = $this->add($usercenter_member);
    			if ($uid === false) {
    				//如果注册失败，则回去Memeber表删除掉错误的记录
    				D('User/Member')->where(array('uid' => $result))->delete();
    			}
    			action_log('reg','ucenter_member',1,1);
    			return $uid ? $uid : 0; //0-未知错误，大于0-注册成功
    		} else {
    			return $result;
    		}
    	} else {
    		return $this->getError(); //错误详情见自动验证注释
    	}
    }
    /**
     * 用户登录认证
     * @param  string $username 用户名
     * @param  string $password 用户密码
     * @param  integer $type 用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @return integer           登录成功-用户ID，登录失败-错误编号
     */
    public function login($username, $password, $type = 1, $remember = 1)
    {
        $map = array();
        switch ($type) {
            case 1:
                $map['username'] = $username;
                break;
            case 2:
                $map['email'] = $username;
                break;
            case 3:
                $map['mobile'] = $username;
                break;
            case 4:
                $map['id'] = $username;
                break;
            default:
                return 0; //参数错误
        }
        $user = $this->where($map)->find();
        $return = check_action_limit('input_password','ucenter_member',$user['id'],$user['id']);
        if($return && !$return['state']){
        	return $return['info'];
        }
        if (is_array($user) && $user['status']>-1) {
            /* 验证用户密码 */
            if (think_ucenter_md5($password, UC_AUTH_KEY) === $user['password']) {
                session('temp_login_uid', $user['id']);
                session('temp_login_group_id', $user['last_login_group']);

                if ($user['status'] == 3) { /*判断是否激活*/
                    return -3;
                }

                if (1 != $user['status']) {
                    return -4; //'当前账号已禁用！有问题请联系客服'; //应用级别禁用
                }

                /* 登录用户 */
                $this->autoLogin($user, $remember);

                session('temp_login_uid', null);
                session('temp_login_group_id', null);
                //记录行为
                action_log('user_login', 'member', $user['id'], $user['id']);
                return $user['id']; //登录成功，返回用户ID
            } else {
            	action_log('input_password','ucenter_member',$user['id'],$user['id']);
                return -2; //密码错误
            }
        } else {
            return -1; //用户不存在或被删除
        }
    }

    public function checkLogin($uid){
        $user = $this->field(true)->find($uid);
        if(empty($user)){
            session('_AUTH_LIST_'.get_uid().'1',null);
            session('_AUTH_LIST_'.get_uid().'2',null);
            session('user_auth', null);
            session('user_auth_sign', null);
            cookie('QN_LOGGED_USER_'.C('DATA_AUTH_KEY'), NULL);
            $this->error('用户登录信息失效！');
        }

        if ($user['status'] == 3) {
            session('temp_login_uid', $uid);
            session('temp_login_group_id', $user['last_login_group']);

            header('Content-Type:application/json; charset=utf-8');
            $data['status'] = 1;
            $data['url'] = U('home/member/activate');
            redirect($data['url']);
        }

        if (1 != $user['status']) {
            $this->error('用户已被禁用！');
        }

        /* 登录用户 */
        $this->autoLogin($user, 1);

        session('temp_login_uid', null);
        session('temp_login_group_id', null);
        //记录行为
        action_log('user_login', 'member', $uid, $uid);
    }

    /**
     * 自动登录用户
     * @param  integer $user 用户信息数组
     */
    private function autoLogin($user, $remember = false)
    {
        /* 新增登录ip检测机制 start*/
        $logindata = array(
            'uid' => $user['id'],
            'username' => $user['username'],
            'nickname' => get_nickname($user['id']),
            'create_time' => NOW_TIME,
            'login_ip' => get_client_ip(1),
        );
        D('UserLoginLog')->add($logindata);
        $reg_remark = '';
        $login_remark = '';
        $isloginout = D('UserLoginLog')->where('login_ip ='.get_client_ip(1).' and uid <>'.$user['id'])->select();
        if($isloginout){
            foreach($isloginout as &$v){
                $login_remark .= "【".date('Y-m-d H:i:s', $v['create_time'])." - （".$v['nickname']." - [".$v['uid']."]）】；<br>";
            }
        }
        $isregout = $this->where('reg_ip ='.get_client_ip(1).' and uid <>'.$user['id'])->select();
        if($isregout){
            foreach($isregout as &$v){
                $reg_remark .= "【".$v['uid']."】";
            }
        }
        if(!empty($login_remark) || !empty($reg_remark)){
            if(!empty($login_remark)){
                $remark .= "[登录]与<br>".$login_remark."的登录IP【".long2ip(get_client_ip(1))."】相同。<br>";
            }
            if(!empty($reg_remark)){
                $remark .= "[登录]与<br>".$reg_remark."<br>的注册IP【".long2ip(get_client_ip(1))."】相同。";
            }
            $outdata = array(
                'uid' => $user['id'],
                'username' => $user['username'],
                'nickname' => get_nickname($user['id']),
                'remark' => $remark,
                'create_time' => NOW_TIME,
            );
            D('UserOutLog')->add($outdata);
        }
        /* 新增登录ip检测机制 end*/

        //判断角色用户是否审核
        $map['uid'] = $user['id'];
        $map['status'] = 1;
        $auth_group = M('AuthGroupAccess')->where($map)->order('group_id desc')->limit(1)->find();
        if($auth_group['group_id'] == $user['last_login_group']){
            $group_data = M('AuthGroup')->where(array('id'=>$auth_group['group_id']))->getField('id,title');
            $last_login_group = $user['last_login_group'];
        }else{
            $group_data = M('AuthGroup')->where(array('id'=>$auth_group['group_id']))->getField('id,title');
            $last_login_group = $auth_group['group_id'];
        }
        //$audit = D('AuthGroupAccess')->where($map)->getField('status');
        //判断角色用户是否审核 end

        /* 更新登录信息 */
        $data = array(
            'id' => $user['id'],
            'login' => array('exp', '`login`+1'),
            'last_login_time' => NOW_TIME,
            'last_login_ip' => get_client_ip(1),
            'last_login_group' => $last_login_group,
        );
        $this->save($data);

        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'uid' => $user['id'],
            'username' => $user['username'],
            'nickname' => get_nickname($user['id']),
            'last_login_time' => NOW_TIME,
            'group_id' => $last_login_group,
            'group_name' => $group_data[$last_login_group],
            'audit' => $auth_group['status'],
        );

        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));
        if ($remember) {
            $user1 = D('user_token')->where('uid=' . $user['id'])->find();
            $token = $user1['token'];
            if ($user1 == null) {
                $token = build_auth_key();
                $data['token'] = $token;
                $data['time'] = time();
                $data['uid'] = $user['id'];
                D('user_token')->add($data);
            }
        }

        if (!$this->getCookieUid() && $remember) {
            $expire = 3600 * 24 * 7;
            cookie('QN_LOGGED_USER_'.C('DATA_AUTH_KEY'), $this->jiami($this->change() . ".{$user['uid']}.{$token}"), $expire);
        }
    }

    public function getCookieUid()
    {
        static $cookie_uid = null;
        if (isset($cookie_uid) && $cookie_uid !== null) {
            return $cookie_uid;
        }
        $cookie = cookie('QN_LOGGED_USER_'.C('DATA_AUTH_KEY'));
        $cookie = explode(".", $this->jiemi($cookie));
        $map['uid'] = $cookie[1];
        $user = D('user_token')->where($map)->find();
        $cookie_uid = ($cookie[0] != $this->change()) || ($cookie[2] != $user['token']) ? false : $cookie[1];
        $cookie_uid = $user['time'] - time() >= 3600 * 24 * 7 ? false : $cookie_uid;
        return $cookie_uid;
    }

    /**
     * 加密函数
     * @param string $txt 需加密的字符串
     * @param string $key 加密密钥，默认读取SECURE_CODE配置
     * @return string 加密后的字符串
     */
    private function jiami($txt, $key = null)
    {
        empty($key) && $key = $this->change();

        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-=_";
        $nh = rand(0, 64);
        $ch = $chars[$nh];
        $mdKey = md5($key . $ch);
        $mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
        $txt = base64_encode($txt);
        $tmp = '';
        $i = 0;
        $j = 0;
        $k = 0;
        for ($i = 0; $i < strlen($txt); $i++) {
            $k = $k == strlen($mdKey) ? 0 : $k;
            $j = ($nh + strpos($chars, $txt [$i]) + ord($mdKey[$k++])) % 64;
            $tmp .= $chars[$j];
        }
        return $ch . $tmp;
    }

    /**
     * 解密函数
     * @param string $txt 待解密的字符串
     * @param string $key 解密密钥，默认读取SECURE_CODE配置
     * @return string 解密后的字符串
     */
    private function jiemi($txt, $key = null)
    {
        empty($key) && $key = $this->change();

        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-=_";
        $ch = $txt[0];
        $nh = strpos($chars, $ch);
        $mdKey = md5($key . $ch);
        $mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
        $txt = substr($txt, 1);
        $tmp = '';
        $i = 0;
        $j = 0;
        $k = 0;
        for ($i = 0; $i < strlen($txt); $i++) {
            $k = $k == strlen($mdKey) ? 0 : $k;
            $j = strpos($chars, $txt[$i]) - $nh - ord($mdKey[$k++]);
            while ($j < 0) {
                $j += 64;
            }
            $tmp .= $chars[$j];
        }

        return base64_decode($tmp);
    }

    private function change()
    {
        preg_match_all('/\w/', C('DATA_AUTH_KEY'), $sss);
        $str1 = '';
        foreach ($sss[0] as $v) {
            $str1 .= $v;
        }
        return $str1;
    }

    public function getLocal($username, $password)
    {
		$type = check_username($username);
		
        $map = array();
        switch ($type) {
            case 1:
                $map['username'] = $username;
                break;
            case 2:
                $map['email'] = $username;
                break;
            case 3:
                $map['mobile'] = $username;
                break;
            case 4:
                $map['id'] = $username;
                break;
            default:
                return 0; //参数错误
        }
        /* 获取用户数据 */
        $user = $this->where($map)->find();
        if (is_array($user) && $user['status']) {
            /* 验证用户密码 */
            if (think_ucenter_md5($password, UC_AUTH_KEY) === $user['password']) {
                return $user; //登录成功，返回用户ID
            } else {
                return false; //密码错误
            }
        } else {
            return false; //用户不存在或被禁用
        }
    }
    /**
     * 用户密码找回认证
     * @param  string $username 用户名
     * @param  string $password 用户密码
     * @param  integer $type 用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @return integer           登录成功-用户ID，登录失败-错误编号
     */
    public function lomi($username, $email)
    {
        $map = array();
        $map['username'] = $username;
        $map['email'] = $email;
        /* 获取用户数据 */
        $user = $this->where($map)->find();
        if (is_array($user)) {
            /* 验证用户 */
            //if($user['last_login_time']){
            //return $user['last_login_time']; //成功，返回用户最后登录时间
            return $user; //成功，返回用户最后登录时间
            //}else{
            //return $user['reg_time']; //返回用户注册时间
            //return -1; //成功，返回用户最后登录时间
            //}
        } else {
            return -2; //用户和邮箱不符
        }
    }
	
    //用户密码找回认证2
    public function reset($uid)
    {
        $map = array();
        $map['id'] = $uid;
        /* 获取用户数据 */
        $user = $this->where($map)->find();
        if (is_array($user)) {
            return $user; //成功，返回用户数据
        } else {
            return -2; //用户和邮箱不符
        }
    }
	
    //根据IP获取用户最后注册时间
    public function infos($regip)
    {
        $map['reg_ip'] = $regip;
        $user = $this->where($map)->max('reg_time');
        if ($user) {
            return $user;
        } else {
            return -1; //用户不存在或被禁用
        }
    }
	
    // 获取用户信息
    public function info($uid, $is_username = false)
    {
        $map = array();
        if ($is_username) { //通过用户名获取
            $map['username'] = $uid;
        } else {
            $map['id'] = $uid;
        }
        $user = $this->where($map)->field('id,username,email,mobile,status')->find();
        if (is_array($user) && $user['status'] = 1) {
            return array($user['id'], $user['username'], $user['email'], $user['mobile']);
        } else {
            return -1; //用户不存在或被禁用
        }
    }
	
    //检测用户信息
    public function checkField($field, $type = 1)
    {
        $data = array();
        switch ($type) {
            case 1:
                $data['username'] = $field;
                break;
            case 2:
                $data['email'] = $field;
                break;
            case 3:
                $data['mobile'] = $field;
                break;
            default:
                return 0; //参数错误
        }
        return $this->create($data) ? 1 : $this->getError();
    }
	
    //更新用户登录信息
    protected function updateLogin($uid)
    {
        $data = array(
            'id' => $uid,
            'last_login_time' => NOW_TIME,
            'last_login_ip' => get_client_ip(1),
        );
        $this->save($data);
    }
	
    //更新用户信息
    public function updateUserFields($uid, $password, $data)
    {
        if (empty($uid) || empty($password) || empty($data)) {
            $this->error = '参数错误！25';
            return false;
        }
        //更新前检查用户密码
        if (!$this->verifyUser($uid, $password)) {
            $this->error = '验证出错：密码不正确！';
            return false;
        }
        //更新用户信息
        $data = $this->create($data, 2); //指定此处为更新数据
        if ($data) {
            return $this->where(array('id' => $uid))->save($data);
        }
        return false;
    }
	
    //重置用户密码
    public function updateUserFieldss($uid, $data)
    {
        if (empty($uid) || empty($data)) {
            $this->error = '参数错误！26';
            return false;
        }
        //更新用户信息
        $data = $this->create($data, 2);
        if ($data) {
            return $this->where(array('id' => $uid))->save($data);
        }
        return false;
    }
	
    //验证用户密码
    public function verifyUser($uid, $password_in)
    {
        $password = $this->getFieldById($uid, 'password');
        if (think_ucenter_md5($password_in, UC_AUTH_KEY) === $password) {
            return true;
        }
        return false;
    }
	
    //修改密码
    public function changePassword($old_password, $new_password)
    {
        //检查旧密码是否正确
        if (!$this->verifyUser(get_uid(), $old_password)) {
            $this->error = -41;
            return false;
        }
        //更新用户信息
        $model = $this;
        $data = array('password' => $new_password);
        $data = $model->create($data);
        if (!$data) {
            $this->error = $model->getError();
            return false;
        }
        $model->where(array('id' => get_uid()))->save($data);
        //返回成功信息
        clean_query_user_cache(get_uid(), 'password');//删除缓存
        D('user_token')->where('uid=' . get_uid())->delete();
        return true;
    }
    
    public function getErrorMessage($error_code = null)
    {
        $error = $error_code == null ? $this->error : $error_code;
        switch ($error) {
            case -1:
                $error = '用户名长度必须在'.modC('USERNAME_MIN_LENGTH',2,'USERCONFIG').'-'.modC('USERNAME_MAX_LENGTH',32,'USERCONFIG').'个字符之间！';
                break;
            case -2:
                $error = '用户名被禁止注册！';
                break;
            case -3:
                $error = '用户名被占用！';
                break;
            case -4:
                $error = '密码长度必须在6-30个字符之间！';
                break;
            case -41:
                $error = '用户旧密码不正确';
                break;
            case -5:
                $error = '邮箱格式不正确！';
                break;
            case -6:
                $error = '邮箱长度必须在4-32个字符之间！';
                break;
            case -7:
                $error = '邮箱被禁止注册！';
                break;
            case -8:
                $error = '邮箱被占用！';
                break;
            case -9:
                $error = '手机格式不正确！';
                break;
            case -10:
                $error = '手机被禁止注册！';
                break;
            case -11:
                $error = '手机号被占用！';
                break;
            case -12:
                $error = '用户名必须以中文或字母开始，只能包含拼音数字，字母，汉字！';
                break;
            case -31:
                $error = '昵称禁止注册';
                break;
            case -33:
                $error = '昵称长度必须在'.modC('NICKNAME_MIN_LENGTH',2,'USERCONFIG').'-'.modC('NICKNAME_MAX_LENGTH',32,'USERCONFIG').'个字符之间！';
                break;
            case -32:
                $error = '昵称不合法';
                break;
            case -30:
                $error = '昵称已被占用';
                break;
            default:
                $error = '未知错误';
        }
        return $error;
    }
	
    public function addSyncData()
    {
        //$data['username'] = $this->rand_username();
        $data['email'] = $this->rand_email();
        $data['password'] = create_rand(10);
        $data['type'] = 2;  // 视作用邮箱注册
        $data1 = $this->create($data);
        $uid = $this->add($data1);
        return $uid;
    }
	
    public function rand_email()
    {
        $email = create_rand(10).'@qn.com';
        if ($this->where(array('email' => $email))->select()) {
            $this->rand_email();
        } else {
            return $email;
        }
    }
	
    public function rand_username()
    {
        $username = create_rand(10);
        if ($this->where(array('username' => $username))->select()) {
            $this->rand_username();
        } else {
            return $username;
        }
    }
}