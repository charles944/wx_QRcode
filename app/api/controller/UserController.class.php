<?php
namespace api\controller;
use user\model\UcenterMemberModel;

class UserController extends ApiController{

	public $info = array(
        'name' => 'user',
        'title' => '用户接口',
        'description' => '用户接口',
        'status' => 1,
        'author' => '靑年',
        'version' => '1.0.0'
    );

    public function install(){
        return true;
    }

    public function uninstall(){
        return true;
    }

    public function _initialize(){
        $this->model = new UcenterMemberModel();
    }

    /**
     * 注册一个新用户
     * @param  string $username 用户名
     * @param  string $password 用户密码
     * @param  string $email    用户邮箱
     * @param  string $mobile   用户手机号码
     * @return integer          注册成功-用户信息，注册失败-错误编号
     */
    public function register($username,$nickname, $password, $email, $ups, $mobile = ''){
        return $this->model->register($username,$nickname, $password, $email, $mobile, 1, $ups);
    }

    /**
     * 用户登录认证
     * @param  string  $username 用户名
     * @param  string  $password 用户密码
     * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @return integer           登录成功-用户ID，登录失败-错误编号
     */
    public function login($username, $password, $type = 1, $ups = null){
        return $this->model->login($username, $password, $type);
    }

    /**
     * 获取用户信息
     * @param  string  $uid         用户ID或用户名
     * @param  boolean $is_username 是否使用用户名查询
     * @return array                用户信息
     */
    public function info($uid, $is_username = false){
        return $this->model->info($uid, $is_username);
    }
    /**
     * 根据用户名和邮箱获取用户数据
     * @param  string  $username 用户名
     * @param  string  $password 用户密码
     * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @return integer           登录成功-用户ID，登录失败-错误编号
     */
    public function lomi($username, $email){
        return $this->model->lomi($username, $email);
    }
    /**
     * 根据用户ID获取用户所以数据
     * @param  string  $username 用户名
     * @param  string  $password 用户密码
     * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @return integer           登录成功-用户ID，登录失败-错误编号
     */
    public function reset($uid){
        return $this->model->reset($uid);
    }
    /**
     * 获取用户信息2
     * @param  string  $uid         用户ID或用户名
     * @param  boolean $is_username 是否使用用户名查询
     * @return array                用户信息
     */
    public function infos($regip){
        return $this->model->infos($regip);
    }
    /**
     * 检测用户名
     * @param  string  $field  用户名
     * @return integer         错误编号
     */
    public function checkUsername($username){
        return $this->model->checkField($username, 1);
    }

    /**
     * 检测邮箱
     * @param  string  $email  邮箱
     * @return integer         错误编号
     */
    public function checkEmail($email){
        return $this->model->checkField($email, 2);
    }

    /**
     * 检测手机
     * @param  string  $mobile  手机
     * @return integer         错误编号
     */
    public function checkMobile($mobile){
        return $this->model->checkField($mobile, 3);
    }

    /**
     * 更新用户信息
     * @param int $uid 用户id
     * @param string $password 密码，用来验证
     * @param array $data 修改的字段数组
     * @return true 修改成功，false 修改失败
     * @author 乾坤网络有限公司
     */
    public function updateInfo($uid, $password, $data){
        if($this->model->updateUserFields($uid, $password, $data) !== false){
            $return['status'] = true;
        }else{
            $return['status'] = false;
            $return['info'] = $this->model->getError();
        }
        return $return;
    }
    /**
     * 重置用户密码2
     * @param int $uid 用户id
     * @param string $password 密码，用来验证
     * @param array $data 修改的字段数组
     * @return true 修改成功，false 修改失败
     * @author 乾坤网络有限公司
     */
    public function updateInfos($uid, $data){
        if($this->model->updateUserFieldss($uid, $data) !== false){
            $return['status'] = true;
        }else{
            $return['status'] = false;
            $return['info'] = $this->model->getError();
        }
        return $return;
    }

    public function addSyncData(){
        return $this->model->addSyncData();
    }


    public function changePassword($old_password, $new_password)
    {
        $this->requireLogin();
        //检查旧密码是否正确
        $this->verifyPassword($this->getUid(), $old_password);
        //更新用户信息
        $model = D('User/UcenterMember');
        $data = array('password' => $new_password);
        if (!$data) {
            $this->apiError(0, $this->getRegisterErrorMessage($model->getError()));
        }
        $model->where(array('id' => $this->getUid()))->save($data);
        //返回成功信息
        clean_query_user_cache($this->getUid(),'password');//删除缓存
        D('user_token')->where('uid='.$this->getUid())->delete();

        $this->apiSuccess("密码修改成功");
    }
    
    public function bindSafeCode($loginpwd, $safecode, $old_safecode='' ){
    	$this->requireLogin();
    	//检查旧密码是否正确
    	$this->verifyPassword($this->getUid(), $loginpwd);
    	//更新用户信息
    	$model = D('User/Member');
    	$data = array('safecode' => md5($safecode));
    	if (!$data) {
    		$this->apiError(0, $this->getRegisterErrorMessage($model->getError()));
    	}
    	$model->where(array('uid' => $this->getUid()))->save($data);
    	unset($data);
    	//返回成功信息
    	clean_query_user_cache($this->getUid(),'safecode');//删除缓存
    	//$this->apiSuccess("安全码修改成功");
    	$this->apiSuccess("安全码绑定成功");
    }

