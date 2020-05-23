<?php
namespace qrcode\controller;
use think\Controller;

class AjaxController extends Controller{

    protected $qrcodeModel;
    protected $qrcodechildModel;
    protected $qrcodelogModel;

    function _initialize()
    {
        $this->qrcodeModel = D('Qrcode/Qrcode');
        $this->qrcodechildModel = D('Qrcode/QrcodeChild');
        $this->qrcodelogModel = D('Qrcode/QrcodeLog');
    }

    public function gethmlist($offset=0, $limit=20)
    {
		$this->_needLogin();
        $map['status']=1;
        $uid = is_login();
		$map['uid']=$uid;
        $list = $this->qrcodeModel->where($map)->limit($offset, $limit)->order('sort desc')->select();
        $default_in_domain = modC('IN_DOMAIN','','QRCODE');
        $short_url_on = modC('SHORT_URL_ON','','QRCODE');
        $domain=D('Qrcode/QrcodeDomain')->where("uid=".$uid)->find();
        if(!empty($domain['in_domain'])){
            $in_domain_arr=explode("\r\n",$domain['in_domain']);
            $the_domain = $in_domain_arr[0];
        }else{
            if(!empty($default_in_domain)){
                $in_domain_arr=explode("\r\n",$default_in_domain);
                $the_domain = $in_domain_arr[0];
            }else{
                $the_domain = SITE_URL;
            }
        }
		foreach($list as &$val){
            $val['qr_domain'] = md5($the_domain);
			$map['qr_pid'] = $val['id'];
			$val['time'] = date('Y.m.d H:i',$val['create_time']);
            $val['times'] = TIME();
			$val['qrcode_child'] = $this->qrcodechildModel->where($map)->count();
            switch($val['type_mode']){
                case "1":
                    $val['mod'] = '群二维码';
                    break;
                case "2":
                    $val['mod'] = '城市分组码';
                    break;
                case "3":
                    $val['mod'] = '自定义分组码';
                    break;
                case "4":
                    $val['mod'] = '客服循环码';
                    break;
                case "5":
                    $val['mod'] = '机型码';
                    break;
            }
		}
		$list = $list ? $list : array();
		$this->ajaxReturn(array('status'=>1,'result'=>$list));
    }

    public function gethmchildlist($offset=0, $limit=20,$id = 0)
    {
        $this->_needLogin();
        if(empty($id)){
            $this->ajaxReturn(array('status'=>0,'message'=>'参数错误'));
        }
        $datas = $this->qrcodeModel->getData($id);
        if(empty($datas)){
           $this->ajaxReturn(array('status'=>0,'message'=>'无此数据'));
        }
        $map['qr_pid'] = $id;
        $map['uid'] = is_login();
        $list = $this->qrcodechildModel->where($map)->limit($offset, $limit)->order('sort desc')->select();
        foreach($list as &$val){
            $val['time'] = date('Y.m.d H:i',$val['create_time']);
            $val['img'] = get_cover($val['qr_img'],'path');
            $val['pid'] = $val['qr_pid'];
            switch($datas['type_mode']){
                case "1":
                    $val['mod'] = '二维码';
                    break;
                case "2":
                    $province = D('district')->where(array('level'=>1,'id'=>$val['qr_province']))->getField('name');
                    $city = D('district')->where(array('level'=>2,'id'=>$val['qr_city']))->getField('name');
                    $district = D('district')->where(array('level'=>3,'id'=>$val['qr_district']))->getField('name');
                    $val['mod'] = $province.'-'.$city.'-'.$district;
                    break;
                case "3":
                    $val['mod'] = '二维码';
                    break;
                case "4":
                    $val['mod'] = '二维码';
                    break;
                case "5":
                    $val['mod'] = '二维码';
                    break;
            }
        }
        $list = $list ? $list : array();
        $this->ajaxReturn(array('status'=>1,'result'=>$list));
    }

