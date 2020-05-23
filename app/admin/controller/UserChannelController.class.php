<?php
namespace admin\controller;

use admin\builder\AdminListBuilder;
use admin\builder\AdminConfigBuilder;
use admin\builder\AdminSortBuilder;
use admin\builder\AdminTreeListBuilder;

/**
 * 后台频道控制器
 * @author 乾坤网络有限公司
 */

class UserChannelController extends AdminController {

    /**
     * 频道列表
     * @author 乾坤网络有限公司
     */
    public function index(){
        $pid = i('get.pid', 0);
        /* 获取频道列表 */
        $map  = array('status' => array('gt', -1), 'pid'=>$pid);
        $list = M('UserChannel')->where($map)->order('sort asc,id asc')->select();

        $this->assign('list', $list);
        $this->assign('pid', $pid);
        $this->meta_title = '会员中心导航管理';
        $this->display();
    }

    /**
     * 添加频道
     * @author 乾坤网络有限公司
     */
    public function add(){
        if(IS_POST){
            $Channel = D('UserChannel');
            $data = $Channel->create();
            if($data){
                $id = $Channel->add();
                if($id){
                    $this->success('新增成功', U('index'));
                    //记录行为
                    action_log('update_channel', 'userchannel', $id, UID);
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Channel->getError());
            }
        } else {
            $pid = i('get.pid', 0);
            //获取父导航
            if(!empty($pid)){
                $parent = M('UserChannel')->where(array('id'=>$pid))->field('id,title')->find();
            }
            if(!empty($parent)){
            	$nav[$parent['id']] = $parent['title'];
            }
            $pnav=D('UserChannel')->where(array('pid'=>0))->select();
            foreach($pnav as &$val){
            	$nav[$val['id']] = $val['title'];
            }
			if(empty($nav)){
				$nav = array('0'=>'顶级分类');
			}
            $configBuilder = new AdminConfigBuilder();
            $configBuilder->title($parent ? '新增会员中心导航<small>[&nbsp;父导航：'.$parent['title'].'&nbsp;]</small>' : '新增会员中心导航');
            $configBuilder->keyId()
            ->keyText('title', '导航标题','用于显示的文字')
            ->keyText('url', '导航连接','用于调转的URL，支持带http://的URL或U函数参数格式')
            ->keySelect('pid', '父导航','', $nav)
            ->keyRadio('target', '新窗口打开','', array('0'=>'否','1'=>'是'))
            ->keyText('sort','排序','导航显示顺序')
            ->keyColor('color', '文字颜色','文字颜色，支持各类css表示方式')
            ->keyColor('band_color', '标志点颜色','右上角的标志点颜色，支持各类css表示方式')
            ->keyText('band_text', '标志点文字','右上角的标志点文字，不要太长，没有自动隐藏')
            ->buttonSubmit()
            ->buttonBack();

            $configBuilder->data();
            $configBuilder->display();
        }
    }

    /**
     * 编辑频道
     * @author 乾坤网络有限公司
     */
    public function edit($id = 0){
        if(IS_POST){
            $Channel = D('UserChannel');
            $data = $Channel->create();
            if($data){
                if($Channel->save()){
                    //记录行为
                    action_log('update_channel', 'userchannel', $data['id'], UID);
                    $this->success('编辑成功', U('index'));
                } else {
                    $this->error('编辑失败');
                }

            } else {
                $this->error($Channel->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('UserChannel')->find($id);

            if(false === $info){
                $this->error('获取配置信息错误');
            }

            $pid = i('get.pid', 0);

            //获取父导航
            if(!empty($pid)){
            	$parent = M('UserChannel')->where(array('id'=>$pid))->field('title')->find();
            }
            $nav[0] = '顶级菜单';
            if(!empty($parent)){
            	$nav[$parent['id']] = $parent['title'];
            }
            $pnav=D('UserChannel')->where(array('pid'=>0))->select();
            foreach($pnav as &$val){
            	$nav[$val['id']] = $val['title'];
            }

            $configBuilder = new AdminConfigBuilder();
            $configBuilder->title($parent ? '编辑会员中心导航<small>[&nbsp;父导航：'.$parent['title'].'&nbsp;]</small>' : '编辑会员中心导航');
            $configBuilder->keyId()
            ->keyText('title', '导航标题','用于显示的文字')
            ->keyText('url', '导航连接','用于调转的URL，支持带http://的URL或U函数参数格式')
            ->keySelect('pid', '父导航','', $nav)
            ->keySelect('target', '新窗口打开','', array('0'=>'否','1'=>'是'))
            ->keyText('sort','排序','导航显示顺序')
            ->keyColor('color', '文字颜色','文字颜色，支持各类css表示方式')
            ->keyColor('band_color', '标志点颜色','右上角的标志点颜色，支持各类css表示方式')
            ->keyText('band_text', '标志点文字','右上角的标志点文字，不要太长，没有自动隐藏')
            ->buttonSubmit()
            ->buttonBack();
            
            $configBuilder->data($info);
            $configBuilder->display();
        }
    }

    /**
     * 删除频道
     * @author 乾坤网络有限公司
     */
    public function del(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('UserChannel')->where($map)->delete()){
            //记录行为
            action_log('update_channel', 'userchannel', $id, UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 导航排序
     * @author 乾坤网络有限公司
     */
    public function sort(){
        if(IS_GET){
            $ids = I('get.ids');
            $pid = I('get.pid');

            //获取排序的数据
            $map = array('status'=>array('gt',-1));
            if(!empty($ids)){
                $map['id'] = array('in',$ids);
            }else{
                if($pid !== ''){
                    $map['pid'] = $pid;
                }
            }
            $list = M('UserChannel')->where($map)->field('id,title')->order('sort asc,id asc')->select();
            
            //显示页面
            $builder = new AdminSortBuilder();
            $builder->title('会员中心导航排序')
            ->data($list)
            ->buttonSubmit(U('sort'))->buttonBack()
            ->display();
        }elseif (IS_POST){
            $ids = I('post.ids');
            $ids = explode(',', $ids);
            foreach ($ids as $key=>$value){
                $res = M('UserChannel')->where(array('id'=>$value))->setField('sort', $key+1);
            }
            if($res !== false){
                $this->success('排序成功！');
            }else{
                $this->eorror('排序失败！');
            }
        }else{
            $this->error('非法请求！');
        }
    }
}
