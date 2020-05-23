<?php
// 常量定义
use admin\model\AuthRuleModel;
use think\Storage;
use common\model\ChineseModel;
require_once(SITE_PATH . '/data/version.php');
require_once(APP_PATH . 'common/common/pagination.php');
require_once(APP_PATH . 'common/common/query_user.php');
require_once(APP_PATH . 'common/common/thumb.php');
require_once(APP_PATH . 'common/common/api.php');
require_once(APP_PATH . 'common/common/time.php');
require_once(APP_PATH . 'common/common/match.php');
require_once(APP_PATH . 'common/common/seo.php');
require_once(APP_PATH . 'common/common/type.php');
require_once(APP_PATH . 'common/common/cache.php');
require_once(APP_PATH . 'common/common/vendors.php');
require_once(APP_PATH . 'common/common/parse.php');
require_once(APP_PATH . 'common/common/user.php');
require_once(APP_PATH . 'common/common/limit.php');
require_once(APP_PATH . 'common/common/ext_parse.php');
require_once(APP_PATH . 'common/common/ip.php');
require_once(APP_PATH . 'common/common/tz.php');
require_once(APP_PATH . 'common/common/qncore.php');
require_once(APP_PATH . 'common/common/group.php');
/*require_once(APP_PATH . '/common/common/extend.php');*/

//=========系统公共库文件 主要定义系统公共函数库====================//

//获取货币类型名 $type 数字：类型ID，字符：类型名
function getScoreTypeName($type){
	return D('User/Score')->getTypeName($type);
}

// 获取货币类型图标
function getScoreTypeIcon($type){
	return D('User/Score')->getTypeIcon($type);
}

//获取货币类型的单位
function getScoreTypeUnit($type){
	return D('User/Score')->getTypeUnit($type);
}

function need_login()
{
    $uid = is_login();
    if (empty($uid)) {
        if ($uid = D('User/UcenterMember')->getCookieUid()) {
            D('User/UcenterMember')->checkLogin($uid);
        }
    }else{
        if($uid>0){
            $user_status = D('User/UcenterMember')->getStatus($uid);
            if($user_status == -2){
                clean_query_user_cache($uid);
                D('User/Member')->logout();
            }else if ($user_status == 3) {
                session('temp_login_uid', $uid);
                session('temp_login_group_id', $_SESSION['user_auth']['group_id']);

                header('Content-Type:application/json; charset=utf-8');
                $data['status'] = 1;
                $data['url'] = U('home/member/activate');
                redirect($data['url']);
            }else if (1 != $user_status) {
                clean_query_user_cache($uid);
                D('User/Member')->logout();
            }else if(1 == $user_status){
                $user_group = M('AuthGroupAccess');
                $overdue_role = $user_group->table("__AUTH_GROUP_ACCESS__ ur")->join("__AUTH_GROUP__ r on r.id= ur.group_id")->where("r.day>0 and r.id != 1 and ur.start_time != ur.end_time and ur.uid=".$uid." and ur.end_time<".time())->field("ur.group_id,r.id")->select();
                if($overdue_role){
                    $data=array();
                    $must_exit = 0;
                    foreach($overdue_role as $or){
                        if($or['group_id'] == $_SESSION['user_auth']['group_id']){
                            $must_exit = 1;
                        }
                        $data[]=$or["group_id"];
                    }
                    $map=array();
                    $map['uid']=$uid;
                    $map['group_id']=array("in",$data);
                    unset($data);
                    $user_group->where($map)->delete();
                    unset($map);
                    if($must_exit){
                        clean_query_user_cache($uid);
                        D('User/Member')->logout();
                        D('User/UcenterMember')->checkLogin($uid);
                    }
                    clean_query_user_cache($uid);
                }
            }
        }
    }
}

//获取用户ID
function get_uid()
{
    return is_login();
}

//检测用户是否登录 @return integer 0-未登录，大于0-当前登录用户ID
function is_login()
{
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        $uid = session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
        return $uid;
    }
}

//设置前台主题
function set_theme($theme=''){
    $themes = D('Config')->where("name = 'FRONT_THEME'")->select();
    $theme = $themes[0]['value'];
    //判断是否存在设置的模板主题
        if(empty($theme)){
           $theme_name=C('DEFAULT_THEME');
        }else{
           if(is_dir (MODULE_PATH.'view/'.$theme )){
              $theme_name=$theme;
           }else{
              $theme_name=C('DEFAULT_THEME');
           }
        }
        //替换COMMON模块中设置的模板值    
        if(C('Current_Theme')){
            C('TMPL_PARSE_STRING',str_replace (C('Current_Theme') ,  $theme_name ,  C('TMPL_PARSE_STRING') ));        
        }else{
            C('TMPL_PARSE_STRING',str_replace ( "MODULE_NAME" ,  MODULE_NAME ,  C('TMPL_PARSE_STRING') ));    
            C('TMPL_PARSE_STRING',str_replace ( "DEFAULT_THEME" ,  $theme_name ,  C('TMPL_PARSE_STRING') ));
        }
        C('Current_Theme',$theme_name);
        C('DEFAULT_THEME', $theme_name);
}

//设置会员中心主题
function set_user_theme($theme=''){
    $themes = D('Config')->where("name = 'FRONT_USER_THEME'")->select();
    $theme = $themes[0]['value'];
    //判断是否存在设置的模板主题
    if(empty($theme)){
        $theme_name=C('DEFAULT_USER_THEME');
    }else{
        if(is_dir (MODULE_PATH.'view/'.$theme )){
            $theme_name=$theme;
        }else{
            $theme_name=C('DEFAULT_USER_THEME');
        }
         
    }
    //替换COMMON模块中设置的模板值
    if(C('Current_Theme')){
        C('TMPL_PARSE_STRING',str_replace (C('Current_Theme') ,  $theme_name ,  C('TMPL_PARSE_STRING') ));
    }else{
        C('TMPL_PARSE_STRING',str_replace ( "MODULE_NAME" ,  MODULE_NAME ,  C('TMPL_PARSE_STRING') ));
        C('TMPL_PARSE_STRING',str_replace ( "DEFAULT_USER_THEME" ,  $theme_name ,  C('TMPL_PARSE_STRING') ));
    }
    C('Current_Theme',$theme_name);
    C('DEFAULT_USER_THEME', $theme_name);
    C('DEFAULT_THEME', $theme_name);
}


