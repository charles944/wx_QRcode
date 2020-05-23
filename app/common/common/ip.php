<?php
use common\model\ChineseModel;

//根据IP获取来源地
function ipfrom($ip) {
	if(!preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $ip)) {
		return '';
	}
	if( !is_file(SITE_PATH.'/public/ip/ip.dat') ){
		return '<a title><A HREF="http://down.qibosoft.com/ip.rar" title="点击下载后,解压放到整站/inc/目录即可">IP库不存在,请点击下载一个!</A></a>';
	}
	if($fd = @fopen(SITE_PATH.'/public/ip/ip.dat', 'rb')) {

		$ip = explode('.', $ip);
		$ipNum = $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];

		$DataBegin = fread($fd, 4);
		$DataEnd = fread($fd, 4);
		$ipbegin = implode('', unpack('L', $DataBegin));
		if($ipbegin < 0) $ipbegin += pow(2, 32);
		$ipend = implode('', unpack('L', $DataEnd));
		if($ipend < 0) $ipend += pow(2, 32);
		$ipAllNum = ($ipend - $ipbegin) / 7 + 1;

		$BeginNum = 0;
		$EndNum = $ipAllNum;

		while($ip1num > $ipNum || $ip2num < $ipNum) {
			$Middle= intval(($EndNum + $BeginNum) / 2);

			fseek($fd, $ipbegin + 7 * $Middle);
			$ipData1 = fread($fd, 4);
			if(strlen($ipData1) < 4) {
				fclose($fd);
				return '- System Error';
			}
			$ip1num = implode('', unpack('L', $ipData1));
			if($ip1num < 0) $ip1num += pow(2, 32);

			if($ip1num > $ipNum) {
				$EndNum = $Middle;
				continue;
			}

			$DataSeek = fread($fd, 3);
			if(strlen($DataSeek) < 3) {
				fclose($fd);
				return '- System Error';
			}
			$DataSeek = implode('', unpack('L', $DataSeek.chr(0)));
			fseek($fd, $DataSeek);
			$ipData2 = fread($fd, 4);
			if(strlen($ipData2) < 4) {
				fclose($fd);
				return '- System Error';
			}
			$ip2num = implode('', unpack('L', $ipData2));
			if($ip2num < 0) $ip2num += pow(2, 32);

			if($ip2num < $ipNum) {
				if($Middle == $BeginNum) {
					fclose($fd);
					return '- Unknown';
				}
				$BeginNum = $Middle;
			}
		}

		$ipFlag = fread($fd, 1);
		if($ipFlag == chr(1)) {
			$ipSeek = fread($fd, 3);
			if(strlen($ipSeek) < 3) {
				fclose($fd);
				return '- System Error';
			}
			$ipSeek = implode('', unpack('L', $ipSeek.chr(0)));
			fseek($fd, $ipSeek);
			$ipFlag = fread($fd, 1);
		}

		if($ipFlag == chr(2)) {
			$AddrSeek = fread($fd, 3);
			if(strlen($AddrSeek) < 3) {
				fclose($fd);
				return '- System Error';
			}
			$ipFlag = fread($fd, 1);
			if($ipFlag == chr(2)) {
				$AddrSeek2 = fread($fd, 3);
				if(strlen($AddrSeek2) < 3) {
					fclose($fd);
					return '- System Error';
				}
				$AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
				fseek($fd, $AddrSeek2);
			} else {
				fseek($fd, -1, SEEK_CUR);
			}

			while(($char = fread($fd, 1)) != chr(0))
				$ipAddr2 .= $char;

			$AddrSeek = implode('', unpack('L', $AddrSeek.chr(0)));
			fseek($fd, $AddrSeek);

			while(($char = fread($fd, 1)) != chr(0))
				$ipAddr1 .= $char;
		} else {
			fseek($fd, -1, SEEK_CUR);
			while(($char = fread($fd, 1)) != chr(0))
				$ipAddr1 .= $char;

			$ipFlag = fread($fd, 1);
			if($ipFlag == chr(2)) {
				$AddrSeek2 = fread($fd, 3);
				if(strlen($AddrSeek2) < 3) {
					fclose($fd);
					return '- System Error';
				}
				$AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
				fseek($fd, $AddrSeek2);
			} else {
				fseek($fd, -1, SEEK_CUR);
			}
			while(($char = fread($fd, 1)) != chr(0))
				$ipAddr2 .= $char;
		}
		fclose($fd);

		if(preg_match('/http/i', $ipAddr2)) {
			$ipAddr2 = '';
		}
		$ipaddr = "$ipAddr1 $ipAddr2";
		$ipaddr = preg_replace('/CZ88\.NET/is', '', $ipaddr);
		$ipaddr = preg_replace('/^\s*/is', '', $ipaddr);
		$ipaddr = preg_replace('/\s*$/is', '', $ipaddr);
		if(preg_match('/http/i', $ipaddr) || $ipaddr == '') {
			$ipaddr = '- Unknown';
		}

		/* if(WEB_LANG=='big5'){
			$cnvert = new Chinese("GB2312","BIG5",$ipaddr,SITE_PATH."/public/ip/gbkcode/");
			$ipaddr = $cnvert->ConvertIT();
		}elseif(WEB_LANG=='utf-8'){ */
			$cnvert_model = new ChineseModel();
			
			$cnvert = $cnvert_model->Chinese("GB2312","UTF8",$ipaddr,SITE_PATH."/public/ip/gbkcode/");
			$ipaddr = $cnvert_model->ConvertIT();
		//}

		return $ipaddr;
	}
}