    public function gettjlog($offset=0, $limit=20)
    {
        $cid = I('get.cid', '', 'intval');
        $id = I('get.id', '', 'intval');
        $this->_needLogin();
        if(!empty($cid)){
            $map['qr_cid'] = $cid;
            $data = $this->qrcodechildModel->getData($cid);
            $list = $this->qrcodelogModel->where($map)->field('qr_cid,qr_id,count(*) as total, YEAR(from_unixtime(create_time,"%Y-%m-%d")) AS year , MONTH(from_unixtime(create_time,"%Y-%m-%d")) AS month ,DAY(from_unixtime(create_time,"%Y-%m-%d")) AS day')->group('YEAR(from_unixtime(create_time,"%Y-%m-%d")) desc , MONTH(from_unixtime(create_time,"%Y-%m-%d")) desc ,DAY(from_unixtime(create_time,"%Y-%m-%d")) desc')->select();
            //$totalCount=$this->qrcodelogModel->where($map)->field('YEAR(from_unixtime(create_time,"%Y-%m-%d")) AS year , MONTH(from_unixtime(create_time,"%Y-%m-%d")) AS month ,DAY(from_unixtime(create_time,"%Y-%m-%d")) AS day')->group('YEAR(from_unixtime(create_time,"%Y-%m-%d")) desc , MONTH(from_unixtime(create_time,"%Y-%m-%d")) desc ,DAY(from_unixtime(create_time,"%Y-%m-%d")) desc')->select();
            //$totalCount = count($totalCount);
        }else if(!empty($id)){
            $map['qr_id'] = $id;
            $data = $this->qrcodeModel->getData($id);
            $list = $this->qrcodelogModel->where($map)->field('qr_cid,qr_id,count(*) as total, YEAR(from_unixtime(create_time,"%Y-%m-%d")) AS year , MONTH(from_unixtime(create_time,"%Y-%m-%d")) AS month ,DAY(from_unixtime(create_time,"%Y-%m-%d")) AS day')->group('YEAR(from_unixtime(create_time,"%Y-%m-%d")) desc , MONTH(from_unixtime(create_time,"%Y-%m-%d")) desc ,DAY(from_unixtime(create_time,"%Y-%m-%d")) desc')->select();
            foreach($list as $key=>$val){
                $min = strtotime($val['year'].'-'.$val['month'].'-'.$val['day'].' 00:00:00');
                $max = strtotime($val['year'].'-'.$val['month'].'-'.$val['day'].' 23:59:59');
                $where['create_time'] = array('between', array($min, $max));
                $where['qr_id'] = $id;
                $list[$key]['sub'] = $this->qrcodelogModel->where($where)->field('qr_cid,qr_id,count(*) as total')->group('qr_cid')->select();
            }
            //$totalCount=$this->qrcodelogModel->where($map)->field('YEAR(from_unixtime(create_time,"%Y-%m-%d")) AS year , MONTH(from_unixtime(create_time,"%Y-%m-%d")) AS month ,DAY(from_unixtime(create_time,"%Y-%m-%d")) AS day')->group('YEAR(from_unixtime(create_time,"%Y-%m-%d")) desc , MONTH(from_unixtime(create_time,"%Y-%m-%d")) desc ,DAY(from_unixtime(create_time,"%Y-%m-%d")) desc')->select();
            //$totalCount = count($totalCount);
        }else{
            $data = array();
            $list = $this->qrcodelogModel->where($map)->field('qr_cid,qr_id,count(*) as total, YEAR(from_unixtime(create_time,"%Y-%m-%d")) AS year , MONTH(from_unixtime(create_time,"%Y-%m-%d")) AS month ,DAY(from_unixtime(create_time,"%Y-%m-%d")) AS day')->group('YEAR(from_unixtime(create_time,"%Y-%m-%d")) desc , MONTH(from_unixtime(create_time,"%Y-%m-%d")) desc ,DAY(from_unixtime(create_time,"%Y-%m-%d")) desc')->select();
            foreach($list as $key=>$val){
                $min = strtotime($val['year'].'-'.$val['month'].'-'.$val['day'].' 00:00:00');
                $max = strtotime($val['year'].'-'.$val['month'].'-'.$val['day'].' 23:59:59');
                $where['create_time'] = array('between', array($min, $max));
                $where['qr_id'] = $val['qr_id'];
                $list[$key]['sub'] = $this->qrcodelogModel->where($where)->field('qr_cid,qr_id,count(*) as total')->group('qr_cid')->select();
            }
            // $totalCount=$this->qrcodelogModel->where($map)->field('YEAR(from_unixtime(create_time,"%Y-%m-%d")) AS year , MONTH(from_unixtime(create_time,"%Y-%m-%d")) AS month ,DAY(from_unixtime(create_time,"%Y-%m-%d")) AS day')->group('YEAR(from_unixtime(create_time,"%Y-%m-%d")) desc , MONTH(from_unixtime(create_time,"%Y-%m-%d")) desc ,DAY(from_unixtime(create_time,"%Y-%m-%d")) desc')->select();
            // $totalCount = count($totalCount);
        }
        $list = $list ? $list : array();
        $this->ajaxReturn(array('status'=>1,'result'=>$list,'data'=>$data));
    }