/**
 * 构造用户配置表 D('UserConfig')查询条件
 * @param string $name 表中name字段的值(配置标识)
 * @param string $model 表中model字段的值(模块标识)
 * @param int $uid 用户uid
 * @param int $role_id 登录的角色id
 */
function getUserConfigMap($name = '', $model = '', $uid = 0)
{
	$uid = $uid ? $uid : is_login();
	$map = array();
	//构造查询条件
	$map['uid'] = $uid;
	$map['name'] = $name;
	$map['model'] = $model;
	return $map;
}


//检测权限
function CheckPermission($uids)
{
    if (is_administrator()) {
        return true;
    }
    if (in_array(is_login(), $uids)) {
        return true;
    }
    return false;
}

//检测权限
function check_auth($rule = '', $except_uid = -1, $type = AuthRuleModel::RULE_URL, $mode = 'url')
{
    if (is_administrator()) {
        return true;//管理员允许访问任何页面
    }
    $uid = is_login();
    if ($except_uid != -1) {
        if (!is_array($except_uid)) {
            $except_uid = explode(',', $except_uid);
        }
        if (in_array($uid, $except_uid)) {
            return true;
        }
    }
    $rule = empty($rule) ? MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME : $rule;
    // 检测是否有该权限
    if (!M('auth_rule')->where(array('name' => $rule, 'status' => 1))->find()) {
        return false;
    }

    static $Auth = null;
    if (!$Auth) {
        $Auth = new \think\Auth();
    }
    if (!$Auth->check($rule, $uid , $type,$mode)) {
        return false;
    }
    return true;

}

//检测当前用户是否为管理员 @return boolean true-管理员，false-非管理员
function is_administrator($uid = null)
{
    $uid = is_null($uid) ? is_login() : $uid;
    $admin_uids = explode(',', C('USER_ADMINISTRATOR'));//调整验证机制，支持多管理员，用,分隔
    return $uid && (in_array(intval($uid), $admin_uids));//调整验证机制，支持多管理员，用,分隔
}

//字符串转换为数组，主要用于把分隔符调整到第二个参数 @param  string $str 要分割的字符串 @param  string $glue 分割符
function str2arr($str, $glue = ',')
{
    return explode($glue, $str);
}

//数组转换为字符串，主要用于把分隔符调整到第二个参数 @param  array  $arr 要连接的数组 @param  string $glue 分割符
function arr2str($arr, $glue = ',')
{
    return implode($glue, $arr);
}

/**
 * 字符串截取，支持中文和其他编码
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true)
{
    if (function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
        if (false === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice . '...' : $slice;
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key 加密密钥
 * @param int    $expire 过期时间 单位 秒
 */
function think_encrypt($data, $key = '', $expire = 0)
{
    $key = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = base64_encode($data);
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    $str = sprintf('%010d', $expire ? $expire + time() : 0);

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
    }
    return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param  string $key 加密密钥
 */
function think_decrypt($data, $key = '')
{
    $key = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = str_replace(array('-', '_'), array('+', '/'), $data);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    $data = base64_decode($data);
    $expire = substr($data, 0, 10);
    $data = substr($data, 10);

    if ($expire > 0 && $expire < time()) {
        return '';
    }
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}

/**
 * 数据签名认证
 * @param  array $data 被认证的数据
 * @return string 签名
 */
function data_auth_sign($data)
{
    //数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
 * 对查询结果集进行排序
 * @access public
 * @param array  $list 查询结果
 * @param string $field 排序的字段名
 * @param array  $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 */
function list_sort_by($list, $field, $sortby = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = array();
        foreach ($list as $i => $data)
            $refer[$i] = & $data[$field];
        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc': // 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val)
            $resultSet[] = & $list[$key];
        return $resultSet;
    }
    return false;
}

/**
 * 把返回的数据集转换成Tree
 * @param array  $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
{
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array  $tree 原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list 过渡用的中间数组，
 * @return array        返回排过序的列表数组
 */
function tree_to_list($tree, $child = '_child', $order = 'id', &$list = array())
{
    if (is_array($tree)) {
        $refer = array();
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if (isset($reffer[$child])) {
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby = 'asc');
    }
    return $list;
}

/**
 * 格式化字节大小
 * @param  number $size 字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '')
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 设置跳转页面URL
 * 使用函数再次封装，方便以后选择不同的存储方式（目前使用cookie存储）
 */
function set_redirect_url($url)
{
    cookie('redirect_url', $url);
}

/**
 * 获取跳转页面URL
 * @return string 跳转页URL
 */
function get_redirect_url()
{
    $url = cookie('redirect_url');
    return empty($url) ? __APP__ : $url;
}

/**
 * 处理插件钩子
 * @param string $hook 钩子名称
 * @param mixed  $params 传入参数
 */
function hook($hook, $params = array())
{
    \think\Hook::listen($hook, $params);
}

/**
 * 获取插件类的类名
 * @param strng $name 插件名
 */
function get_addon_class($name)
{
	$name = ucfirst($name);
	$lowername = strtolower($name);
	$class = "addons\\{$lowername}\\{$name}Addon";
	if (! class_exists ( $class )) {
		$class = "plugins\\{$lowername}\\{$name}Plugin";
	}
	return $class;
}

/**
 * 获取插件类的配置文件数组
 * @param string $name 插件名
 */
function get_addon_config($name)
{
    $class = get_addon_class($name);
    if (class_exists($class)) {
        $addon = new $class();
        return $addon->getConfig();
    } else {
        return array();
    }
}

//插件显示内容里生成访问插件的url
function addons_url($url, $param = array())
{
	$urlArr = explode ( '://', $url );
	if (stripos ( $urlArr [0], '_' ) !== false) {
		$addons = $urlArr [0];
		$url = 'http://' . $urlArr [1];
	}
    $url = parse_url($url);
    $case = C('URL_CASE_INSENSITIVE');
	! $addons || $url ['scheme'] = $addons;
    $addons = $case ? parse_name($url['scheme'],1) : $url['scheme'];
    $controller = $case ? parse_name($url['host'],1) : $url['host'];
    $action = trim($case ? strtolower($url['path']) : $url['path'], '/');

    /* 解析URL带的参数 */
    if (isset($url['query'])) {
        parse_str($url['query'], $query);
        $param = array_merge($query, $param);
    }

    /* 基础参数 */
	$params = array (
			//'_addons' => ucfirst ( $addons ),
			'_addons' => strtolower ( $addons ),
			'_controller' => ucfirst ( $controller ),
			'_action' => $action 
	);
    $params = array_merge($params, $param); //添加额外参数
    //if (strtolower(MODULE_NAME) == 'admin') {
    //    return U('Admin/Addons/execute', $params);
    //} else {
    //    return U('Home/Addons/execute', $params);
    //}
	$qurl = is_dir ( QN_PLUGIN_PATH . $params ['_addons'] ) ? "home/addons/plugin" : "home/addons/execute";
	return U ( $qurl, $params );
}

