<?php
function curl($url, $postFields = null)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FAILONERROR, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	if ($this->readTimeout) {
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->readTimeout);
	}
	if ($this->connectTimeout) {
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
	}
	curl_setopt ( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 Safari/537.11" );
	//https 请求
	if(strlen($url) > 5 && strtolower(substr($url,0,5)) == "https" ) {
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	}

	if (is_array($postFields) && 0 < count($postFields))
	{
		$postBodyString = "";
		$postMultipart = false;
		foreach ($postFields as $k => $v)
		{
			if(!is_string($v))
				continue ;

			if("@" != substr($v, 0, 1))//判断是不是文件上传
			{
				$postBodyString .= "$k=" . urlencode($v) . "&"; 
			}
			else//文件上传用multipart/form-data，否则用www-form-urlencoded
			{
				$postMultipart = true;
				if(class_exists('\CURLFile')){
					$postFields[$k] = new \CURLFile(substr($v, 1));
				}
			}
		}
		unset($k, $v);
		curl_setopt($ch, CURLOPT_POST, true);
		if ($postMultipart)
		{
			if (class_exists('\CURLFile')) {
			    curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
			} else {
			    if (defined('CURLOPT_SAFE_UPLOAD')) {
			        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
			    }
			}
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
		}
		else
		{
			$header = array("content-type: application/x-www-form-urlencoded; charset=UTF-8");
			curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
			curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString,0,-1));
		}
	}
	$reponse = curl_exec($ch);
	
	if (curl_errno($ch))
	{
		throw new Exception(curl_error($ch),0);
	}
	else
	{
		$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if (200 !== $httpStatusCode)
		{
			throw new Exception($reponse,$httpStatusCode);
		}
	}
	curl_close($ch);
	return $reponse;
}

function curlPost($url, $data) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$info = curl_exec($ch);
	curl_close($ch);
	return $info;
}

function curlGet($url){
	$oCurl = curl_init();
	if(stripos($url,"https://")!==FALSE){
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
	}
	curl_setopt($oCurl, CURLOPT_URL, $url);
	curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
	$sContent = curl_exec($oCurl);
	$aStatus  = curl_getinfo($oCurl);
	curl_close($oCurl);
	if(intval($aStatus["http_code"])==200){
		return $sContent;
	}else{
		return false;
	}
}

/*
*@通过curl方式获取指定的图片到本地
*@ 完整的图片地址
*@ 要存储的文件名
*/
function getImg($url = "", $filename = "")
{
	//去除URL连接上面可能的引号
	//$url = preg_replace( '/(?:^['"]+|['"/]+$)/', '', $url );
	$hander = curl_init();
	$fp = fopen($filename,'wb');
	curl_setopt($hander,CURLOPT_URL,$url);
	curl_setopt($hander,CURLOPT_FILE,$fp);
	curl_setopt($hander,CURLOPT_HEADER,0);
	curl_setopt($hander,CURLOPT_FOLLOWLOCATION,1);
	//curl_setopt($hander,CURLOPT_RETURNTRANSFER,false);//以数据流的方式返回数据,当为false是直接显示出来
	curl_setopt($hander,CURLOPT_TIMEOUT,60);
	curl_exec($hander);
	curl_close($hander);
	fclose($fp);
	Return true;
}

function file_get_contents_diy($url, $method = 'get') {
	$context['http'] = array (
		'timeout' => 15,
	);
	$head = '';
	if($head!=''){
		$context['http']['header']= $head;
	}
	if ($method == 'get') {
		$output = file_get_contents($url,0,stream_context_create($context));
		return $output;
	} else {
		$a = explode('?', $url);
		$context['http']['method']='POST';
		$context['http']['content']=$a[1];
		$output = file_get_contents($a[0], false, stream_context_create($context));
		return $output;
	}
}

function getCookie($cookies)
{

	$cookies_string = '';
	foreach ($cookies as $name => $value) {
		$cookies_string .= $name . '=' . $value . ';';
	}
	return $cookies_string;
}



//生成系统AUTH_KEY
function build_auth_key()
{
	$chars = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$chars .= '`~!@#$%^&*()_+-=[]{};:"|,.<>/?';
	$chars = str_shuffle($chars);
	return substr($chars, 0, 40);
}


