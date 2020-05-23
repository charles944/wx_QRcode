<?php
namespace admin\model;

use think\Model;

class CurlModel extends Model
{
    public function curl($url)
    {
    	if(function_exists('curl_exec')){
	        $cookie_file = '~runtime/cookie.txt';
	        $curl = curl_init();
	        curl_setopt($curl, CURLOPT_URL, $url);
	        //curl_setopt($curl, CURLOPT_TIMEOUT, 15);
	        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 Safari/537.11');
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	
	
	        if (isset($_SESSION['cloud_cookie'])) {
	            curl_setopt($curl, CURLOPT_COOKIE, $this->getCookie(array('PHPSESSID' => $_SESSION['cloud_cookie'])));
	        }
	        $result = curl_exec($curl);
	        if($result==false){
	            $this->error=curl_error($curl);
	            return false;
	        }
	        curl_close($curl);
			
    	}else if(function_exists('file_get_contents')){
    		$result = $this->file_get_contents($url,'get');
    	}
        return $result;
    }
	
	public function curlPost($url, $data) {
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
	
	private function curlGet($url){
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
    
    private function file_get_contents($url, $method = 'get') {
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

    private function getCookie($cookies)
    {

        $cookies_string = '';
        foreach ($cookies as $name => $value) {
            $cookies_string .= $name . '=' . $value . ';';
        }
        return $cookies_string;
    }
}