/**
 * 根据用户ID获取用户名
 * @param  integer $uid 用户ID
 * @return string       用户名
 */
function get_username($uid = 0)
{
    static $list;
    if (!($uid && is_numeric($uid))) { //获取当前登录用户名
       return $_SESSION['user_auth']['username'];
    }

    /* 获取缓存数据 */
    if (empty($list)) {
        $list = S('sys_active_user_list');
    }

    /* 查找用户信息 */
    $key = "u{$uid}";
    if (isset($list[$key])) { //已缓存，直接使用
        $name = $list[$key];
    } else { //调用接口获取用户信息
        $User = D('User/UcenterMember');
        $info = $User->info($uid);
        if ($info && isset($info[1])) {
            $name = $list[$key] = $info[1];
            /* 缓存用户 */
            $count = count($list);
            $max = C('USER_MAX_CACHE');
            while ($count-- > $max) {
                array_shift($list);
            }
            S('sys_active_user_list', $list);
        } else {
            $name = '';
        }
    }
    return $name;
}

/**
 * 根据用户ID获取用户昵称
 * @param  integer $uid 用户ID
 * @return string       用户昵称
 * @author 乾坤网络有限公司
 */
function get_nickname($uid = 0)
{
    static $list;
    if (!($uid && is_numeric($uid))) { //获取当前登录用户名
        return $_SESSION['user_auth']['nickname'];
    }

    /* 获取缓存数据 */
    if (empty($list)) {
        $list = S('sys_user_nickname_list');
    }

    /* 查找用户信息 */
    $key = "u{$uid}";
    if (isset($list[$key])) { //已缓存，直接使用
        $name = $list[$key];
    } else { //调用接口获取用户信息
        $info = M('Member')->field('nickname')->find($uid);
        if ($info !== false && $info['nickname'] != '') {
            $nickname = $info['nickname'];
            $name = $list[$key] = $nickname;
            /* 缓存用户 */
            $count = count($list);
            $max = C('USER_MAX_CACHE');
            while ($count-- > $max) {
                array_shift($list);
            }
            S('sys_user_nickname_list', $list);
        } else {
            $name = get_username($uid);
        }
    }
    return $name;
}


/**
 * 解析UBB数据
 * @param string $data UBB字符串
 * @return string 解析为HTML的数据
 */
function ubb($data)
{
    //TODO: 待完善，目前返回原始数据
    return $data;
}

/**
 * 记录行为日志，并执行该行为的规则
 * @param string $action 行为标识
 * @param string $model 触发行为的模型名
 * @param int $record_id 触发行为的记录id
 * @param int $user_id 执行行为的用户id
 */
function action_log($action = null, $model = null, $record_id = null, $user_id = null)
{

    //参数检查
    if (empty($action) || empty($model) || empty($record_id)) {
        return '参数不能为空';
    }
    if (empty($user_id)) {
        $user_id = is_login();
    }

    //查询行为,判断是否执行
    $action_info = M('Action')->getByName($action);

    if ($action_info['status'] != 1) {
        return '该行为被禁用或删除';
    }

	$user_group_id =  get_login_group();

    //插入行为日志
    $data['action_id'] = $action_info['id'];
    $data['user_id'] = $user_id;
    $data['action_ip'] = ip2long(get_client_ip());
    $data['model'] = $model;
    $data['record_id'] = $record_id;
    $data['create_time'] = NOW_TIME;
	$data['group_id'] = $user_group_id;

    //解析日志规则,生成日志备注
    if (!empty($action_info['log'])) {
        if (preg_match_all('/\[(\S+?)\]/', $action_info['log'], $match)) {
            $log['user'] = $user_id;
            $log['record'] = $record_id;
            $log['model'] = $model;
            $log['time'] = NOW_TIME;
            $log['data'] = array('user' => $user_id, 'model' => $model, 'record' => $record_id, 'time' => NOW_TIME);
            foreach ($match[1] as $value) {
                $param = explode('|', $value);
                if (isset($param[1])) {
                    $replace[] = call_user_func($param[1], $log[$param[0]]);
                } else {
                    $replace[] = $log[$param[0]];
                }
            }
            $data['remark'] = str_replace($match[0], $replace, $action_info['log']);
        } else {
            $data['remark'] = $action_info['log'];
        }
    } else {
        //未定义日志规则，记录操作url
        $data['remark'] = '操作url：' . $_SERVER['REQUEST_URI'];
    }


    $log_id = M('ActionLog')->add($data);

    if (!empty($action_info['rule'])) {
        //解析行为
        $rules = parse_action($action, $user_id);
        //执行行为
        $res = execute_action($rules, $action_info['id'], $user_id, $log_id, $user_group_id);
    }
}

/**
 * 解析行为规则
 * 规则定义  table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
 * 规则字段解释：table->要操作的数据表，不需要加表前缀；
 *              field->要操作的字段；
 *              condition->操作的条件，目前支持字符串，默认变量{$self}为执行行为的用户
 *              rule->对字段进行的具体操作，目前支持四则混合运算，如：1+score*2/2-3
 *              cycle->执行周期，单位（小时），表示$cycle小时内最多执行$max次
 *              max->单个周期内的最大执行次数（$cycle和$max必须同时定义，否则无效）
 * 单个行为后可加 ； 连接其他规则
 * @param string $action 行为id或者name
 * @param int $self 替换规则里的变量为执行用户的id
 * @return boolean|array: false解析出错 ， 成功返回规则数组
 */
