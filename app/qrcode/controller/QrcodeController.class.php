<?php
namespace admin\controller;

use admin\builder\AdminConfigBuilder;
use admin\builder\AdminListBuilder;
use admin\builder\AdminTreeListBuilder;
use admin\model\AuthGroupModel;
use common\model\ContentHandlerModel;

class QrcodeController extends AdminController{

    protected $qrcodeModel;
    protected $qrcodechildModel;
    protected $qrcodelogModel;

    function _initialize()
    {
		parent::_initialize();
        $this->qrcodeModel = D('Qrcode/Qrcode');
        $this->qrcodechildModel = D('Qrcode/QrcodeChild');
        $this->qrcodelogModel = D('Qrcode/QrcodeLog');
    }

    public function config(){
        $builder = new AdminConfigBuilder();
        $data = $builder->handleConfig();
        $builder->title('活码基础设置');
        $builder->keyTextArea('IN_DOMAIN', '默认入口域名','设置以后所有用户使用此默认的入口域名，建议选择使用二级域名作为入口域名，当想要同时设置多个入口域名时，请一行一个域名，不能空行，创建活码时会同时生成多个入口域名的活码二维码');
        $builder->keyTextArea('OUT_DOMAIN', '落地域名','设置以后所有用户使用此默认的落地域名，落地域名请添加绑定多个二级域名，会随机跳转');
        $builder->keyRadio('SHORT_URL_ON','开启短连接开关','是否开启短连接，默认使用urls.cn短连接',array('0'=>'不开','1'=>'开启urls.cn','2'=>'新浪短域名'));
        $builder->keyEditor('TUO_TEXT','子码二维码底部文字','子码图片底部拓展显示文字，比如 请长按二维码识别添加客服人员等字样');
        $builder->group('活码基础设置', 'IN_DOMAIN,OUT_DOMAIN,SHORT_URL_ON,TUO_TEXT');
        $builder->buttonSubmit();
        $builder->data($data);
        $builder->display();
    }

    public function index($page=1,$r=20)
    {
        //$map['status']=1;
        $aDead=I('dead',0,'intval');
        switch($aDead){
            case 0:
                $map['status'] = array('egt','-1');
                break;
            case 1:
                $map['status'] = '1';
                break;
            case 2:
                $map['status'] = '0';
                break;
            case 3:
                $map['status'] = '-1';
                break;
            default:
                $map['status'] = '1';
                break;
        }
        $aTitle=I('title','','op_t');
        if($aTitle){
            $map['uid'] = $aTitle;
        }
        list($list,$totalCount)=$this->getListByPage('qrcode_child',$map,$page,'id desc','*',$r);

        $roption = array(array('id'=>20,'value'=>'显示20行'),array('id'=>50,'value'=>'显示50行'),array('id'=>100,'value'=>'显示100行'));

        $builder=new AdminListBuilder();
        $builder->title('所有子码管理')
            ->suggest('此页面主要用于查看并管理站点所有上传的二维码图片，防止出现违规内容，对于违规的内容可以进行直接封禁或者删除')
            ->data($list)
            ->Button('批量禁用',array('class'=>'layui-btn layui-btn-mini fbutton ajax-post','href'=>U('qrcode/setqrchildstatus?status=0'),'target-form'=>'ids'));
            if($aDead == 2){
                $builder->buttonRestore(U('qrcode/setqrchildstatus'),'批量还原');
            }else{
                $builder->buttonDelete(U('qrcode/setqrchildstatus'),'批量彻底删除');
            }
            $builder->setStatusUrl(U('setqrchildstatus'))
            ->setSelectPostUrl(U('admin/qrcode/index'))
            ->setSearchPostUrl(U('admin/qrcode/index'))
            ->search('搜索', 'title', 'text', '请输入用户ID')
            ->select('显示：','r','select','','','',$roption)
            ->select('状态：','dead','select','','','',array(array('id'=>0,'value'=>'全部'),array('id'=>1,'value'=>'使用中'),array('id'=>2,'value'=>'已禁用'),array('id'=>3,'value'=>'已删除')))
            ->keyId()
            ->keyImage('qr_img','图片')
            ->keyText('title','子码标题')
            ->keyText('qr_type','子码类型',array('1'=>'图片模式','2'=>'跳转链接','3'=>'文本文字'))
            ->keyText('scan_count','扫描次数')
            ->keyUid()
            ->keyStatus()
            ->keyCreateTime()
            ->keyDoAction('qrcode/setqrchildstatus?ids=###&status=-1','删除', array('class'=>'layui-btn layui-btn-mini ajax-get hbutton btn_trash'))
            ->keyDoAction('qrcode/setqrchildstatus?ids=###&status=0','禁用' , array('class'=>'layui-btn layui-btn-mini ajax-get hbutton btn_edit'));
        $builder->pagination($totalCount,$r)
            ->display();
    }

    public function setqrchildstatus($ids,$status=1)
    {
        !is_array($ids)&&$ids=explode(',',$ids);
        $builder = new AdminListBuilder();

        $messageModel=D('Common/Message');
        foreach($ids as &$val){
            $qr_data = $this->qrcodechildModel->getData($val);
            if($status == -1){
                $tip = '您的子码【'.$qr_data['title'].'】由于违规，已被删除!';
                $messageModel->sendMessage($qr_data['uid'],'违规清理通知', $tip, '', '', is_login());
            }else if($status == 0){
                $tip = '您的子码【'.$qr_data['title'].'】由于违规，已被禁用!';
                $messageModel->sendMessage($qr_data['uid'],'违规清理通知', $tip, '', '', is_login());
            }else if($status == 1){
                $tip = '您的子码【'.$qr_data['title'].'】已恢复正常!';
                $messageModel->sendMessage($qr_data['uid'],'恢复通知', $tip, '', '', is_login());
            }
        }
        if($status == -1){
            $builder->doDeleteTrue('qrcode_child', $ids);
        }else{
            $builder->doSetStatus('qrcode_child', $ids, $status);
        }
    }

}