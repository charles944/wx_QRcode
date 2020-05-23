<?php
namespace qrcode\controller;
use think\Controller;

class IndexController extends Controller{

    protected $qrcodeModel;
    protected $qrcodechildModel;
    protected $qrcodelogModel;
    protected $qrcodechildlogModel;

    function _initialize()
    {
        $this->qrcodeModel = D('Qrcode/Qrcode');
        $this->qrcodechildModel = D('Qrcode/QrcodeChild');
        $this->qrcodelogModel = D('Qrcode/QrcodeLog');
        $this->qrcodechildlogModel = D('Qrcode/QrcodeChildLog');
    }

    //20180719
	public function domain(){
		$this->_needLogin();
		$uid=is_login();
		if(IS_POST){
			$aId = I('post.id',0,'intval');
			if($aId){
				$data['id']= $aId;
			}
			$data['in_domain'] = I('post.in_domain','','text');
			$data['out_domain'] = I('post.out_domain','','text');
			//if(!mb_strlen($data['in_domain'],'utf-8')){
			//	$this->error('入口域名不能为空！');
			//}
			$change_flag=false;
			if($aId){
				$datas = D('Qrcode/QrcodeDomain')->find($aId);
				if(empty($datas)||$datas['uid']!=$uid){
					$this->error('不是你的数据无法编辑');
				}
				if($datas['in_domain']!=$data['in_domain']){
					$change_flag=true;
				}
				$res = D('Qrcode/QrcodeDomain')->save($data);
			}else{
				$data['uid']=$uid;
				$data['create_time'] = TIME();
				$res = D('Qrcode/QrcodeDomain')->add($data);
				$change_flag=true;
			}
			$title = $aId ? "编辑" : "新增";
			$short_url_on = modC('SHORT_URL_ON','','QRCODE');
			if($res){
				if($change_flag==true){
					//更新用户的二维码解析后台的地址
					$qrcode_list=$this->qrcodeModel->where(array("uid"=>$uid))->select();
					foreach($qrcode_list as $k=>$v){
						if($data['in_domain']){
							$in_domain_arr=explode("\r\n",$data['in_domain']);
							foreach($in_domain_arr as &$val){
								if(!empty(trim($val))){
									//$tmp_url = "http://".$val.'/qrcode/index/query/id/'.$v['id'];
                                    $tmp_url = "http://".$val.U('qrcode/index/query', array('id'=>$v['id']));
									if(empty($short_url_on)){
										$short_tmp_url = $tmp_url;
									}elseif(intval($short_url_on) == 1){
										$short_tmp_url = api('Request/fun',array('url'=>'http://sa.sogou.com/gettiny?url='.$tmp_url,'method'=>'get'));
									}elseif(intval($short_url_on) == 2){
                                        $short_tmp_url = api('SinaUrl/getShort','url='.$tmp_url);
                                    }
									$filename = qrcode($short_tmp_url, $v['id'].'_'.md5($val).'.png',false, false);
			                        unset($tmp_url);unset($short_tmp_url);
								}
							}
						}
					}
				}
				$this->success($title.'成功',U('qrcode/index/domain'));
			}else{
				$this->error($title.'数据未修改'.D('Qrcode/QrcodeDomain')->getError());
			}
		}else{
			$data=D('Qrcode/QrcodeDomain')->where("uid=".$uid)->find();
			$data=$data?$data:array();
			$this->assign("data",$data);
			$this->display();
		}
	}

