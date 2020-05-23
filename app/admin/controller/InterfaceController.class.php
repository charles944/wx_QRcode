<?php
namespace admin\controller;

use admin\builder\AdminConfigBuilder;
use admin\builder\AdminListBuilder;
use admin\builder\AdminTreeListBuilder;
/**
 * 扩展后台管理页面
 */
class InterfaceController extends AdminController {

    public function _initialize(){
        parent::_initialize();
    }
	
    /**
     * 接口列表
     */
    public function index(){
        $this->meta_title = '接口列表';
        $map['status']= array('neq','-1');
		$list = D('Interface')->getList();
        //$list = D('Interface')->where($map)->order('id asc')->select();
        $total = D('Interface')->where($map)->count();
        
        $request    =   (array)I('request.');
        //$listRows   =   C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $listRows   =   $total;
        $page       =   new \think\Page($total, $listRows, $request);
        $p          =   $page->show();
        $this->assign('_list', $list);
        $this->assign('_page', $p? $p: '');
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->display();
    }

    /**
     * 启用接口
     */
    public function enable(){
        $id     =   I('id');
        $msg    =   array('success'=>'启用成功', 'error'=>'启用失败');
        $this->resume('Interface', "id={$id}", $msg);
    }

    /**
     * 禁用接口
     */
    public function disable(){
        $id     =   I('id');
        $msg    =   array('success'=>'禁用成功', 'error'=>'禁用失败');
        $this->forbid('Interface', "id={$id}", $msg);
    }

