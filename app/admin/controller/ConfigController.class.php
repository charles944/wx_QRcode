<?php
namespace admin\controller;

use admin\builder\AdminListBuilder;
use admin\builder\AdminConfigBuilder;
use admin\builder\AdminSortBuilder;
use admin\builder\AdminTreeListBuilder;
/**
 * 后台配置控制器
 * @author 乾坤网络有限公司
 */
class ConfigController extends AdminController {

    /**
     * 配置管理
     * @author 乾坤网络有限公司
     */
    public function index(){
        /* 查询条件初始化 */
        $map = array();
        $map = array('status' => 1, 'title' => array('neq', ''));
        if(isset($_GET['group'])){
            $map['group']   =   I('group',0);
        }
        if(isset($_GET['name'])){
            $map['name']    =   array('like', '%'.(string)I('name').'%');
        }

        $list = $this->lists('Config', $map,'sort,id');
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);

        $this->assign('group',C('CONFIG_GROUP_LIST'));
        $this->assign('group_id',I('get.group',0));
        $this->assign('list', $list);
        $this->meta_title = '配置管理';
        $this->display();
    }

    /**
     * 新增配置
     * @author 乾坤网络有限公司
     */
    public function add(){
        if(IS_POST){
            $Config = D('Config');
            $data = $Config->create();
            if($data){
                if($Config->add()){
                    S('DB_CONFIG_DATA',null);
                    $this->success('新增成功', U('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            $this->meta_title = '新增配置';
            $this->assign('info',null);
            $this->display('edit');
        }
    }

    /**
     * 编辑配置
     * @author 乾坤网络有限公司
     */
    public function edit($id = 0){
        if(IS_POST){
            $Config = D('Config');
            $data = $Config->create();
            if($data){
                if($Config->save()){
                    S('DB_CONFIG_DATA',null);
                    //记录行为
                    action_log('update_config','config',$data['id'],UID);
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('Config')->field(true)->find($id);

            if(false === $info){
                $this->error('获取配置信息错误');
            }
            $this->assign('info', $info);
            $this->meta_title = '编辑配置';
            $this->display();
        }
    }

    /**
     * 批量保存配置
     * @author 乾坤网络有限公司
     */
    public function save($config){
        if($config && is_array($config)){
            $Config = M('Config');
            foreach ($config as $name => $value) {
                if(is_array($value)){
                    $value = implode(',',$value);
                }
                $map = array('name' => $name);
                $Config->where($map)->setField('value', $value);
            }
        }
        S('DB_CONFIG_DATA',null);
        $this->success('保存成功！');
    }

    /**
     * 删除配置
     * @author 乾坤网络有限公司
     */
    public function del(){
        $id = array_unique((array)I('id',0));

    	$id = is_array($id) ? implode(',', $id) : $id;
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }

    	$map = array('id' => array('in', $id));
        if (M('Config')->where($map)->delete()) {
            S('DB_CONFIG_DATA', null);
            //记录行为
            action_log('update_config', 'config', $id, UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    // 获取某个标签的配置参数
    public function group() {
        $id     =   I('get.id',1);
        $type   =   C('CONFIG_GROUP_LIST');
        $list   =   M("Config")->where(array('status'=>1,'group'=>$id))->field('id,name,title,extra,value,remark,type')->order('sort')->select();
        if($list) {
            $this->assign('list',$list);
        }
        $this->assign('id',$id);
        $this->meta_title = $type[$id].'设置';
        $this->display();
    }

    /**
     * 配置排序
     * @author 乾坤网络有限公司
     */
    public function sort(){
        if(IS_GET){
            $ids = I('get.ids');

            //获取排序的数据
            $map = array('status' => array('gt', -1), 'title' => array('neq', ''));
            if(!empty($ids)){
                $map['id'] = array('in',$ids);
            }elseif(I('group')){
                $map['group']	=	I('group');
            }
            $list = M('Config')->where($map)->field('id,title')->order('sort asc,id asc')->select();

            //显示页面
            $builder = new AdminSortBuilder();
            $builder->title('配置排序')
            ->data($list)
            ->buttonSubmit(U('sort'))->buttonBack()
            ->display();
        }elseif (IS_POST){
            $ids = I('post.ids');
            $ids = explode(',', $ids);
            foreach ($ids as $key=>$value){
                $res = M('Config')->where(array('id'=>$value))->setField('sort', $key+1);
            }
            if($res !== false){
                $this->success('排序成功！',Cookie('__forward__'));
            }else{
                $this->eorror('排序失败！');
            }
        }else{
            $this->error('非法请求！');
        }
    }
    
    /**网站信息设置
     * @auth
     */
    public function website()
    {
    	$builder = new AdminConfigBuilder();
    	$data = $builder->handleConfig();
    	$builder->title('网站信息');
		$builder->suggest('此处配置网站的一般信息。');
		$builder->data($data);
    	$builder->keyText('WEB_SITE_NAME', '网站名', '用于邮件,短信,站内信显示');
    	$builder->keyText('HEADER_NAV_COLOR','前台导航背景色','可使用英文或者RGB如：#fff');
    	$builder->keyText('ICP', '网站备案号', '设置在网站底部显示的备案号，如“沪ICP备12007941号-2');
    	$builder->keySingleImage('LOGO', '网站Logo', '网站的logo设置，建议尺寸156*50');
    	$builder->keySingleImage('JUMP_BACKGROUND', '跳转页背景图片', '跳转页背景图片');
    	$builder->keyText('SUCCESS_WAIT_TIME', '成功等待时间', '设置成功时页面等待页面')->keyDefault('SUCCESS_WAIT_TIME',2);
    	$builder->keyText('ERROR_WAIT_TIME', '失败等待时间', '设置失败时页面等待页面')->keyDefault('ERROR_WAIT_TIME',5);

    	$builder->keyEditor('ABOUT_US', '关于我们内容', '页脚关于我们介绍，请先使用HTML编辑模式编辑，编辑完成之后切换回来再保存');
    	$builder->keyEditor('SUBSCRIB_US', '关注我们', '页脚关注我们内容，请先使用HTML编辑模式编辑，编辑完成之后切换回来再保存');
    	$builder->keyEditor('COPY_RIGHT', '版权信息', '页脚版权信息，请先使用HTML编辑模式编辑，编辑完成之后切换回来再保存');
		//获取支持图片上传相关插件
		$addons = \think\Hook::get('uploaddriver');
        $opt = array('local' => '本地');
        foreach ($addons as $name) {
            if (class_exists($name)) {
                $class = new $name();
                $config = $class->getConfig();
                if ($config['switch']) {
                    $opt[$class->info['name']] = $class->info['title'];
                }
            }
        }

        $builder->keySelect('PICTURE_UPLOAD_DRIVER', '图片上传驱动', '图片上传驱动', $opt);
        $builder->keySelect('DOWNLOAD_UPLOAD_DRIVER', '附件上传驱动', '附件上传驱动', $opt);
		
		//获取支持短信发送相关插件
		$addons_sms = \think\Hook::get('sms');
        $opt_sms = array('none' => '无');
        foreach ($addons_sms as $name) {
            if (class_exists($name)) {
                $class = new $name();
                $config_sms = $class->getConfig();
                if ($config_sms['switch']) {
                    $opt_sms[$class->info['name']] = $class->info['title'];
                }
            }
        }
		$builder->keySelect('SMS_HOOK', '短信发送服务商', '短信发送服务商，需要安装插件', $opt_sms);
        $builder->keyInteger('SMS_RESEND', '短信重发时间', '短信重发时间')->keyDefault('SMS_RESEND',60);
        $builder->keyInteger('SMS_LIMIT', '每日限制次数', '每天同个账号最多发送多少条短信')->keyDefault('SMS_LIMIT',3);

    	$builder->group('基本信息', array('WEB_SITE_NAME','HEADER_NAV_COLOR', 'ICP', 'LOGO', 'XMLOGO', 'QRCODE'));
    	$builder->group('页脚信息', array('ABOUT_US', 'SUBSCRIB_US', 'COPY_RIGHT'));
    	$builder->group('错误跳转页面', array('JUMP_BACKGROUND', 'SUCCESS_WAIT_TIME', 'ERROR_WAIT_TIME'));
		$builder->group('上传配置', array('PICTURE_UPLOAD_DRIVER', 'DOWNLOAD_UPLOAD_DRIVER'));
		$builder->group('短信发送商配置',array('SMS_HOOK','SMS_RESEND','SMS_LIMIT'));

    	$builder->buttonSubmit();
    	$builder->display();
    }
	
	public function avatarlist($page=1,$r=20)
	{
		$map['status']=1;
        list($list,$totalCount)=$this->getListByPage('avatar_list',$map,$page,'id desc','*',$r);
		$builder=new AdminListBuilder();
        $builder->title('用户头像选择列表')
            ->data($list)
            ->buttonNew(U('config/editavatarlist'))
            ->keyId()->keyImage('thumb', '缩略图')
            ->keyStatus()
            ->keyDoActionEdit('config/editavatarlist?id=###');
			$builder->pagination($totalCount,$r)
            ->display();
	}
	
	public function editavatarlist($id = 0, $thumb = 0, $create_time = 0, $status = 0, $update_time = 0)
    {
    	$isEdit = $id ? 1 : 0;
    	if (IS_POST) {
    		if (! is_numeric($thumb)) {
    			$this->error('请上传头像图片');
    		}
    		$avatartpl['thumb'] = $thumb;
    		if ($isEdit) {
    			$avatartpl['update_time'] = time();
    			$avatartpl['status'] = $status;
    			$rs = D('avatar_list')->where('id=' . $id)->save($avatartpl);
    		} else {
    			$avatartpl['create_time'] = time();
    			$avatartpl['update_time'] = time();
    			$avatartpl['status'] = $status;
    			$rs =  D('avatar_list')->add($avatartpl);
    		}
    		if ($rs) {
    			$this->success($isEdit ? '编辑成功' : '添加成功', U('config/avatarlist'));
    		} else {
    			$this->error($isEdit ? '编辑失败' : '添加失败');
    		}
    	} else {
    		$builder = new AdminConfigBuilder();
    		$builder->title($isEdit ? '编辑用户头像' : '添加用户头像');
    		$builder->meta_title = $isEdit ? '编辑用户头像' : '添加用户头像';
    
    		$builder->keyId()
    		->keySingleImage('thumb', '缩略图')
    		->keyStatus('status', '使用状态');
    		if ($isEdit) {
    			$avatartpl =  D('avatar_list')->where('id=' . $id)->find();
    			$builder->data($avatartpl);
    			$builder->buttonSubmit(U('config/editavatarlist'));
    			$builder->buttonBack();
    			$builder->display();
    		} else {
    			$avatardata['status'] = 1;
    			$builder->buttonSubmit(U('config/editavatarlist'));
    			$builder->buttonBack();
    			$builder->data($avatardata);
    			$builder->display();
    		}
    	}
    }
	
	public function mailtemplate($page=1,$r=20)
    {
        $map['status']=1;
        list($list,$totalCount)=$this->getListByPage('mail_tpl',$map,$page,'id desc','*',$r);
        

        $builder=new AdminListBuilder();
        $builder->title('邮件模板')
            ->data($list)
            ->buttonNew(U('config/editmailtpl'))
            ->keyId()->keyText('action_name','模板标识')->keyText('title','模板标题')
            ->keyStatus()
			->keyUpdateTime()
            ->keyDoActionEdit('config/editmailtpl?id=###');
			$builder->pagination($totalCount,$r)
            ->display();
	}
	
	public function editmailtpl()
    {
        $aId=I('id',0,'intval');
        $title=$aId?"编辑":"新增";
        if(IS_POST){
            $aId&&$data['id']=$aId;
            //$data['uid']=I('post.uid',get_uid(),'intval');
            $data['title']=I('post.title','','op_t');
            $data['template_content']=I('post.template_content','','');
            $data['action_name']=I('post.action_name','','op_t');
            $data['status']=I('post.status',1,'intval');
            
			if($aId){
				$data['update_time'] = TIME();
				$result=D('mail_tpl')->save($data);
			}else{
				$data['create_time'] = TIME();
				$data['update_time'] = TIME();
				$result=D('mail_tpl')->add($data);
			}
            if($result){
                $aId=$aId?$aId:$result;
                $this->success($title.'成功！',U('config/mailtemplate'));
            }else{
                $this->error($title.'失败！',D('mail_tpl')->getError());
            }
        }else{
            if($aId){
                $data=D('mail_tpl')->find($aId);
            }
            
            $builder=new AdminConfigBuilder();
            $builder->title($title.'邮件模板')
                ->data($data)
                ->keyId()
				->keyText('action_name','模板英文标识')
                ->keyText('title','模板标题')
                ->keyEditor('template_content','内容','','all',array('width' => '700px', 'height' => '400px'))
                ->keyStatus()->keyDefault('status',1)
                ->buttonSubmit()->buttonBack()
                ->display();
        }
    }
}
