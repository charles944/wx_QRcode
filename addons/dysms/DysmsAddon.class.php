<?php
namespace addons\dysms;

use common\controller\Addon;
use think\Db;

/**
 * 阿里云集成阿里大鱼新短信发送插件
 */
class DysmsAddon extends Addon
{
    public $info = array(
        'name' => 'dysms',
        'title' => '阿里云短信服务',
        'description' => '阿里云短信服务短信发送插件',
        'status' => 1,
        'author' => '靑年',
        'version' => '1.1.1'
    );
    public $addon_path = './addons/dysms/';
	
    public $admin_list = array(
        'listKey' => array(
            'action' => '行为标识',
			'type'=>'模板类型',
            'sign'=>'短信签名',
            'template_code' => '模板ID',
            'status' => '状态',
            'preview' => '预览',
        ),
        'model' => 'DysmsTpl',
        'order' => 'id asc'
    );
    public $custom_adminlist = 'adminlist.html';

	public function install(){
		$install_sql = './addons/dysms/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}

	public function uninstall(){
		$uninstall_sql = './addons/dysms/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

    //实现的sms钩子方法
    public function sms($param)
    {
        return true;
    }

	public function sendSms($mobile,$content,$action){
		
		$cacheAction2DyTpl=S('ACTION2DYSMSTPLCODE');
		if(empty($cacheAction2DyTpl)){
			$cacheAction2DyTpl = D('Addons://dysms/DysmsTpl')->action2tplcode();
			S('ACTION2DYSMSTPLCODE',$cacheAction2DyTpl);
		}
		$DysmsTemplateCode=$cacheAction2DyTpl[$action]['template_code'];
		if(!$DysmsTemplateCode)return '操作名称为：'.$action.'的短信模板不存在';
        $DysmsFreeSignName=$cacheAction2DyTpl[$action]['sign'];
        $DysmsTypeCode = $cacheAction2DyTpl[$action]['type'];
		
		$config=$this->getConfig();
		if(intval($DysmsTypeCode) == 1){
			$smsParam=array('code'=>$content);
		}else{
			$smsParam=$content;
		}
		
		include 'sdk/aliyun-php-sdk-core/Config.php';
		include_once 'sdk/Dysmsapi/Request/V20170525/SendSmsRequest.php';
		include_once 'sdk/Dysmsapi/Request/V20170525/QuerySendDetailsRequest.php';
		
		//此处需要替换成自己的AK信息
		$accessKeyId = $config['appkey'];
		$accessKeySecret = $config['appsecret'];
		//短信API产品名
		$product = "Dysmsapi";
		//短信API产品域名
		$domain = $config['method']; //"dysmsapi.aliyuncs.com";
		//暂时不支持多Region
		$region = "cn-hangzhou";
		
		//初始化访问的acsCleint
		$profile_class = new \DefaultProfile();
		$profile = $profile_class->getProfile($region, $accessKeyId, $accessKeySecret);
		$profile_class->addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
		$acsClient= new \DefaultAcsClient($profile);
    
		$request = new sdk\Dysmsapi\Request\V20170525\SendSmsRequest();
		//必填-短信接收号码
		$request->setPhoneNumbers($mobile);
		//必填-短信签名
		$request->setSignName($DysmsFreeSignName);
		//必填-短信模板Code
		$request->setTemplateCode($DysmsTemplateCode);
		//选填-假如模板中存在变量需要替换则为必填(JSON格式)
		$request->setTemplateParam(json_encode($smsParam));
		//选填-发送短信流水号
		$request->setOutId(TIME());
		
		//发起访问请求
		$acsResponse = $acsClient->getAcsResponse($request);


		if($acsResponse->Code != 'OK'){
			return '错误：'.$acsResponse->Message.'；Code：'.$acsResponse->Code;
        }else{
            return true;
        }
    }

}