function parse_action($action = null, $self)
{
    if (empty($action)) {
        return false;
    }

    //参数支持id或者name
    if (is_numeric($action)) {
        $map = array('id' => $action);
    } else {
        $map = array('name' => $action);
    }

    //查询行为信息
    $info = M('Action')->where($map)->find();

    if (!$info || $info['status'] != 1) {
        return false;
    }


    //解析规则:table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
    $rules = unserialize($info['rule']);
    foreach ($rules as $key => &$rule) {
        foreach ($rule as $k => &$v) {
            if (empty($v)) {
                unset($rule[$k]);
            }
        }
        unset($k, $v);
    }
    unset($key, $rule);

    return $rules;
}

/**
 * 执行行为
 * @param array $rules 解析后的规则数组
 * @param int $action_id 行为id
 * @param array $user_id 执行的用户id
 * @return boolean false 失败 ， true 成功
 */
function execute_action($rules = false, $action_id = null, $user_id = null, $log_id = null, $user_group_id = 1)
{
    $log_score = '';

    //hook('handleAction',array('action_id'=>$action_id,'user_id'=>$user_id,'log_id'=>$log_id,'log_score'=>&$log_score));

    if (!$rules || empty($action_id) || empty($user_id)) {
        return false;
    }
    $return = true;

    $action_log = M('ActionLog')->where(array('id' => $log_id))->find();
	if(!empty($rules)){
		foreach($rules as $key => $v){
			$usergroups_arr[] = $v['usergroups'];
		}
		if(intval($user_group_id) == 1){
			$user_group_real_id = 1;
		}else{
			if(in_array($user_group_id, $usergroups_arr)){
				$user_group_real_id = intval($user_group_id);
			}else{
				$user_group_real_id = 1;
			}
		}
		foreach ($rules as $rule) {
			if(intval($user_group_real_id) == intval($rule['usergroups'])){
				//检查执行周期
				$map = array('action_id' => $action_id, 'user_id' => $user_id, 'usergroups' => $rule['usergroups']);
				$ago = get_time_ago($rule['time_unit'], $rule['cycle'], NOW_TIME);
				$map['create_time'] = array('egt', $ago);
				$exec_count = M('ActionLog')->where($map)->count();
				if ($exec_count > $rule['max']) {
					continue;
				}
				//执行数据库操作
				$Model = M(ucfirst($rule['table']));
				
				$field = $rule['field'];
				
				$scoreModel= D('User/Score');

				$rule['rule'] = (is_bool(strpos($rule['rule'], '+')) ? '+' : '') . $rule['rule'];

				$sType = D('user_score_type')->where(array('id' => $field))->find();
				$log_score .= '【' . $sType['title'] . '：' . $rule['rule'] . $sType['unit'] . '】';

				$action = strpos($rule['rule'], '-')?'dec':'inc';
				
				$res = $scoreModel->setUserScore($user_id,$action,$sType['mark'],substr($rule['rule'],1,strlen($rule['rule'])-1));

				D('User/UserScoreLog')->sendmingxiWithoutCheckSelf($user_id, 0, $action_log['model'], $action, $sType['mark'], substr($rule['rule'],1,strlen($rule['rule'])-1), $action_log['remark'].'【' . $sType['title'] . '：' . $rule['rule'] . $sType['unit'] . '】',$action_log['record_id']);
				
				if ($log_score) {
					M('ActionLog')->where(array('id' => $log_id))->setField('remark', array('exp', "CONCAT(remark,'" . $log_score . "')"));
				}
				
				if (!$res) {
					$return = false;
				}
			}else{
				continue;
			}
		}
		
		
		
	}else{
		
	}
    return $return;
}

/**
 * 获取行为指定用户组名
 * @param intger $usergroupid 类型id
 */
function get_usergroups($usergroupid){
	if(empty($usergroupid)){
		return '通用';
	}else{
		$list = M('AuthGroup')->where(array('status' => 1,'id'=>$usergroupid))->order('id asc')->find();
		if(!empty($list)){
			return $list['title'];
		}else{
			return '用户组被禁用或失效';
		}
	}
	
}

//基于数组创建目录和文件
function create_dir_or_files($files)
{
    foreach ($files as $key => $value) {
        if (substr($value, -1) == '/') {
            mkdir($value);
        } else {
            @file_put_contents($value, '');
        }
    }
}

function array_gets($array, $fields) {
	$result = array();
	foreach($fields as $e) {
		if(array_key_exists($e, $array)) {
			$result[$e] = $array[$e];
		}
	}
	return $result;
}
if (!function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = null)
    {
        $result = array();
        if (null === $indexKey) {
            if (null === $columnKey) {
                $result = array_values($input);
            } else {
                foreach ($input as $row) {
                    $result[] = $row[$columnKey];
                }
            }
        } else {
            if (null === $columnKey) {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row;
                }
            } else {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row[$columnKey];
                }
            }
        }
        return $result;
    }
}


/**
 * 根据条件字段获取指定表的数据
 * @param mixed  $value 条件，可用常量或者数组
 * @param string $condition 条件字段
 * @param string $field 需要返回的字段，不传则返回整个数据
 * @param string $table 需要查询的表
 * @author 乾坤网络有限公司
 */
function get_table_field($value = null, $condition = 'id', $field = null, $table = null)
{
    if (empty($value) || empty($table)) {
        return false;
    }

    //拼接参数
    $map[$condition] = $value;
    $info = M(ucfirst($table))->where($map);
    if (empty($field)) {
        $info = $info->field(true)->find();
    } else {
        $info = $info->getField($field);
    }
    return $info;
}

/**
 * 获取文档封面图片
 * @param int    $cover_id
 * @param string $field
 * @return 完整的数据  或者  指定的$field字段值
 * @author 乾坤网络有限公司
 */
function get_cover($cover_id, $field = null)
{
    if (empty($cover_id)) {
        return false;
    }
    $picture = M('Picture')->where(array('status' => 1))->getById($cover_id);
    if(is_bool(strpos($picture['path'],'http://'))){
        $picture['path']=get_pic_src($picture['path']);
    }
    return empty($field) ? $picture : $picture[$field];
}

/**
 * 获取数据的所有子孙数据的id值
 */

function get_stemma($pids, Model &$model, $field = 'id')
{
    $collection = array();

    //非空判断
    if (empty($pids)) {
        return $collection;
    }

    if (is_array($pids)) {
        $pids = trim(implode(',', $pids), ',');
    }
    $result = $model->field($field)->where(array('pid' => array('IN', (string)$pids)))->select();
    $child_ids = array_column((array)$result, 'id');

    while (!empty($child_ids)) {
        $collection = array_merge($collection, $result);
        $result = $model->field($field)->where(array('pid' => array('IN', $child_ids)))->select();
        $child_ids = array_column((array)$result, 'id');
    }
    return $collection;
}