// 防超时的file_get_contents改造函数
function qn_file_get_contents($url) {
	$context = stream_context_create (
		array (
			'http' => array (
				'timeout' => 30 
			)
		)
	); // 超时时间，单位为秒
	
	return file_get_contents ( $url, 0, $context );
}

/**
 * qn_get_headers 获取链接header
 * @param $url
 */
function qn_get_headers($url){
    $ch= curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $f=curl_exec($ch);
    curl_close($ch);
    $h=explode("\n",$f);
    $r=array();
    foreach( $h as $t){
        $rr=explode(":",$t,2);
        if(count($rr)==2 ){ $r[$rr[0]]=trim($rr[1]);}
    }
    return $r;
}


function qn_addons_url($url, $param)
{
    // 拆分URL
    $url = explode('/', $url);
    $addon = $url[0];
    $controller = $url[1];
    $action = $url[2];

    // 调用u函数
    $param['_addons'] = $addon;
    $param['_controller'] = $controller;
    $param['_action'] = $action;
    return U("home/addons/execute", $param);
}



function qn_func($url){
	$c=api('Collect/fun',array('url'=>$url, 'method'=> 'get'));
	return $c;
}

function qn_get_xml($url){
    $c=api('Collect/get_xml','url='.$url);
    return $c;
}

function qn_get_json($url){
    $c=api('Collect/get_json','url='.$url);
    return $c;
}

function qk_stripslashes($string) {
	!defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
	if(MAGIC_QUOTES_GPC) {
		return stripslashes($string);
	} else {
		return $string;
	}
}

function appstoreu($url,$p=array()){
	return "http://www.qkephp.com" .cloudu($url,$p);
}

function appstoreinstallu($url,$p=array()){
	$s = $sep = '';
	foreach($p as $k => $v) {
		$k = urlencode($k);
		if(is_array($v)) {
			$s2 = $sep2 = '';
			foreach($v as $k2 => $v2) {
				$k2 = urlencode($k2);
				$s2 .= "$sep2{$k}[$k2]=".urlencode(uc_stripslashes($v2));
				$sep2 = '&';
			}
			$s .= $sep.$s2;
		} else {
			$s .= "$sep$k=".urlencode(qk_stripslashes($v));
		}
		$sep = '&';
	}
	return "http://www.qkephp.com" .$url.'?'.$s;
}

function cloudu($url, $p=array())
{
	$url = U($url, $p);
	return str_replace(strtolower(__ROOT__), '', $url);
}

//====================================================================

//获取插件的配置数组
function getAddonConfig($name, $token = '') {
	static $_config = array ();
	if (isset ( $_config [$name] )) {
		return $_config [$name];
	}
	$config = array ();
	//$token = empty ( $token ) ? get_token () : $token;
	// dump($token);
	// if (! empty ( $token )) {
		// $addon_config = get_token_appinfo ( $token, 'addon_config' );
		// $addon_config = json_decode ( $addon_config, true );
		// if (isset ( $addon_config [$name] ))
			// $config = $addon_config [$name];
	// }
	if (empty ( $config )) {
		$config = D ( 'Home/Addons' )->getInfoByName ( $name );
		$config = json_decode ( $config ['config'], true );
	}
	if (! $config) {
		$temp_arr = include_once QN_ADDON_PATH . $name . '/config.php';
		foreach ( $temp_arr as $key => $value ) {
			$config [$key] = $temp_arr [$key] ['value'];
		}
	}
	$_config [$name] = $config;
	return $config;
}


function getPluginConfig($name, $token = '') {
	static $_config = array ();
	if (isset ( $_config [$name] )) {
		return $_config [$name];
	}
	
	$config = array ();
	
	// $token = empty ( $token ) ? get_token () : $token;
	//dump($token);
	// if (! empty ( $token )) {
		// $addon_config = get_token_appinfo ( $token, 'addon_config' );
		// $addon_config = json_decode ( $addon_config, true );
		// if (isset ( $addon_config [$name] ))
			// $config = $addon_config [$name];
	// }
	if (empty ( $config )) {
		$config = D ( 'Home/Plugins' )->getInfoByName ( $name );
		$config = json_decode ( $config ['config'], true );
	}
	if (! $config) {
		$temp_arr = include_once QN_PLUGIN_PATH . $name . '/config.php';
		foreach ( $temp_arr as $key => $value ) {
			$config [$key] = $temp_arr [$key] ['value'];
		}
	}
	$_config [$name] = $config;
	return $config;
}

