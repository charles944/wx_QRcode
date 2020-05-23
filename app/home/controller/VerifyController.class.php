<?php
namespace home\controller;
use think\Controller;
class VerifyController extends Controller
{
    /**
     * sendVerify 发送验证码
     */
    public function sendVerify()
    {
        $aAccount = $cUsername = I('post.account', '', 'op_t');
        $aAccount=str_replace(array('"',"'",'`',',',')','(','='),'',$aAccount);//过滤防止被注入攻击
        $aType = I('post.type', '', 'op_t');
        $aType = $aType == 'mobile' ? 'mobile' : 'email';
        $aAction = I('post.action', 'config', 'op_t');
        
        if (!check_reg_type($aType)) {
            $str = $aType == 'mobile' ? '手机' : '邮箱';
            $this->error($str . '选项已关闭！');
        }
        if (empty($aAccount)) {
            $this->error('帐号不能为空');
        }
        //check_username($cUsername, $cEmail, $cMobile);
        $time = time();
        if($aType == 'mobile'){
            $return = check_action_limit('send_verify', $aAction,0, 1, false);//通过行为限制在全站层面防止频繁发送验证码
            if ($return && !$return['state']) {
                $this->error( $return['info']);
            }
            $resend_time =  modC('SMS_RESEND','60','CONFIG');
            if($time <= session('verify_time')+$resend_time ){
                $this->error('请'.($resend_time-($time-session('verify_time'))).'秒后再发');
            }
        
        }
        if ($aType == 'email' && empty($aAccount)) {
            $this->error('请验证邮箱格式是否正确');
        }
        if ($aType == 'mobile' && empty($aAccount)) {
            $this->error('请验证手机格式是否正确');
        }
        
        $todo=I('post.todo','','op_t');
        $checkIsExist = D('User/UcenterMember')->where(array($aType => $aAccount))->find();
        if ($checkIsExist) {
            if($todo=='register'){
                $str = $aType == 'mobile' ? '手机' : '邮箱';
                $this->error('该' . $str . '已被其他用户使用！');
            }
        }else{
            if($todo=='foundpassword'){
                $str = $aType == 'mobile' ? '手机' : '邮箱';
                $this->error( $str . '不存在');
            }
        }
        $uid=$checkIsExist?$checkIsExist['id']:0;
        $verify = D('Common/Verify')->addVerify($aAccount, $aType, $uid);
        
        action_log('send_verify', 'Ucenter',-1,1);
        
        if (!$verify) {
            $this->error('发送失败！');
        }
        $res = $this->doSendVerify($aAccount, $verify, $aType, $todo);
        if ($res === true) {
            if($aType == 'mobile'){
                session('verify_time',$time);
            }
            $this->success('发送成功，请查收');
        } else {
            $this->error($res);
        }
    }
    
    /**
     * doSendVerify  发送验证码
     * @param $account
     * @param $verify
     * @param $type
     * @return bool|string
     * @author:
     */
    private function doSendVerify($account, $verify, $type, $todo)
    {
        switch ($type) {
            case 'mobile':
                $cur_date = strtotime(date('Y-m-d 00:00:00'));
                $sms_history = D('Sms')->where(array('uid'=>is_login(),'create_time'=> array('egt',$cur_date),'status'=>1,'ip'=>get_client_ip(1)))->count();
                $sms_limit = modC('SMS_LIMIT','3','CONFIG');
                if((int)$sms_history >= (int)$sms_limit){
                    return '亲，今日短信验证数量已经超出限额，请明天再试！';
                }
                $res = sendSMS($account, $verify, $todo);
                if($res){
                    $sms_data['uid']= is_login();
                    $sms_data['verify'] = $verify;
                    $sms_data['mobile'] = $account;
                    $sms_data['status'] = 1;
                    $sms_data['msg'] = $res;
                    $sms_data['ip'] = get_client_ip(1);
                    $sms_data['create_time'] = TIME();
                    $result = D('Sms')->add($sms_data);
                }
                /* $find = "失败";
                if(strpos($res,$find) > 0){
                    $res = $res;
                }else{
                    $res = explode("|",$res);
                    $res = $res[0];
                } */
                return $res;
                break;
            case 'email':
                //发送验证邮箱
                $sitetitle = modC('WEB_SITE_NAME', '靑年PHP', 'Config');
                $content = D('mail_tpl')->where(array('action_name'=>'reg_email_verify'))->getField('template_content');
                $content = str_replace('{title}', $sitetitle, $content);
                $content = str_replace('{verify}', $verify, $content);
                $content = str_replace('{account}', $account, $content);
                $data['uid'] = is_login();
                $data['mail_to'] = $account;
                $data['mail_subject'] = $sitetitle . "邮箱验证";
                $data['mail_body'] = $content;
                $data['add_time'] = TIME();
                $queue_id = D('mail_queue')->add($data);
                $res = send_mail($account, $sitetitle . '邮箱验证', $content);
                D('mail_queue')->where(array('id'=>$queue_id))->save(array('msg'=>json_encode($res)));
                return $res;
                break;
        }
    }
}