/**
 * 获取导航URL
 * @param  string $url 导航URL
 * @return string      解析或的url
 */
function get_nav_url($url)
{
    switch ($url) {
        case 'http://' === substr($url, 0, 7):
        case '#' === substr($url, 0, 1):
            break;
        default:
            $url = U($url);
            break;
    }
    return $url;
}

/**
 * @param $url 检测当前url是否被选中
 * @return bool|string
 * @author 乾坤网络有限公司
 */
function get_nav_active($url)
{
    switch ($url) {
       // case 'http://' === substr($url, 0, 7):
        //    if (strtolower($url) === strtolower($_SERVER['HTTP_REFERER'])) {
        //        return 1;
        //    }
        case '#' === substr($url, 0, 1):
            return 0;
            break;
        default:
            $url_array = explode('/', $url);
            if ($url_array[0] == '') {
                $MODULE_NAME = $url_array[1];
            } else {
                $MODULE_NAME = $url_array[0]; //发现模块就是当前模块即选中。
            }
            if (strtolower($MODULE_NAME) === strtolower(MODULE_NAME)) {
                return 1;
            };
            break;
    }
    return 0;
}

function GetCurUrl()
{
	if(!empty($_SERVER["REQUEST_URI"]))
	{
		$scriptName = $_SERVER["REQUEST_URI"];
		$nowurl = $scriptName;
	}
	else
	{
		$scriptName = $_SERVER["PHP_SELF"];
		if(empty($_SERVER["QUERY_STRING"]))
		{
			$nowurl = $scriptName;
		}
		else
		{
			$nowurl = $scriptName."?".$_SERVER["QUERY_STRING"];
		}
	}
	return $nowurl;
}

/**
 * t函数用于过滤标签，输出没有html的干净的文本
 * @param string text 文本内容
 * @return string 处理后内容
 * @author 乾坤网络有限公司
 */
function op_t($text, $addslanshes = true)
{
	$text = nl2br($text);
	$text = real_strip_tags($text);
	if ($addslanshes)
		$text = addslashes($text);
	$text = trim($text);
	return $text;
}

/**过滤函数，别名函数，op_t的别名
 * @param $text
 * @auth 
 */
function text($text, $addslanshes = true)
{
	return op_t($text, $addslanshes);
}

/**过滤函数，别名函数，op_h的别名
 * @param $text
 * @auth 
 */
function html($text)
{
	return op_h($text);
}

/**
 * h函数用于过滤不安全的html标签，输出安全的html
 * @param string $text 待过滤的字符串
 * @param string $type 保留的标签格式
 * @return string 处理后内容
 * @author 乾坤网络有限公司
 */
function op_h($text, $type = 'html')
{
    // 无标签格式
    $text_tags = '';
    //只保留链接
    $link_tags = '<a>';
    //只保留图片
    $image_tags = '<img>';
    //只存在字体样式
    $font_tags = '<i><b><u><s><em><strong><font><big><small><sup><sub><bdo><h1><h2><h3><h4><h5><h6>';
    //标题摘要基本格式
    $base_tags = $font_tags . '<p><br><hr><a><img><map><area><pre><code><q><blockquote><acronym><cite><ins><del><center><strike>';
    //兼容Form格式
    $form_tags = $base_tags . '<form><input><textarea><button><select><optgroup><option><label><fieldset><legend>';
    //内容等允许HTML的格式
    $html_tags = $base_tags . '<ul><ol><li><dl><dd><dt><table><caption><td><th><tr><thead><tbody><tfoot><col><colgroup><div><span><object><embed><param>';
    //专题等全HTML格式
    $all_tags = $form_tags . $html_tags . '<!DOCTYPE><meta><html><head><title><body><base><basefont><script><noscript><applet><object><param><style><frame><frameset><noframes><iframe>';
    //过滤标签
    $text = real_strip_tags($text, ${$type . '_tags'});
    // 过滤攻击代码
    if ($type != 'all') {
        // 过滤危险的属性，如：过滤on事件lang js
        while (preg_match('/(<[^><]+)(ondblclick|onclick|onload|onerror|unload|onmouseover|onmouseup|onmouseout|onmousedown|onkeydown|onkeypress|onkeyup|onblur|onchange|onfocus|action|background[^-]|codebase|dynsrc|lowsrc)([^><]*)/i', $text, $mat)) {
            $text = str_ireplace($mat[0], $mat[1] . $mat[3], $text);
        }
        while (preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i', $text, $mat)) {
            $text = str_ireplace($mat[0], $mat[1] . $mat[3], $text);
        }
    }
    return $text;
}

function real_strip_tags($str, $allowable_tags = "")
{
   // $str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
    return strip_tags($str, $allowable_tags);
}

/**
 * 获取楼层信息
 * @param $k
 */
function getLou($k)
{
    $lou = array(
        2 => '沙发',
        3 => '板凳',
        4 => '地板'
    );
    !empty($lou[$k]) && $res = $lou[$k];
    empty($lou[$k]) && $res = $k . '楼';
    return $res;
}

function getMyScore($score_name = 'score')
{
    $uid = is_login();
	clean_query_user_cache($uid,array($score_name));
    $user = query_user(array($score_name), $uid);
    $score = $user[$score_name];
    return $score;
}

// function getScoreTip($before, $after)
// {
//     $score_change = $after - $before;
//     $tip = '';
//     if ($score_change) {
//         $tip = '积分' . ($score_change > 0 ? '加&nbsp;' . $score_change : '减&nbsp;' . $score_change) . ' 。';
//     }
//     return $tip;
// }

function getGoldOptionName($op)
{
	$gold_coin_name = getScoreTypeName($op);
	return $gold_coin_name;
}

// function action_log_and_get_score($action = null, $model = null, $record_id = null, $user_id = null)
// {
//     $score_before = getMyScore();
//     action_log($action, $model, $record_id, $user_id);
//     $score_after = getMyScore();
//     return $score_after - $score_before;
// }


function array_subtract($a, $b)
{
    return array_diff($a, array_intersect($a, $b));
}