function ip_test($ip,$iprule){
    $ipruleregexp=str_replace('.*','ph',$iprule);
    $ipruleregexp=preg_quote($ipruleregexp,'/');
    $ipruleregexp=str_replace('ph','\.[0-9]{1,3}',$ipruleregexp);

    if(preg_match('/^'.$ipruleregexp.'$/',$ip)) return true;
    else return false;
}

function get_city_by_ip($ip)
{
	$url = "http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip;
	$ipinfo = json_decode(file_get_contents($url));
	if ($ipinfo->code == '1') {
		return false;
	}
	$city = $ipinfo->data->region . $ipinfo->data->city; //省市县
	$ip = $ipinfo->data->ip; //IP地址
	$ips = $ipinfo->data->isp; //运营商
	$guo = $ipinfo->data->country; //国家
	if ($guo == '中国') {
		$guo = '';
	}
	return $guo . $city . $ips . '[' . $ip . ']';

}

/**
 * 获取 IP  地理位置
 * 淘宝IP接口
 * @Return: array
 * @author 朝兮夕兮，那你自己想想
 */
function getCity($ip)
{
	$url="http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
	$ipinfo=json_decode(file_get_contents($url));
	if($ipinfo->code=='1'){
		return false;
	}
	$city = $ipinfo->data->region.$ipinfo->data->city;//省市县
	$ip = $ipinfo->data->ip;//IP地址
	$ips = $ipinfo->data->isp;//运营商
	$guo = $ipinfo->data->country;//国家
	if($guo == '中国'){
		$guo = '';
	}
	if(in_array(strtok($ip, '.'), array('10', '127', '168', '192'))){
		$city = "本机地址或局域网";
	}
	//$ipp = '10.0.0.172';
	//$ipss = ip2long($ipp);
	//return $guo.$city.$ips.'['.$ip.']';
	return $guo.$city.$ips;
}

/* 获取ip + 地址*/
function _get_ip_dizhi(){
	$opts = array(
		'http'=>array(
			'method'=>"GET",
			'timeout'=>5,
		)
	);
	$context = stream_context_create($opts);
	$ipmac=_get_ip();
	if(strpos($ipmac,"127.0.0.") === true) return '本地';
	$url_ip='http://ip.taobao.com/service/getIpInfo.php?ip='.$ipmac;
	$str = @file_get_contents($url_ip, false, $context);
	if(!$str) return "";
	$json=json_decode($str,true);
	if($json['code']==0){
		$ipcity= $json['data']['region'].$json['data']['city'];
		$ip= $ipcity.','.$ipmac;
	}else{
		$ip="";
	}
	return $ip;
}

/*获取客户端ip*/
function _get_ip(){
	if (isset($_SERVER['HTTP_CLIENT_IP']) && strcasecmp($_SERVER['HTTP_CLIENT_IP'], "unknown"))
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], "unknown"))
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	else if (isset($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
		$ip = $_SERVER['REMOTE_ADDR'];
	else if (isset($_SERVER['REMOTE_ADDR']) && isset($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
		$ip = $_SERVER['REMOTE_ADDR'];
	else $ip = "";
	return ($ip);
}