    public function index($page=1,$r=20)
    {
    	$this->_needLogin();
    	$uid = is_login();
        $map['status']=1;
		$map['uid']=$uid;
        list($list,$totalCount) = $this->qrcodeModel->getListByPage($map,$page,'sort desc,id desc','*',$r);
        $min = strtotime(date('Y-m-d 00:00:00'));
        $max = strtotime(date('Y-m-d 23:59:59'));


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
			$map2['qr_pid'] = $val['id'];
			$map2['uid']=is_login();
			$tmp_child = $this->qrcodechildModel->field('id')->where($map2)->select();
            $tmp_child_ids=array_column($tmp_child,'id');

            $val['qrcode_child'] = count($tmp_child_ids);
			$val['qr_domain'] = md5($the_domain);
            $where['create_time'] = array('between', array($min, $max));
            $where['qr_id'] = $val['id'];
            $tmp_data = $this->qrcodelogModel->field('count(*) as total,ip')->where($where)->group('ip')->select();
            $val['today_people'] = count($tmp_data);

            $where2['create_time'] = array('between', array($min, $max));
            $where2['qr_cid'] = array('in',$tmp_child_ids);
            $val['scan_child_count'] = $this->qrcodechildlogModel->where($where2)->count();
		}
        $this->assign('list', $list);
		$this->assign('r',$r);
        $this->assign('totalCount',$totalCount);
        $this->display();
    }
	
	public function edit()
    {
    	$this->_needLogin();
        if(IS_POST){
            $this->_doEdit();
        }else{
            $aId = I('id',0,'intval');
            if($aId){
                $data = $this->qrcodeModel->getMyData($aId);
				if(empty($data)){
					$this->error('无此数据');
				}
                $this->assign('data',$data);
				$this->setTitle('编辑{$data.title|text}');
				$this->setKeywords('{$data.title|text}');
            }else{
				$this->setTitle('新增活码');
				$this->setKeywords('新增活码');
            }
            $title = $aId ? "编辑活码" : "新增活码";
            $this->assign('title',$title);
        }
        $this->display();
    }
	
	private function _doEdit()
    {
    	$this->_needLogin();
    	$uid = is_login();
        $aId = I('post.id',0,'intval');
        if($aId){
            $data['id']= $aId;
        }
        $data['title'] = I('post.title','','text');
        $data['sort'] = I('post.sort',0,'intval');
		$data['max_scan'] = I('post.max_scan','','text');
		$data['remark'] = I('post.remark','','text');
		$data['scan_mode'] = I('post.scan_mode','','text');
		$data['type_mode'] = I('post.type_mode','','text');
        $data['view_mode'] = I('post.view_mode','','text');
        $data['view_limit_mode'] = I('post.view_limit_mode','','text');
		$data['update_time'] = TIME();

        if(!mb_strlen($data['title'],'utf-8')){
            $data['title'] = '活码'.TIME();
        }
        if(empty($data['max_scan'])){
        	$data['max_scan'] = 100;
        }
		if(empty($data['scan_mode'])){
        	$data['scan_mode'] = 1;
        }
        if(empty($data['type_mode'])){
        	$this->error('请选择一种活码类型');
        }
        if(empty($data['view_mode'])){
        	$this->error('请选择一种显示模式');
        }
		
        if(empty($data['view_limit_mode'])){
        	$this->error('请选择一种显示限制模式');
        }
		if($aId){
			$datas = $this->qrcodeModel->getMyData($aId);
			if(empty($datas)){
				$this->error('不是你的数据无法编辑');
			}
			$res = $this->qrcodeModel->save($data);
		}else{
			$data['uid'] = $uid;
			$data['status'] = 1;
			$data['create_time'] = TIME();
			$res = $this->qrcodeModel->add($data);
			
            $default_in_domain = modC('IN_DOMAIN','','QRCODE');
            $short_url_on = modC('SHORT_URL_ON','','QRCODE');
            $domain=D('Qrcode/QrcodeDomain')->where("uid=".$uid)->find();
            if($domain['in_domain']){
            	$in_domain_arr=explode("\r\n",$domain['in_domain']);
				foreach($in_domain_arr as &$val){
					if(!empty(trim($val))){
						//$tmp_url = "http://".$val.'/qrcode/index/query/id/'.$res;
                        $tmp_url = "http://".$val.U('qrcode/index/query', array('id'=>$res));
						if(empty($short_url_on)){
							$short_tmp_url = $tmp_url;
						}elseif(intval($short_url_on) == 1){
							$short_tmp_url = api('Request/fun',array('url'=>'http://sa.sogou.com/gettiny?url='.$tmp_url,'method'=>'get'));
						}elseif(intval($short_url_on) == 2){
                            $short_tmp_url = api('SinaUrl/getShort','url='.$tmp_url);
                        }
						$filename = qrcode($short_tmp_url, $res.'_'.md5($val).'.png',false, false);
                        unset($tmp_url);unset($short_tmp_url);
					}
				}
              	//$short_tmp_url = api('SinaUrl/getShort','url='.$tmp_url);
            }else{
                if(!empty($default_in_domain)){

                	$in_domain_arr=explode("\r\n",$default_in_domain);
					foreach($in_domain_arr as &$val){
						if(!empty(trim($val))){
							//$tmp_url = "http://".$val.'/qrcode/index/query/id/'.$res;
                            $tmp_url = "http://".$val.U('qrcode/index/query', array('id'=>$res));
							if(empty($short_url_on)){
								$short_tmp_url = $tmp_url;
							}elseif(intval($short_url_on) == 1){
								$short_tmp_url = api('Request/fun',array('url'=>'http://sa.sogou.com/gettiny?url='.$tmp_url,'method'=>'get'));
							}elseif(intval($short_url_on) == 2){
                                $short_tmp_url = api('SinaUrl/getShort','url='.$tmp_url);
                            }
							$filename = qrcode($short_tmp_url, $res.'_'.md5($val).'.png',false, false);
	                        unset($tmp_url);unset($short_tmp_url);
						}
					}
                }else{
                   	//$tmp_url = SITE_URL.'/qrcode/index/query/id/'.$res;
                    $tmp_url = SITE_URL.U('qrcode/index/query', array('id'=>$res));
                   	if(empty($short_url_on)){
						$short_tmp_url = $tmp_url;
					}elseif(intval($short_url_on) == 1){
                   		$short_tmp_url = api('Request/fun',array('url'=>'http://sa.sogou.com/gettiny?url='.$tmp_url,'method'=>'get'));
                   	}elseif(intval($short_url_on) == 2){
                        $short_tmp_url = api('SinaUrl/getShort','url='.$tmp_url);
                    }
                   	$filename = qrcode($short_tmp_url, $res.'_'.md5(SITE_URL).'.png',false, false);
                }
            }
		}
        $title = $aId ? "编辑" : "新增";
        if($res){
            $this->success($title.'成功',U('qrcode/index/index'));
        }else{
            $this->error($title.'数据未修改'.$this->qrcodeModel->getError());
        }
    }

    public function child($page=1,$r=20,$id = 0)
    {
        $this->_needLogin();
        if(empty($id)){
			$this->error('参数错误');
		}
		$datas = $this->qrcodeModel->getMyData($id);
		if(empty($datas)){
			$this->error('无此数据');
		}
		$map['qr_pid'] = $id;
		$map['uid'] = get_uid();
        list($list,$totalCount) = $this->qrcodechildModel->getListByPage($map,$page,'sort desc,id desc','*',$r);
        foreach($list as &$val){
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
                    $val['mod'] = '自定义';
                    break;
                case "4":
                    $val['mod'] = '客服码';
                    break;
                case "5":
                    $val['mod'] = '机型码';
                    break;
                default:
                	$val['mod'] = '二维码';
                	break;
            }
        }

        $this->assign('list', $list);
		$this->assign('pdata', $datas);
		$this->assign('r',$r);
        $this->assign('totalCount',$totalCount);
		$this->setTitle('子码列表');
		$this->setKeywords('子码列表');
        $this->setDescription('子码列表');
        $this->display();
    }
	
	public function editchild()
    {
    	$this->_needLogin();
        if(IS_POST){
            $this->_doEditchild();
        }else{
            $aId = I('id', 0,'intval');
			$pid = I('pid', 0, 'intval');
            if($aId){
                $data = $this->qrcodechildModel->getMyData($aId);
				if(empty($data)){
					$this->error('无此数据');
				}
				if($data['status'] != 1){
					$this->error('当前子码因违规已被禁用或删除，无法编辑！');
				}
				$pdata = $this->qrcodeModel->getMyData($data['qr_pid']);
				if(empty($pdata)){
					$this->error('无此数据');
				}
                $this->assign('data',$data);
                $this->assign('pdata', $pdata);
				$this->setTitle('编辑{$data.title|text}');
				$this->setKeywords('{$data.title|text}');
            }else{
				if(empty($pid)){
					$this->error('无此父类数据');
				}
				$pdata = $this->qrcodeModel->getMyData($pid);
				if(empty($pdata)){
					$this->error('无此数据');
				}
				$this->assign('pdata', $pdata);
				$this->setTitle('新增子码');
				$this->setKeywords('新增子码');
            }
            $title = $aId ? "编辑子码" : "新增子码";
            $this->assign('title',$title);
        }
        $this->display();
    }
	
	private function _doEditchild()
    {
    	$this->_needLogin();
        $aId = I('post.id',0,'intval');
		$pId = I('post.pid',0,'intval');
        if($aId){
            $data['id']= $aId;
        }
		if(empty($pId)){
			$this->error('参数错误');
		}
        $data['title'] = I('post.title','','text');
        $data['sort'] = I('post.sort',0,'intval');
		$data['qr_pid'] = $pId;
		$data['qr_province'] = I('post.province','','text');
		$data['qr_city'] = I('post.city','','text');
		$data['qr_district'] = I('post.district','','text');
		$data['qr_img'] = I('post.qr_img','','text');
        $data['qr_link'] = I('post.qr_link','','text');
		$data['qr_text'] = I('post.qr_text','','html');
		$data['qr_type'] = I('post.qr_type','','text');
		$data['qr_mob_type'] = I('post.qr_mob_type','','text');
		//$data['remark'] = I('post.remark','','html');
		$data['update_time'] = TIME();
		
        if(!mb_strlen($data['title'],'utf-8')){
            $data['title'] = '子码'.TIME();
        }
        if(empty($data['qr_type'])){
        	$this->error('请选择显示类型');
        }
		if($aId){
			$datas = $this->qrcodechildModel->getMyData($aId);
			if(empty($datas)){
				$this->error('不是你的数据无法编辑');
			}
			if($datas['status'] != 1){
				$this->error('当前子码因违规已被禁用或删除，无法编辑！');
			}
			$data['scan_count'] = I('post.scan_count','','intval');
			$res = $this->qrcodechildModel->save($data);
		}else{
			$data['scan_count'] = 0;
			$data['uid'] = is_login();
			$data['status'] = 1;
			$data['create_time'] = TIME();
			$res = $this->qrcodechildModel->add($data);
		}
        $title = $aId ? "编辑" : "新增";
        if($res){
            $this->success($title.'成功',U('qrcode/index/child').'?id='.$pId);
        }else{
            $this->error($title.'数据未修改'.$this->qrcodeModel->getError());
        }
    }

    public function editmultichild()
    {
     	$this->_needLogin();
        if(IS_POST){
            $this->_doEditMultichild();
        }else{
            $pid = I('pid', 0, 'intval');
            if(empty($pid)){
              $this->error('无此父类数据');
            }
            $pdata = $this->qrcodeModel->getMyData($pid);
            if(empty($pdata)){
              $this->error('无此数据');
            }
            $this->assign('pdata', $pdata);
            $this->setTitle('批量新增子码');
            $this->setKeywords('批量新增子码');
            $title = "批量新增子码";
            $this->assign('title',$title);
        }
        $this->display();
    }
  
    private function _doEditMultichild()
    {
        $pId = I('post.pid',0,'intval');
        $uid = is_login();
        if(empty($pId)){
            $this->error('参数错误');
        }
      	$datas = $this->qrcodeModel->getMyData($pId);
        if(empty($datas)){
            $this->error('无此活码数据');
        }
        $data['qr_pid'] = $pId;
        $data['qr_province'] = I('post.province','','text');
        $data['qr_city'] = I('post.city','','text');
        $data['qr_district'] = I('post.district','','text');
        $qr_img = I('post.qr_img','','text');
        $data['qr_link'] = I('post.qr_link','','text');
        $data['qr_text'] = I('post.qr_text','','html');
        $data['qr_type'] = I('post.qr_type','','text');
        $data['qr_mob_type'] = I('post.qr_mob_type','','text');
        $data['remark'] = I('post.remark','','html');
        $data['update_time'] = TIME();
        $data['qr_read_mode']= I('post.qr_read_mode','','intval');

        if(empty($qr_img)){
            $this->error('请先上传相应图片！');
        }
        if(empty($data['qr_type'])){
            $this->error('请选择显示类型');
        }
        $new_arr_img = explode(',', $qr_img);
        $count = $uncount = 0;
        foreach($new_arr_img as $key=>$val){
            if($data['qr_read_mode'] == 1){
                $filename_s = get_cover($val, 'path');
                $filename_s = 'http://'.I("server.HTTP_HOST").$filename_s;
                $qrcode_url = api('Request/fun',array('url'=>'http://www.qkecloud.com/api/qrcoderead/url_read/?urls='.$filename_s ,'method'=>'get'));
                if(!empty($qrcode_url)){
                    $result_qrcode_url = json_decode($qrcode_url, true);
                }
                if(!empty($result_qrcode_url['result'])){
                    $filename = qrcode($result_qrcode_url['result'], $pId.'_'.TIME().'.png',false, false);
                    $data['qr_img'] = $filename;
                }else{
                  	$data['qr_img'] = $val;
                }
            }else{
            	$data['qr_img'] = $val;
            }
            $data['scan_count'] = 0;
            $data['uid'] = is_login();
            $data['status'] = 1;
            $data['sort'] = $key+1;
            $data['create_time'] = TIME();
            $data['title'] = '子码'.date('YmdHis', TIME()).rand(0,999);
            $res = $this->qrcodechildModel->add($data);
            if($res){
               $count = $count + 1;
            }else{
               $uncount = $uncount + 1;
            }
        }
        $title = "批量新增";
        if($count > 0){
            $this->success($title.'成功'.$count.'个子码，失败'.$uncount.'个',U('qrcode/index/child').'?id='.$pId);
        }else{
            $this->error($title.'失败，数据未修改'.$this->qrcodeModel->getError());
        }
    }
	
	public function qr($id){
		$this->_needLogin();
		if(empty($id)){
			$this->error('参数错误');
		}
		$uid = is_login();
		$domain = I("server.HTTP_HOST");

      	$default_in_domain = modC('IN_DOMAIN','','QRCODE');
      	$short_url_on = modC('SHORT_URL_ON','','QRCODE');
        $domain=D('Qrcode/QrcodeDomain')->where("uid=".$uid)->find();
        if($domain['in_domain']){

        	$in_domain_arr=explode("\r\n",$domain['in_domain']);
			foreach($in_domain_arr as $key=>$val){
				if(!empty(trim($val))){
					//$tmp_url = "http://".$val.'/qrcode/index/query/id/'.$id;
                    $tmp_url = "http://".$val.U('qrcode/index/query', array('id'=>$id));

					if(empty($short_url_on)){
						$short_tmp_url = $tmp_url;
					}elseif(intval($short_url_on) == 1){
						$short_tmp_url = api('Request/fun',array('url'=>'http://sa.sogou.com/gettiny?url='.$tmp_url,'method'=>'get'));
					}elseif(intval($short_url_on) == 2){
                        $short_tmp_url = api('SinaUrl/getShort','url='.$tmp_url);
                    }
					$filename[$key] = SITE_URL.'/uploads/picture/QRcode/'.$id.'_'.md5($val).'.png';
					$url[$key] = $short_tmp_url;
					//读取原先的二维码判断和当前配置的链接是否一致，如果不一致，重新生成二维码
                    $qrcode_url = api('Request/fun','url=http://www.qkecloud.com/api/qrcoderead/url_read/?urls='.$filename[$key].'&method=get');
			        if(!empty($qrcode_url)){
			          	$result_qrcode_url = json_decode($qrcode_url, true);
			        }
			        if(!empty($result_qrcode_url['result'])){
			          	if($result_qrcode_url['result'] != $short_tmp_url){
			              	$filename_tmp = qrcode($short_tmp_url, $id.'_'.md5($val).'.png',false, false);
			            }
			        }
			        if(file_exists('uploads/picture/QRcode/'.$id.'_'.md5($val).'.png')){

					}else{
						$filename_tmp = qrcode($short_tmp_url, $id.'_'.md5($val).'.png',false, false);
					}
			        unset($tmp_url);unset($short_tmp_url);
				}
			}
        }else{
			if(!empty($default_in_domain)){

				$in_domain_arr=explode("\r\n",$default_in_domain);
				foreach($in_domain_arr as $key=>$val){
					if(!empty(trim($val))){
						//$tmp_url = "http://".$val.'/qrcode/index/query/id/'.$id;
                        $tmp_url = "http://".$val.U('qrcode/index/query', array('id'=>$id));

						if(empty($short_url_on)){
							$short_tmp_url = $tmp_url;
						}elseif(intval($short_url_on) == 1){
							$short_tmp_url = api('Request/fun',array('url'=>'http://sa.sogou.com/gettiny?url='.$tmp_url,'method'=>'get'));
						}elseif(intval($short_url_on) == 2){
                            $short_tmp_url = api('SinaUrl/getShort','url='.$tmp_url);
                        }
						$filename[$key] = SITE_URL.'/uploads/picture/QRcode/'.$id.'_'.md5($val).'.png';
						$url[$key] = $short_tmp_url;

						//读取原先的二维码判断和当前配置的链接是否一致，如果不一致，重新生成二维码
			            $qrcode_url = api('Request/fun','url=http://www.qkecloud.com/api/qrcoderead/url_read/?urls='.$filename[$key].'&method=get');
				        if(!empty($qrcode_url)){
				          	$result_qrcode_url = json_decode($qrcode_url, true);
				        }
				        if(!empty($result_qrcode_url['result'])){
				          	if($result_qrcode_url['result'] != $short_tmp_url){
				              	$filename_tmp = qrcode($short_tmp_url, $id.'_'.md5($val).'.png',false, false);
				            }
				        }
				        if(file_exists('uploads/picture/QRcode/'.$id.'_'.md5($val).'.png')){

						}else{
							$filename_tmp = qrcode($short_tmp_url, $id.'_'.md5($val).'.png',false, false);
						}
				        unset($tmp_url);unset($short_tmp_url);
					}
				}
			}else{
			    $filename_path = SITE_URL.'/uploads/picture/QRcode/'.$id.'_'.md5(SITE_URL).'.png';
				//$url = SITE_URL.'/qrcode/index/query/id/'.$id;
                $url_path = SITE_URL.$val.U('qrcode/index/query', array('id'=>$id));

			  //读取原先的二维码判断和当前配置的链接是否一致，如果不一致，重新生成二维码
			    $qrcode_url = api('Request/fun','url=http://www.qkecloud.com/api/qrcoderead/url_read/?urls='.$filename_path.'&method=get');
			    if(!empty($qrcode_url)){
			      	$result_qrcode_url = json_decode($qrcode_url, true);
			    }
			    if(!empty($result_qrcode_url['result'])){
			      	if($result_qrcode_url['result'] != $url_path){
			          	$filename_tmp = qrcode($url_path, $id.'_'.md5(SITE_URL).'.png',false, false);
			        }
			    }
			    if(file_exists('uploads/picture/QRcode/'.$id.'_'.md5(SITE_URL).'.png')){
			    	$filename[] = $filename_path;
			    	$url[] = $url_path;
				}else{
					$filename_tmp = qrcode($url_path, $id.'_'.md5(SITE_URL).'.png',false, false);
                    $filename[] = $filename_path;
					$url[] = $url_path;
				}
			}
        }
        
		/* if(file_exists($filename)){
			$filename = $filename;
		}else{
			$filename = qrcode(SITE_URL.'/qrcode/index/query/id/'.$id,$id.'.png',false, false);
			$filename = SITE_URL.$filename;
		} */
		$this->assign('filename', $filename);
		$this->assign('url' ,$url);
		$this->assign('id', $id);
		$this->display();
	}
	
	public function log($page=1,$r=20)
    {
		$cid = I('get.cid', '', 'intval');
		$id = I('get.id', '', 'intval');
		$t = i('get.t','','text');
		$this->_needLogin();
		if(!empty($cid)){
			$map['qr_cid'] = $cid;
			$data = $this->qrcodechildModel->getMyData($cid);
		}
		if(!empty($id)){
			$map['qr_id'] = $id;
			$data = $this->qrcodeModel->getMyData($id);
		}
		if(!empty($t)){
			$min = strtotime($t.' 00:00:00');
			$max = strtotime($t.' 23:59:59');
			$map['create_time'] = array('between', array($min, $max));
		}
        list($list,$totalCount) = $this->qrcodelogModel->getListByPage($map,$page,'id desc','*',$r);
		$this->assign('data', $data);
        $this->assign('list', $list);
		$this->assign('r',$r);
        $this->assign('totalCount',$totalCount);
        $this->display();
    }

    public function tjlog($page=1,$r=20)
    {
		$cid = I('get.cid', '', 'intval');
		$id = I('get.id', '', 'intval');
		$this->_needLogin();
		if(!empty($cid)){
			$map['qr_cid'] = $cid;
			$data = $this->qrcodechildModel->getMyData($cid);
			$list = $this->qrcodelogModel->where($map)->field('qr_cid,qr_id,count(*) as total, YEAR(from_unixtime(create_time,"%Y-%m-%d")) AS year , MONTH(from_unixtime(create_time,"%Y-%m-%d")) AS month ,DAY(from_unixtime(create_time,"%Y-%m-%d")) AS day')->group('YEAR(from_unixtime(create_time,"%Y-%m-%d")) desc , MONTH(from_unixtime(create_time,"%Y-%m-%d")) desc ,DAY(from_unixtime(create_time,"%Y-%m-%d")) desc')->select();
			$totalCount=$this->qrcodelogModel->where($map)->field('YEAR(from_unixtime(create_time,"%Y-%m-%d")) AS year , MONTH(from_unixtime(create_time,"%Y-%m-%d")) AS month ,DAY(from_unixtime(create_time,"%Y-%m-%d")) AS day')->group('YEAR(from_unixtime(create_time,"%Y-%m-%d")) desc , MONTH(from_unixtime(create_time,"%Y-%m-%d")) desc ,DAY(from_unixtime(create_time,"%Y-%m-%d")) desc')->select();
			$totalCount = count($totalCount);
		}else if(!empty($id)){
			$map['qr_id'] = $id;
			$data = $this->qrcodeModel->getMyData($id);
			$list = $this->qrcodelogModel->where($map)->field('qr_cid,qr_id,count(*) as total, YEAR(from_unixtime(create_time,"%Y-%m-%d")) AS year , MONTH(from_unixtime(create_time,"%Y-%m-%d")) AS month ,DAY(from_unixtime(create_time,"%Y-%m-%d")) AS day')->group('YEAR(from_unixtime(create_time,"%Y-%m-%d")) desc , MONTH(from_unixtime(create_time,"%Y-%m-%d")) desc ,DAY(from_unixtime(create_time,"%Y-%m-%d")) desc')->select();
			foreach($list as $key=>$val){
				$min = strtotime($val['year'].'-'.$val['month'].'-'.$val['day'].' 00:00:00');
				$max = strtotime($val['year'].'-'.$val['month'].'-'.$val['day'].' 23:59:59');
				$where['create_time'] = array('between', array($min, $max));
				$where['qr_id'] = $id;
				$list[$key]['sub'] = $this->qrcodelogModel->where($where)->field('qr_cid,qr_id,count(*) as total')->group('qr_cid')->select();
			}
			$totalCount=$this->qrcodelogModel->where($map)->field('YEAR(from_unixtime(create_time,"%Y-%m-%d")) AS year , MONTH(from_unixtime(create_time,"%Y-%m-%d")) AS month ,DAY(from_unixtime(create_time,"%Y-%m-%d")) AS day')->group('YEAR(from_unixtime(create_time,"%Y-%m-%d")) desc , MONTH(from_unixtime(create_time,"%Y-%m-%d")) desc ,DAY(from_unixtime(create_time,"%Y-%m-%d")) desc')->select();
			$totalCount = count($totalCount);
		}else{
			$list = $this->qrcodelogModel->where($map)->field('qr_cid,qr_id,count(*) as total, YEAR(from_unixtime(create_time,"%Y-%m-%d")) AS year , MONTH(from_unixtime(create_time,"%Y-%m-%d")) AS month ,DAY(from_unixtime(create_time,"%Y-%m-%d")) AS day')->group('YEAR(from_unixtime(create_time,"%Y-%m-%d")) desc , MONTH(from_unixtime(create_time,"%Y-%m-%d")) desc ,DAY(from_unixtime(create_time,"%Y-%m-%d")) desc')->select();
            foreach($list as $key=>$val){
                $min = strtotime($val['year'].'-'.$val['month'].'-'.$val['day'].' 00:00:00');
                $max = strtotime($val['year'].'-'.$val['month'].'-'.$val['day'].' 23:59:59');
                $where['create_time'] = array('between', array($min, $max));
                $where['qr_id'] = $id;
                $list[$key]['sub'] = $this->qrcodelogModel->where($where)->field('qr_cid,qr_id,count(*) as total')->group('qr_cid')->select();
            }
            $totalCount=$this->qrcodelogModel->where($map)->field('YEAR(from_unixtime(create_time,"%Y-%m-%d")) AS year , MONTH(from_unixtime(create_time,"%Y-%m-%d")) AS month ,DAY(from_unixtime(create_time,"%Y-%m-%d")) AS day')->group('YEAR(from_unixtime(create_time,"%Y-%m-%d")) desc , MONTH(from_unixtime(create_time,"%Y-%m-%d")) desc ,DAY(from_unixtime(create_time,"%Y-%m-%d")) desc')->select();
            $totalCount = count($totalCount);
		}

        //list($list,$totalCount) = $this->qrcodelogModel->getListByPage($map,$page,'id desc','*',$r);
		$this->assign('data', $data);
        $this->assign('list', $list);
        $this->assign('cid', $cid);
        $this->assign('id',$id);
		$this->assign('r',$r);
        $this->assign('totalCount',$totalCount);
        $this->display();
    }
	
	public function del($id){
		$this->_needLogin();
		if(empty($id)){
			$this->error('参数错误');
		}
		$data = $this->qrcodeModel->getMyData($id);
		if(empty($data)){
			$this->error('无此数据');
		}
		$res = $this->qrcodeModel->where('id='.$id)->delete();
		if($res){
			$this->success('删除成功','refresh');
		}else{
			$this->error('删除失败','refresh');
		}
	}
	
	public function delchild($id){
		$this->_needLogin();
		if(empty($id)){
			$this->error('参数错误');
		}
		$data = $this->qrcodechildModel->getMyData($id);
		if(empty($data)){
			$this->error('无此数据');
		}
		$res = $this->qrcodechildModel->where('id='.$id)->delete();
		if($res){
			$this->success('删除成功','refresh');
		}else{
			$this->error('删除失败','refresh');
		}
	}
	
	public function query($id){
		if(empty($id)){
			$this->error('参数错误');
		}
		$data = $this->qrcodeModel->getData($id);
		if(empty($data)){
			$this->error('无此数据');
		}
		//20180719s
		$in_domain=$_SERVER['HTTP_HOST'];
		$default_in_domain = modC('IN_DOMAIN','','QRCODE');
		$default_out_domain = modC('OUT_DOMAIN','','QRCODE');
      
		$domain=D('Qrcode/QrcodeDomain')->where("uid=".$data['uid']." and in_domain like '%".$in_domain."%'")->find();
		if($domain['out_domain']){
			$out_domain_arr=explode("\r\n",$domain['out_domain']);
			$count=count($out_domain_arr);
			$index=rand(0,$count-1);
			//$url="http://".$out_domain_arr[$index]."/qrcode/index/query/id/".$id;
            $url = "http://".$out_domain_arr[$index].U('qrcode/index/query', array('id'=>$id));
            S('toshow', md5($id));
			header("Location:".$url);
            exit;
		}else{
			if(!empty($default_in_domain) && !empty($default_out_domain) && in_array($in_domain,explode("\r\n",$default_in_domain))){
				$out_domain_arr=explode("\r\n",$default_out_domain);
				$count=count($out_domain_arr);
				$index=rand(0,$count-1);
				//$url="http://".$out_domain_arr[$index]."/qrcode/index/query/id/".$id;
                $url = "http://".$out_domain_arr[$index].U('qrcode/index/query', array('id'=>$id));
                S('toshow', md5($id));
				header("Location:".$url);
                exit;
			}
		}
		//新增访问落地域名限制，直接访问将打不开2019.5.13 朝兮夕兮
      	if(!empty($domain['in_domain'])){
            if(!in_array($in_domain,explode("\r\n",$domain['in_domain']))){
                if(empty(S('toshow'))){
                    $this->display('404');
                    exit;
                }else{
                  	S('toshow', null);
                }
            }
        }elseif(!empty($default_in_domain)){
          	if(!in_array($in_domain,explode("\r\n",$default_in_domain))){
                if(empty(S('toshow'))){
                    $this->display('404');
                    exit;
                }else{
                  	S('toshow', null);
                }
            }
        }
		if($data['type_mode'] != 2){
			//20181204 新增
			if($data['view_limit_mode'] == 2){//如果为同ip显示同一张
				$user_ip = get_client_ip(1);
				$has_scan_data = $this->qrcodelogModel->where(array('ip'=>$user_ip, 'qr_id'=>$data['id'],'scan_state'=>0))->order('id desc')->find();
				if(!empty($has_scan_data)){
					$map['id'] = $has_scan_data['qr_cid'];
				}
				$map['status'] = 1;
				if($data['scan_mode'] == 1){
					$map['scan_child_count'] = array('lt',$data['max_scan']); //条件已扫描次数小于 
				}else{
					$map['scan_count'] = array('lt',$data['max_scan']); //条件已扫描次数小于 
				}
				$map['qr_pid'] = $data['id'];
			}else{
				$map['qr_pid'] = $data['id'];
				if($data['scan_mode'] == 1){
					$map['scan_child_count'] = array('lt',$data['max_scan']);
				}else{
					$map['scan_count'] = array('lt',$data['max_scan']);
				}
				$map['status'] = 1;
			}

			//判断机型
			if($data['type_mode'] == 5){

			}

			if($data['view_mode'] == 1){//随机显示
                if(!empty($has_scan_data)){
                  	$datachild_one = $this->qrcodechildModel->where($map)->limit(1)->find();
                }else{
                	$datachild_one = $this->qrcodechildModel->where($map)->limit(1)->order('rand()')->find();
                }
            }else if($data['view_mode'] == 2){//逐条显示，大的序号先显示
              	if(!empty($has_scan_data)){
                  	$datachild_one = $this->qrcodechildModel->where($map)->limit(1)->find();
                }else{
                	$datachild_one = $this->qrcodechildModel->where($map)->limit(1)->order('sort desc')->find();
                }
            }

			$result = $datachild_one;
			if(!empty($result)){
				if(empty($has_scan_data)){//20181204 新增如果是同IP扫描不计次数
					$this->qrcodechildModel->where(array('id' => $result['id']))->setInc('scan_count');
					$this->qrcodeModel->where(array('id' => $result['qr_pid']))->setInc('scan_count');
				}
			}else{
				//如果同ip检测有数据，但又没有子码记录，则将此 同ip记录标记为不可用
                if(!empty($has_scan_data)){
                  	$this->qrcodelogModel->where(array('id'=>$has_scan_data['id']))->save(array('scan_state'=>1));
                }
				//判断客服循环码
				if($data['type_mode'] == 4){
					$this->qrcodechildModel->where(array('qr_pid' => $data['id']))->setField(array('scan_count'=>'0','scan_child_count'=>'0'));
					if($data['view_mode'] == 1){//随机显示
						$result = $this->qrcodechildModel->where($map)->limit(1)->order('rand()')->find();
					}else if($data['view_mode'] == 2){//逐条显示
						$result = $this->qrcodechildModel->where($map)->limit(1)->order('sort desc')->find();
					}
					$this->qrcodechildModel->where(array('id' => $result['id']))->setInc('scan_count');
                    $this->qrcodeModel->where(array('id' => $result['qr_pid']))->setInc('scan_count');
				}else{//如果不是客服循环码，全部循环完之后始终显示最后一条，排序最小的那一条
					$map2['qr_pid'] = $data['id'];
					$map2['status'] = 1;
					$result = $this->qrcodechildModel->where($map2)->limit(1)->order('sort asc')->find();
				}
			}
			$ipdata['ip'] = get_client_ip(1);
			$ipdata['ip_addr'] = ipfrom(long2ip($ipdata['ip']));
			$ipdata['scan_device'] = json_encode($this->getAgentInfo());
			$ipdata['qr_id'] = $result['qr_pid'];
			$ipdata['qr_cid'] = $result['id'];
			$ipdata['create_time'] = TIME();
			$this->qrcodelogModel->add($ipdata);
			$this->assign('data', $result);
		}

		$this->assign('pdata', $data);
		$this->display();
	}

	public function getqrbypro(){
		$province_id = I('post.province_id','','intval');
		$qr_id = I('post.pid','','intval');
		$city = I('post.city','','text');
		$province = I('post.province','','text');
		$district = I('post.district','','text');
		$addr = I('post.addr','','text');

		$return['state'] = 0;
		if(empty($qr_id)){
			$return['info'] = '参数错误';
			$this->ajaxReturn($return, 'JSONP');
		}
		$data = $this->qrcodeModel->getData($qr_id);
		if(empty($data)){
			$return['info'] = '无此数据';
			$this->ajaxReturn($return, 'JSONP');
		}
		if($data['type_mode'] == 2){
			if(!empty($province_id)){
				//默认区级
				$map['qr_district'] = $province_id;
				//20181204 新增
				if($data['view_limit_mode'] == 2){//如果为同ip显示同一张
					$user_ip = get_client_ip(1);
					$has_scan_data = $this->qrcodelogModel->where(array('ip'=>$user_ip, 'qr_id'=>$data['id']))->order('id desc')->find();
					if(!empty($has_scan_data)){
						$map['id'] = $has_scan_data['qr_cid'];
					}
					$map['status'] = 1;
					$map['qr_pid'] = $data['id'];
				}else{
					$map['qr_pid'] = $data['id'];
					if($data['scan_mode'] == 1){
						$map['scan_child_count'] = array('lt',$data['max_scan']);
					}else{
						$map['scan_count'] = array('lt',$data['max_scan']);
					}
					$map['status'] = 1;
				}
				if($data['view_mode'] == 1){
					$datachild_one = $this->qrcodechildModel->where($map)->limit(1)->order('rand()')->find();
				}else if($data['view_mode'] == 2){
					$datachild_one = $this->qrcodechildModel->where($map)->limit(1)->order('sort desc')->find();
				}
				//默认市级
				if(empty($datachild_one)){
					$up_city = D('district')->where(array('level'=>3,'id'=>$province_id))->find();
					if(!empty($up_city)){
						$map2['qr_city'] = $up_city['upid'];
						//20181204 新增
						if($data['view_limit_mode'] == 2){//如果为同ip显示同一张
							$user_ip = get_client_ip(1);
							$has_scan_data = $this->qrcodelogModel->where(array('ip'=>$user_ip, 'qr_id'=>$data['id']))->order('id desc')->find();
							if(!empty($has_scan_data)){
								$map2['id'] = $has_scan_data['qr_cid'];
							}
							$map2['status'] = 1;
							$map2['qr_pid'] = $data['id'];
						}else{
							$map2['qr_pid'] = $data['id'];
							if($data['scan_mode'] == 2){
								$map2['scan_count'] = array('lt',$data['max_scan']);
							}else{
								$map2['scan_child_count'] = array('lt',$data['max_scan']);
							}
							$map2['status'] = 1;
						}
						if($data['view_mode'] == 1){
							$datachild_one = $this->qrcodechildModel->where($map2)->limit(1)->order('rand()')->find();
						}else if($data['view_mode'] == 2){
							$datachild_one = $this->qrcodechildModel->where($map2)->limit(1)->order('sort desc')->find();
						}
					}
					if(empty($datachild_one)){
						$map3['qr_district'] = '';
						$map3['qr_city'] = '';
						$map3['qr_province'] = '';
						//20181204 新增
						if($data['view_limit_mode'] == 2){//如果为同ip显示同一张
							$user_ip = get_client_ip(1);
							$has_scan_data = $this->qrcodelogModel->where(array('ip'=>$user_ip, 'qr_id'=>$data['id']))->order('id desc')->find();
							if(!empty($has_scan_data)){
								$map3['id'] = $has_scan_data['qr_cid'];
							}
							$map3['status'] = 1;
							$map3['qr_pid'] = $data['id'];
						}else{
							$map3['qr_pid'] = $data['id'];
							if($data['scan_mode'] == 1){
								$map3['scan_child_count'] = array('lt',$data['max_scan']);
							}else{
								$map3['scan_count'] = array('lt',$data['max_scan']);
							}
							$map3['status'] = 1;
						}
						if($data['view_mode'] == 1){
							$datachild_one = $this->qrcodechildModel->where($map3)->limit(1)->order('rand()')->find();
						}else if($data['view_mode'] == 2){
							$datachild_one = $this->qrcodechildModel->where($map3)->limit(1)->order('sort desc')->find();
						}
					}
				}
			}else{
				$map['qr_district'] = '';
				$map['qr_city'] = '';
				$map['qr_province'] = '';
				//20181204 新增
				if($data['view_limit_mode'] == 2){//如果为同ip显示同一张
					$user_ip = get_client_ip(1);
					$has_scan_data = $this->qrcodelogModel->where(array('ip'=>$user_ip, 'qr_id'=>$data['id']))->order('id desc')->find();
					if(!empty($has_scan_data)){
						$map['id'] = $has_scan_data['qr_cid'];
					}
					$map['status'] = 1;
					$map['qr_pid'] = $data['id'];
				}else{
					$map['qr_pid'] = $data['id'];
					if($data['scan_mode'] == 1){
						$map['scan_child_count'] = array('lt',$data['max_scan']);
					}else{
						$map['scan_count'] = array('lt',$data['max_scan']);
					}
					$map['status'] = 1;
				}
				if($data['view_mode'] == 1){
					$datachild_one = $this->qrcodechildModel->where($map)->limit(1)->order('rand()')->find();
				}else if($data['view_mode'] == 2){
					$datachild_one = $this->qrcodechildModel->where($map)->limit(1)->order('sort desc')->find();
				}
			}
			
			$result = $datachild_one;
			if(!empty($result)){
				if(empty($has_scan_data)){//20181204 新增如果是同IP扫描不计次数
					$this->qrcodechildModel->where(array('id' => $result['id']))->setInc('scan_count');
					$this->qrcodeModel->where(array('id' => $result['qr_pid']))->setInc('scan_count');
				}
				$ipdata['ip'] = get_client_ip(1);
				$ipdata['ip_addr'] = $province.$city.$district.$addr;
				$ipdata['scan_device'] = json_encode($this->getAgentInfo());
				$ipdata['qr_id'] = $result['qr_pid'];
				$ipdata['qr_cid'] = $result['id'];
				$ipdata['create_time'] = TIME();
				$this->qrcodelogModel->add($ipdata);
                if(is_numeric($result['qr_img'])){
				    $result['qr_img_path'] = get_cover($result['qr_img'],'path');
                }else{
                    $result['qr_img_path'] = str_replace('./','/',$result['qr_img']);
                }
			}else{
				$return['info'] = '暂无数据';
				$this->ajaxReturn($return, 'JSONP');
			}

		}
		$return['state'] = 1;
		$return['result'] = $result;
		$this->ajaxReturn($return, 'JSONP');
	}
	
	private function getAgentInfo(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $brower = array(
            'MSIE' => 1,
            'Firefox' => 2,
            'QQBrowser' => 3,
            'QQ/' => 3,
            'UCBrowser' => 4,
            'MicroMessenger' => 9,
            'Edge' => 5,
            'Chrome' => 6,
            'Opera' => 7,
            'OPR' => 7,
            'Safari' => 8,
            'Trident/' => 1
        );
        $system = array(
            'Windows Phone' => 4,
            'Windows' => 1,
            'Android' => 2,
            'iPhone' => 3,
            'iPad' => 5
        );
        $browser_num = 0;//未知
        $system_num = 0;//未知
        foreach($brower as $bro => $val){
            if(stripos($agent, $bro) !== false){
                $browser_num = $bro;
                break;
            }
        }
        foreach($system as $sys => $val){
            if(stripos($agent, $sys) !== false){
                $system_num = $sys;
                break;
            }
        }
        return array('sys' => $system_num, 'bro' => $browser_num);
    }

    //上传子码图片长按识别数据
    public function upanalysis(){
        $cid = I('post.cid','','text');
        $pid = I('post.pid','','text');

        $return['state'] = 0;
        if(empty($cid)){
            $return['info'] = 'error';
            $this->ajaxReturn($return, 'JSONP');
        }
        $data = $this->qrcodechildModel->getData($cid);
        if(empty($data)){
            $return['info'] = 'none';
            $this->ajaxReturn($return, 'JSONP');
        }
        $up_tmp['create_time'] = TIME();
        $up_tmp['qr_cid'] = $cid;
        $res = $this->qrcodechildlogModel->add($up_tmp);
        if($res){
            $this->qrcodechildModel->where(array('id' => $cid))->setInc('scan_child_count');
            $return['state'] = 1;
            $return['result'] = 'success';
        }
        $this->ajaxReturn($return, 'JSONP');
    }

    private function _needLogin()
    {
        if(!is_login()){
            $this->error('请先登录！',U('home/member/login'));
        }
    }
}