    public function getlog($offset=0, $limit=20)
    {
        $cid = I('get.cid', '', 'intval');
        $id = I('get.id', '', 'intval');
        $t = i('get.t','','text');
        $this->_needLogin();
        if(!empty($cid)){
            $map['qr_cid'] = $cid;
            $data = $this->qrcodechildModel->getData($cid);
        }
        if(!empty($id)){
            $map['qr_id'] = $id;
            $data = $this->qrcodeModel->getData($id);
        }
        if(!empty($t)){
            $min = strtotime($t.' 00:00:00');
            $max = strtotime($t.' 23:59:59');
            $map['create_time'] = array('between', array($min, $max));
        }

        $list = $this->qrcodelogModel->where($map)->limit($offset, $limit)->order('id desc')->select();
        foreach($list as &$val){
            $val['ip'] = ip2long($val['ip']);
            $val['time'] = date('Y.m.d H:i:s', $val['create_time']);
            $tmp_dec = json_decode($val['scan_device'],true);
            $val['device'] = '扫描设备:'.$tmp_dec['sys'].' , 浏览器:'.$tmp_dec['bro'];
        }
        $list = $list ? $list : array();
        $this->ajaxReturn(array('status'=>1,'result'=>$list,'data'=>$data));
    }

    private function _needLogin()
    {
        if(!is_login()){
            $this->error('请先登录！');
        }
    }

    public function del($id){
        $this->_needLogin();
        if(empty($id)){
            $this->ajaxReturn(array('status'=>0,'info'=>'参数错误'));
        }
        $data = $this->qrcodeModel->getMyData($id);
        if(empty($data)){
            $this->ajaxReturn(array('status'=>0,'info'=>'无此数据'));
        }
        $res = $this->qrcodeModel->where('id='.$id)->delete();
        if($res){
            $this->ajaxReturn(array('status'=>1,'info'=>'删除成功'));
        }else{
            $this->ajaxReturn(array('status'=>0,'info'=>'删除失败'));
        }
    }

    public function delchild($id){
        $this->_needLogin();
        if(empty($id)){
            $this->ajaxReturn(array('status'=>0,'info'=>'参数错误'));
        }
        $data = $this->qrcodechildModel->getMyData($id);
        if(empty($data)){
            $this->ajaxReturn(array('status'=>0,'info'=>'无此数据'));
        }
        $res = $this->qrcodechildModel->where('id='.$id)->delete();
        if($res){
            $this->ajaxReturn(array('status'=>1,'info'=>'删除成功'));
        }else{
            $this->ajaxReturn(array('status'=>0,'info'=>'删除失败'));
        }
    }
}