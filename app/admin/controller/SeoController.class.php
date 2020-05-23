<?php
namespace admin\controller;

use admin\builder\AdminListBuilder;
use admin\builder\AdminConfigBuilder;
use admin\builder\AdminSortBuilder;

class SeoController extends AdminController
{
    public function index($page = 1, $r = 20)
    {
        //读取规则列表
        $map = array('status' => array('EGT', 0));
        $model = M('SeoRule');
        $ruleList = $model->where($map)->page($page, $r)->order('sort asc')->select();
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();
        $builder->title('SEO规则配置')
            ->setStatusUrl(U('setRuleStatus'))
            ->buttonNew(U('editRule'))
            ->buttonEnable()
            ->buttonDisable()
            ->button('删除', array('class' => 'layui-btn layui-btn-xs fbutton ajax-post', 'url' => U('doClear', array()), 'target-form' => 'ids'))
            ->buttonSort(U('sortRule'))
            ->keyId()->keyTitle()->keyText('app', '模块')->keyText('controller', '控制器')->keyText('action', '方法')
            ->keyText('seo_title', 'SEO标题')->keyText('seo_keywords', 'SEO关键字')->keyText('seo_description', 'SEO描述')
            ->keyStatus()->keyDoActionEdit('editRule?id=###')
            ->data($ruleList)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function setRuleStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        $builder->doSetStatus('SeoRule', $ids, $status);
    }

    public function doClear($ids){
        $builder = new AdminListBuilder();
        $builder->doDeleteTrue('SeoRule', $ids);
    }

    public function sortRule()
    {
        //读取规则列表
        $list = M('SeoRule')->where(array('status' => array('EGT', 0)))->order('sort asc')->select();

        //显示页面
        $builder = new AdminSortBuilder();
        $builder->title('排序SEO规则')
            ->data($list)
            ->buttonSubmit(U('doSortRule'))
            ->buttonBack()
            ->display();
    }

    public function doSortRule($ids)
    {
        $builder = new AdminSortBuilder();
        $builder->doSort('SeoRule', $ids);
    }

    public function editRule($id = null)
    {
        //判断是否为编辑模式
        $isEdit = $id ? true : false;

        //读取规则内容
        if ($isEdit) {
            $rule = M('SeoRule')->where(array('id' => $id))->find();
        } else {
            $rule = array('status' => 1);
        }

        //
        $rule['action2'] = $rule['action'];

        //显示页面
        $builder = new AdminConfigBuilder();


        $modules = D('Module')->getAll();


        $app=array(''=>'-所有模块-');
        foreach ($modules as $m) {
            if($m['is_setup']){
                $app[$m['name']]=$m['alias'];
            }
        }

        $builder->title($isEdit ? '编辑规则' : '添加规则')
            ->keyId()->keyText('title', '名称', '规则名称，方便记忆')->keySelect('app', '模块名称', '不填表示所有模块',$app)->keyText('controller', '控制器', '不填表示所有控制器')
            ->keyText('action2', '方法', '不填表示所有方法')->keyText('seo_title', 'SEO标题', '不填表示使用下一条规则，支持变量')
            ->keyText('seo_keywords', 'SEO关键字', '不填表示使用下一条规则，支持变量')->keyTextArea('seo_description', 'SEO描述', '不填表示使用下一条规则，支持变量')
            ->keyStatus()
            ->data($rule)
            ->buttonSubmit(U('doEditRule'))->buttonBack()
            ->display();
    }

    public function doEditRule($id = null, $title, $app, $controller, $action2, $seo_title, $seo_keywords, $seo_description, $status)
    {
        //判断是否为编辑模式
        $isEdit = $id ? true : false;


        //写入数据库
        $data = array('title' => $title, 'app' => $app, 'controller' => $controller, 'action' => $action2, 'seo_title' => $seo_title, 'seo_keywords' => $seo_keywords, 'seo_description' => $seo_description, 'status' => $status);
        $model = M('SeoRule');
        if ($isEdit) {
            $result = $model->where(array('id' => $id))->save($data);
        } else {
            $result = $model->add($data);
        }

        clean_all_cache();
        //如果失败的话，显示失败消息
        if (!$result) {
            $this->error($isEdit ? '编辑失败' : '创建失败');
        }

        //显示成功信息，并返回规则列表
        $this->success($isEdit ? '编辑成功' : '创建成功', U('index'));
    }
}