// 获取当前用户的Token
function get_token($token = NULL) {
	$stoken = session ( 'token' );
	$domain = explode ( '.', SITE_DOMAIN );

	if ($token !== NULL && $token != '-1') {
		session ( 'token', $token );
	} elseif (empty ( $stoken ) && C ( 'DIV_DOMAIN' ) && ! is_numeric ( $domain [0] ) && SITE_DOMAIN != 'localhost') { // 泛域名支持
		$domain = explode ( '.', SITE_DOMAIN );
		$map ['domain'] = $domain [0];
		! $GLOBALS ['is_wap'] && $GLOBALS ['mid'] && $map ['uid'] = $GLOBALS ['uid'];
		$token = D ( 'Common/Public' )->where ( $map )->getField ( 'token' );
		$token && session ( 'token', $token );
	} elseif (! empty ( $_REQUEST ['token'] ) && $_REQUEST ['token'] != '-1') {
		session ( 'token', $_REQUEST ['token'] );
	} elseif (! empty ( $_REQUEST ['publicid'] )) {
		$publicid = I ( 'publicid' );
		$token = D ( 'Common/Public' )->getInfo ( $publicid, 'token' );
		$token && session ( 'token', $token );
	}
	$token = session ( 'token' );
	
	if (empty ( $token ) || $token == '-1') {
		// $map ['uid'] = session ( 'mid' );
		// if ($map ['uid'] > 0) {
		// $user = get_userinfo ( $map ['uid'] );
		
		// $user ['level'] < 2 && $user ['manager_id'] > 0 && $map ['uid'] = $user ['manager_id'];
		// $token = $user ['level'] < 2 || $user ['has_public'] ? D ( 'Common/Public' )->where ( $map )->getField ( 'token' ) : DEFAULT_TOKEN;
		
		// isset ( $user ['has_public'] ) && $token && session ( 'token', $token );
		// } else {
		$token = DEFAULT_TOKEN;
		// }
	}
	
	return $token;
}

//============================================================================



function error($errno, $message = '') {
	return array(
		'errno' => $errno,
		'message' => $message,
	);
}

function is_error($data) {
	if (empty($data) || !is_array($data) || !array_key_exists('errno', $data) || (array_key_exists('errno', $data) && $data['errno'] == 0)) {
		return false;
	} else {
		return true;
	}
}

//增加 redis
if (!(function_exists('redis'))) 
{
	function redis() 
	{
		static $redis;
		if (is_null($redis)) 
		{
			$config['server'] 	= C('REDIS_SERVER');
			$config['port'] 	= C('REDIS_PORT');
			$config['pconnect'] = C('REDIS_PCONNECT');
			$config['timeout'] 	= C('REDIS_TIMEOUT');
			$config['requirepass'] = C('REDIS_REQUIREPASS');
			
			if (!(extension_loaded('redis'))) 
			{
				return error(-1, 'PHP 未安装 redis 扩展');
			}
			if (!(isset($config['server']))) 
			{
				return error(-1, '未配置 redis, 请检查 conf/common.php 中参数设置');
			}
			
			if (empty($config['server'])) 
			{
				$config['server'] = '127.0.0.1';
			}
			if (empty($config['port'])) 
			{
				$config['port'] = '6379';
			}
			$redis_temp = new Redis();
			if ($config['pconnect']) 
			{
				$connect = $redis_temp->pconnect($config['server'], $config['port'], $config['timeout']);
			} else {
				$connect = $redis_temp->connect($config['server'], $config['port'], $config['timeout']);
			}
			if (!($connect)) 
			{
				return error(-1, 'redis 连接失败, 请检查 conf/common.php 中参数设置');
			}
			if (!(empty($config['requirepass']))) 
			{
				$redis_temp->auth($config['requirepass']);
			}
            try {
			    $ping = $redis_temp->ping();
	        }catch (ErrorException $e) {
			    return error(-1, 'redis 无法正常工作，请检查 redis 服务');
		    }
			if ($ping != '+PONG')
			{
				return error(-1, 'redis 无法正常工作，请检查 redis 服务');
			}
			$redis = $redis_temp;
		}
		return $redis;
	}
}