/**
 * 取一个二维数组中的每个数组的固定的键知道的值来形成一个新的一维数组
 * @param $pArray 一个二维数组
 * @param $pKey 数组的键的名称
 * @return 返回新的一维数组
 * @author 乾坤网络有限公司
 */
function getSubByKey($pArray, $pKey = "", $pCondition = "")
{
    $result = array();
    if (is_array($pArray)) {
        foreach ($pArray as $temp_array) {
            if (is_object($temp_array)) {
                $temp_array = (array)$temp_array;
            }
            if (("" != $pCondition && $temp_array[$pCondition[0]] == $pCondition[1]) || "" == $pCondition) {
                $result[] = ("" == $pKey) ? $temp_array : isset($temp_array[$pKey]) ? $temp_array[$pKey] : "";
            }
        }
        return $result;
    } else {
        return false;
    }
}

/**
 * create_rand  随机生成一个字符串
 * @param int $length  字符串的长度
 * @return string
 * @author 乾坤网络有限公司
 */
function create_rand($length = 8, $type = 'all')
{
    $num = '0123456789';
    $letter = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if ($type == 'num') {
        $chars = $num;
    } elseif ($type == 'letter') {
        $chars = $letter;
    } else {
        $chars = $letter . $num;
    }

    $str = '';
    for ($i = 0; $i < $length; $i++) {
        $str .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $str;

}

//获取字符串长度，一个汉字2个字节,字符1个字节
function _strlen($str=''){
    if(empty($str)){
        return 0;
    }
    if(!_is_utf8($str)){
        $str=iconv("GBK","UTF-8",$str);
    }
    return ceil((strlen($str)+mb_strlen($str,'utf-8'))/2);
}

//数组转字符串
function Array2String($Array){
    if(!$Array)return false;
    $Return='';
    $NullValue="^^^";
    foreach ($Array as $Key => $Value) {
        if(is_array($Value))
            $ReturnValue='^^array^'.Array2String($Value);
        else
            $ReturnValue=(strlen($Value)>0)?$Value:$NullValue;
        $Return.=urlencode(base64_encode($Key)) . '|' . urlencode(base64_encode($ReturnValue)).'||';
    }
    return urlencode(substr($Return,0,-2));
}

//字符串转数组
function String2Array($String){
    if(NULL==$String)return false;
    $Return=array();
    $String=urldecode($String);
    $TempArray=explode('||',$String);
    $NullValue=urlencode(base64_encode("^^^"));
    foreach ($TempArray as $TempValue) {
        list($Key,$Value)=explode('|',$TempValue);
        $DecodedKey=base64_decode(urldecode($Key));
        if($Value!=$NullValue) {
            $ReturnValue=base64_decode(urldecode($Value));
            if(substr($ReturnValue,0,8)=='^^array^')
                $ReturnValue=String2Array(substr($ReturnValue,8));
            $Return[$DecodedKey]=$ReturnValue;
        }
        else
            $Return[$DecodedKey]=NULL;
    }
    return $Return;
}

function GET_Temp_Key(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = chr(123)// "{"
        .substr($charid, 0, 8).$hyphen
        .substr($charid, 8, 4).$hyphen
        .substr($charid,12, 4).$hyphen
        .substr($charid,16, 4).$hyphen
        .substr($charid,20,12)
        .chr(125);// "}"
        return $uuid;
    }
}

function ARSC($Data,$Key)
{
    $key[] ="";
    $box[] ="";
    $pwd_length = strlen($Key);
    $data_length = strlen($Data);
    for ($i = 0; $i < 256; $i++)
    {
        $key[$i] = ord($Key[$i % $pwd_length]);
        $box[$i] = $i;
    }
    for ($j = $i = 0; $i < 256; $i++)
    {
    $j = ($j + $box[$i] + $key[$i]) % 256;
    $tmp = $box[$i];
    $box[$i] = $box[$j];
    $box[$j] = $tmp;
    }
    for ($a = $j = $i = 0; $i < $data_length; $i++)
        {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
    
        $k = $box[(($box[$a] + $box[$j]) % 256)];
        $cipher .= chr(ord($Data[$i]) ^ $k);
    }
    return $cipher;
}

function BytesHex($s){
    $r = "";
    for ( $i = 0; $i<strlen($s); $i += 2)
    {
    $x1 = ord($s{$i});
    $x1 = ($x1>=48 && $x1<58) ? $x1-48 : $x1-97+10;
    $x2 = ord($s{$i+1});
    $x2 = ($x2>=48 && $x2<58) ? $x2-48 : $x2-97+10;
    $r .= chr((($x1 << 4) & 0xf0) | ($x2 & 0x0f));
    }
    return $r;
}

function HexBytes($s) {
    $r = "";
    $hexes = array ("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f");
    for ($i=0; $i<strlen($s); $i++) {$r .= ($hexes [(ord($s{$i}) >> 4)] . $hexes [(ord($s{$i}) & 0xf)]);}
    return $r;
}


function str_decode($str,$key,$key_rand){
    return (string)ARSC(BytesHex((string)ARSC(BytesHex($str),(string)$key_rand)),$key);
}

function str_encode($str,$key,$key_rand){
    $key_temp = HexBytes(ARSC($str,(string)$key_rand));
    $key_temp = strtoupper(HexBytes(ARSC($key_temp,$key)));
    return $key_temp;
}

function numtopic($num){
	$strlen=strlen($num);
	for ($i = 0; $i < $strlen; $i++) {
		switch(substr($num,$i,1)){
			case 0:
				$newnum.= '<span class="zero"></span>';
				break;
			case 1:
				$newnum.= '<span class="one"></span>';
				break;
			case 2:
				$newnum.= '<span class="two"></span>';
				break;
			case 3:
				$newnum.= '<span class="three"></span>';
				break;
			case 4:
				$newnum.= '<span class="four"></span>';
				break;
			case 5:
				$newnum.= '<span class="five"></span>';
				break;
			case 6:
				$newnum.= '<span class="six"></span>';
				break;
			case 7:
				$newnum.= '<span class="seven"></span>';
				break;
			case 8:
				$newnum.= '<span class="eight"></span>';
				break;
			case 9:
				$newnum.= '<span class="nine"></span>';
				break;
			default:
				$newnum.= '<span class="zero"></span>';
		}
	}
	return $newnum;
}

/**
 * 用户扩展资料可添加关联字段
 * @param string $id 关联数据表ID
 * @param string $field 需要返回的字段内容
 * @param string $table 关联数据表
 */
