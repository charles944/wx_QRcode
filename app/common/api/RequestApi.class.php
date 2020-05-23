<?php
namespace common\api;

class RequestApi {
	public static $num = 3;
	public static $val = '';
	public static $connect_timeout = 15; //超时时间
	public static $set_func = 'file_get_contents';
	public static $head = '';
	public static $charset = 'utf-8';
	public static $target_charset = 'utf-8';
	public static $sock_name = '';

	public static function fun($url, $method) {
		//dump(self::$set_func);
		if(self::$set_func != ''){
			$set_func=self::$set_func;
			if($set_func=='fsockopen'){
				if(function_exists('fsockopen')){
		    		self::$sock_name='fsockopen';
				}
				elseif(function_exists('pfsockopen')){
		    		self::$sock_name='pfsockopen';
				}
				if(self::$sock_name!=''){
					return self::fsockopen($url,$method);
				}
			}
			elseif($set_func=='curl' && function_exists('curl_exec')){
				return self::curl($url,$method);					
			}
			elseif($set_func=='file_get_contents' && function_exists('file_get_contents')){
				return self::file_get_contents($url,$method);					
			}
		}
		$collect=S('collect');
		foreach($collect as $k=>$v){
			if($k=='fsockopen'){
				if(function_exists('fsockopen')){
		    		self::$sock_name='fsockopen';
				}
				elseif(function_exists('pfsockopen')){
		    		self::$sock_name='pfsockopen';
				}
				if(self::$sock_name!=''){
					return self::fsockopen($url,$method);
				}
			}
			elseif($k=='curl' && function_exists('curl_exec')){
				return self::curl($url,$method);					
			}
			elseif($k=='file_get_contents' && function_exists('file_get_contents')){
				return self::file_get_contents($url,$method);					
			}
		}
	}

	public static function file_get_contents($url, $method = 'get') {
		$context['http'] = array (
			'timeout' => self::$connect_timeout,
		);
		if(self::$head!=''){
		    $context['http']['header']=self::$head;
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

	public static function curl($url, $method = 'get') {
		$urlinfo = parse_url($url);
	    if (empty ($urlinfo['path'])) {
		    $urlinfo['path'] = '/';
	    }
	    $host = $urlinfo['host'];
		if(!array_key_exists('query',$urlinfo)){
		    $urlinfo['query']='';
		}
	    $uri = $urlinfo['path'] . $urlinfo['query']; 

	    //$header[]= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.0; zh-CN; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1\r\n";
	    //$header[]= "Referer: http://" . $urlinfo['host'] . "\r\n";
	    //$header[]= "Connection: Close\r\n\r\n";
		
		/* 开始一个新会话 */
		$curl_session = curl_init();

		/* 基本设置 */
		curl_setopt($curl_session, CURLOPT_FORBID_REUSE, true); // 处理完后，关闭连接，释放资源
		curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true); //把结果返回，而非直接输出
		curl_setopt($curl_session, CURLOPT_FOLLOWLOCATION,1);  //是否抓取跳转后的页面
		curl_setopt($curl_session, CURLOPT_CONNECTTIMEOUT, self::$connect_timeout); //超时时间

		if(preg_match('|^https://|',$url)==1){
		    curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl_session, CURLOPT_SSL_VERIFYHOST,  false);
		}

		$url_parts = self::parse_raw_url($url);

		if ($method == 'get') {
			$header[]= "GET " . $urlinfo['path'] . "?" . $urlinfo['query'] . " HTTP/1.1\r\n";
	    	$header[]= "Host: " . $urlinfo['host'] . "\r\n";
			curl_setopt($curl_session, CURLOPT_HTTPGET, true);
		} else {
			$a = explode('?', $url);
			$url = $a[0];
			$params = $a[1];
			curl_setopt($curl_session, CURLOPT_POST, true);
			curl_setopt($curl_session, CURLOPT_POSTFIELDS, $params);
			//$header[]= "POST " . $urlinfo['path'] . " HTTP/1.1\r\n";
	    	//$header[]= "Host: " . $urlinfo['host'] . "\r\n";
			//$header[] = 'Content-Type: application/x-www-form-urlencoded';
			//$header[] = 'Content-Length: ' . strlen($params);
		}

		/* 设置请求地址 */
		curl_setopt($curl_session, CURLOPT_URL, $url);
		
		/* 设置头部信息 */
		if(self::$head!=''){
			unset($header);
			$header=array(self::$head);
		}
		
		if(!empty($header)){
			curl_setopt($curl_session, CURLOPT_HTTPHEADER, $header);
		}

		$http_response = curl_exec($curl_session);
		curl_close($curl_session);
		return $http_response;
	}
	
