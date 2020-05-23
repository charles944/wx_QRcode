<?php
namespace addons\smsbao;

use common\controller\Addon;
use think\Db;

class SmsbaoAddon extends Addon
{
    public $info = array(
        'name' => 'smsbao',
        'title' => '短信宝',
        'description' => '短信宝短信插件 http://www.smsbao.com/ ',
        'status' => 1,
        'author' => '靑年',
        'version' => '2.1.2'
    );

    public $addon_path = './addons/smsbao/';

    public $admin_list = array(
        'listKey' => array(
            'action' => '行为标识',
            'status' => '状态',
            'preview' => '预览',
        ),
        'model' => 'SmsbaoTpl',
        'order' => 'id asc'
    );
    public $custom_adminlist = 'adminlist.html';

    public function install(){
        $install_sql = './addons/smsbao/install.sql';
        if (file_exists ( $install_sql )) {
            execute_sql_file ( $install_sql );
        }
        return true;
    }

    public function uninstall(){
        $uninstall_sql = './addons/smsbao/uninstall.sql';
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

        $cacheAction2Tpl=S('ACTION2SMSBAOTPLCODE');
        if(empty($cacheAction2Tpl)){
            $cacheAction2Tpl = D('Addons://smsbao/SmsbaoTpl')->action2tplcode();
            S('ACTION2SMSBAOTPLCODE',$cacheAction2Tpl);
        }

        if(!empty($action)){
            $SmsbaoTpl=$cacheAction2Tpl[$action]['template_content'];
            if(!$SmsbaoTpl)return '操作名称为：'.$action.'的短信模板不存在';

            $content_c = str_replace('{code}', $content, $SmsbaoTpl);
        }else{
            $content_c = $content;
        }

        $http = 'http://api.smsbao.com/sms';

        $url = $http .'?u=' . $uid . '&p=' . strtolower(md5($pwd)) . '&m=' . $mobile . '&c=' . urlencode($content);
        $return = file_get_contents($url);
        if ($return == 0) {
            return true;
        } else {
            return "发送失败! 状态：" . $return .' '. $this->getCode($return);
        }

    }


    private function getCode($code){
        switch($code){
            case 0: return '提交成功';
            case 30: return '密码错误';
            case 40: return '账号不存在';
            case 41: return '余额不足';
            case 42: return '帐号过期';
            case 43: return 'IP地址限制';
            case 50: return '内容含有敏感词';
            case 51: return '手机号码不正确';
            default : return '未知参数';
        }
    }


}