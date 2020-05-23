<?php
/**
 * 后台公共文件
 * 主要定义后台公共函数库
 */

function Char_cv($mixed,$isint=false,$istrim=false) {
	if (is_array($mixed)) {
		foreach ($mixed as $key => $value) {
			$mixed[$key] = Char_cv($value,$isint,$istrim);
		}
	} elseif ($isint == 2) {
		$mixed = (int)$mixed;
	} elseif (!is_numeric($mixed) && ($istrim ? $mixed = trim($mixed) : $mixed) && $mixed) {
		$mixed = str_replace(array("\0","%00","\r"),'',$mixed);
		$mixed = preg_replace(
				array('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/','/&(?!(#[0-9]+|[a-z]+);)/is'),
				array('','&amp;'),
				$mixed
		);
		$mixed = str_replace(array("%3C",'<'),'&lt;',$mixed);
		$mixed = str_replace(array("%3E",'>'),'&gt;',$mixed);
		$mixed = str_replace(array('"',"'","\t",'  '),array('&quot;','&#39;','    ','&nbsp;&nbsp;'),$mixed);
	}
	return $mixed;
}
function openfile($filename){
	$filedb = explode('<:wind:>',str_replace("\n","\n<:wind:>",readover($filename)));
	$count = count($filedb)-1;
	if ($count > -1 && (!$filedb[$count] || $filedb[$count]=="\r")) {
		unset($filedb[$count]);
	}
	empty($filedb) && $filedb[0] = '';
	return $filedb;
}
function readover($filename,$method='rb'){
	$filename = Pcv($filename);
	$filedata = '';
	if ($handle = @fopen($filename,$method)) {
		flock($handle,LOCK_SH);
		$filedata = @fread($handle,filesize($filename));
		fclose($handle);
	}
	return $filedata;
}
function writeover($filename,$data,$method='rb+',$iflock=1,$check=1,$chmod=1){
	$filename = Pcv($filename,$check);
	touch($filename);
	$handle = fopen($filename,$method);
	$iflock && flock($handle,LOCK_EX);
	fwrite($handle,$data);
	$method=='rb+' && ftruncate($handle,strlen($data));
	fclose($handle);
	$chmod && @chmod($filename,0777);
}
function Pcv($filename,$ifcheck=1){
	$tmpname = strtolower($filename);
	$tmparray = array('://',"\0");
	$ifcheck && $tmparray[] = '..';
	if (str_replace($tmparray,'',$tmpname)!=$tmpname) {
		exit('Forbidden');
	}
	return $filename;
}
/* 解析列表定义规则*/

function get_list_field($data, $grid,$model){

	// 获取当前字段数据
    foreach($grid['field'] as $field){
        $array  =   explode('|',$field);
        $temp  =	$data[$array[0]];
        // 函数支持
        if(isset($array[1])){
            $temp = call_user_func($array[1], $temp);
        }
        $data2[$array[0]]    =   $temp;
    }
    if(!empty($grid['format'])){
        $value  =   preg_replace_callback('/\[([a-z_]+)\]/', function($match) use($data2){return $data2[$match[1]];}, $grid['format']);
    }else{
        $value  =   implode(' ',$data2);
    }

	// 链接支持
	if(!empty($grid['href'])){
		$links  =   explode(',',$grid['href']);
        foreach($links as $link){
            $array  =   explode('|',$link);
            $href   =   $array[0];
            if(preg_match('/^\[([a-z_]+)\]$/',$href,$matches)){
                $val[]  =   $data2[$matches[1]];
            }else{
                $show   =   isset($array[1])?$array[1]:$value;
                // 替换系统特殊字符串
                $href	=	str_replace(
                    array('[DELETE]','[EDIT]','[MODEL]'),
                    array('del?ids=[id]&model=[MODEL]','edit?id=[id]&model=[MODEL]',$model['id']),
                    $href);

                // 替换数据变量
                $href	=	preg_replace_callback('/\[([a-z_]+)\]/', function($match) use($data){return $data[$match[1]];}, $href);

                $val[]	=	'<a href="'.U($href).'" class="btn btn-sm btn-info">'.$show.'</a>';
            }
        }
        $value  =   implode(' ',$val);
	}
    return $value;
}

// 获取模型名称
function get_model_by_id($id){
    return $model = M('Model')->getFieldById($id,'title');
}

// 获取属性类型信息
function get_attribute_type($type=''){
    // TODO 可以加入系统配置
    static $_type = array(
        'num'       =>  array('数字','int(10) UNSIGNED NOT NULL'),
        'string'    =>  array('字符串','varchar(255) NOT NULL'),
        'textarea'  =>  array('文本框','text NOT NULL'),
        'datetime'  =>  array('时间','int(10) NOT NULL'),
        'bool'      =>  array('布尔','tinyint(2) NOT NULL'),
        'select'    =>  array('枚举','char(50) NOT NULL'),
    	'radio'		=>	array('单选','char(10) NOT NULL'),
    	'checkbox'	=>	array('多选','varchar(100) NOT NULL'),
    	'editor'    =>  array('编辑器','text NOT NULL'),
    	'picture'   =>  array('上传图片','int(10) UNSIGNED NOT NULL'),
    	'file'    	=>  array('上传附件','int(10) UNSIGNED NOT NULL'),
    );
    return $type?$_type[$type][0]:$_type;
}