	public static function fsockopen($url,$method='get', $time_out = "15"){
	    $urlarr = parse_url($url);
	    $errno = "";
	    $errstr = "";
	    $transports = "";
	    if ($urlarr["scheme"] == "https") {
		    $transports = "ssl://";
		    $urlarr["port"] = "443";
	    } else {
		    $transports = "";  //加tcp://有些主机就出错
		    $urlarr["port"] = "80";
	    }
        $sock=self::$sock_name;
	    $fp =  $sock($transports . $urlarr['host'], $urlarr['port'], $errno, $errstr, self::$connect_timeout);
	    if (!$fp) {
		    //die("ERROR: $errno - $errstr<br />\n");
	    } else {
			if(!isset($urlarr["query"])){
			    $urlarr["query"]='';
				$url_query='';
		    }
			else{
				$url_query=$urlarr["query"];
				$urlarr["query"]='?'.$urlarr["query"];
			}
			if(!isset($urlarr["path"])){
			    $urlarr["path"]='/';
		    }
		    if($method=='get'){
				$headers="GET " . $urlarr["path"].$urlarr["query"] . " HTTP/1.0\r\n";
				$headers.="Host: " . $urlarr["host"] . "\r\n";
				//$headers.="Host: " . $urlarr["host"] . "\r\n";
				//$headers.="Host: " . $urlarr["host"] . "\r\n";
		    }
		    elseif($method=='post'){
				$headers="POST " . $urlarr["path"].$urlarr["query"] . " HTTP/1.0\r\n";
				$headers.="Host: " . $urlarr["host"] . "\r\n";
				$headers.="Content-type: application/x-www-form-urlencoded\r\n";
				$headers.="Content-length: " . strlen($url_query) . "\r\n";
				$headers.="Accept: */*\r\n";
				$headers.="\r\n".$url_query."\r\n";
		    }

			//$headers .='User-Agent: Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)\r\n';
			if(self::$head!=''){
			    //unset($headers);
			    $headers.=self::$head."\r\n";
		    }
			$headers .= "\r\n";

			fputs($fp, $headers, strlen($headers));
			$info='';
			$temp_info='';
			$body=0;
		    while (!feof($fp)) {
				$temp_info=fgets($fp,500000);
				if($temp_info == "\r\n" || $body==1){
				    $info.=$temp_info;
					$body=1;
				}
		    }
	    }
		fclose($fp);
		return trim($info);
	}

	public static function parse_raw_url($raw_url) {
		$retval = array ();
		$raw_url = (string) $raw_url;

		// make sure parse_url() recognizes the URL correctly.
		if (strpos($raw_url, '://') === false) {
			$raw_url = 'http://' . $raw_url;
		}

		// split request into array
		$retval = parse_url($raw_url);

		// make sure a path key exists
		if (!isset ($retval['path'])) {
			$retval['path'] = '/';
		}

		// set port to 80 if none exists
		if (!isset ($retval['port'])) {
			$retval['port'] = '80';
		}

		return $retval;
	}

	public static function generate_crlf()
    {
        $crlf = '';

        if (strtoupper(substr(PHP_OS, 0, 3) === 'WIN'))
        {
            $crlf = "\r\n";
        }
        elseif (strtoupper(substr(PHP_OS, 0, 3) === 'MAC'))
        {
            $crlf = "\r";
        }
        else
        {
            $crlf = "\n";
        }

        return $crlf;
    }