    private function getImageFromForm()
    {
        $image = $_FILES['image'];
        if (!$image) {
            $this->apiError(1103, '图像不能为空');
        }
        return $image;
    }

    public function getProfile($uid = null)
    {
        //默认查看自己的详细资料
        if (!$uid) {
            $this->requireLogin();
            $uid = $this->getUid();
        }
        //读取数据库中的用户详细资料
        $map = array('uid' => $uid);
        $user1 = D('User/Member')->where($map)->find();
        $user2 = D('User/UcenterMember')->where(array('id' => $uid))->find();


        //获取等级
        $title = D('User/Title')->getTitle($user1['score']);

        //只返回必要的详细资料
        $this->apiSuccess("获取成功", null, array(
            'uid' => $uid,
            'avatar_url' => $avatar_url,
            'avatar128_url' => $avatar128_url,
            'signature' => $user1['signature'],
            'email' => $user2['email'],
            'mobile' => $user2['mobile'],
        	'email_ver' => $user2['email_ver'],
        	'mobile_ver' => $user2['mobile_ver'],
        	'idcard' => $user1['idcard'],
        	'qq' => $user1['qq'],
        	'safecode'=> $user1['safecode'],
            'score' => $user1['score'],
        	'gold_coin' => $user1['gold_coin'],
        	'money' => $user1['money'],
        	'jifenbao' => $user1['jifenbao'],
        	'money_ticket' => $user1['money_ticket'],
            'name' => $user1['name'],
            'sex' => $this->encodeSex($user1['sex']),
            'birthday' => $user1['birthday'],
            'title' => $title,
            'username' => $user2['username'],
        ));
    }

    public function setProfile($signature = null, $email = null, $name = null, $sex = null, $birthday = null)
    {
        $this->requireLogin();
        //获取用户编号
        $uid = $this->getUid();
        //将需要修改的字段填入数组
        $fields = array();
        if ($signature !== null) $fields['signature'] = $signature;
        if ($email !== null) $fields['email'] = $email;
        if ($name !== null) $fields['name'] = $name;
        if ($sex !== null) $fields['sex'] = $sex;
        if ($birthday !== null) $fields['birthday'] = $birthday;

        foreach($fields as $key=> $field)
        {
            clean_query_user_cache($this->getUid(),$key);//删除缓存
        }
        //将字段分割成两部分，一部分属于ucenter，一部分属于home
        $split = $this->splitUserFields($fields);
        $home = $split['home'];
        $ucenter = $split['ucenter'];
        //分别将数据保存到不同的数据表中
        if ($home) {
            /*if (isset($home['sex'])) {
                $home['sex'] = $this->decodeSex($home['sex']);
            }*/
            $home['uid'] = $uid;
            $model = D('Home/Member');
            $result = $model->where(array('uid' => $uid))->save($home);
            if (!$result) {
                $this->apiError(0, '设置失败，请检查输入格式!');
            }
        }
        if ($ucenter) {
            $model = D('User/UcenterMember');
            $ucenter['id'] = $uid;
            $result = $model->where(array('id' => $uid))->save($ucenter);
            if (!$result) {
                $this->apiError(0, '设置失败，请检查输入格式!');
            }
        }
        //返回成功信息
        $this->apiSuccess("设置成功!");
    }

    /* public function bindMobile($verify)
    {
        $this->requireLogin();
        //确认用户未绑定手机
        $uid = $this->getuid();
        $user = D('User/UcenterMember')->where(array('id' => $uid))->find();
        if ($user['mobile']) {
            $this->apiError(1801, "您已经绑定手机，需要先解绑");
        }
        //确认手机验证码正确
        $mobile = getMobileFromSession();
        $addon = new TianyiAddon();
        if (!$addon->checkVerify($mobile, $verify)) {
            $this->apiError(1802, "手机验证码错误");
        }
        //确认手机号码没有重复
        $user = D('User/UcenterMember')->where(array('mobile' => $mobile, 'status' => 1))->find();
        if ($user) {
            $this->apiError(1803, '该手机号码已绑定到另一个账号，不能重复绑定');
        }
        //修改数据库
        $uid = $this->getUid();
        D('User/UcenterMember')->where(array('id' => $uid))->save(array('mobile' => $mobile));
        write_query_user_cache($uid, 'mobile', $mobile);
        //返回成功结果
        $this->apiSuccess("绑定成功");
    }

    public function unbindMobile($verify)
    {
        $uid = $this->getUid();

        clean_query_user_cache($uid, 'mobile');
        $this->requireLogin();
        //确认用户已经绑定手机
        $model = D('User/UcenterMember');
        $user = $model->where(array('id' => $this->getUid()))->find();
        if (!$user['mobile']) {
            $this->apiError(1901, "您尚未绑定手机");
        }
        //确认被验证的手机号码与用户绑定的手机号相符
        $mobile = getMobileFromSession();
        if ($mobile != $user['mobile']) {
            $this->apiError(1902, "验证的手机与绑定的手机不符合");
        }
        //确认验证码正确
        $addon = new TianyiAddon;
        if (!$addon->checkVerify($mobile, $verify)) {
            $this->apiError(1903, "手机验证码错误");
        }
        //写入数据库

        $model->where(array('uid' => $uid))->save(array('mobile' => ''));

        //返回成功结果
        $this->apiSuccess("解绑成功");
    } */

}