/**
 * 获取对应状态的文字信息
 * @param int $status
 * @return string 状态文字 ，false 未获取到
 * @author 乾坤网络有限公司
 */
function get_status_title($status = null){
    if(!isset($status)){
        return false;
    }
    switch ($status){
        case -1 : return    '已删除';   break;
        case 0  : return    '禁用';     break;
        case 1  : return    '正常';     break;
        case 2  : return    '待审核';   break;
        default : return    false;      break;
    }
}

// 获取数据的状态操作
function show_status_op($status) {
    switch ($status){
        case 0  : return    '启用';     break;
        case 1  : return    '禁用';     break;
        case 2  : return    '审核';		break;
        default : return    '发布';      break;
    }
}

/**
 * 获取文档的类型文字
 * @param string $type
 * @return string 状态文字 ，false 未获取到
 * @author 乾坤网络有限公司
 */
function get_document_type($type = null){
    if(!isset($type)){
        return false;
    }
    switch ($type){
        case 1  : return    '目录'; break;
        case 2  : return    '主题'; break;
        case 3  : return    '段落'; break;
        default : return    false;  break;
    }
}

/**
 * 获取配置的类型
 * @param string $type 配置类型
 * @return string
 * @author 乾坤网络有限公司
 */
function get_config_type($type=0){
    $list = C('CONFIG_TYPE_LIST');
    return $list[$type];
}

/**
 * 获取配置的分组
 * @param string $group 配置分组
 * @return string
 * @author 乾坤网络有限公司
 */
function get_config_group($group=0){
    $list = C('CONFIG_GROUP_LIST');
    return $group?$list[$group]:'';
}

/**
 * select返回的数组进行整数映射转换
 *
 * @param array $map  映射关系二维数组  array(
 *                                          '字段名1'=>array(映射关系数组),
 *                                          '字段名2'=>array(映射关系数组),
 *                                           ......
 *                                       )
 * @author 乾坤网络有限公司
 * @return array
 *
 *  array(
 *      array('id'=>1,'title'=>'标题','status'=>'1','status_text'=>'正常')
 *      ....
 *  )
 *
 */
function int_to_string(&$data,$map=array('status'=>array(1=>'正常',-1=>'删除',0=>'禁用',2=>'未审核',3=>'草稿'))) {
    if($data === false || $data === null ){
        return $data;
    }
    $data = (array)$data;
    foreach ($data as $key => $row){
        foreach ($map as $col=>$pair){
            if(isset($row[$col]) && isset($pair[$row[$col]])){
                $data[$key][$col.'_text'] = $pair[$row[$col]];
            }
        }
    }
    return $data;
}

/**
 * 动态扩展左侧菜单,base.html里用到
 * @author 乾坤网络有限公司
 */
function extra_menu($extra_menu,&$base_menu){
    foreach ($extra_menu as $key=>$group){
        if( isset($base_menu['child'][$key]) ){
            $base_menu['child'][$key] = array_merge( $base_menu['child'][$key], $group);
        }else{
            $base_menu['child'][$key] = $group;
        }
    }
}

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 乾坤网络有限公司
 */
function check_verify($code, $id = 1){
    $verify = new \think\Verify();
    return $verify->check($code, $id);
}

 // 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string) {
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')){
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    }else{
        $value  =   $array;
    }
    return $value;
}


 // 分析枚举类型字段值 格式 a:名称1,b:名称2
 // 暂时和 parse_config_attr功能相同
 // 但请不要互相使用，后期会调整
function parse_field_attr($string) {
    if(0 === strpos($string,':')){
        // 采用函数定义
        return   eval(substr($string,1).';');
    }
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')){
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    }else{
        $value  =   $array;
    }
    return $value;
}

/**
 * 获取行为数据
 * @param string $id 行为id
 * @param string $field 需要获取的字段
 * @author 乾坤网络有限公司
 */
function get_action($id = null, $field = null){
	if(empty($id) && !is_numeric($id)){
		return false;
	}
	$list = S('action_list');
	if(empty($list[$id])){
		$map = array('status'=>array('gt', -1), 'id'=>$id);
		$list[$id] = M('Action')->where($map)->field(true)->find();
	}
	return empty($field) ? $list[$id] : $list[$id][$field];
}

/**
 * 根据条件字段获取数据
 * @param mixed $value 条件，可用常量或者数组
 * @param string $condition 条件字段
 * @param string $field 需要返回的字段，不传则返回整个数据
 * @author 乾坤网络有限公司
 */