	public static function get($url,$method='get') {
		/*if(!preg_match('/^http/',$url)){
		    return file_get_contents($url);
		}*/

		//if(preg_match('#^http://\w+\.duoduo123\.com#',$url)==1){
		//	self::set_func='file_get_contents';
		//}
		$a = self::fun($url,$method);
		self::$num--;
		if (self::$num > 0 && ($a=='' || $a=='null')) {
			$a = self::get($url);
		} 
		else {
			if(self::$charset!=self::$target_charset){
		        $a=iconv(self::$target_charset,self::$charset.'//IGNORE',$a);
		    }
			self::$val = $a;
		}
	}

	public static function get_xml($url){
		self::get($url);
		$xml=self::$val;
		if(self::$charset!=self::$target_charset){
		    $xml=str_replace(self::$target_charset,self::$charset,$xml);
		}
		
		$xmlCode = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
		$arrdata = self::get_object_vars_final($xmlCode);
		return $arrdata;
	}
	
	public static function get_json($url){
		self::get($url);
		$json=self::$val;
		$arrdata = json_decode($json,1);
		return $arrdata;
	}

	public static function get_object_vars_final($obj) {
		if (is_object($obj)) {
			$obj = get_object_vars($obj);
		}

		if (is_array($obj)) {
			foreach ($obj as $key => $value) {
				$obj[$key] = self::get_object_vars_final($value);
			}
		}
		return $obj;
	}
	
	public static function curl_get_http_status($url){
	    $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL,$url);
        curl_setopt($curl,CURLOPT_HEADER,1);
        curl_setopt($curl,CURLOPT_NOBODY,1);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION,0);
        curl_exec($curl);
        $rtn= curl_getinfo($curl,CURLINFO_HTTP_CODE);
        curl_close($curl);
        return  $rtn;
	}
	
	public static function fsockopen_get_http_status($url = "localhost", $port = 80, $fsock_timeout = 10) {
		ignore_user_abort(true); // 记录开始时间
		list ($usec, $sec) = explode(" ", microtime());
		$timer['start'] = (float) $usec + (float) $sec; // 校验URL
		if (!preg_match("/^https?:\/\//i", $url)) {
			$url = "http://" . $url;
		} // 支持HTTPS
		if (preg_match("/^https:\/\//i", $url)) {
			$port = 443;
		} // 解析URL
		$urlinfo = parse_url($url);
		if (empty ($urlinfo['path'])) {
			$urlinfo['path'] = '/';
		}
		$host = $urlinfo['host'];
		$uri = $urlinfo['path'] . (empty ($urlinfo['query']) ? '' : $urlinfo['query']); // 通过fsock打开连接
		if (!$fp = fsockopen($host, $port, $errno, $error, $fsock_timeout)) {
			list ($usec, $sec) = explode(" ", microtime(true));
			$timer['end'] = (float) $usec + (float) $sec;
			$usetime = (float) $timer['end'] - (float) $timer['start'];
			return array (
				'code' => -1,
				'usetime' => $usetime
			);
		} // 提交请求
		$status = socket_get_status($fp);
		$out = "GET {$uri} HTTP/1.1\r\n";
		$out .= "Host: {$host}\r\n";
		$out .= "Connection: Close\r\n\r\n";
		$write = fwrite($fp, $out);
		if (!$write) {
			list ($usec, $sec) = explode(" ", microtime(true));
			$timer['end'] = (float) $usec + (float) $sec;
			$usetime = (float) $timer['end'] - (float) $timer['start'];
			return array (
				'code' => -2,
				'usetime' => $usetime
			);
		}
		$ret = fgets($fp, 1024);
		preg_match("/http\/\d\.\d\s(\d+)/i", $ret, $m);
		$code = $m[1];
		fclose($fp);
		list ($usec, $sec) = explode(" ", microtime());
		$timer['end'] = (float) $usec + (float) $sec;
		$usetime = (float) $timer['end'] - (float) $timer['start'];
		return $code;
		//	return array (
		//		'code' => $code,
		//		'usetime' => $usetime
		//	);
	}
	
	public static function get_http_status($url){
	    if (function_exists('curl_exec')) {
			return self::curl_get_http_status($url);
		}
		elseif(function_exists('fsockopen')){
			return self::fsockopen_get_http_status($url);
		}
	}
}