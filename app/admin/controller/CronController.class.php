<?php
namespace admin\controller;

use admin\builder\AdminListBuilder;
use admin\builder\AdminConfigBuilder;
use admin\builder\AdminSortBuilder;
use admin\builder\AdminTreeListBuilder;
/**
 * 后台频道控制器
 * @author 朝兮夕兮
 */
class CronController extends AdminController {

    public function index(){
        $admin_config = new AdminConfigBuilder();
        if (IS_POST) {
            if(!empty($_POST['QUEUE_SET'])){
                $config_set = $_POST['QUEUE_SET'];

                //写入应用配置文件
                if(!IS_WRITE){
                    $this->error('由于您的环境不可写，请先修改相关权限！');
                }else{
                    if(file_put_contents('./data/auto.php', $config_set)){
                        chmod('./data/auto.php',0777);
                    }
                }
            }
        }
        $data = $admin_config->handleConfig();

        $admin_config->title('计划任务列表配置')
            ->data($data)
            ->keyTextArea('QUEUE_SET','','计划任务列表','每一行一条计划任务url')
            ->group('计划任务列表配置', 'QUEUE_SET');
        $admin_config->buttonSubmit('', '保存')->display();
    }
}