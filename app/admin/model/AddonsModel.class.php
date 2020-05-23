<?php
namespace admin\model;
use think\Model;

//插件模型

class AddonsModel extends Model {

	protected $tokenFile = '/version.json';
    /**
     * 查找后置操作
     * @author 乾坤网络有限公司
     */
    protected function _after_find(&$result,$options) {

    }

    protected function _after_select(&$result,$options){

        foreach($result as &$record){
            $this->_after_find($record,$options);
        }
    }
	/* 自动验证规则 */
	protected $_validate = array (
		array (
			'name',
			'require',
			'插件标识不能为空',
			self::MUST_VALIDATE,
			'regex',
			self::MODEL_BOTH 
		),
		array (
			'name',
			'/^[a-zA-Z][\w_]{1,29}$/',
			'插件标识不合法',
			self::MUST_VALIDATE,
			'regex',
			self::MODEL_BOTH 
		),
		array (
			'name',
			'',
			'插件已安装，请勿重复安装。或者请先卸载后再安装',
			self::VALUE_VALIDATE,
			'unique',
			self::MODEL_BOTH 
		) 
	);
    /**
     * 文件模型自动完成
     */
    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
    );
	
	public function install($name)
    {

        $class = get_addon_class($name);
        if (!class_exists($class)) {
            $this->error = '插件不存在';
            return false;
        }
        $addons = new $class;
        $info = $addons->info;
        if (!$info || !$addons->checkInfo())//检测信息的正确性
        {
            $this->error = '插件信息缺失';
            return false;
        }
        session('addons_install_error', null);
        $install_flag = $addons->install();
        if (!$install_flag) {
            $this->error = '执行插件预安装操作失败' . session('addons_install_error');
            return false;
        }
        $addonsModel = D('Addons');
        $data = $addonsModel->create($info);

        if (is_array($addons->admin_list) && $addons->admin_list !== array()) {
            $data['has_adminlist'] = 1;
        } else {
            $data['has_adminlist'] = 0;
        }
        if (!$data) {
            $this->error = $addonsModel->getError();
            return false;
        }
        if ($addonsModel->add($data)) {
            $config = array('config' => json_encode($addons->getConfig()));
            $addonsModel->where("name='{$name}'")->save($config);
            $hooks_update = D('Hooks')->updateHooks($name);
            if ($hooks_update) {
                S('hooks', null);
                return true;
            } else {
                $addonsModel->where("name='{$name}'")->delete();
                $this->error = '更新钩子处插件失败,请卸载后尝试重新安装';
                return false;
            }

        } else {
            $this->error = '写入插件数据失败';
            return false;
        }
    }
	
	public function updatelist(){
		 if(!$addon_dir)
            $addon_dir = QN_ADDON_PATH;
        $dirs = array_map('basename',glob($addon_dir.'*', GLOB_ONLYDIR));
        if($dirs === FALSE || !file_exists($addon_dir)){
            $this->error = '插件目录不可读或者不存在';
            return FALSE;
        }
		$addonsModel = D('Addons');
		$addons			=	array();
		$where['name']	=	array('in',$dirs);
		$list			=	$this->where($where)->field(true)->select();
		foreach($list as $addon){
			$addon['uninstall']		=	0;
			$addons[$addon['name']]	=	$addon;
		}
		
		foreach ($dirs as $value) {
			//$value = ucfirst($value);
			$class = get_addon_class($value);

			if(!class_exists($class)){ // 实例化插件失败忽略执行
				\think\Log::record('插件'.$value.'的入口文件不存在！');
				continue;
			}

			$obj    =   new $class;
			$addons[$value]	= $obj->info;
			if (!$addons[$value] || !$obj->checkInfo())//检测信息的正确性
			{
				\think\Log::record('插件'.$value.'信息缺失！');
				continue;
			}
			if($addons[$value]){
				$addons[$value]['uninstall'] = 1;
				unset($addons[$value]['status']);
			}
			
			//$addons[$value][] = array('config' => json_encode($obj->getConfig()));
            $addonsModel->where("name='{$value}'")->save($addons[$value]);
			
            $hooks_update = D('Hooks')->updateHooks($value);
            if ($hooks_update) {
                S('hooks', null);
                //return true;
            } else {
                $addonsModel->where("name='{$value}'")->delete();
                $this->error = '更新钩子处插件失败,请卸载后尝试重新安装';
                //return false;
            }
		}
		return true;
	}

    // 获取插件列表
    public function getList($addon_dir = ''){
        if(!$addon_dir)
            $addon_dir = QN_ADDON_PATH;
        $dirs = array_map('basename',glob($addon_dir.'*', GLOB_ONLYDIR));
        if($dirs === FALSE || !file_exists($addon_dir)){
            $this->error = '插件目录不可读或者不存在';
            return FALSE;
        }
		$addons			=	array();
		$where['name']	=	array('in',$dirs);
		$list			=	$this->where($where)->field(true)->select();
		foreach($list as $addon){
			$addon['uninstall']		=	0;
			$addons[$addon['name']]	=	$addon;
			
		}
        foreach ($dirs as $value) {
			//$value = ucfirst($value);
			$value = $value;
            if(!isset($addons[$value])){
				$class = get_addon_class($value);
				if(!class_exists($class)){ // 实例化插件失败忽略执行
					\think\Log::record('插件'.$value.'的入口文件不存在！');
					continue;
				}
				
                $obj    =   new $class;
				$addons[$value]	= $obj->info;
				if($addons[$value]){
					$addons[$value]['uninstall'] = 1;
                    unset($addons[$value]['status']);
				}
			}
			//如果token存在的话
			$this->addonsName = $value;
			$token_arr = $this->getToken($this->addonsName);
			if (!empty($token_arr)) {
				$addons[$value]['token'] = $token_arr['token'];
				$addons[$value]['oid'] = $token_arr['oid'];
				$addons[$value]['md5'] = $token_arr['md5'];
				unset($token_arr);
			}
        }
        int_to_string($addons, array('status'=>array(-1=>'损坏', 0=>'禁用', 1=>'启用', null=>'未安装')));
        $addons = list_sort_by($addons,'uninstall','desc');
		//S();
        return $addons;
    }
	
	//通过模块名来获取模块信息
    public function getAddons($name)
    {
        $addons = $this->where(array('name' => $name))->find();
        if ($addons === false || $addons == null) {
			$value = ucfirst($name);

			$class = get_addon_class($value);
			if(!class_exists($class)){ // 实例化插件失败忽略执行
				\think\Log::record('插件'.$value.'的入口文件不存在！');
				continue;
			}
			
			$obj    =   new $class;
			$m	= $obj->info;
			if($m){
				$m['uninstall'] = 1;
				unset($m['status']);
			}
			//如果token存在的话
			$this->addonsName = $value;
			$token_arr = $this->getToken($this->addonsName);
			if (!empty($token_arr)) {
				$m['token'] = $token_arr['token'];
				$m['oid'] = $token_arr['oid'];
				$m['md5'] = $token_arr['md5'];
				unset($token_arr);
			}
		
			return $m;
        } else {
            $value = ucfirst($name);

			$class = get_addon_class($value);
			if(!class_exists($class)){ // 实例化插件失败忽略执行
				\think\Log::record('插件'.$value.'的入口文件不存在！');
				continue;
			}
			
			$obj    =   new $class;
			$m	= $obj->info;
			if($m){
				$m['uninstall'] = 1;
				unset($m['status']);
			}
			//如果token存在的话
			$this->addonsName = $value;
			$token_arr = $this->getToken($this->addonsName);
			if (!empty($token_arr)) {
				$m['token'] = $token_arr['token'];
				$m['oid'] = $token_arr['oid'];
				$m['md5'] = $token_arr['md5'];
				unset($token_arr);
			}
            return $m;
        }
    }

    /**
     * 获取插件的后台列表
     * @author 乾坤网络有限公司
     */
    public function getAdminList(){
        $admin = array();
        $db_addons = $this->where("status=1 AND has_adminlist=1")->field('title,name')->select();
        if($db_addons){
            foreach ($db_addons as $value) {
                $admin[] = array('title'=>$value['title'],'url'=>U("Addons/adminList",array("name"=>"{$value['name']}")));
            }
        }
        return $admin;
    }
	
	/**获取模块的token
     * @param $name 模块名
     * @return string
     */
    public function getToken($name)
    {
        $this->addonsName = $name;
		$token = '';
        if (file_exists($this->getRelativePath($this->tokenFile))) {
            $token_tmp = file_get_contents($this->getRelativePath($this->tokenFile));
			if(!empty($token_tmp)){
				$token = json_decode($token_tmp, true);
			}
        }
        return $token;
    }

    /**设置模块的token
     * @param $name 模块名
     * @param $token Token
     * @return string
     */
    public function setToken($name, $token)
    {
        $this->addonsName = $name;
        @chmod($this->getRelativePath($this->tokenFile),0777);
        $result = file_put_contents($this->getRelativePath($this->tokenFile), $token);
        @chmod($this->getRelativePath($this->tokenFile),0777);
        return $result;
    }
	
	/*——————————————————————————私有域—————————————————————————————*/

    /**获取模块的相对目录
     * @param $file
     * @return string
     */
    private function getRelativePath($file)
    {
        return QN_ADDON_PATH . $this->addonsName . $file;
    }
}
