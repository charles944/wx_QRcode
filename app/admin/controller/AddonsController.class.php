<?php
namespace admin\controller;

/**
 * 扩展后台管理页面
 */
class AddonsController extends AdminController 
{

    public function _initialize()
    {
		$this->cloudModel = D('Cloud');
        parent::_initialize();
    }

    public function index()
    {
        $this->meta_title = '插件列表';
        $type = I('get.type', 'no', 'text');
        $list = D('Addons')->getList('');
		$list = $this->cloudModel->getVersionInfoList($list);
        $request = (array)I('request.');

        $listRows = 12;
        if ($type == 'yes') {//已安装的
            foreach ($list as $key => $value) {
            	$list[$key]['path'] = ltrim(QN_ADDON_PATH,'.'). strtolower($value['name']);
                if ($value['uninstall'] != 1) {
                    unset($list[$key]);
                }
                
            }
        } else if ($type == 'no') {
            foreach ($list as $key => $value) {
            	$list[$key]['path'] = ltrim(QN_ADDON_PATH,'.'). strtolower($value['name']);
                if ($value['uninstall'] == 1) {
                    unset($list[$key]);
                }
            }
        } else {
            $type = 'all';
            foreach ($list as $key => $value) {
            	$list[$key]['path'] = ltrim(QN_ADDON_PATH,'.'). strtolower($value['name']);
            }
        }
        $total = $list ? count($list) : 1;
        $this->assign('type', $type);
        $page = new \think\Page($total, $listRows, $request);
        $voList = array_slice($list, $page->firstRow, $page->listRows);
        $p = $page->show();
        $this->assign('_list', $voList);
        $this->assign('_page', $p ? $p : '');
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->display();
    }
	
	public function updatelist(){
		$return = D('Addons')->updatelist();
		$this->success('更新成功！');
	}

    /**
     * 插件后台显示页面
     * @param string $name 插件名
     */
    public function adminlist($name, $method = null)
    {
		//如果插件自带admincontroller控制器，则启用 20170214
		if(empty($method)){
			if (method_exists(A('addons://' . $name . '/admin'), 'buildList')) {
				A('addons://' . $name . '/admin')->buildList();
				exit;
			}
		}else{
			if (method_exists(A('addons://' . $name . '/admin'), $method)) {
				A('addons://' . $name . '/admin')->$method();
				exit;
			}
		}
		//如果没有admincontroller控制器则执行下面的 20170214
		
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $class = get_addon_class($name);
        if (!class_exists($class))
            $this->error('插件不存在');
        $addon = new $class();
        $this->assign('addon', $addon);
        $param = $addon->admin_list;
        if (!$param)
            $this->error('插件列表信息不正确');
        $this->meta_title = $addon->info['title'];
        extract($param);
        $this->assign('title', $addon->info['title']);
        $this->assign($param);
        if (!isset($fields))
            $fields = '*';
        if (!isset($map))
            $map = array();
        if (isset($model))
            $list = $this->lists(D("Addons://{$model}/{$model}")->field($fields), $map, $order);
        $this->assign('_list', $list);
        if ($addon->custom_adminlist)
            $this->assign('custom_adminlist', $this->fetch($addon->addon_path . $addon->custom_adminlist));
        $this->display();
    }