    public function del($ids = ''){
        $ids = array_unique((array)I('ids',0));
        if ( empty($ids) ) {
            $this->error('请选择要操作的数据!');
        }
        $interfaceModel = D('Interface');
        $map = array('id' => array('in', $ids));
        if($interfaceModel->where($map)->delete()){
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
    
    /**
     * 设置插件页面
     */
    public function config(){
    	$id     =   (int)I('id');
    	$interface  =  M('Interface')->find($id);
    	if(!$interface)
    		$this->error('接口未安装');
    	
    	$builder = new AdminConfigBuilder();
    	$builder->title('编辑接口');
    	$builder->meta_title = '编辑接口';
    	
    	$builder->keyId()
    	->keyText('title', '接口名称', '只需要添加些简单的游戏名字即可')
    	->keyRadio('review','是否需要审核','是或者否，选择之后接口返回的数据需要后台手动审核', array(
    			0 => '否',
    			1 => '是',
    	))
    	->keyText('name', '标识', '英文标识')
    	->keyTextArea('description', '接口简单描述', '简单的介绍接口功能')
    	->keyText('websiteid', '网站ID', '接口可能需要用到的网站ID参数')
    	->keyText('key', '加密值Key', '接口加密验证需要用到的Key')
    	->keyTextArea('config', '配置值', '不同的接口可能需要用到不同的配置值，这个参数放着套用，没有默认为空')
    	->keyText('author', '作者', '填写作者名')
    	->keyText('version', '接口版本', '当前接口版本')
    	->keyStatus();
        $builder->data($interface);
        $builder->buttonSubmit(U('Interface/saveconfig'));
        $builder->buttonBack();
        $builder->display();
    }
	//安装接口
	public function install()
    {
        $class = A('api/'.ucfirst(I('name')));
		if(!$class->info){ // 实例化插件失败忽略执行
			\think\Log::record('接口'.$name.'文件不存在！');
			$this->error('插件不存在');
		}
        $apiclass = new $class;
        $info = $apiclass->info;
        if (!$info)//检测信息的正确性
        {
            $this->error('插件信息缺失');
        }
        $install_flag = $apiclass->install();
        if (!$install_flag) {
            $this->error('执行插件预安装操作失败');
        }
        $interfaceModel = D('Interface');
        $data = $interfaceModel->create($info);

        if (!$data) {
            $this->error($interfaceModel->getError());

        }
        if ($interfaceModel->add($data)) {
			$this->success('安装成功');
        } else {
            $this->error('写入接口数据失败');
        }
    }

    /**
     * 卸载接口
     */
    public function uninstall(){
        $interfaceModel    =   M('Interface');
        $id             =   trim(I('id'));
        $db_interface      =   $interfaceModel->find($id);
        
        $this->assign('jumpUrl',U('index'));
        $class = A('api/'.$name);
		if(!$class->info){ // 实例化插件失败忽略执行
			\think\Log::record('接口'.$name.'文件不存在！');
			$this->error('接口不存在');
		}
        $interface =   new $class;
        $uninstall_flag =   $interface->uninstall();
        if(!$uninstall_flag)
            $this->error('执行插件预卸载操作失败');
        $delete = $interfaceModel->where("name='{$db_interface['name']}'")->delete();
        if($delete === false){
            $this->error('卸载接口失败');
        }else{
            $this->success('卸载成功');
        }
    }
    
    /**
     * 新增插件页面
     */
    public function create(){
    	$builder = new AdminConfigBuilder();
    	$builder->title('添加接口');
    	$builder->meta_title = '添加接口';
    	 
    	$builder
    	->keyText('title', '接口名称', '只需要添加些简单的游戏名字即可')
    	->keyRadio('review','是否需要审核','是或者否，选择之后接口返回的数据需要后台手动审核', array(
    			0 => '否',
    			1 => '是',
    	))
    	->keyText('name', '标识', '英文标识')
    	->keyTextArea('description', '接口简单描述', '简单的介绍接口功能')
    	->keyText('websiteid', '网站ID', '接口可能需要用到的网站ID参数')
    	->keyText('key', '加密值Key', '接口加密验证需要用到的Key')
    	->keyTextArea('config', '配置值', '不同的接口可能需要用到不同的配置值，这个参数放着套用，没有默认为空')
    	->keyText('author', '作者', '填写作者名')
    	->keyText('version', '接口版本', '当前接口版本')
		->keyStatus('status','状态','接口使用状态', array(
    	));
    	$builder->buttonSubmit(U('Interface/saveconfig'));
    	$builder->buttonBack();
    	$builder->display();
    }
    
    public function saveconfig($id=0, $title='', $name='', $review=0, $status=0, $description='', $websiteid='', $key='', $config='', $author='', $version=''){
    	$isEdit = $id ? 1 : 0;
    	if (IS_POST) {
    		if ($title == '' || $title == null) {
    			$this->error('请输入接口名称');
    		}
    		if ($name == '' || $name == null) {
    			$this->error('请输入接口英文标识，重要');
    		}
    		$interface['title'] = $title;
    		$interface['name'] = $name;
    		$interface['review'] = $review;
    		$interface['status'] = $status;
    		$interface['description'] = $description;
    		$interface['websiteid'] = $websiteid;
    		$interface['key'] = $key;
    		$interface['config'] = $config;
    		$interface['author'] = $author;
    		$interface['version'] = $version;
    		$interface['create_time'] = time();
    		
    		if ($isEdit) {
    			$rs = D('Interface')->where('id=' . $id)->save($interface);
    		}
    		else{
    			$rs = D('Interface')->add($interface);
    		}
    		if ($rs) {
    			$this->success($isEdit ? '编辑成功' : '添加成功', U('Interface/Index'));
    		} else {
    			$this->error($isEdit ? '编辑失败' : '编辑失败');
    		}
    	}
    	
    	
    }
    
    public function log($page = 1, $r = 50)
    {
        $aTitle=I('title','','op_t');
    	if($aTitle){
    		$map['action_name'] = array('like','%'.$aTitle.'%');
    	}
    	list($list,$totalCount)=$this->getListByPage('ApiLog',$map,$page,'id desc','*',$r);
    	$roption = array(array('id'=>50,'value'=>'显示50行'),array('id'=>100,'value'=>'显示100行'),array('id'=>200,'value'=>'显示200行'));
    	
    	$builder = new AdminListBuilder();
    	$builder->title("接口返回记录");
    	$builder->meta_title = '接口返回记录';
    	
    	$builder->setSelectPostUrl(U('interface/log'))
        ->setSearchPostUrl(U('interface/log'))
        ->search('搜索', 'title', 'text', '请输入接口标识')
        ->select('显示：','r','select','','','',$roption)
    	->button('接口列表', array('class'=>'layui-btn layui-btn-xs fbutton','href'=>U('interface/index')))
    	->button('清空记录',array('class'=>'layui-btn layui-btn-xs fbutton ajax-get','href'=>U('logdel')))
        ->button('批量彻底删除',array('class'=>'layui-btn layui-btn-xs fbutton ajax-post','href'=>U('logdel'),'target-form'=>'ids'))
    	
    	->keyText('action_name','访问接口')
    	->keyText('url', 'URL')
        ->keyText('urldata', '内容')
    	->keyTime('create_time','创建时间')
    	->data($list)
    	->pagination($totalCount, $r)
    	->display();

    }
    
    public function logdel(){
        $ids = I('ids',0);
        if(empty($ids)){
            $rs = D('ApiLog')->where('1=1')->delete();
        	if($rs){
        		$this->success('清空完毕！');
        	}else{
        		$this->error('错误');
        	}
        }else{
            $ids=is_array($ids)?$ids:explode(',',$ids);
        	$rs = D('ApiLog')->where(array('id'=>array('in',$ids)))->delete();
            if($rs){
                $this->success('操作成功！');
            }else{
                $this->error('未知操作！');
            }
        }
        
        
    	
    }

}
