<?php

function curl_email($smtp, $username, $password, $from_email, $to_email, $from_name, $title, $content) {
	list ($msec, $sec) = explode(" ", microtime());
	$curl_session = curl_init();
	$header[] = "HELO localhost";
	$header[] = "AUTH LOGIN " . base64_encode($username);
	$header[] = base64_encode($password);
	$header[] = "MAIL FROM:<" . $from_email . ">";
	$header[] = "RCPT TO:<" . $to_email . ">";
	$header[] = "DATA";
	$header[] = "MIME-Version:1.0
Content-Type:text/html
To: " . $to_email . "
From: " . $from_name . "<" . $from_email . ">
Subject: " . $title . "
Date: " . date('r') . "
X-Mailer:By Redhat (PHP/5.3.8)
Message-ID: <" . date("YmdHis", $sec) . "." . ($msec * 1000000) . "." . $from_email . ">

" . $content;
	$header[] = "\r\n.\r\n";
	$header[] = "QUIT";

	$content = '';
	foreach ($header as $v) {
		$content .= $v . "\r\n";
	}

	/* 基本设置 */
	curl_setopt($curl_session, CURLOPT_URL, $smtp);
	curl_setopt($curl_session, CURLOPT_PORT, 25);

	curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true); //把结果返回，而非直接输出
	curl_setopt($curl_session, CURLOPT_CONNECTTIMEOUT, 15); //超时时间

	curl_setopt($curl_session, CURLOPT_CUSTOMREQUEST, $content);

	$http_response = curl_exec($curl_session);
	curl_close($curl_session);
	return $http_response;
}

function mail_send($to, $title, $html, $from='',  $usepassword='', $smtp='',$type=1) {
	if($type==2){
        $headers = "From: ".$from."" . "\r\n" ."CC: somebodyelse@example.com";
        $re=mail($to,$title,$html,$headers);
		if ($re==1) {
		    $mymsg = 1;
	    } else {
		    $mymsg = 0;
	    }
		return $mymsg;
	}
	
	global $webset;
	$from=$from?$from:$webset['smtp']['name'];
	$usepassword=$usepassword?$usepassword:$webset['smtp']['pwd'];
	$smtp=$smtp?$smtp:$webset['smtp']['host'];
	unset($webset);
	
	if($from=='' || $usepassword=='' || $smtp==''){
		return 0;
	}
	
	$fromname=WEBNICK;
	$usename=$from;
	
	if (!function_exists('fsockopen') && !function_exists('pfsockopen')) {
		if(!function_exists('curl_exec')){
			return 'curl模块，fsockopen或者pfsockopen函数不可用，联系空间商为您开启此函数';
		}
        
		curl_email($smtp, $usename, $usepassword, $from, $to, $fromname, $title, $html);
		return 1;
	}
	if(!class_exists('PHPMailer')){include(DDROOT.'/comm/class.phpmailer.php');}

	$mail = new PHPMailer();
	//$mail->SMTPDebug  = 3;
	$mail->CharSet = "UTF-8"; // charset
	$mail->Encoding = "base64";
	$mail->IsSMTP(); // telling the class to use SMTP

	//邮件系统配置
	$mail->SMTPAuth = true;
	$mail->Host = $smtp; // SMTP server
	$mail->Username = $usename; // SMTP account username
	$mail->Password = $usepassword; // SMTP account password

	$mail->From = $from; //必填，发件人Email
	$mail->FromName = $fromname; //必填，发件人昵称或姓名

	$mail->WordWrap = 50; // 自动换行的字数
	$mail->IsHTML(true); // 以 HTML发送

	//主题
	$mail->Subject = (isset ($title)) ? $title : ''; //必填，邮件标题（主题）

	//邮件主体
	$mail->MsgHTML($html);

	//发送地址
	if ($to) {
		$address = explode("|", $to);
		foreach ($address AS $key => $val) {
			$mail->AddAddress($val, "");
		}
	}
	if (!$mail->Send()) {
		$mymsg = 0;
	} else {
		$mymsg = 1;
	}
	/*$args = func_get_args();
	$args['re']=$mymsg;
	$args['from']=$from;
	$args['usepassword']=$usepassword;
	$args['smtp']=$smtp;
	dd_file_put('aaaa.txt',var_export($args,1)."\r\n",FILE_APPEND);*/
	return $mymsg;
}

