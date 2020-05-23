<?php
namespace addons\yixinxi;

use common\controller\Addon;
use think\Db;

class YixinxiAddon extends Addon
{
    public $info = array(
        'name' => 'yixinxi',
        'title' => '第一信息',
        'description' => '第一信息网（YiXinxi），第一信息网短信插件 http://www.1xinxi.cn',
        'status' => 1,
        'author' => '靑年',
        'version' => '2.1.2'
    );

    public $addon_path = './addons/yixinxi/';

    public $admin_list = array(
        'listKey' => array(
            'action' => '行为标识',
            'status' => '状态',
            'preview' => '预览',
        ),
        'model' => 'YixinxiTpl',
        'order' => 'id asc'
    );
    public $custom_adminlist = 'adminlist.html';

    public function install(){
        $install_sql = './addons/yixinxi/install.sql';
        if (file_exists ( $install_sql )) {
            execute_sql_file ( $install_sql );
        }
        return true;
    }

    public function uninstall(){
        $uninstall_sql = './addons/yixinxi/uninstall.sql';
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


    public function sendSms($mobile,$content,$action = false){
        $config=$this->getConfig();
        $uid = $config['smsuid'];
        $pwd = $config['smspwd'];
        if (empty($uid) || empty($pwd)) {
            return '还未配置短信信息!';
        }

        $cacheAction2Tpl=S('ACTION2YIXINXITPLCODE');
        if(empty($cacheAction2Tpl)){
            $cacheAction2Tpl = D('Addons://yixinxi/YixinxiTpl')->action2tplcode();
            S('ACTION2YIXINXITPLCODE',$cacheAction2Tpl);
        }

        if(!empty($action)){
            $YixinxiTpl=$cacheAction2Tpl[$action]['template_content'];
            if(!$YixinxiTpl)return '操作名称为：'.$action.'的短信模板不存在';

            $content_c = str_replace('{code}', $content, $YixinxiTpl);
        }else{
            $content_c = $content;
        }

        $flag = 0;
        $params='';//要post的数据
        $argv = array(
            'name'=>$uid,     //必填参数。用户账号
            'pwd'=>$pwd,     //必填参数。（web平台：基本资料中的接口密码）
            'content'=> $content_c,   //必填参数。发送内容（1-500 个汉字）UTF-8编码
            'mobile'=>$mobile,   //必填参数。手机号码。多个以英文逗号隔开
            'stime'=>'',   //可选参数。发送时间，填写时已填写的时间发送，不填时为当前时间发送
            'sign'=>'',    //必填参数。用户签名。
            'type'=>'pt',  //必填参数。固定值 pt
            'extno'=>''    //可选参数，扩展码，用户定义扩展码，只能为数字
        );
        foreach ($argv as $key=>$value) {
            if ($flag!=0) {
                $params .= "&";
                $flag = 1;
            }
            $params.= $key."="; $params.= urlencode($value);// urlencode($value);
            $flag = 1;
        }
        $url = "http://sms.1xinxi.cn/asmx/smsservice.aspx?".$params; //提交的url地址
        $con= substr( file_get_contents($url), 0, 1 );  //获取信息发送后的状态
        if($con == '0'){
            return true;
        }else{
            return "发送失败! 状态：" . $con .' '. $this->getCode($con);
        }
    }


    private function getCode($code){
        switch($code){
            case 0: return '提交成功';
            case 1: return '含有敏感词汇';
            case 2: return '余额不足';
            case 3: return '没有号码';
            case 4: return '包含sql语句';
            case 10: return '账号不存在';
            case 11: return '账号注销';
            case 12: return '账号停用';
            case 13: return 'IP鉴权失败';
            case 14: return '格式错误';
            case -1: return '系统异常';
            default : return '未知参数';
        }
    }


}