function get_document_field($value = null, $condition = 'id', $field = null){
	if(empty($value)){
		return false;
	}

	//拼接参数
	$map[$condition] = $value;
	$info = M('Model')->where($map);
	if(empty($field)){
		$info = $info->field(true)->find();
	}else{
		$info = $info->getField($field);
	}
	return $info;
}

/**
 * 获取行为类型
 * @param intger $type 类型
 * @param bool $all 是否返回全部类型
 * @author 乾坤网络有限公司
 */
function get_action_type($type, $all = false){
	$list = array(
		1=>'系统',
		2=>'用户',
	);
	if($all){
		return $list;
	}
	return $list[$type];
}

/**
 * 获取行为指定用户组名
 * @param intger $usergroupid 类型id
 * @author 朝兮夕兮
 */
function get_action_usergroups($usergroupid){
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

/**
 * 友好时间显示开始
 * @Return: array
 * @author 乾坤网络有限公司
 */
function f_date($time) {
	if (!$time)
		return false;
	$fdate = '';
	$d = time() - intval($time);
	$ld = $time - mktime(0, 0, 0, 0, 0, date('Y')); //得出年
	$md = $time - mktime(0, 0, 0, date('m'), 0, date('Y')); //得出月
	$byd = $time - mktime(0, 0, 0, date('m'), date('d') - 2, date('Y')); //前天
	$yd = $time - mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')); //昨天
	$dd = $time - mktime(0, 0, 0, date('m'), date('d'), date('Y')); //今天
	$td = $time - mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')); //明天
	$atd = $time - mktime(0, 0, 0, date('m'), date('d') + 2, date('Y')); //后天
	if ($d == 0) {
		$fdate = '刚刚';
	} else {
		switch ($d) {
			case $d < $atd:
				$fdate = date('Y年m月d日', $time);
				break;
			case $d < $td:
				$fdate = '后天' . date('H:i', $time);
				break;
			case $d < 0:
				$fdate = '明天' . date('H:i', $time);
				break;
			case $d < 60:
				$fdate = $d . '秒前';
				break;
			case $d < 3600:
				$fdate = floor($d / 60) . '分钟前';
				break;
			case $d < $dd:
				$fdate = floor($d / 3600) . '小时前';
				break;
			case $d < $yd:
				$fdate = '昨天' . date('H:i', $time);
				break;
			case $d < $byd:
				$fdate = '前天' . date('H:i', $time);
				break;
			case $d < $md:
				$fdate = date('m月d日 H:i', $time);
				break;
			case $d < $ld:
				$fdate = date('m月d日', $time);
				break;
			default:
				$fdate = date('Y年m月d日', $time);
				break;
		}
	}
	return $fdate;
}
//友好时间显示结束

function get_nick_name($uid){
	if(empty($uid))
	{
		return '无';
	}
	return D('User/Member')->where(array('uid'=>(int)$uid))->getField('nickname');
}

function formatLog($log)
{
	$log = explode("\r\n", $log);
	$log = '<li>' . implode('</li><li>', $log) . '</li>';
	return $log;
}

/**
 *
 * @param type $v 要传入的组合阵列
 * @param type $keyname 对应的KEY名称。
 * @param type $valname 要写入的值KEY名称
 * @param type $s 重组译的阵例。预设空值。
 */
function test($v,$keyname,$valname,$s=Array())
{
    foreach($v AS $_k => $d)
    {

        if(!isset($s[$d['uid']])){
            $s[$d['uid']]=$d;
        }else{

            $s[$d['uid']]['sumgold'] =(float)$s[$d['uid']]['sumgold']+(float)$d['sumgold'];
        }
    }
    return $s;
}


/**
 * 根据数组中的某个键值大小进行排序，仅支持二维数组
 *
 * @param array $array 排序数组
 * @param string $key 键值
 * @param bool $asc 默认正序
 * @return array 排序后数组
 */
function arraySortByKey(array $array, $key, $asc = true)
{
    $result = array();
    // 整理出准备排序的数组
    foreach ( $array as $k => &$v ) {
        $values[$k] = isset($v[$key]) ? $v[$key] : '';
    }
    unset($v);
    // 对需要排序键值进行排序
    $asc ? asort($values) : arsort($values);
    // 重新排列原有数组
    foreach ( $values as $k => $v ) {
        $result[$k] = $array[$k];
    }

    return $result;
}

function show_cloud_cover($path){
    //不存在http://
    $not_http_remote=(strpos($path, 'http://') === false);
    //不存在https://
    $not_https_remote=(strpos($path, 'https://') === false);
    if ($not_http_remote && $not_https_remote) {
        //本地url
        return str_replace('//', '/', C('TMPL_PARSE_STRING.__CLOUD__') . $path); //防止双斜杠的出现
    } else {
        //远端url
        return $path;
    }
}