    public function changeStatus($method = null)
    {
        $id     =   I('id');
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }
        switch (strtolower($method)) {
            case 'enableaddons':
                $msg    =   array('success'=>'启用成功', 'error'=>'启用失败');
                S('hooks', null);
                $this->resume('Addons', "id={$id}", $msg);
                break;
            case 'disableaddons':
                $msg    =   array('success'=>'禁用成功', 'error'=>'禁用失败');
                S('hooks', null);
                $this->forbid('Addons', "id={$id}", $msg);
                break;
            default:
                $this->error($method . '参数非法');
        }
    }

    /**
     * 设置插件页面
     */
    public function config(){
        
        if(IS_POST){
            $id = (int)I('id');
            $config = I('config');
            $flag = M('Addons')->where("id={$id}")->setField('config', json_encode($config));
            if (isset($config['addons_cache'])) {//清除缓存
                S($config['addons_cache'], null);
            }
            if ($flag !== false) {
                $this->success('保存成功', Cookie('__forward__'));
            } else {
                $this->error('保存失败');
            }
        }else{
            $id     =   (int)I('id');
            $addon  =   M('Addons')->find($id);
            if(!$addon)
                $this->error('插件未安装');
            $addon_class = get_addon_class($addon['name']);
            if(!class_exists($addon_class))
                trace("插件{$addon['name']}无法实例化,",'ADDONS','ERR');
            $data  =   new $addon_class;
            $addon['addon_path'] = $data->addon_path;
            $addon['custom_config'] = $data->custom_config;
            $this->meta_title   =   '设置插件-'.$data->info['title'];
            $db_config = $addon['config'];
            $addon['config'] = include $data->config_file;
            if($db_config){
                $db_config = json_decode($db_config, true);
                foreach ($addon['config'] as $key => $value) {
                    if($value['type'] != 'group'){
                        $addon['config'][$key]['value'] = $db_config[$key];
                    }else{
                        foreach ($value['options'] as $gourp => $options) {
                            foreach ($options['options'] as $gkey => $value) {
                                $addon['config'][$key]['options'][$gourp]['options'][$gkey]['value'] = $db_config[$gkey];
                            }
                        }
                    }
                }
            }
            $this->assign('data',$addon);
            if($addon['custom_config'])
                $this->assign('custom_config', $this->fetch($addon['addon_path'].$addon['custom_config']));
            $this->display();
        }
    }

    /**
     * 安装插件
     */
    public function install()
    {
        $addon_name = trim(I('addon_name'));
        $class = get_addon_class($addon_name);
        if (!class_exists($class))
            $this->error('插件不存在');
        $addons = new $class;
        $info = $addons->info;
        if (!$info || !$addons->checkInfo())//检测信息的正确性
            $this->error('插件信息缺失');
        session('addons_install_error', null);
        $install_flag = $addons->install();
        if (!$install_flag) {
            $this->error('执行插件预安装操作失败' . session('addons_install_error'));
        }
        $addonsModel = D('Addons');
        $data = $addonsModel->create($info);

        if (is_array($addons->admin_list) && $addons->admin_list !== array()){
            $data['has_adminlist'] = 1;
        } else {
            $data['has_adminlist'] = 0;
        }
        if (!$data)
            $this->error($addonsModel->getError());
        if ($addonsModel->add($data)) {
            $config = array('config' => json_encode($addons->getConfig()));
            $addonsModel->where("name='{$addon_name}'")->save($config);
            $hooks_update = D('Hooks')->updateHooks($addon_name);
            if ($hooks_update) {
                S('hooks', null);
                $this->success('安装成功');
            } else {
                $addonsModel->where("name='{$addon_name}'")->delete();
                $this->error('更新钩子处插件失败,请卸载后尝试重新安装');
            }

        } else {
            $this->error('写入插件数据失败');
        }
    }

    /**
     * 卸载插件
     */
    public function uninstall(){
        $addonsModel    =   M('Addons');
        $id             =   trim(I('id'));
        $db_addons      =   $addonsModel->find($id);
        $class          =   get_addon_class($db_addons['name']);
        $this->assign('jumpUrl',U('index'));
        if(!$db_addons || !class_exists($class))
            $this->error('插件不存在');
        session('addons_uninstall_error',null);
        $addons =   new $class;
        $uninstall_flag =   $addons->uninstall();
        if(!$uninstall_flag)
            $this->error('执行插件预卸载操作失败'.session('addons_uninstall_error'));
        $hooks_update   =   D('Hooks')->removeHooks($db_addons['name']);
        if($hooks_update === false){
            $this->error('卸载插件所挂载的钩子数据失败');
        }
        S('hooks', null);
        $delete = $addonsModel->where("name='{$db_addons['name']}'")->delete();
        if($delete === false){
            $this->error('卸载插件失败');
        }else{
            $this->success('卸载成功');
        }
    }

    /**
     * 钩子列表
     */
    public function hooks(){
        $this->meta_title   =   '钩子列表';
        $map    =   $fields =   array();
        $list   =   $this->lists(D("Hooks")->field($fields),$map);
        int_to_string($list, array('type'=>C('HOOKS_TYPE')));
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('list', $list );
        $this->display();
    }

    //钩子出编辑挂载插件页面
    public function edithook(){
        $id = I('id',0,'intval');
        if(!empty($id)){
            if(IS_POST){
                $hookModel  =   D('Hooks');
                $data       =   $hookModel->create();
                if($data){
                    if($data['id']){
                        $flag = $hookModel->save($data);
                        if($flag !== false)
                            $this->success('更新成功', Cookie('__forward__'));
                        else
                            $this->error('更新失败');
                    }else{
                        $flag = $hookModel->add($data);
                        if($flag)
                            $this->success('新增成功', Cookie('__forward__'));
                        else
                            $this->error('新增失败');
                    }
                }else{
                    $this->error($hookModel->getError());
                }
            }else{
                $this->meta_title = '编辑钩子';
                $hook = M('Hooks')->field(true)->find($id);
                $this->assign('data',$hook);
            }
        }else{
            $this->assign('data', null);
            $this->meta_title = '新增钩子';
        }
        $this->display();
    }

    //超级管理员删除钩子
    public function delhook($id){
        if(M('Hooks')->delete($id) !== false){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    public function execute($_addons = null, $_controller = null, $_action = null){
        if(C('URL_CASE_INSENSITIVE')){
            $_addons        =   ucfirst(parse_name($_addons, 1));
            $_controller    =   parse_name($_controller,1);
        }

        $TMPL_PARSE_STRING = C('TMPL_PARSE_STRING');
        $TMPL_PARSE_STRING['__ADDONROOT__'] = __ROOT__ . "/addons/{$_addons}";
        C('TMPL_PARSE_STRING', $TMPL_PARSE_STRING);


        if(!empty($_addons) && !empty($_controller) && !empty($_action)){

            $Addons = A("Addons://{$_addons}/{$_controller}")->$_action();
        } else {
            $this->error('没有指定插件名称，控制器或操作！');
        }
    }

    public function edit($name, $id = 0)
    {
        $this->assign('name', $name);
        $class = get_addon_class($name);
        if(!class_exists($class))
            $this->error('插件不存在');
        $addon = new $class();
        $this->assign('addon', $addon);
        $param = $addon->admin_list;
        if(!$param)
            $this->error('插件列表信息不正确');
        extract($param);
        $this->assign('title', $addon->info['title']);
        if(isset($model)){
            $addonModel = D("Addons://{$name}/{$model}");
            if(!$addonModel)
                $this->error('模型无法实列化');
            $model = $addonModel->model;
            $this->assign('model', $model);
        }
        if($id){
            $data = $addonModel->find($id);
            $data || $this->error('数据不存在！');
            $this->assign('data', $data);
        }

        if(IS_POST){
            // 获取模型的字段信息
            if(!$addonModel->create())
                $this->error($addonModel->getError());

            if($id){
                $flag = $addonModel->save();
                if($flag !== false)
                    $this->success("编辑{$model['title']}成功！", Cookie('__forward__'));
                else
                    $this->error($addonModel->getError());
            }else{
                $flag = $addonModel->add();
                if($flag)
                    $this->success("添加{$model['title']}成功！", Cookie('__forward__'));
            }
            $this->error($addonModel->getError());
        } else {
            $fields = $addonModel->_fields;
            $this->assign('fields', $fields);
            $this->meta_title = $id? '编辑'.$model['title']:'新增'.$model['title'];
            if($id)
                $template = $model['template_edit']? $model['template_edit']: '';
            else
                $template = $model['template_add']? $model['template_add']: '';
            $this->display($addon->addon_path.$template);
        }
    }

    public function del($id = '', $name){
        $ids = array_unique((array)I('ids',0));

        if ( empty($ids) ) {
            $this->error('请选择要操作的数据!');
        }

        $class = get_addon_class($name);
        if(!class_exists($class))
            $this->error('插件不存在');
        $addon = new $class();
        $param = $addon->admin_list;
        if(!$param)
            $this->error('插件列表信息不正确');
        extract($param);
        if(isset($model)){
            $addonModel = D("Addons://{$name}/{$model}");
            if(!$addonModel)
                $this->error('模型无法实列化');
        }

        $map = array('id' => array('in', $ids) );
        if($addonModel->where($map)->delete()){
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

}