// function get_userdata_join($id = null, $field = null, $table = null)
// {
// 	if (empty($table) || empty($field)) {
// 		return false;
// 	}
// 	if (empty($id)) {
// 		$data = D($table)->select();
// 		foreach ($data as $key => $val) {
// 			$list[$key] = $val;
// 		}
// 		return $list;
// 	} else {
// 		if (is_array($id)) {
// 			$map['id'] = array('in', $id);
// 			$data = D($table)->where($map)->getField($field, true);
// 			return implode(',', $data);
// 		} else {
// 			$map['id'] = $id;
// 			$data = D($table)->where($map)->getField($field);
// 			return $data;
// 		}
// 	}
// }

/**
 * 获取指定表字段信息，可定义多个组合查询条件（查阅thinkphp）返回查询字段和ID
 * @param string $map 数组：条件字段以及条件（array('level'=>1,'name'=>array('like','%UUIMA'));）
 * @param string $field 需要返回的字段
 * @param string $table 查询表
 * @param string $yesnoid 是否返回ID(预留·)
 */
// function get_data_field_id($map = null, $field = null, $table = null, $yesnoid = '')
// {
// 	if (empty($table) || empty($field)) {
// 		return false;
// 	}

// 	if (empty($map)) {
// 		$data = D($table)->select();
// 		foreach ($data as $key => $val) {
// 			$list[$key]['id'] = $val['id'];
// 			$list[$key]['value'] = $val[$field];
// 		}
// 		return $list;
// 	} else {
// 		if (empty($yesnoid)) {
// 			$data = D($table)->where($map)->select();
// 			foreach ($data as $key => $val) {
// 				$list[$key]['id'] = $val['id'];
// 				$list[$key]['value'] = $val[$field];
// 			}
// 		} else {
// 			$list = D($table)->where($map)->getField($field);
// 		}
// 		return $list;
// 	}
// }

function verify()
{
	$type = C('VERIFY_TYPE');
	$verify = new \think\Verify();
	switch ($type) {
		case 1 :
			$verify->useZh = true;
			break;
		case 2 :
			$verify->codeSet = 'abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY';
			break;
		case 3 :
			$verify->codeSet = '0123456789';
			break;
		case 4 :
			break;
		default:

	}
	$verify->entry(1);
}

function check_verify_open($open)
{
	$config = C('VERIFY_OPEN');

	if ($config) {
		$config = explode(',', $config);
		if (in_array($open, $config)) {
			return true;
		}
	}
	return false;
}

// function check_is_in_config($key,$config){
// 	!is_array($config)  && $config = explode(',',$config);
// 	return in_array($key,$config);

// }

// function convert_url_query($query)
// {
// 	if(!empty($query)){
// 		$query = urldecode($query);
// 		$queryParts = explode('&', $query);
// 		$params = array();
// 		foreach ($queryParts as $param)
// 		{
// 			$item = explode('=', $param);
// 			$params[$item[0]] = $item[1];
// 		}
// 		return $params;
// 	}
// 	return '';
// }


/**
 * cut_str  截取字符串
 * @param $search
 * @param $str
 * @param string $place
 */
function cut_str($search,$str,$place=''){
	switch($place){
		case 'l':
			$result = preg_replace('/.*?'.addcslashes(quotemeta($search),'/').'/','',$str);
			break;
		case 'r':
			$result = preg_replace('/'.addcslashes(quotemeta($search),'/').'.*/','',$str);
			break;
		default:
			$result =  preg_replace('/'.addcslashes(quotemeta($search),'/').'/','',$str);
	}
	return $result;
}


/**
 * array_search_key 搜索数组中某个键为某个值的数组
 * @param $array
 * @param $key
 * @param $value
 */
function array_search_key($array, $key, $value)
{
	foreach ($array as $k => $v) {
		if ($v[$key] == $value) {
			return $array[$k];
		}
	}
	return false;
}


/**
 * array_delete  删除数组中的某个值
 * @param $array
 * @param $value
 */
function array_delete($array,$value){
	$key = array_search($value, $array);
	if ($key !== false)
		array_splice($array, $key, 1);
	return $array;
}

/**
 * get_upload_config  获取上传驱动配置
 * @param $driver
 */
function get_upload_config($driver){
	if($driver == 'local'){
		$uploadConfig =     C("UPLOAD_{$driver}_CONFIG");
	}else{
		$name = get_addon_class($driver);
		$class = new $name();
		$uploadConfig = $class->uploadConfig();
	}
	return $uploadConfig;
}

/**
 * check_driver_is_exist 判断上传驱动插件是否存在
 * @param $driver
 */
function check_driver_is_exist($driver){
	if($driver == 'local'){
		return $driver;
	}else{
		$name = get_addon_class($driver);
		if (class_exists($name)) {
			return $driver;
		}else{
			return 'local';
		}
	}
}

/**
 * check_sms_hook_is_exist  判断短信服务插件是否存在，不存在则返回none
 * @param $driver
 */
function check_sms_hook_is_exist($driver){
	if($driver == 'none'){
		return $driver;
	}else{
		$name = get_addon_class($driver);
		if (class_exists($name)) {
			return $driver;
		}else{
			return 'none';
		}
	}
}

/**
 *自动判断把gbk或gb2312编码的字符串转为utf8
 *能自动判断输入字符串的编码类，如果本身是utf-8就不用转换，否则就转换为utf-8的字符串
 *支持的字符编码类型是：utf-8,gbk,gb2312
 *@$str:string 字符串
 */
function yang_gbk2utf8($str){
	$charset = mb_detect_encoding($str,array('UTF-8','GBK','GB2312'));
	$charset = strtolower($charset);
	if('cp936' == $charset){
		$charset='GBK';
	}
	if("utf-8" != $charset){
		$str = iconv($charset,"UTF-8//IGNORE",$str);
	}
	return $str;
}

