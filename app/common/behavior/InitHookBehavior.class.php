<?php
namespace common\behavior;
use think\Behavior;
use think\Hook;
defined('THINK_PATH') or exit();

// 初始化钩子信息
class InitHookBehavior extends Behavior {

    // 行为扩展的执行入口必须是run
    public function run(&$content){
        if(defined('BIND_MODULE') && BIND_MODULE === 'install') return;
		
        $data = S('hooks');
        if(!$data){
            $hooks = M('Hooks')->getField('name,addons');
            foreach ($hooks as $key => $value) {
                if($value){
                    $map['status']  =   1;
                    $names          =   explode(',',$value);
                    $map['name']    =   array('IN',$names);
					$data = (array)M('addons')->where($map)->getField('id,name');
					$data_plugin = (array)M('plugins')->where($map)->getField('id,name');
					$data = array_merge($data,$data_plugin);

                    if($data){
                        $addons = array_intersect($names, $data);
                        Hook::add($key,array_map('get_addon_class',$addons));
                    }
                }
            }
			
            S('hooks',Hook::get());
        }else{
            Hook::import($data,false);
        }
    }
}