function get_tao_id($url, $tao_id_arr) {
	$query=preg_replace('/(.*?)\?/','',$url);
    parse_str($query,$a);
	$c=count($tao_id_arr);
	for ($i = 0; $i < $c; $i++) {
		if (array_key_exists($tao_id_arr[$i], $a)) {
			if ($a[$tao_id_arr[$i]] != '') {
				return $a[$tao_id_arr[$i]];
			}
		}
	}
}

function in_tao_cat($cid){
	$tao_cat=include(APP_PATH.'/data/tao_cat.php');
    foreach($tao_cat as $k=>$v){
	    if(in_array($cid,$v)){
		    return $k;
	    }
    }
	return 999;
}

function get2var(){
	foreach($_GET as $k=>$v){
		global $$k;
		$$k=$v;
	}
}

function post2var($arr=array(),$strict=0){
	$re=1;
	foreach($_POST as $k=>$v){
		if($v==='' && $strict==1){ //严格检测post，不准有空
			$re=0;
			break;
		}
		global $$k;
		$$k=htmlspecialchars($v);
		if(!empty($arr)){
			$arr=array_diff($arr,array($k));
		}

	}
	if(!empty($arr)){  //严格检测post，不准不存在
		if(count($arr)>0){
		    $re=0;
	    }
	}
	return $re;
}

// count only chinese words
function str_utf8_chinese_word_count($str = ""){
	$utf8_chinese_pattern="/[\x{4e00}-\x{9fff}\x{f900}-\x{faff}]/u";
	$utf8_symbol_pattern="/[\x{ff00}-\x{ffef}\x{2000}-\x{206F}]/u";
    $str = preg_replace($utf8_symbol_pattern, "", $str);
    return preg_match_all($utf8_chinese_pattern, $str, $arr);
}
// count both chinese and english
function str_utf8_mix_word_count($str = ""){
	$utf8_chinese_pattern="/[\x{4e00}-\x{9fff}\x{f900}-\x{faff}]/u";
	$utf8_symbol_pattern="/[\x{ff00}-\x{ffef}\x{2000}-\x{206F}]/u";
    $str = preg_replace($utf8_symbol_pattern, "", $str);
    return str_utf8_chinese_word_count($str) + strlen(preg_replace($utf8_chinese_pattern, "", $str));
}

