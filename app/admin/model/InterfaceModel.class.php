<?php
namespace admin\model;
use think\Model;

/**
 * 接口模型
 * @author 乾坤网络有限公司
 */

class InterfaceModel extends Model {

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
    /**
     * 文件模型自动完成
     */
    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
    );
	
	public function install($name)
    {
		$class = A('api/'.$name);
		if(!$class->info){ // 实例化插件失败忽略执行
			\think\Log::record('接口'.$name.'文件不存在！');
			$this->error = '插件不存在';
            return false;
		}
        $apiclass = new $class;
        $info = $apiclass->info;
        if (!$info || !$apiclass->checkInfo())//检测信息的正确性
        {
            $this->error = '插件信息缺失';
            return false;
        }
        session('addons_install_error', null);
        $install_flag = $apiclass->install();
        if (!$install_flag) {
            $this->error = '执行插件预安装操作失败' . session('addons_install_error');
            return false;
        }
        $interfaceModel = D('Interface');
        $data = $interfaceModel->create($info);

        if (!$data) {
            $this->error = $interfaceModel->getError();
            return false;
        }
        if ($interfaceModel->add($data)) {
            return true;
        } else {
            $this->error = '写入接口数据失败';
            return false;
        }
    }

    /**
     * 获取接口列表
     */
    public function getList($api_dir = ''){
		if(!$api_dir)
            $api_dir = './app/api/controller/';
		$dirs = scandir( $api_dir );  
        foreach( $dirs as $file )  
        {
			preg_match('/(.*)\Controller.class.php$/', $file, $html);
			if(!empty($html) && $html[1] != 'Api'){
				$apidirs[] = $html[1];
			}
		}
        if($apidirs === FALSE || !file_exists($api_dir)){
            $this->error = '接口目录不可读或者不存在';
            return FALSE;
        }
		$intfaces			=	array();
		$where['name']	=	array('in',$apidirs);
		$list			=	$this->where($where)->field(true)->select();
		foreach($list as $intface){
			$intface['uninstall']		=	0;
			$intfaces[ucfirst($intface['name'])]	=	$intface;
			
		}
        foreach ($apidirs as $value) {
			//$value = ucfirst($value);
			$value = $value;
            if(!isset($intfaces[$value])){
				$class = A('api/'.$value);
				if(!$class->info){ // 实例化插件失败忽略执行
					\think\Log::record('接口'.$value.'的入口文件不存在！');
					continue;
				}
                //$obj    =   new $class;
				$intfaces[$value]	= $class->info;
				if($intfaces[$value]){
					$intfaces[$value]['uninstall'] = 1;
                    unset($intfaces[$value]['status']);
				}
			}else{
				
			}
        }
        int_to_string($intfaces, array('status'=>array(-1=>'损坏', 0=>'禁用', 1=>'启用', null=>'未安装')));
        $intfaces = list_sort_by($intfaces,'uninstall','desc');
        return $intfaces;
    }

    /**
     * 获取接口的后台列表
     * @author 乾坤网络有限公司
     */
    public function getAdminList(){
        $admin = array();
        $db_intfaces = $this->where("status=1 AND has_adminlist=1")->field('title,name')->select();
        if($db_intfaces){
            foreach ($db_intfaces as $value) {
                $admin[] = array('title'=>$value['title'],'url'=>"Interface/adminList?name={$value['name']}");
            }
        }
        return $admin;
    }
}