//判断域名是否正常
function is_domain($domain)
{
	return !empty($domain) && strpos($domain, '--') === false &&
	//preg_match('/^[a-z0-9]+([a-z0-9-]*(?:[a-z0-9]+))?(\.us|\.tv|\.org\.cn|\.org|\.net\.cn|\.net|\.mobi|\.me|\.la|\.info|\.hk|\.gov\.cn|\.edu|\.com\.cn|\.com|\.co\.jp|\.co|\.cn|\.cc|\.biz)$/i', $domain) ? true : false;
	preg_match('/^[a-z0-9]+([a-z0-9-]*(?:[a-z0-9]+))?(.[a-z0-9]+([a-z0-9-]*(?:[a-z0-9]+))?)(\.us|\.tv|\.org\.cn|\.org|\.net\.cn|\.net|\.mobi|\.me|\.la|\.info|\.hk|\.gov\.cn|\.edu|\.com\.cn|\.com|\.co\.jp|\.co|\.cn|\.cc|\.biz)$/i', $domain) ? true : false;
}

// function home_addons_url($url, $param=array(), $suffix = true, $domain = false)
// {
    // $url = parse_url($url);
    // $case = C('URL_CASE_INSENSITIVE');
    // $addons = $case ? parse_name($url['scheme']) : $url['scheme'];
    // $controller = $case ? parse_name($url['host']) : $url['host'];
    // $action = trim($case ? strtolower($url['path']) : $url['path'], '/');

    // /* 解析URL带的参数 */
    // if (isset($url['query'])) {
        // parse_str($url['query'], $query);
        // $param = array_merge($query, $param);
    // }

    // /* 基础参数 */
    // $params = array(
        // '_addons' => $addons,
        // '_controller' => $controller,
        // '_action' => $action,
    // );

    // $params = array_merge($params, $param); //添加额外参数
    // return U('Home/Addons/execute', $params, $suffix, $domain);

// }

/**
 * ===========================================
 *  add with 2016.2.25 by 朝兮夕兮 start
 * ===========================================
 * 执行SQL文件
 */
function execute_sql_file($sql_path) {
	// 读取SQL文件
	$sql = qn_file_get_contents ( $sql_path );
	$sql = str_replace ( "\r", "\n", $sql );
	$sql = explode ( ";\n", $sql );
	
	// 替换表前缀
	$orginal = 'qn_';
	$orginal2 = 'wzb_';
	$prefix = C ( 'DB_PREFIX' );
	$sql = str_replace ( "{$orginal}", "{$prefix}", $sql );
	$sql = str_replace ( "{$orginal2}", "{$prefix}", $sql );
	// 开始安装
	foreach ( $sql as $value ) {
		$value = trim ( $value );
		if (empty ( $value ))
			continue;
		
		$res = M ()->execute ( $value );
		//dump($res);
		// dump(M()->getLastSql());
	}
}


// 全局的安全过滤函数
function safe($text, $type = 'html') {
	// 无标签格式
	$text_tags = '';
	// 只保留链接
	$link_tags = '<a>';
	// 只保留图片
	$image_tags = '<img>';
	// 只存在字体样式
	$font_tags = '<i><b><u><s><em><strong><font><big><small><sup><sub><bdo><h1><h2><h3><h4><h5><h6>';
	// 标题摘要基本格式
	$base_tags = $font_tags . '<p><br><hr><a><img><map><area><pre><code><q><blockquote><acronym><cite><ins><del><center><strike><section><header><footer><article><nav><audio><video>';
	// 兼容Form格式
	$form_tags = $base_tags . '<form><input><textarea><button><select><optgroup><option><label><fieldset><legend>';
	// 内容等允许HTML的格式
	$html_tags = $base_tags . '<meta><ul><ol><li><dl><dd><dt><table><caption><td><th><tr><thead><tbody><tfoot><col><colgroup><div><span><object><embed><param>';
	// 全HTML格式
	$all_tags = $form_tags . $html_tags . '<!DOCTYPE><html><head><title><body><base><basefont><script><noscript><applet><object><param><style><frame><frameset><noframes><iframe>';
	// 过滤标签
	$text = html_entity_decode ( $text, ENT_QUOTES, 'UTF-8' );
	$text = strip_tags ( $text, ${$type . '_tags'} );
	
	// 过滤攻击代码
	if ($type != 'all') {
		// 过滤危险的属性，如：过滤on事件lang js
		while ( preg_match ( '/(<[^><]+)(ondblclick|onclick|onload|onerror|unload|onmouseover|onmouseup|onmouseout|onmousedown|onkeydown|onkeypress|onkeyup|onblur|onchange|onfocus|codebase|dynsrc|lowsrc)([^><]*)/i', $text, $mat ) ) {
			$text = str_ireplace ( $mat [0], $mat [1] . $mat [3], $text );
		}
		while ( preg_match ( '/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i', $text, $mat ) ) {
			$text = str_ireplace ( $mat [0], $mat [1] . $mat [3], $text );
		}
	}
	return $text;
}

function remove_xss($val)
{
	$val = htmlspecialchars_decode($val);
	$val = strip_tags($val, '<img><attach><u><p><b><i><a><strike><pre><code><font><blockquote><span><ul><li><table><tbody><tr><td><ol><iframe><embed>');
	$val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
	$search = 'abcdefghijklmnopqrstuvwxyz';
	$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$search .= '1234567890!@#$%^&*()';
	$search .= '~`";:?+/={}[]-_|\'\\';
	for ($i = 0; $i < strlen($search); $i++) {
		$val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val);
		$val = preg_replace('/(�{0,8}'.ord($search[$i]).';?)/', $search[$i], $val);
	}
	
	$ra1 = array('embed', 'iframe', 'frame', 'ilayer', 'layer', 'javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'object', 'frameset', 'bgsound', 'title', 'base');
	$ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
	$ra = array_merge($ra1, $ra2);

	$found = true;
	while ($found == true) {
		$val_before = $val;
		for ($i = 0; $i < sizeof($ra); $i++) {
			$pattern = '/';
			for ($j = 0; $j < strlen($ra[$i]); $j++) {
				if ($j > 0) {
					$pattern .= '(';
					$pattern .= '(&#[xX]0{0,8}([9ab]);)';
					$pattern .= '|';
					$pattern .= '|(�{0,8}([9|10|13]);)';
					$pattern .= ')*';
				}
				$pattern .= $ra[$i][$j];
			}
			$pattern .= '/i';
			$replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2);
			$val = preg_replace($pattern, $replacement, $val);
			if ($val_before == $val) {
				$found = false;
			}
		}
	}
	$val=htmlspecialchars($val);
	return $val;
}

require_once(APP_PATH . 'common/common/tao.func.php');
require_once(APP_PATH . 'common/conf/config_ucenter.php');