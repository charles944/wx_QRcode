<?php
namespace ucenter\controller;
use think\Controller;
class IndexController extends BaseController
{
    protected $user_info;
    public function _initialize()
    {
        parent::_initialize();
        $uid = isset($_GET['uid']) ? op_t($_GET['uid']) : 0;
        if(!$uid){
        	$this->error('参数错误!');
        }
        $user_data = D('User/Member')->where('uid ='.$uid)->find();
        if(empty($user_data)){
        	$this->error('无此会员!');
        }
        unset($user_data);
        $user_data = D('UcenterMember')->where('id ='.$uid)->find();
        if(empty($user_data)){
        	$this->error('无此会员!');
        }
        unset($user_data);
        $this->_tab_menu();
        $this->assign('uid',$uid);
		$this->userInfo($uid);
    }
    
    protected function userInfo($uid = 0)
    {
    	if($uid == 0){
    		return false;
    	}else{
	    	$user_info = query_user(array('avatar128','type','last_login_time', 'last_login_ip', 'nickname','username','email','email_ver','mobile_ver','mobile', 'uid', 'space_url', 'icons_html', 'score', 'title', 'rank_link', 'signature','reg_time','safecode'), $uid);
	    	//获取用户封面id
	    	$map=getUserConfigMap('user_cover','',$uid);
	    	$map['role_id']=0;
	    	$model=D('User/UserConfig');
	    	$cover=$model->findData($map);
	    	$user_info['cover_id']=$cover['value'];
	    	$user_info['cover_path']=getThumbImageById($cover['value'],998,128);
	    	$this->assign('user_info', $user_info);
	    	return $user_info;
    	}
    }
    public function index($uid = 0)
    {
        $appArr = $this->_tab_menu();
        $user_info = $this->userInfo($uid);
        $str = $user_info['nickname'];
        $this->setTitle($str . "的个人主页");
        $this->setKeywords($str . "的个人主页");
        $this->setDescription($str . "的个人主页");
        $this->display();
    }
   
    public function appList($uid = null, $page = 1, $tab = null)
    {
        $appArr = $this->_tab_menu();
        $type = op_t($_GET['type']);
        if (!isset ($appArr [$type])) {
            $this->error('参数出错！！');
        }
        $this->assign('type', $type);
        $this->assign('module',$appArr[$type]['data-id']);
        $this->assign('page',$page);
        $this->assign('tab',$tab);
        $str = '{$user_info.nickname|op_t}';
        $str_app = '{$appArr.'.$type.'.title|op_t}';
        $this->setTitle($str . "的个人" . $str_app . "页");
        $this->setKeywords($str . "，个人主页，个人" . $str_app);
        $this->setDescription($str . "的个人" . $str_app . "页");
        $this->display();
    }
    public function _tab_menu()
    {
        $modules = D('Common/Module')->getAll();
        $apps = array();
        foreach ($modules as $m) {
            if ($m['is_setup'] == 1 && $m['entry'] != '') {
                if (file_exists(APP_PATH . $m['name'] . '/widget/UcenterBlockWidget.class.php')) {
                    $apps[] = array('data-id' => $m['name'], 'title' => $m['alias'],'sort'=>$m['sort'],'key'=>strtolower($m['name']));
                }
            }
        }
        $apps = $this->sortApps($apps);
        $apps=array_combine(array_column($apps,'key'),$apps);
        $this->assign('appArr', $apps);
        return $apps;
    }
    private function sortApps($apps)
    {
        return $this->multi_array_sort($apps, 'sort', SORT_DESC);
    }
    function multi_array_sort($multi_array, $sort_key, $sort = SORT_ASC)
    {
        if (is_array($multi_array)) {
            foreach ($multi_array as $row_array) {
                if (is_array($row_array)) {
                    $key_array[] = $row_array[$sort_key];
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
        array_multisort($key_array, $sort, $multi_array);
        return $multi_array;
    }
}