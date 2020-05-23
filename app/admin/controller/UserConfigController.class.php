<?php
namespace admin\controller;

use admin\builder\AdminConfigBuilder;

/**
 * Class UserConfigController  后台用户配置控制器
 * @package Admin\Controller
 */
class UserConfigController extends AdminController
{

    public function index()
    {

        $admin_config = new AdminConfigBuilder();
        $data = $admin_config->handleConfig();

            empty($data['LEVEL']) && $data['LEVEL'] = <<<str
0:Lv1 实习
50:Lv2 试用
100:Lv3 转正
200:Lv4 助理
400:Lv 5 经理
800:Lv6 董事
1600:Lv7 董事长
str;
        empty($data['OPEN_QUICK_LOGIN']) && $data['OPEN_QUICK_LOGIN'] = 0;
        empty($data['OPEN_QUICK_REGISTER']) && $data['OPEN_QUICK_REGISTER'] = 0;

		empty($data['LOGIN_SWITCH']) && $data['LOGIN_SWITCH'] = 'username';
		
        $register_options=array(
            'normal'=>L('_ORDINARY_REGISTRATION_'),
            'invite'=>L('_INVITED_TO_REGISTER_')
        );
		
        $admin_config->title('用户配置')
			->suggest('设置用户注册相关配置')
			->data($data)
			->keyCheckBox('REGISTER_TYPE[]', L('_REGISTERED_TYPE_'), '默认勾选普通注册',$register_options)->keyDefault('REGISTER_TYPE[]',$data['REGISTER_TYPE'])
            ->keyCheckBox('REG_SWITCH[]', '注册开关', '允许使用的注册选项,全不选即为关闭注册',array('username' => '用户名', 'email' => '邮箱', 'mobile' => '手机'))->keyDefault('REG_SWITCH[]',$data['REG_SWITCH'])
            ->keyRadio('EMAIL_VERIFY_TYPE', '邮箱验证类型', '邮箱验证的类型', array(0 => '不验证', 1 => '注册后发送激活邮件', 2 => '注册前发送验证邮件'))
            ->keyRadio('MOBILE_VERIFY_TYPE', '手机验证类型', '手机验证的类型', array(0 => '不验证', 1 => '注册前发送验证短信'))
 			->keySingleImage('REG_BACKGROUND', '注册基础页面背景图')
            ->keyRadio('OPEN_QUICK_REGISTER', '快捷弹窗注册','默认开启，开启后用户注册方式更换成快捷弹窗注册！', array(0 => '关闭', 1 => '开启'))

            ->keyTextArea('LEVEL', '等级配置', '每行一条，名称和积分之间用冒号分隔')
			->keyInteger('NICKNAME_MIN_LENGTH', '昵称长度最小值')->keyDefault('NICKNAME_MIN_LENGTH',2)
            ->keyInteger('NICKNAME_MAX_LENGTH', '昵称长度最大值')->keyDefault('NICKNAME_MAX_LENGTH',32)
            ->keyInteger('USERNAME_MIN_LENGTH', '用户名长度最小值')->keyDefault('USERNAME_MIN_LENGTH',2)
            ->keyInteger('USERNAME_MAX_LENGTH', '用户名长度最大值')->keyDefault('USERNAME_MAX_LENGTH',32)

            ->keyRadio('OPEN_QUICK_LOGIN','快捷弹窗登录','默认关闭，开启后用户登录方式更换成快捷弹窗登录！', array(0 => '关闭', 1 => '开启'))
			->keyCheckBox('LOGIN_SWITCH[]', '登录提示开关', '仅用于登录框的提示作用', array('username' => '用户名', 'email' => '邮箱', 'mobile' => '手机'))->keyDefault('LOGIN_SWITCH[]',$data['LOGIN_SWITCH'])
            
            ->keyRadio('UPS_TYPE_MODE','上线用户名/用户ID模式','选择整站上线是按用户名设置，还是按用户ID设置',array('id'=>'用户ID','username'=>'用户名'))
            
            ->group('注册配置', 'REGISTER_TYPE[],REG_SWITCH[],EMAIL_VERIFY_TYPE,MOBILE_VERIFY_TYPE,REG_BACKGROUND,OPEN_QUICK_REGISTER')
            ->group('登录配置', 'OPEN_QUICK_LOGIN,LOGIN_SWITCH[]')
            ->group('基础设置', 'UPS_TYPE_MODE,LEVEL,NICKNAME_MIN_LENGTH,NICKNAME_MAX_LENGTH,USERNAME_MIN_LENGTH,USERNAME_MAX_LENGTH')
            ->buttonSubmit();
        $admin_config->display();
    }
}
