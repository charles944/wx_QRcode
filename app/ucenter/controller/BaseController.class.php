<?php
namespace ucenter\controller;
use think\Controller;
class BaseController extends Controller
{
    public function _initialize()
    {
		$uid = intval($_REQUEST['uid']) ? intval($_REQUEST['uid']) : is_login();
		$page = intval($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        if (!$uid) {
            $this->error(L('_ERROR_NEED_LOGIN_'),U('home/member/login'));
        }
        $this->assign('uid', $uid);
		$this->assign('page',$page);
        $this->mid = $uid;
    }

    protected function _tab_menu()
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

    private function multi_array_sort($multi_array, $sort_key, $sort = SORT_ASC)
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

    protected function _setTab($name)
    {
        $this->assign('tab', $name);
    }

    protected function _assignSelf($uid = 0)
    {
    	$self = query_user(array('avatar128','type','last_login_time', 'last_login_ip', 'nickname','username', 'uid', 'space_url', 'icons_html', 'rank_link', 'signature','reg_time'),$uid);
    	$map=getUserConfigMap('user_cover','',$uid);
    	$map['role_id']=0;
    	$model=D('User/UserConfig');
    	$cover=$model->findData($map);
    	$self['cover_id']=$cover['value'];
    	$self['cover_path']=getThumbImageById($cover['value'],998,128);

    	$user1 = D('User/Member')->where(array('uid' => $uid))->find();
    	$user2 = D('User/UcenterMember')->where(array('id' => $uid))->find();
    	$title = D('User/Title')->getTitle($uid);
    	$self['email'] = $user2['email'];
    	$self['mobile'] = $user2['mobile'];
    	$self['email_ver'] = $user2['email_ver'];
    	$self['mobile_ver'] = $user2['mobile_ver'];
    	$self['idcard'] = $user1['idcard'];
    	$self['safecode'] = $user1['safecode'];
    	$self['score'] = $user1['score'];
    	$self['gold_coin'] = $user1['gold_coin'];
    	$self['money'] = $user1['money'];
    	$self['jifenbao'] = $user1['jifenbao'];
    	$self['money_ticket'] = $user1['money_ticket'];
    	$self['name'] = $user1['name'];
    	$self['title'] = $title;
    	$self['qq'] = $user1['qq'];
		$self['sex'] = $user1['sex'];

        $purse = D('MemberPurse')->where('status = 1')->select();
        foreach($purse as &$v){
            $v['user'] = D('MemberPurseId')->where('uid='.$uid.' and pid='.$v['id'])->find();
        }
        $this->assign('purse_list',$purse);
    	$this->assign('self', $self);
    }

    protected function get_self_info($uid = 0){
    	$user1 = D('User/Member')->where(array('uid' => $uid))->find();
    	$user2 = D('User/UcenterMember')->where(array('id' => $uid))->find();
    	$title = D('User/Title')->getTitle($user1['score']);
    	$profile = array(
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
    	);
    	return $profile;
    }

    protected function encodeSex($sex) {
    	$map = array(0=>'m', 1=>'f');
    	return $map[$sex];
    }
    protected function defaultTabHash($tabHash)
    {
        $tabHash = op_t($_REQUEST['tabHash']) ?  op_t($_REQUEST['tabHash']): $tabHash;
        $this->assign('tabHash', $tabHash);
    }
    protected function ensureApiSuccess($result)
    {
        if (!$result['success']) {
            $this->error($result['message'], $result['url']);
        }
    }
    protected function requireLogin()
    {
        if (!is_login()) {
            $this->error(L('_ERROR_MUST_LOGIN_'),U('home/member/login'));
        }
    }
}