function authcode($string, $operation = 'DECODE', $key=DDKEY, $expiry = 0) {
	$ckey_length = 4; // 随机密钥长度 取值 0-32;
	// 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
	// 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
	// 当此值为 0 时，则不产生随机密钥

	//取消UC_KEY，改为必须传入$key才能运行
	if (empty ($key)) {
		exit ('PARAM $key IS EMPTY! ENCODE/DECODE IS NOT WORK!');
	} else {
		$key = md5($key);
	}

	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), - $ckey_length)) : '';

	$cryptkey = $keya . md5($keya . $keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry +TIME : 0) . substr(md5($string . $keyb), 0, 16) . $string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array ();
	for ($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for ($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for ($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a +1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if ($operation == 'DECODE') {
		if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - TIME > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc . str_replace('=', '', base64_encode($result));
	}
}

function check_jump_url($url, $uid = 0) {
	$param=array();
	$p='';
	if(strpos($url,'s8.taobao.com')!==false){
	    $u='unid';
		$lm='alimama';
	}
	elseif(strpos($url,'s.click.taobao.com')!==false){
		$u='unid';
		$lm='alimama';
	}
	elseif(strpos($url,'taobao.com')!==false){
	    $u='u';
		$lm='alimama';
	}
	elseif(strpos($url,'chanet.com')!==false){
	    $u='u';
		$lm='chanet';
	}
	elseif(strpos($url,'yiqifa.com')!==false){
	    $u='e';
		$lm='yiqifa';
	}
	elseif(strpos($url,'linktech.cn')!==false){
	    $u='u_id';
		$lm='linktech';
	}
	elseif(strpos($url,'duomai.com')!==false){
	    $u='euid';
		$lm='duomai';
	}
	elseif(strpos($url,'paipai.com')!==false || strpos($url,'etg.qq.com')!==false){
	    $u='outInfo';
		$lm='paipai';
	}
    if(strpos($url,'?')!==false){
    	$arr=explode('?',$url);
    	parse_str($arr[1],$param);
    	$param[$u]=$uid;
    	foreach($param as $k=>$v){
			if($k!=''){
				if($lm=='alimama' || $lm=='duomai' || $lm=='linktech' || $lm=='chanet'){
				    $p.=$k.'='.urlencode($v).'&';
				}
			    else{ //yiqifa网址中的url参数不能urlencode
				    $p.=$k.'='.urldecode($v).'&';
				}
			}
    	}
    	$url=str_del_last($arr[0].'?'.$p);
		if($lm=='yiqifa' && isset($arr[2])){
    		$url.='?'.$arr[2];
    	}
    }
    else{
    	$url=$url.'?'.$u.'='.$uid;
    }
	
	if($lm=='alimama'){
		$url=spm($url);
	}
	return $url;
}

function spm($url){
	if($url==''){return '';}
	$ddkey=21114278;
	if(strpos($url,'&spm=2014.'.$ddkey.'.')!==false){
		return $url;
	}	
	elseif(strpos($url,'&spm=')!==false){
		preg_match('#&spm=2014\.(\d+)\.\d+\.\d+#',$url,$a);
		$spm='2014.'.$ddkey.'.'.$a[1].'.0';
		$url=preg_replace('#&spm=\d+\.\d+\.\d+\.\d+#','&spm='.$spm,$url);
	}
	else{
		global $webset;
		$key=$webset['appkey'][0]['key'];
		$url.='&spm=2014.'.$ddkey.'.'.$key.'.0';
		unset($webset);
	}
	return $url;
}

function qn_strtotime($datetime){
    if($datetime>'2038-01-19 11:14:07'){
    	$a=explode(' ',$datetime);
    	$date=$a[0];
    	if(isset($a[1])){
    		$time=$a[1];
    	}
    	else{
    		$time='00:00:00';
    	}

    	$b=explode('-',$date);

    	$ceil=ceil(($b[0]-2038)/68);
    	$liuba=0;
    	for($i=1;$i<$ceil;$i++){
    		$liuba+=2147483647;
    	}

    	$date_y=$b[0]-2038-($ceil-1)*68+1970;
    	$date_m=$b[1];
    	$date_d=$b[2];
    	$b=explode(':',$time);
    	$time_h=$b[0];
    	$time_i=$b[1];
    	$time_s=$b[2];
    	return 2147483647+strtotime($new_datetime=$date_y.'-'.$date_m.'-'.$date_d.' '.$time_h.':'.$time_i.':'.$time_s)+2793600+$liuba;
    }
    else{
    	$t=strtotime($datetime);
		return $t<0?0:$t;
    }
}

function qn_date($strtime,$type=1){
	if($strtime>2147483647){

		$ceil=ceil(($strtime-2147483647)/2147483647);
		$liuba=0;
    	for($i=1;$i<$ceil;$i++){
    		$liuba+=1;
    	}

	    $datetime=date('Y-m-d H:i:s',$strtime-2147483647-2793600-$liuba*2147483647);
        $a=explode(' ',$datetime);
    	$date=$a[0];
    	$time=$a[1];
    	$b=explode('-',$date);
    	$date_y=$b[0]-1970+2038+$liuba*68;
    	$date_m=$b[1];
    	$date_d=$b[2];
    	$b=explode(':',$time);
    	$time_h=$b[0];
    	$time_i=$b[1];
    	$time_s=$b[2];
    	if($type==1){
    		return $date_y.'-'.$date_m.'-'.$date_d.' '.$time_h.':'.$time_i.':'.$time_s;
    	}
    	elseif($type==2){
    		return $date_y.'-'.$date_m.'-'.$date_d;
    	}
	}
	else{
		if($type==1){
		    $t= date('Y-m-d H:i:s',$strtime);
			if($t=='1970-01-01 08:00:00'){
				$t='';
			}
		}
		elseif($type==2){
		    $t= date('Y-m-d',$strtime);
			if($t=='1970-01-01'){
				$t='';
			}
		}
		return $t;
	}
}

function beijing_time(){
	$timeac='http://www.time.ac.cn/timeflash.asp?user=flash';
	$time_arr=dd_get_xml($timeac);
	if(is_array($time_arr)){
		$cur_datetime=$time_arr['time']['year'].'-'.$time_arr['time']['month'].'-'.$time_arr['time']['day'].' '.$time_arr['time']['hour'].':'.$time_arr['time']['minite'].':'.$time_arr['time']['second'];
		$cur_time=strtotime($cur_datetime);
	}
	else{
		$cur_time=0;
	}
	return $cur_time;
}

function get_domain($url='') {

	if($url==''){
		$url=$_SERVER['HTTP_HOST'];
	}
	if($url=='localhost' || preg_match('/127\.\d+\.\d+\.\d+/',$url) || preg_match('/192\.168\.\d+\.\d+/',$url) || preg_match('/172\.16|31\.\d+\.\d+/',$url)){
		return 'localhost';
	}
	if(preg_match('#^http://#',$url)){
		$url=str_replace('http://','',$url);
	}

	$a=explode('/',$url);
	$url=$a[0];

	preg_match('/^(\d+\.\d+\.\d+\.\d+)/',$url,$a);
	if(isset($a[1])){
		return $a[1];
	}

	$pattern = "/[\w-]+\.(com|net|org|gov|la|cc|biz|info|cn|tk|mobi|co|so|tel|tv|name|asia|me|中国|公司|网络)((\.cn|\.hk|))$/";
	preg_match($pattern, $url, $matches);
	if (count($matches) > 0) {
		$domain=$matches[0];
	} else {
		$rs = parse_url($url);
		$main_url = $rs["host"];
		if (!strcmp(long2ip(sprintf("%u", ip2long($main_url))), $main_url)) {
			return $main_url;
		} else {
			$arr = explode(".", $main_url);
			$count = count($arr);
			$endArr = array (
				"com",
				"net",
				"org",
				"wang",
				"top",
				"cn",
				"cc",
				"site"
			); //com.cn  net.cn 等情况
			if (in_array($arr[$count -2], $endArr)) {
				$domain = $arr[$count -3] . "." . $arr[$count -2] . "." . $arr[$count -1];
			} else {
				$domain = $arr[$count -2] . "." . $arr[$count -1];
			}
		}
	}
	return $domain;
}

function iptocity($ip=''){
	if($ip==''){$ip=get_client_ip();}
	if(preg_match('/127\.0\.\d+\.\d+/',$ip)==1){
		return '本地';
	}
	$url='http://ip.taobao.com/service/getIpInfo.php?ip='.$ip;
	$a=dd_get_json($url);
	return str_replace('市','',$a['data']['city']);
}

function mobiletocity($mobile){
	if(reg_mobile($mobile)==0){
		return 0;
	}
	$url='http://webservice.webxml.com.cn/WebServices/MobileCodeWS.asmx/getMobileCodeInfo?mobileCode='.$mobile.'&userID=';
	$a=dd_get($url);
	preg_match('#<string xmlns="http://WebXml.com.cn/">\d+：(.*)</string>#',$a,$b);
	return $b[1];
}

function qn_crc32($str){
	return (float)sprintf("%u\n", crc32($str));
}

function qn_file_put($file,$data,$mode=FILE_USE_INCLUDE_PATH){
	if($mode!=FILE_APPEND && is_file($file)) unlink($file);
	return file_put_contents($file,$data,$mode);
}

function StopAttack($ArrPGR=array()){
	if(empty($ArrPGR)) $ArrPGR=array_merge($_GET,$_POST,$_REQUEST);
	$filter="\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)|'|\"|<body onload=prompt|alert(\s+|)\(";
	foreach($ArrPGR as $key=>$value){
		if(is_array($value)){
    		$value=implode($value);
		}
		if(strpos($value,"'")!==false){
			$value=str_replace("'",'',$value);
		}
		if(strpos($value,'\"')!==false){
			$value=str_replace('\"','',$value);
		}
		if (preg_match("/".$filter."/is",$value)==1){
        	//slog("<br><br>操作IP: ".$_SERVER["REMOTE_ADDR"]."<br>操作时间: ".strftime("%Y-%m-%d %H:%M:%S")."<br>操作页面:".$_SERVER["PHP_SELF"]."<br>提交方式: ".$_SERVER["REQUEST_METHOD"]."<br>提交参数: ".$StrFiltKey."<br>提交数据: ".$StrFiltValue);
        	dd_exit("what are you doing man!");
		}
	}
}
function php2js_array($array, $name)
{
    if (empty($array)) {
        return false;
    }
    $enter = "\r\n";
    echo $name . "=new Array();" . $enter;
    foreach ($array as $k => $v) {
        if (strpos($v, "'") !== false) {
            $v = str_replace("'", "\'", $v);
        }
        echo $name . "['" . $k . "']='" . $v . "';" . $enter;
    }
}

function php2js_object($array, $name)
{
    if (empty($array)) {
        return false;
    }
    $enter = "\r\n";
    echo $name . "=new Object();" . $enter;
    foreach ($array as $k => $v) {
        if (strpos($v, "'") !== false) {
            $v = str_replace("'", "\'", $v);
        }
        echo $name . "['" . $k . "']='" . $v . "';" . $enter;
    }
}

function str2arr2($str, $num)
{
    $re = array();
    $a  = '';
    if (is_string($str)) {
        $arr = explode(',', $str);
    } else {
        $arr = $str;
    }
    $c = count($arr);
    if ($c > $num) {
        $i = 0;
        foreach ($arr as $v) {
            $a .= $v . ',';
            $i++;
            if ($i == $num) {
                $a    = preg_replace('/,$/', '', $a);
                $re[] = $a;
                $a    = '';
                $i    = 0;
            }
        }
        $a = preg_replace('/,$/', '', $a);
        if ($a != '') {
            $re[] = $a;
        }
    } else {
        $re[] = $str;
    }
    return $re;
}

function bijia($key, $page = 1, $price1 = 0, $price2 = 0, $order = '')
{
    if (DDMALL == 1)
        return false;
    $url = 'http://sf.manmanbuy.com/s.aspx?id=71&key=' . urlencode($key) . '&PageID=' . $page;
    if ($price2 < $price1)
        return false;
    if ($price1 > 0) {
        $url .= '&price1=' . $price1;
    }
    if ($price2 > 0) {
        $url .= '&price2=' . $price2;
    }
    if ($order != '') {
        $url .= '&orderby=' . $order;
    }
    $agent     = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
    $md5       = md5($url);
    $catch_dir = DDROOT . '/data/temp/bijia/' . substr($md5, 0, 2) . '/' . substr($md5, 2, 2) . '/' . $md5 . '.json';
    if (file_exists($catch_dir) && 0) {
        $goods = file_get_contents($catch_dir);
        $goods = dd_json_decode($goods, 1);
    } else {
        if (function_exists('curl_exec')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, $agent);
            $a = curl_exec($ch);
        } else {
            $opts    = array(
                'http' => array(
                    'method' => "GET",
                    'header' => "User-Agent: " . $agent
                )
            );
            $context = stream_context_create($opts);
            $a       = file_get_contents($url, false, $context);
        }
        $a = compact_html($a);
        preg_match('/共有(\d+)条记录/', $a, $b);
        $total = $b[1];
        preg_match('/<divid="listpro"(.*)<divid="dispage"style="margin:20px8px;width:600px;/', $a, $html);
        preg_match_all('/<divclass="bjline">(.*?)<divclass=\'clear\'><\/div>/', $html[1], $a);
        $goods = array();
        foreach ($a[1] as $k => $v) {
            preg_match('#<ahref=".*&url=(.*)"target="_blank">.*\'"src="(.*)"class=\'sppicclass\'>.*class="f14sc">(.*)</a><spanstyle.*align=\'absmiddle\'>(.*?)</div></div><divclass=.*<fontclass=\'priceword\'>(.*)</font></div><divclass#', $v, $a);
            if ($a[1] != '') {
                $goods[$k]['url']   = urldecode($a[1]);
                $goods[$k]['img']   = $a[2];
                $goods[$k]['title'] = str_replace('<fontcolor="red">', '<font color="red">', $a[3]);
                $goods[$k]['mall']  = preg_replace('/\(第三方\)|\(自营\)/', '', $a[4]);
                $goods[$k]['price'] = $a[5];
            }
        }
        $goods['total'] = $total;
        create_file($catch_dir, dd_json_encode($goods));
    }
    return $goods;
}
?>