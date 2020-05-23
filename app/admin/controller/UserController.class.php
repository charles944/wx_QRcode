<?php
namespace admin\controller;

use admin\builder\AdminConfigBuilder;
use admin\builder\AdminListBuilder;
use admin\builder\AdminSortBuilder;
use user\model\MemberModel;
use vendor\PHPExcel;

/**
 * 后台用户控制器
 * 
 */
class UserController extends AdminController
{

    /**
     * 用户管理首页
     *  
     */
    public function index($page = 1, $r = 50)
    {
        $nickname = I('nickname','','op_t');
        if(!empty($nickname)){
        	if (is_numeric($nickname)) {
        		$map['l.uid|l.nickname'] = array(intval($nickname), array('like', '%' . $nickname . '%'), '_multi' => true);
        	} else {
        		$map['l.nickname'] = array('like', '%' . (string)$nickname . '%');
        	}
        }
        $qq = I('qq','','op_t');
        if(!empty($qq)){
        		$map['l.qq'] = array('like', '%' . $qq . '%');
        }
        $mobile = I('mobile','','op_t');
        if(!empty($mobile)){
        	$map['r.mobile'] = array('like', '%' . $mobile . '%');
        }
        $email = I('email','','op_t');
        if(!empty($email)){
        	$map['r.email'] = array('like', '%' . $email . '%');
        }
        
        $aDead=I('dead',0,'intval');
        switch($aDead){
        	case 0:
        		$map['r.status'] = array('egt',-1);
        		break;
        	case 1:
        		$map['r.status'] = '1';
        		break;
        	case 2:
        		$map['r.status'] = '0';
        		break;
        	case 3:
        		$map['r.status'] = '3';
        		break;
        	case 4:
        		$map['r.status'] = '-1';
        		break;
        	default:
        		$map['r.status'] = '1';
        		break;
        }
        
        $vsort = I('vsort',0);
        $sort = I('st',0);
        if(!empty($vsort)){
            switch($vsort){
                case 'lower_num':
                    $order = 'r.'.$vsort.' '.$sort;
                    break;
                default:
                    $order = 'l.'.$vsort.' '.$sort;
                    break;
            }
        }else{
            $order = 'l.uid desc';
        }
		
		$aUserGroup = I('get.ugroup', 0, 'intval');
		if (!empty($aUserGroup)) {
            $uids = $this->getUids($aUserGroup);
            $map['uid'] = array('in', $uids);
        }
		
         $group = D('AuthGroup')->getGroups();
        $user_group = array(array('id' => 0, 'value' => '全部'));
        foreach ($group as $key => $v) {
            array_push($user_group, array('id' => $v['id'], 'value' => $v['title']));
        } 
		
        $prefix   = C('DB_PREFIX');
        
        $l_table  = $prefix.('member');
        $r_table  = $prefix.('ucenter_member');
        $list     = D() ->table( $l_table.' l' )->where( $map )->order($order)->join ( $r_table.' r ON l.uid = r.id' );
        $list = $this->lists($list,null,null,null,'l.*,r.*');
        foreach($list as &$val){
        	$val['email_ver'] = ($val['email_ver'] == 0) ? '未验证' : '已验证';
        	$val['mobile_ver'] = ($val['mobile_ver'] == 0) ? '未验证' : '已验证';
        	$val['mobile'] = ($val['mobile'] == NULL) ? '无' : $val['mobile'];
        	switch($val['status']){
        		case '0':
        			$val['status_txt'] = '冻结';
        			break;
        		case '1':
        			$val['status_txt'] = '正常';
        			break;
        		case '2':
        			$val['status_txt'] = '未审核';
        			break;
        		case '3':
        			$val['status_txt'] = '未激活';
        			break;
        		case '-1':
        			$val['status_txt'] = '已删除';
        			break;
        	}

			$low_gold = D('friend_rank')->where('from_uid = '.$val['uid'])->group('from_uid')->Sum('gold_coin');
            if($low_gold == null || $low_gold == ''){
				$low_gold = 0;
			}
			$pay_money = D('User/UserPayOffer')->where('uid='.$val['uid'])->group('uid')->Sum('pay_offer');
			if($pay_money == null || $pay_money == ''){
				$pay_money = 0;
			}
            if((double)$val['lower_gold_coin'] != (double)$low_gold || (double)$val['pay_money'] != (double)$pay_money){
                D('User/Member')->where('uid ='.$val['uid'])->save(array('lower_gold_coin'=>$low_gold,'pay_money'=>$pay_money));
            }
            $val['lower_gold_coin'] = $low_gold;
            $val['pay_money'] = $pay_money;
            unset($pay_money,$low_gold);
			
        }
        unset($val);
        $scoretype = D('User/Score')->getTypeList('status = 1');
        $this->assign('scoretypelist', $scoretype);
        $this->assign('_list', $list);
        $this->assign('r',$r);
        $this->assign('dead',$aDead);
		$this->assign('user_group',$user_group);
        $this->meta_title = '用户信息';
        $this->display();
    }
	
	private function getUids($user_group = 0)
    {
        $uids = array();
        if (!empty($user_group)) {
            $users = D('auth_group_access')->where(array('group_id' => $user_group))->field('uid')->select();
            $group_uids = getSubByKey($users, 'uid');
            if ($group_uids) {
                $uids = $group_uids;
            }
        }
        // if (!empty($role) && !empty($user_group)) {
        //     $uids = array_intersect($group_uids, $role_uids);
        // }
        return $uids;
    }
    
    public function mingxi($page  = 1, $r = 50)
    {
    	
    	$nickname = I('nickname','','text');
    	if(!empty($nickname)){
	    	if (is_numeric($nickname)) {
	        	$map['uid'] = array('eq',(int)$nickname);
	        } else {
	        	$map['nickname'] = array('like', '%' . (string)$nickname . '%');
	        }
    	}
    	
    	$ctime = I('create_time','','text');
    	$etime = I('end_time','','text');
    	if(!empty($ctime)){
    		$map['create_time'] = array(array('egt',  $ctime), array('elt',  $etime ), 'AND');
    		
    	}
    	$aTitle=I('remark','','op_t');
    	if($aTitle){
    		$map['remark'] = array('like','%'.$aTitle.'%');
    	}
    	$aDead=I('dead',0,'intval');
    	switch($aDead){
    		case 0:
    			$map['status'] = '1';
    			break;
    		case 1:
    			$map['status'] = '0';
    			break;
    		case 2:
    			$map['status'] = '-1';
    			break;
    		default:
    			$map['status'] = '1';
    			break;
    	}
    	list($list,$totalCount)=$this->getListByPage('UserScoreLog',$map,$page,'create_time desc','*',$r);

    	$roption = array(array('id'=>50,'value'=>'显示50行'),array('id'=>100,'value'=>'显示100行'),array('id'=>200,'value'=>'显示200行'));

    	// 显示页面
    	$builder = new AdminListBuilder();
    	$builder->meta_title = '会员收入明细列表';
    	
    	//foreach ($list as &$val) {
    	//}
    	$score_option=D('User/Score')->getTypeList(array('status = 1'));
    	
    	$builder->title('会员收入明细列表');
    	if($aDead == 2 || $aDead == 1 ){
    		$builder->buttonDelete(U('User/delmingxi'),'批量彻底删除');
    		$builder->buttonRestore(U('User/setmingxistatus'),'批量还原');
    	}else{
    		$builder->buttonSetStatus(U('User/setmingxistatus'),'-3','<i class="layui-icon">&#xe640;</i> 批量+扣奖励+不删除',array('class'=>'layui-btn layui-btn-xs fbutton ajax-post'));
    		$builder->buttonDelete(U('User/setmingxistatus?status=-1'),'批量+扣奖励+删除');
    		$builder->buttonSetStatus(U('User/setmingxistatus'),'-2','<i class="layui-icon">&#xe640;</i> 批量+不扣奖励+删除',array('class'=>'layui-btn layui-btn-xs fbutton ajax-post'));
    	}
    	$builder->setSearchPostUrl(U('User/mingxi'))
    	->setSelectPostUrl(U('User/mingxi'))
    	->search('昵称或ID', 'nickname', 'text', '请输入用户昵称或者ID')
    	->search('说明','remark','text','输入说明关键词')
    	->search('开始时间','create_time','time','开始时间','')
    	->search('结束时间','end_time','time','开始时间','')
    	->select('显示：','r','select','','','',$roption)
    	->select('状态：','dead','select','','','',array(array('id'=>0,'value'=>'使用中'),array('id'=>1,'value'=>'已禁用'),array('id'=>2,'value'=>'已删除')));
    	$builder->keyId()
    	->keyUid('uid', '用户ID|昵称')
		->keyCreateTime();
		if($aDead != 2 && $aDead != 1){
			$builder->keyDoActionDel('User/setmingxistatus?ids=###&status=-1','扣奖励删除',array('style'=>'margin-bottom:4px;'));
			$builder->keyDoActionDel('User/setmingxistatus?ids=###&status=-2','不扣奖励删除');
		}
    	foreach($score_option as &$val){
    		$builder->keyText($val['mark'],$val['title']);
    	}
    	foreach($score_option as &$val){
    		$builder->keyText('re_'.$val['mark'],'剩余'.$val['title']);
    	}
    	$builder->keyText('remark','说明')
    	->keyStatus()
    	->data($list)
    	->pagination($totalCount, $r)
    	->display();
    }
    
    public function setmingxistatus($ids, $status){
    	$ids = is_array($ids) ? $ids : explode(',', $ids);
		if(!empty($ids)){
			switch($status){
				
				case '0':
					D('User/UserScoreLog')->where(array('id' => array('in',$ids)))->save(array('status'=>0));
					break;
				case '1':
					D('User/UserScoreLog')->where(array('id' => array('in',$ids)))->save(array('status'=>1));
					break;
				case '-1':
					$data = D('User/UserScoreLog')->where(array('id' => array('in',$ids)))->select();
					$score_option=D('User/Score')->getTypeList(array('status = 1'));
					foreach($data as &$v){
						foreach($score_option as &$val){
							if($v[$val['mark']] != 0){
								D('User/Score')->setUserScore($v['uid'],'dec', $val['mark'], $v[$val['mark']]);
							}
						}
						D('User/UserScoreLog')->where('id='.$v['id'])->save(array('status'=>-1));
					}
					unset($v);
					break;
				case '-2':
					$data = D('User/UserScoreLog')->where(array('id' => array('in',$ids)))->select();
					foreach($data as &$v){
						D('User/UserScoreLog')->where('id='.$v['id'])->save(array('status' => -1));
					}
					unset($v);
					break;
				case '-3':
					$data = D('User/UserScoreLog')->where(array('id' => array('in',$ids)))->select();
					$score_option=D('User/Score')->getTypeList(array('status = 1'));
					foreach($data as &$v){
						foreach($score_option as &$val){
							if($v[$val['mark']] != 0){
								$num = $v[$val['mark']];
								$gold_type = $val['mark'];
								$gold_name = $val['title'];
								if($num > 0){
									$title= '收入明细['.$v['id'].']错误，还原'.$gold_name.'-'.$num;
									$content = '收入明细['.$v['id'].']错误，还原'.$gold_name.'-'.$num;
									$r = api('User/sendReward', array($v['uid'],0, 'userscorelog', 'dec',$gold_type ,abs($num), $title, $content));
								}else{
									$title= '收入明细['.$v['id'].']错误，还原'.$gold_name.abs($num);
									$content = '收入明细['.$v['id'].']错误，还原'.$gold_name.abs($num);
									$r = api('User/sendReward', array($v['uid'],0, 'userscorelog', 'inc',$gold_type ,abs($num), $title, $content));
								}
								
							}
						}
					}
					unset($v);
					break;
				
				default:
					break;
			}
			
			$this->success('设置成功', $_SERVER['HTTP_REFERER']);
		}else{
			$this->error('请选择操作数据', $_SERVER['HTTP_REFERER']);
		}
    }
    
    public function delmingxi($ids){
    	$ids = is_array($ids) ? $ids : explode(',', $ids);
    	D('User/UserScoreLog')->where(array('id' => array('in',$ids)))->delete();
    	$this->success('设置成功', $_SERVER['HTTP_REFERER']);
    }
    
    //导入数据页面
    public function import()
    {
    	$this->display();
    }
    
    //上传方法
    public function upload()
    {
    	header("Content-Type:text/html;charset=utf-8");
    	$config = array(
			'maxSize'    =>    3145728,
			'rootPath'   =>    './uploads/',
			'savePath'   =>    'member/',
			'exts'       =>    array('xls', 'xlsx', 'csv'),
    	);
    	$File = D('File');
		$file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
		$info = $File->upload($_FILES, $config,	C('DOWNLOAD_UPLOAD_DRIVER'), C("UPLOAD_{$file_driver}_CONFIG"));

        if($info){
            $return['status'] = 1;
            $return['info'] = '上传成功';
            $return['url'] = $info;
        }else{
            $return['status'] = 0;
            $return['info'] = '上传失败';
        }
    
    	$this->ajaxReturn($return);
    }
    
    //导入数据方法
    public function user_import($filename, $exts='xls')
    {
    	if(!$filename){
    		$this->error('请先上传文件！');
    	}
    	 
    	//导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
    	//import("Org.Util.PHPExcel");
    	//创建PHPExcel对象，注意，不能少了\
    	$PHPExcel=new PHPExcel();
    
    	//如果excel文件后缀名为.xls，导入这个类
    	if($exts == 'xls'){
    		//import("Org.Util.PHPExcel.Reader.Excel5");
    		$PHPReader=new \PHPExcel_Reader_Excel5();
    	}else if($exts == 'xlsx'){
    		//import("Org.Util.PHPExcel.Reader.Excel2007");
    		$PHPReader=new \PHPExcel_Reader_Excel2007();
    	}else if($exts == 'csv'){
    		$PHPReader = new \PHPExcel_Reader_CSV();
    	}

    	//载入文件
    	$PHPExcel=$PHPReader->load($filename);
    	//获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
    	$currentSheet=$PHPExcel->getSheet(0);
    	//获取总列数
    	$allColumn=$currentSheet->getHighestColumn();
    	//获取总行数
    	$allRow=$currentSheet->getHighestRow();
    	//循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
    	for($currentRow=1;$currentRow<=$allRow;$currentRow++){
    		//从哪列开始，A表示第一列
    		for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++){
    			//数据坐标
    			$address=$currentColumn.$currentRow;
    			
    			//读取到的数据，保存到数组$arr中
    			$data[$currentRow][$currentColumn]=$currentSheet->getCell($address)->getValue();
    		}
    	}
    	
    	//$this->success($data,$_SERVER['HTTP_REFERER']);
    	//exit($data);
    	//$damacontent = $this->damaModel->where(array('cid' => $cid))->find();
    	//if($damacontent){
    		for($coderow = 2; $coderow <= count($data); $coderow++){
    			foreach($data[$coderow] as $k=>$v){
    			    //exit($k);
    			    switch($k){
    			        case 'A':
    			            $ColumnKey = 'username';
    			            $arr[$coderow-1][$ColumnKey]=trim($v);
    			            break;
    			        case 'B':
    			            $ColumnKey = 'password';
    			            $arr[$coderow-1][$ColumnKey]=trim($v);
    			            break;
    			        case 'C':
    			            $ColumnKey = 'email';
    			            $arr[$coderow-1][$ColumnKey]=trim($v);
    			            break;
    			        case 'D':
    			            $ColumnKey = 'mobile';
    			            $arr[$coderow-1][$ColumnKey]=trim($v);
    			            break;
    			        case 'E':
    			            $ColumnKey = 'ups_one';
    			            $arr[$coderow-1][$ColumnKey]=trim($v);
    			            break;
    			        case 'F':
    			            $ColumnKey = 'ups_two';
    			            $arr[$coderow-1][$ColumnKey]=trim($v);
    			            break;
    			        case 'G':
    			            $ColumnKey = 'ups_three';
    			            $arr[$coderow-1][$ColumnKey]=trim($v);
    			            break;
    			        case 'H':
    			            $ColumnKey = 'ups_four';
    			            $arr[$coderow-1][$ColumnKey]=trim($v);
    			            break;
    			        case 'I':
    			            $ColumnKey = 'ups_five';
    			            $arr[$coderow-1][$ColumnKey]=trim($v);
    			            break;
    			        case 'J':
    			            $ColumnKey = 'ups_six';
    			            $arr[$coderow-1][$ColumnKey]=trim($v);
    			            break;
    			        case 'K':
    			            $ColumnKey = 'ups_seven';
    			            $arr[$coderow-1][$ColumnKey]=trim($v);
    			            break;
    			        default:
    			            break;
    			    }
    			}
    		}
    		$count = 0;
    		foreach($arr as &$val){
    		    
    			$userdatas = D('User/UcenterMember')->where(array('username'=> $val['username']))->find();
    			if(empty($userdatas)){
    			    $ups['ups_one'] = $val['ups_one'];
    			    $ups['ups_two'] = $val['ups_two'];
    			    $ups['ups_three'] = $val['ups_three'];
    			    $ups['ups_four'] = $val['ups_four'];
    			    $ups['ups_five'] = $val['ups_five'];
    			    $ups['ups_six'] = $val['ups_six'];
    			    $ups['ups_seven'] = $val['ups_seven'];
    			    $u = D('User/UcenterMember')->register($val['username'], $val['username'], $val['password'], $val['email'], $val['mobile'], 1, $ups);
    			    if($u > 0){
    			        $count = $count+1;
    			    }
    			}
    		}
    		unset($val);
    		$this->success('共'.count($arr).'条需要导入的数据，成功导入'.$count.'条会员记录！',$_SERVER['HTTP_REFERER']);
    	//}
    	 
    }

    public function add(){
        if(IS_POST){
            $data['username'] = I('post.username','','text');
            $data['nickname'] = I('post.nickname','','text');
            $data['password'] = I('post.password','','text');
            $u = D('User/UcenterMember')->register($data['username'], $data['nickname'], $data['password'], '', '', 1, '');
            if($u){
                D('User/Member')->initUser(1, $u);
                $this->success('新增会员成功');
            }else{
                $this->error('新增会员失败');
            }
        }else{
            $builder = new AdminConfigBuilder();
            $builder->title('新增会员');
            $builder->meta_title = '新增会员';
            $builder->keyId()
            ->keyText('username', '用户名','用户名必须是字母加数字的组合，不能用中文')
            ->keyText('nickname', '昵称','昵称可以是中文')
            ->keyText('password','密码');
            $builder->buttonSubmit(U('User/add'));
            $builder->buttonBack();
            $builder->display();
        }
    }
    
    public function purse(){
    	$map['status'] = array('egt', -1);
    	$list = $this->lists('MemberPurse', $map);
    	// 显示页面
    	$builder = new AdminListBuilder();
    	
    	$attr['class'] = 'btn ajax-post';
    	$attr['target-form'] = 'ids';
    	
    	$builder->title('钱包设置')
    	->buttonNew(U('User/purseEdit'))
    	->setStatusUrl(U('setpurseStatus'))
    	->keyId()
    	->keyLink('pursename', '钱包类型', 'User/purseEdit?id=###')
    	->keyStatus()
    	->keyDoActionEdit('User/purseEdit?id=###')
    	->keyDoAction('User/setPurseStatus?ids=###&status=0','禁用',array('class'=>'ajax-get layui-btn layui-btn-xs layui-btn-danger'))
    	->keyDoActionRealDel('User/setPurseStatus?ids=###&status=-1')
    	->data($list)
    	->display();
    }
    
    /**设置字段状态：删除=-1，禁用=0，启用=1
     * @param $ids
     * @param $status
     */
    public function setPurseStatus($ids, $status)
    {
    	$ids = is_array($ids) ? $ids : explode(',', $ids);
		
		if($status == -1){
			foreach($ids as $v){
				if($v == 1 || $v == 2 || $v == 3){
					$this->success('不能删除ID为：1，2，3的数据！', $_SERVER['HTTP_REFERER']);
				}
			}
			M('MemberPurse')->where(array('id' => array('in',$ids)))->delete();
			$this->success('删除成功', $_SERVER['HTTP_REFERER']);
		}else{
			M('MemberPurse')->where(array('id' => array('in',$ids)))->save(array('status' => $status));
			$this->success('状态设置成功', $_SERVER['HTTP_REFERER']);
		}
    }
    
    
    
    public function purseEdit($id = 0, $pursename = '', $status = 1){
    	$isEdit = $id ? 1 : 0;
    	if(IS_POST){
    		if ($pursename == '' || $pursename == null) {
    			$this->error('请输入任务名称');
    		}
    		$purse['pursename'] = $pursename;
    		$purse['status'] = $status;
    		if ($isEdit) {
    			$purse['update_time'] = time();
    			$rs = D('MemberPurse')->where('id=' . $id)->save($purse);
    		} else {
    			// 商品名存在验证
    			$map['status'] = array(
    					'egt',
    					0
    			);
    			$map['pursename'] = $pursename;
    			if (D('MemberPurse')->where($map)->count()) {
    				$this->error('已存在同名任务');
    			}
    		
    			$purse['create_time'] = time();
    			$purse['update_time'] = time();
    			$rs = D('MemberPurse')->add($purse);
    		}
    		if ($rs) {
    			$this->success($isEdit ? '编辑成功' : '添加成功', U('User/purse'));
    		} else {
    			$this->error($isEdit ? '编辑失败' : '添加失败');
    		}
    		} else {
    			$builder = new AdminConfigBuilder();
    			$builder->title($isEdit ? '编辑钱包类型' : '添加钱包类型');
    			$builder->meta_title = $isEdit ? '编辑钱包类型' : '添加钱包类型';
    			$builder->keyId()
    			->keyText('pursename', '钱包类型')
    			->keyStatus('status', '使用状态');
    			if ($isEdit) {
    				$purse = D('MemberPurse')->where('id=' . $id)->find();
    				$builder->data($purse);
    				$builder->buttonSubmit(U('User/purseEdit'));
    				$builder->buttonBack();
    				$builder->display();
    			} else {
    				$purse['status'] = 1;
    				$builder->buttonSubmit(U('User/purseEdit'));
    				$builder->buttonBack();
    				$builder->data($purse);
    				$builder->display();
    			}
    		}
    }
    
    
    public function myprofile()
    {
    	
    	$this->meta_title = '个人中心';
    	$user = query_user(array('avatar128','nickname'),UID);
    	$user_group_id = M('AuthGroupAccess')->where('uid ='.UID)->getField('group_id');
    	$group_name = M('AuthGroup')->where('id ='.$user_group_id)->getField('title');
    	$this->assign('group_name', $group_name);
    	$this->assign('user', $user);
    	$this->display();
    }
    
    
    public function account()
    {
    	$this->meta_title = '个人资料';
    	$aUid = UID;
    	$aTab = I('get.tab', '', 'op_t');
    	$aNickname = I('post.nickname', '', 'op_t');
    	$aSex = I('post.sex', 0, 'intval');
    	$aEmail = I('post.email', '', 'op_t');
    	$aSignature = I('post.signature', '', 'op_t');
    	$aCommunity = I('post.community', 0, 'intval');
    	$aDistrict = I('post.district', 0, 'intval');
    	$aCity = I('post.city', 0, 'intval');
    	$aProvince = I('post.province', 0, 'intval');
    	$qq = I('post.qq', 0, 'op_t');
    	
    	if (IS_POST) {
    		$this->checkNickname($aNickname);
    		$this->checkSex($aSex);
    		$this->checkSignature($aSignature);
    	
    	
    		$user['pos_province'] = $aProvince;
    		$user['pos_city'] = $aCity;
    		$user['pos_district'] = $aDistrict;
    		$user['pos_community'] = $aCommunity;
    	
    		$user['nickname'] = $aNickname;
    		$user['sex'] = $aSex;
    		$user['signature'] = $aSignature;
    		$user['uid'] = get_uid();
    		$user['qq'] = $qq;
    	
    		$rs_member = D('User/Member')->save($user);
    	
    		$ucuser['id'] = get_uid();
    		$rs_ucmember = D('User/UcenterMember')->save($ucuser);
    		clean_query_user_cache(get_uid(), array('nickname','qq', 'sex', 'signature', 'email', 'pos_province', 'pos_city', 'pos_district', 'pos_community'));
    	
    		//TODO tox 清空缓存
    		if ($rs_member || $rs_ucmember) {
    			$this->success('设置成功。');
    	
    		} else {
    			$this->success('未修改数据。');
    		}
    	
    	} else {
    		//调用API获取基本信息
    		//TODO tox 获取省市区数据
    		
    		$user_group_id = M('AuthGroupAccess')->where('uid ='.UID)->getField('group_id');
    		$group_name = M('AuthGroup')->where('id ='.$user_group_id)->getField('title');
    		$this->assign('group_name', $group_name);
    		
    	
    		$this->accountInfo();
    	
    		$this->assign('tab', $aTab);
    		$this->_setTab('info');
    		$this->display();
    	}
    	

    }

    private function accountInfo()
    {
    	$info = D('User/UcenterMember')->field('id,username,email,mobile,type')->find(UID);
    	$this->assign('accountInfo', $info);
    }

    private function _setTab($name)
    {
    	$this->assign('tab', $name);
    }

    public function mymsg($page  = 1, $r = 50)
    {
    	$map['to_uid'] = UID;
		list($list,$totalCount)=$this->getListByPage('Message',$map,$page,'id desc','*',$r);

		foreach($list as &$v){
			$v['title'] = D('MessageContent')->where('id='.$v['content_id'])->getField('title');
		}

    	$roption = array(array('id'=>50,'value'=>'显示50行'),array('id'=>100,'value'=>'显示100行'),array('id'=>200,'value'=>'显示200行'));

    	$this->meta_title = '站内信';
		
    	$builder = new AdminListBuilder();
		
    	$builder->setSelectPostUrl(U('user/mymsg'))
    	->select('显示：','r','select','','','',$roption);
    	$builder->title('站内信')
    	->buttonNew(U('message/userlist'))
		->buttonDelete(U('user/delmsg'),'批量彻底删除')
		->buttonDelete(U('user/clearmsg'),'清空短信表')
    	->keyId()
    	->keyUid('to_uid', '发给用户')
		->keyText('title','标题')
		->keyText('is_read', '是否已读', array('0'=>'未读','1'=>'已读'))
    	->keyStatus()
    	->keyDoActionRealDel('user/delmsg?ids=###')
		->pagination($totalCount, $r)
    	->data($list)
    	->display();
    }
	
	public function delmsg($ids){
		!is_array($ids)&&$ids=explode(',',$ids);
    	$map['id'] = array('in', $ids);
		$con_id = D('Common/Message')->where($map)->field('content_id')->select();
		foreach($con_id as &$v){
			D('MessageContent')->where(array('id'=>$v['content_id']))->delete();
		}
		unset($v);
    	$res = D('Common/Message')->where($map)->delete();
    	if($res){
    		$this->success('删除成功！');
    	}else{
    		$this->error('删除失败！'.D('Common/Message')->getError());
    	}
	}
	
	public function clearmsg(){
		D('Common/Message')->where('1=1')->delete();
		D('MessageContent')->where('1=1')->delete();
		$this->success('清空成功！');
	}
	
	
    
    public function msgall($page  = 1, $r = 50)
    {
		list($list,$totalCount)=$this->getListByPage('Message',$map,$page,'id desc','*',$r);
		foreach($list as &$v){
			$v['title'] = D('MessageContent')->where('id='.$v['content_id'])->getField('title');
		}

    	$roption = array(array('id'=>50,'value'=>'显示50行'),array('id'=>100,'value'=>'显示100行'),array('id'=>200,'value'=>'显示200行'));

    	$this->meta_title = '站内信';
    	$builder = new AdminListBuilder();

    	$builder->setSelectPostUrl(U('User/msgall'))
    	->select('显示：','r','select','','','',$roption);
    	$builder->title('站内信')
    	->buttonNew(U('message/userlist'))
		->buttonDelete(U('user/delmsg'),'批量彻底删除')
		->buttonDelete(U('user/clearmsg'),'清空短信表')
    	->setStatusUrl(U(''))
    	->keyId()
    	->keyUid('to_uid', '用户')
		->keyText('title','标题')
		->keyText('is_read', '是否已读', array('0'=>'未读','1'=>'已读'))
    	->keyStatus()
    	->keyDoActionRealDel('user/delmsg?ids=###')
		->pagination($totalCount, $r)
    	->data($list)
    	->display();
    }
    
    public function mycalendar()
    {
    	$map['uid'] = UID;
    	$this->meta_title = '日程安排';
    	$this->display();
    }
    
    public function calendar_json()
    {
    	$map['uid'] = UID;
    	$list = $this->lists('Calendar', $map);
    	foreach ($list as &$val){
    		$val['start'] = time_format($val['start']);
    	}
    	$this->ajaxReturn($list);
    }
    
/**
     * 重置用户密码
     */
    public function initPass(){
        $uids=I('ids');
        !is_array($uids)&&$uids=explode(',',$uids);
        foreach($uids as $key=>$val){
            if(!query_user('uid',$val)){
                unset($uids[$key]);
            }
        }
        if(!count($uids)){
            $this->error('前选择要重置的用户！');
        }
        $ucModel=D('User/UcenterMember');
        $data=$ucModel->create(array('password'=>'123456'));
        $res=$ucModel->where(array('id'=>array('in',$uids)))->save(array('password'=>$data['password']));
        if($res){
            $this->success('密码重置(123456)成功！');
        }else{
            $this->error('密码重置失败！可能密码重置前就是“123456”。');
        }
    }
	
	public function userlower($r = 100, $page=1){
    	$aTitle=I('uid','','text');
    	if($aTitle){
    		$maps['from_uid'] = $aTitle;
			$ups_type = modC('UPS_TYPE_MODE','id','USERCONFIG');
			switch($ups_type){
				case 'id':
					$map['ups_one|ups_two|ups_three|ups_four|ups_five|ups_seven'] = $aTitle;
					break;
				case 'username':
					$map['ups_one|ups_two|ups_three|ups_four|ups_five|ups_seven'] = get_username($aTitle);
					break;
			}
			$level_list = D('User/UcenterMember')->where($map)->select();
			if(!empty($level_list)){
				$have = D('friend_rank')->where('from_uid ='.$aTitle)->select();
				if(empty($have)){
					foreach($level_list as &$vc){
						$re = D('friend_rank')->where('from_uid = '.$aTitle.' and uid ='.$vc['id'])->find();
						if(empty($re)){
							$sumget = D('User/UserScoreLog')->where("uid =".$aTitle." and remark like '%好友[".$vc['id']."]%'")->group('uid')->sum('gold_coin');
							$sumget = empty($sumget) ? 0 : $sumget;
							$data['uid'] = $vc['id'];
							$data['from_uid'] = $aTitle;
							$data['create_time'] = TIME();
							$data['update_time'] = TIME();
							$data['gold_coin'] = $sumget;
							D('friend_rank')->add($data);
						}
						unset($sumget,$re);
						//else{
						//	D('friend_rank')->where('from_uid ='.$uid.' and uid = '.$vc['id'])->save(array('gold_coin'=>$sumget,'update_time'=>TIME));
						//}
					}
					unset($vc);
				}else{
					foreach($level_list as &$vc){
						$sumget = D('User/UserScoreLog')->where("uid =".$aTitle." and remark like '%好友[".$vc['id']."]%'")->group('uid')->sum('gold_coin');
						$sumget = empty($sumget) ? 0 : $sumget;
						$re = D('friend_rank')->where('from_uid = '.$aTitle.' and uid ='.$vc['id'])->find();
						if(empty($re)){
							$data['uid'] = $vc['id'];
							$data['from_uid'] = $aTitle;
							$data['create_time'] = TIME();
							$data['update_time'] = TIME();
							$data['gold_coin'] = $sumget;
							D('friend_rank')->add($data);
						}else{
							D('friend_rank')->where('from_uid ='.$aTitle.' and uid = '.$vc['id'])->save(array('gold_coin'=>$sumget,'update_time'=>TIME));
						}
						unset($sumget,$re);
					}
					unset($vc);
				}
			}
			D('User/UcenterMember')->where('id='.$aTitle)->save(array('lower_num'=>count($level_list)));
		}
    	list($list,$totalCount)=$this->getListByPage("friend_rank",$maps,$page,'id desc','*',$r);
    	
    	$roption = array(array('id'=>100,'value'=>'显示100行'),array('id'=>20,'value'=>'显示20行'),array('id'=>50,'value'=>'显示50行'));
        
        // 显示页面
        $builder = new AdminListBuilder();
        $builder->meta_title = '会员下线列表';
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';

        
        $builder->title('会员下线列表')
        ->setSelectPostUrl(U('User/userlower'))
        ->setSearchPostUrl(U('User/userlower'))
        ->search('搜索', 'uid', 'text', '请输入上级用户ID')
        ->select('显示：','r','select','','','',$roption)
        ->keyId()
		->keyUid('uid','下线用户')//'User/userlower?uid=[uid]'
        ->keyText('gold_coin', '给予上线提成')
        ->keyCreateTime();
        $builder->data($list)
		->pagination($totalCount, $r)
        ->display();
    }
	
	
	public function freshuserlower(){
		$uids=I('ids');
        !is_array($uids)&&$uids=explode(',',$uids);
        foreach($uids as $key=>$val){
            if(!query_user('uid',$val)){
                unset($uids[$key]);
            }
        }
        if(!count($uids)){
            $this->error('前选择要更新的用户！');
        }
        $ucModel=D('User/UcenterMember');
		$ups_type = modC('UPS_TYPE_MODE','id','USERCONFIG');
		switch($ups_type){
    		case 'id':
    			$map['ups_one|ups_two|ups_three|ups_four|ups_five|ups_seven'] = $uids;
    			break;
    		case 'username':
    			$map['ups_one|ups_two|ups_three|ups_four|ups_five|ups_seven'] = get_username($uids);
    			break;
    	}
		$level_list = D('User/UcenterMember')->where($map)->select();
		if(!empty($level_list)){
			D('User/UcenterMember')->where('id='.$uid)->save(array('lower_num'=>count($level_list)));
			$have = D('friend_rank')->where('from_uid ='.$uids)->select();
			if(empty($have)){
				foreach($level_list as &$vc){
					$sumget = D('User/UserScoreLog')->where("uid =".$uids." and remark like '%好友[".$vc['id']."]%'")->group('uid')->sum('gold_coin');
					$sumget = empty($sumget) ? 0 : $sumget;
					$re = D('friend_rank')->where('from_uid = '.$uids.' and uid ='.$vc['id'])->find();
					if(empty($re)){
						$data['uid'] = $vc['id'];
						$data['from_uid'] = $uids;
						$data['create_time'] = TIME();
						$data['update_time'] = TIME();
						$data['gold_coin'] = $sumget;
						D('friend_rank')->add($data);
					}
					unset($sumget,$re);
					//else{
					//	D('friend_rank')->where('from_uid ='.$uid.' and uid = '.$vc['id'])->save(array('gold_coin'=>$sumget,'update_time'=>TIME));
					//}
				}
				unset($vc);
			}else{
				foreach($level_list as &$vc){
					$sumget = D('User/UserScoreLog')->where("uid =".$uids." and remark like '%好友[".$vc['id']."]%'")->group('uid')->sum('gold_coin');
					$sumget = empty($sumget) ? 0 : $sumget;
					$re = D('friend_rank')->where('from_uid = '.$uids.' and uid ='.$vc['id'])->find();
					if(empty($re)){
						$data['uid'] = $vc['id'];
						$data['from_uid'] = $uids;
						$data['create_time'] = TIME();
						$data['update_time'] = TIME();
						$data['gold_coin'] = $sumget;
						D('friend_rank')->add($data);
					}else{
						D('friend_rank')->where('from_uid ='.$uid.' and uid = '.$vc['id'])->save(array('gold_coin'=>$sumget,'update_time'=>TIME));
					}
					unset($sumget,$re);
				}
				unset($vc);
			}
		}
		
		$this->success('更新成功');
	}

    public function changeGroup()
    {

        if ($_POST['do'] == 1) {
            //清空group
            $aAll = I('post.all', 0, 'intval');
            $aUids = I('post.uid', array(), 'intval');
            $aGids = I('post.gid', array(), 'intval');

            if ($aAll) {//设置全部用户
                $prefix = C('DB_PREFIX');
                D('')->execute("TRUNCATE TABLE {$prefix}auth_group_access");
                $aUids = D('User/UcenterMember')->getField('id', true);

            } else {
                M('AuthGroupAccess')->where(array('uid' => array('in', implode(',', $aUids))))->delete();;
            }
            foreach ($aUids as $uid) {
                foreach ($aGids as $gid) {
                    M('AuthGroupAccess')->add(array('uid' => $uid, 'group_id' => $gid));
                }
            }


            $this->success('成功。');
        } else {
            $aId = I('post.ids', array(), 'intval');

            foreach ($aId as $uid) {
                $user[] = query_user(array('space_link', 'uid'), $uid);
            }


            $groups = M('AuthGroup')->where(array('status'=>1))->select();
            $this->assign('groups', $groups);
            $this->assign('users', $user);
            $this->display();
        }

    }
  
  
  	public function adduser()
    {
        if (IS_POST) {

            $scoreTypes = D('User/Score')->getTypeList(array('status'=>1));
            $aUids = I('post.id','','text');
            $ups['ups_one'] = I('post.ups_one','','text');
            $ups['ups_two'] = I('post.ups_two','','text');
            $ups['ups_three'] = I('post.ups_three','','text');
            $ups['ups_four'] = I('post.ups_four','','text');
            $ups['ups_five'] = I('post.ups_five','','text');
            $ups['ups_six'] = I('post.ups_six','','text');
            $ups['ups_seven'] = I('post.ups_seven','','text');
            $ups['username'] = I('post.username','','text');
            $ups['email'] = I('post.email',0,'text');
            $ups['mobile'] = I('post.mobile',0,'text');

            $member = M('Member')->where('uid ='.$aUids)->find();
            $member_u = M('UcenterMember')->where('id ='.$aUids)->find();
        	foreach($scoreTypes as $v){
    			$aAction = 'to';
    			$aValue = I('post.'.$v['mark'],0,'intval');
    			if(abs($aValue) != $member[$v['mark']]){
    			//if(abs($aValue) > 0){
	    			//D('User/Score')->setUserScore($aUids,$aValue,$v['mark'],$aAction);
	    			$title = get_nickname(is_login()).'通过系统调整当前'.$v['title'];
	    			$content = get_nickname(is_login()).'通过系统调整当前'.$v['title'].'至：'.$aValue.$v['unit'];
	    			api('User/sendReward', array($aUids,0, 'pay', $aAction, $v['mark'], $aValue, $title, $content));
	    			api('User/sendPayLog', array($aUids,0, $aAction, $v['mark'], $aValue, $title, $content));
    			}
    		}
            unset($v);
            
            $member_info['nickname'] = I('post.nickname','','text');
            $member_info['signature'] = I('post.signature',0,'text');
            $member_info['qq'] = I('post.qq',0,'text');
            $member_info['name'] = I('post.name',0,'text');
            $member_info['idcard'] = I('post.idcard',0,'text');
            
            if($member){
            	if((string)$member_info['nickname'] != (string)$member['nickname']){
            		$member['nickname'] = $member_info['nickname'];
            	}
            	if((string)$member_info['signature'] != (string)$member['signature']){
            		$member['signature'] = $member_info['signature'];
            	}
            	if((string)$member_info['qq'] != (string)$member['qq']){
            		$member['qq'] = $member_info['qq'];
            	}
            	if((string)$member_info['name'] != (string)$member['name']){
            		$member['name'] = $member_info['name'];
            	}
            	if((string)$member_info['idcard'] != (string)$member['idcard']){
            		$member['idcard'] = $member_info['idcard'];
            	}
            	D('User/Member')->where('uid ='.$aUids)->save($member_info);
            }
            
            
            
            
			/*身份设置*/
			//$data = I('post.');
            //$data_role=array();
            //foreach ($data as $key => $val) {
            //    if ( $key == 'role') {
            //        $data_role = explode(',',$val);
            //    }else if (substr($key, 0, 4) == 'role') {
            //        $data_role[] = $val;
             //   }
            //}
            //unset($key,$val);
            //$this->_resetUserRole($uid,$data_role);
            /*身份设置 end*/
			
            if($member_u){
            	if((string)$ups['ups_one'] != (string)$member_u['ups_one']){
            		$member_ups['ups_one'] = $ups['ups_one'];
            	}
            	if((string)$ups['ups_two'] != (string)$member_u['ups_two']){
            		$member_ups['ups_two'] = $ups['ups_two'];
            	}
            	if((string)$ups['ups_three'] != (string)$member_u['ups_three']){
            		$member_ups['ups_three'] = $ups['ups_three'];
            	}
            	if((string)$ups['ups_four'] != (string)$member_u['ups_four']){
            		$member_ups['ups_four'] = $ups['ups_four'];
            	}
            	if((string)$ups['ups_five'] != (string)$member_u['ups_five']){
            		$member_ups['ups_five'] = $ups['ups_five'];
            	}
            	if((string)$ups['ups_six'] != (string)$member_u['ups_six']){
            		$member_ups['ups_six'] = $ups['ups_six'];
            	}
            	if((string)$ups['ups_seven'] != (string)$member_u['ups_seven']){
            		$member_ups['ups_seven'] = $ups['ups_seven'];
            	}
            	if((string)$ups['username'] != (string)$member_u['username']){
            		$member_ups['username'] = $ups['username'];
            	}
            	if((string)$ups['email'] != (string)$member_u['email']){
            		$member_ups['email'] = $ups['email'];
            	}
            	if((string)$ups['mobile'] != (string)$member_u['mobile']){
            		$member_ups['mobile'] = $ups['mobile'];
            	}

            	D('User/UcenterMember')->where('id ='.$aUids)->save($member_ups);

            }
            
			$this->success('设置成功');

        } else {
			$member['email_ver'] = ($member['email_ver'] == 0) ? '未验证' : '已验证';
			$member['mobile_ver'] = ($member['mobile_ver'] == 0) ? '未验证' : '已验证';
			$member['mobile'] = ($member['mobile'] == NULL) ? '无' : $member['mobile'];
			$member['email'] = ($member['email'] == NULL) ? '无' : $member['email'];

            $builder = new AdminConfigBuilder();
            $builder->title("新增会员");
            $builder->meta_title = '新增会员';
            $builder->keyId()
            ->keyText('username', "用户名称")
            ->keyText('nickname', '昵称')
            ->keyRadio('sex', '性别','',array('0'=>'女','1'=>'男'))
            ->keyText('signature', '个性签名')
            ->keyText('qq', 'QQ')
            ->keyText('email', '邮箱')
            ->keyText('mobile', '手机')

            ->keyUid('ups_one', '一级上线')
            ->keyUid('ups_two', '二级上线')
            ->keyUid('ups_three', '三级上线')
            ->keyUid('ups_four', '四级上线')
            ->keyUid('ups_five', '五级上线')
            ->keyUid('ups_six', '六级上线')
            ->keyUid('ups_seven', '七级上线');
            
            $field_key = array('id', 'username', 'nickname','sex','signature','qq','email','mobile');
            foreach ($fields_list as $vt) {
                $field_key[] = $vt['field_name'];
                $builder->keyReadOnly($vt['field_name'], $vt['field_name']);
            }

            /* 积分设置  start*/
            $field = D('User/Score')->getTypeList(array('status' => 1));
            $score_key = array();
            foreach ($field as $vf) {
                $score_key[] = $vf['mark'];
                $builder->keyText($vf['mark'], $vf['title']);
            }
            /*积分设置end*/
			
            $c = modC('UPS_TYPE_MODE','','USERCONFIG');
            switch($c){
            	case 'id':
            		$c= '上线用户ID';
            		break;
            	case 'username':
            		$c= '上线用户名';
            		break;
            }

            $builder->group('基础资料', implode(',', $field_key));
            $builder->group('货币直接调整', implode(',', $score_key));
            $builder->group('上线资料（'.$c.'）','ups_one,ups_two,ups_three,ups_four,ups_five,ups_six,ups_seven');
            //$builder->group('身份设置', implode(',', $role_key));
			$builder->data($member);
            $builder->buttonSubmit('', '保存');
            $builder->buttonBack();
            $builder->display();
        }

    }


     /**用户扩展资料详情
     * @param string $uid
     * 
     */
    public function expandinfo_details($uid = 0)
    {
        if (IS_POST) {

            $scoreTypes = D('User/Score')->getTypeList(array('status'=>1));
            $aUids = I('post.id','','text');
            $ups['ups_one'] = I('post.ups_one','','text');
            $ups['ups_two'] = I('post.ups_two','','text');
            $ups['ups_three'] = I('post.ups_three','','text');
            $ups['ups_four'] = I('post.ups_four','','text');
            $ups['ups_five'] = I('post.ups_five','','text');
            $ups['ups_six'] = I('post.ups_six','','text');
            $ups['ups_seven'] = I('post.ups_seven','','text');
            $ups['username'] = I('post.username','','text');
            $ups['email'] = I('post.email',0,'text');
            $ups['mobile'] = I('post.mobile',0,'text');

            $member = M('Member')->where('uid ='.$aUids)->find();
            $member_u = M('UcenterMember')->where('id ='.$aUids)->find();
        	foreach($scoreTypes as $v){
    			$aAction = 'to';
    			$aValue = I('post.'.$v['mark'],0,'intval');
    			if(abs($aValue) != $member[$v['mark']]){
    			//if(abs($aValue) > 0){
	    			//D('User/Score')->setUserScore($aUids,$aValue,$v['mark'],$aAction);
	    			$title = get_nickname(is_login()).'通过系统调整当前'.$v['title'];
	    			$content = get_nickname(is_login()).'通过系统调整当前'.$v['title'].'至：'.$aValue.$v['unit'];
	    			api('User/sendReward', array($aUids,0, 'pay', $aAction, $v['mark'], $aValue, $title, $content));
	    			api('User/sendPayLog', array($aUids,0, $aAction, $v['mark'], $aValue, $title, $content));
    			}
    		}
            unset($v);
            
            $member_info['nickname'] = I('post.nickname','','text');
            $member_info['signature'] = I('post.signature',0,'text');
            $member_info['qq'] = I('post.qq',0,'text');
            $member_info['name'] = I('post.name',0,'text');
            $member_info['idcard'] = I('post.idcard',0,'text');
            
            if($member){
            	if((string)$member_info['nickname'] != (string)$member['nickname']){
            		$member['nickname'] = $member_info['nickname'];
            	}
            	if((string)$member_info['signature'] != (string)$member['signature']){
            		$member['signature'] = $member_info['signature'];
            	}
            	if((string)$member_info['qq'] != (string)$member['qq']){
            		$member['qq'] = $member_info['qq'];
            	}
            	if((string)$member_info['name'] != (string)$member['name']){
            		$member['name'] = $member_info['name'];
            	}
            	if((string)$member_info['idcard'] != (string)$member['idcard']){
            		$member['idcard'] = $member_info['idcard'];
            	}
            	D('User/Member')->where('uid ='.$aUids)->save($member_info);
            }
            
            
            
            
			/*身份设置*/
			//$data = I('post.');
            //$data_role=array();
            //foreach ($data as $key => $val) {
            //    if ( $key == 'role') {
            //        $data_role = explode(',',$val);
            //    }else if (substr($key, 0, 4) == 'role') {
            //        $data_role[] = $val;
             //   }
            //}
            //unset($key,$val);
            //$this->_resetUserRole($uid,$data_role);
            /*身份设置 end*/
			
            if($member_u){
            	if((string)$ups['ups_one'] != (string)$member_u['ups_one']){
            		$member_ups['ups_one'] = $ups['ups_one'];
            	}
            	if((string)$ups['ups_two'] != (string)$member_u['ups_two']){
            		$member_ups['ups_two'] = $ups['ups_two'];
            	}
            	if((string)$ups['ups_three'] != (string)$member_u['ups_three']){
            		$member_ups['ups_three'] = $ups['ups_three'];
            	}
            	if((string)$ups['ups_four'] != (string)$member_u['ups_four']){
            		$member_ups['ups_four'] = $ups['ups_four'];
            	}
            	if((string)$ups['ups_five'] != (string)$member_u['ups_five']){
            		$member_ups['ups_five'] = $ups['ups_five'];
            	}
            	if((string)$ups['ups_six'] != (string)$member_u['ups_six']){
            		$member_ups['ups_six'] = $ups['ups_six'];
            	}
            	if((string)$ups['ups_seven'] != (string)$member_u['ups_seven']){
            		$member_ups['ups_seven'] = $ups['ups_seven'];
            	}
            	if((string)$ups['username'] != (string)$member_u['username']){
            		$member_ups['username'] = $ups['username'];
            	}
            	if((string)$ups['email'] != (string)$member_u['email']){
            		$member_ups['email'] = $ups['email'];
            	}
            	if((string)$ups['mobile'] != (string)$member_u['mobile']){
            		$member_ups['mobile'] = $ups['mobile'];
            	}

            	D('User/UcenterMember')->where('id ='.$aUids)->save($member_ups);

            }
            
			$this->success('设置成功');

        } else {
            $map = 'l.uid = r.id and l.uid = '.$uid.'';
            $prefix   = C('DB_PREFIX');
            $l_table  = $prefix.('member');
            $r_table  = $prefix.('ucenter_member');
            $list     = D()->table( $l_table.' l,'.$r_table.' r' )->where( $map )->order( 'l.uid DESC')->find();
			$member = $list;
			$member['email_ver'] = ($member['email_ver'] == 0) ? '未验证' : '已验证';
			$member['mobile_ver'] = ($member['mobile_ver'] == 0) ? '未验证' : '已验证';
			$member['mobile'] = ($member['mobile'] == NULL) ? '无' : $member['mobile'];
			$member['email'] = ($member['email'] == NULL) ? '无' : $member['email'];
			switch($member['status']){
				case '0':
					$member['status_txt'] = '冻结';
					break;
				case '1':
					$member['status_txt'] = '正常';
					break;
				case '2':
					 
					break;
				case '3':
					$member['status_txt'] = '未激活';
					break;
				case '-1':
					$member['status_txt'] = '已删除';
					break;
					 
			}
            $member['id'] = $member['uid'];
            $member['username'] = $member['username'];
            switch($member['sex']){
            	case 0:
            		$member['sex'] = '女';
            		break;
            	case 1:
            		$member['sex'] = '男';
            		break;
            }
            $member['reg_time'] = time_format($member['reg_time']);
            $member['last_login_time'] = time_format($member['last_login_time']);
            //扩展信息查询
            $map_profile['status'] = 1;
            $field_group = D('field_group')->where($map_profile)->select();
            $field_group_ids = array_column($field_group, 'id');
            $map_profile['profile_group_id'] = array('in', $field_group_ids);
            $fields_list = D('field_setting')->where($map_profile)->getField('id,field_name,form_type');
            $fields_list = array_combine(array_column($fields_list, 'field_name'), $fields_list);
            $map_field['uid'] = $member['uid'];
            foreach ($fields_list as $key => $val) {
                $map_field['field_id'] = $val['id'];
                $field_data = D('field')->where($map_field)->getField('field_data');
                if ($field_data == null || $field_data == '') {
                    $member[$key] = '';
                } else {
                    $member[$key] = $field_data;
                }
                $member[$key] = $field_data;
            }
            $builder = new AdminConfigBuilder();
            $builder->title("用户扩展资料详情");
            $builder->meta_title = '用户扩展资料详情';
            $builder->keyId()
            ->keyReadOnly('status_txt','账号状态')
            ->keyText('username', "用户名称")
            ->keyText('nickname', '昵称')
            ->keyReadOnly('sex', '性别')
            ->keyReadOnly('login', '登录次数')
            ->keyReadOnly('reg_ip', '注册IP')
            ->keyReadOnly('reg_time', '注册时间')
            ->keyReadOnly('last_login_ip', '最后登录IP')
            ->keyReadOnly('last_login_time', '最后登录时间')
            ->keyText('signature', '个性签名')
            ->keyText('qq', 'QQ')
            ->keyText('name', '真实姓名')
            ->keyText('idcard', '身份证信息')
            ->keyText('email', '邮箱')
            ->keyReadOnly('email_ver', '邮箱认证')
            ->keyText('mobile', '手机')
            ->keyReadOnly('mobile_ver', '手机认证')
            
            ->keyUid('ups_one', '一级上线')
            ->keyUid('ups_two', '二级上线')
            ->keyUid('ups_three', '三级上线')
            ->keyUid('ups_four', '四级上线')
            ->keyUid('ups_five', '五级上线')
            ->keyUid('ups_six', '六级上线')
            ->keyUid('ups_seven', '七级上线');
            
            //->keyReadOnly('email', '邮箱')
            //->keyReadOnly('mobile','');
            $field_key = array('id', 'username', 'nickname','sex','login','reg_ip','reg_time','last_login_ip','last_login_time','signature','qq','name','idcard','status_txt','email','email_ver','mobile','mobile_ver');
            foreach ($fields_list as $vt) {
                $field_key[] = $vt['field_name'];
                $builder->keyReadOnly($vt['field_name'], $vt['field_name']);
            }

            /* 积分设置  start*/
            $field = D('User/Score')->getTypeList(array('status' => 1));
            $score_key = array();
            foreach ($field as $vf) {
                $score_key[] = $vf['mark'];
                $builder->keyText($vf['mark'], $vf['title']);
            }
            $score_data = D('User/Member')->where(array('uid' => $uid))->field(implode(',', $score_key))->find();
            $member = array_merge($member, $score_data);
            /*积分设置end*/
			
            $c = modC('UPS_TYPE_MODE','','USERCONFIG');
            switch($c){
            	case 'id':
            		$c= '上线用户ID';
            		break;
            	case 'username':
            		$c= '上线用户名';
            		break;
            }

            $builder->group('基础资料', implode(',', $field_key));
            $builder->group('货币直接调整', implode(',', $score_key));
            $builder->group('上线资料（'.$c.'）','ups_one,ups_two,ups_three,ups_four,ups_five,ups_six,ups_seven');
            //$builder->group('身份设置', implode(',', $role_key));
			$builder->data($member);
            $builder->buttonSubmit('', '保存');
            $builder->buttonBack();
            $builder->display();
        }

    }

    /**验证用户名
     * @param $nickname
     * @auth
     */
    private function checkNickname($nickname)
    {
    	$length = mb_strlen($nickname, 'utf8');
    	if ($length == 0) {
    		$this->error('请输入昵称。');
    	} else if ($length > 32) {
    		$this->error('昵称不能超过32个字。');
    	} else if ($length < 4) {
    		$this->error('昵称不能少于4个字。');
    	}
    	$match = preg_match('/^(?!_|\s\')[A-Za-z0-9_\x80-\xff\s\']+$/', $nickname);
    	if (!$match) {
    		$this->error('昵称只允许中文、字母、下划线和数字。');
    	}
    
    	$map_nickname['nickname'] = $nickname;
    	$map_nickname['uid'] = array('neq', is_login());
    	$had_nickname = D('User/Member')->where($map_nickname)->count();
    	if ($had_nickname) {
    		$this->error('昵称已被人使用。');
    	}
    }
    
    /**
     * @param $sex
     * @return int
     * @auth
     */
    private function checkSex($sex)
    {
    
    	if ($sex < 0 || $sex > 2) {
    		$this->error('性别必须属于男、女、保密。');
    		return $sex;
    	}
    	return $sex;
    }
    
    /**
     * @param $email
     * @param $email
     * @auth
     */
    private function checkEmail($email)
    {
    	$pattern = "/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?/i";
    	if (!preg_match($pattern, $email)) {
    		$this->error('邮箱格式错误。');
    	}
    
    	$map['email'] = $email;
    	$map['id'] = array('neq', get_uid());
    	$had = D('User/UcenterMember')->where($map)->count();
    	if ($had) {
    		$this->error('该邮箱已被人使用。');
    	}
    }
    
    
    /**验证签名
     * @param $signature
     * @auth
     */
    private function checkSignature($signature)
    {
    	$length = mb_strlen($signature, 'utf8');
    	if ($length >= 100) {
    		$this->error('签名不能超过100个字');
    	}
    }

    /**
     * 修改昵称提交
     */
    public function submitNickname()
    {
        //获取参数
        $nickname = I('post.nickname');
        $password = I('post.password');
        empty($nickname) && $this->error('请输入昵称');
        empty($password) && $this->error('请输入密码');

        //密码验证
        //$User = new UserApi();
        $uid = D('User/UcenterMember')->login(UID, $password, 4);
        ($uid == -2) && $this->error('密码不正确');

        $Member = D('User/Member');
        $data = $Member->create(array('nickname' => $nickname));
        if (!$data) {
            $this->error($Member->getError());
        }

        $res = $Member->where(array('uid' => $uid))->save($data);
        
        if ($res) {
            $user = session('user_auth');
            $user['username'] = $data['username'];
			$user['nickname'] = $data['nickname'];
            session('user_auth', $user);
            session('user_auth_sign', data_auth_sign($user));
            clean_query_user_cache(UID, 'nickname');
            $this->success('修改昵称成功！');
        } else {
            $this->error('修改昵称失败！');
        }
    }


    /**
     * 修改密码提交
     */
    public function submitPassword()
    {
        //获取参数
        $password = I('post.old');
        empty($password) && $this->error('请输入原密码');
        $data['password'] = I('post.password');
        empty($data['password']) && $this->error('请输入新密码');
        $repassword = I('post.repassword');
        empty($repassword) && $this->error('请输入确认密码');

        if ($data['password'] !== $repassword) {
            $this->error('您输入的新密码与确认密码不一致');
        }

        if(D('User/UcenterMember')->updateUserFields(UID, $password, $data) !== false){
            $res['status'] = true;
        }else{
            $res['status'] = false;
            $res['info'] = D('User/UcenterMember')->getError();
        }
        if ($res['status']) {
            $this->success('修改密码成功！');
        } else {
            $this->error(D('User/UcenterMember')->getErrorMessage($res['info']));
        }
    }

    /**
     * 用户行为列表
     *  
     */
    public function actions()
    {
        //获取列表数据
        $Action = M('Action')->where(array('status' => array('gt', -1)));
        $list = $this->lists($Action);
        int_to_string($list);
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);

        $this->assign('_list', $list);
        $this->meta_title = '用户行为';
        $this->display();
    }
	
	public function action()
    {
       // $aModule = I('post.module', '-1', 'text');
        $aModule = $this->parseSearchKey('module');

        is_null($aModule) && $aModule =-1;
        if($aModule!=-1){
            $map['module'] = $aModule;
        }
        unset($_REQUEST['module']);
        $this->assign('current_module', $aModule);
        $map['status'] = array('gt', -1);
        //获取列表数据
        $Action = M('Action')->where(array('status' => array('gt', -1)));

        $list = $this->lists($Action, $map);
        int_to_string($list);
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->assign('_list', $list);
        $module = D('Common/Module')->getAll();
        foreach ($module as $key => $v) {
            if ($v['is_setup'] == false) {
                unset($module[$key]);
            }
        }
        $module = array_merge(array(array('name' => '', 'alias' => '系统')), $module);
        $this->assign('module', $module);

        $this->meta_title = '用户行为';
        $this->display();
    }
	
	private function getTimeUnit()
    {
        return get_time_unit();
    }
    
    public function setactionStatus($ids, $status)
    {
    	$ids = is_array($ids) ? $ids : explode(',', $ids);
    	M('Action')->where(array('id' => array('in', $ids)))->save(array('status' => $status));
    	$this->success('设置成功', $_SERVER['HTTP_REFERER']);
    }
    
    
    public function delaction($ids)
    {
    	!is_array($ids)&&$ids=explode(',',$ids);
    	$map['id'] = array('in', $ids);
    	$res=M('Action')->where($map)->delete();
    	if($res){
    		$this->success('操作成功！');
    	}else{
    		$this->error('操作失败！'.M('Action')->getError());
    	}
    }
	
	protected function parseSearchKey($key = null)
    {
        $action = MODULE_NAME . '_' . CONTROLLER_NAME . '_' . ACTION_NAME;
        $post = I('post.');
        if (empty($post)) {
            $keywords = cookie($action);
        } else {
            $keywords = $post;
            cookie($action, $post);
            $_GET['page'] = 1;
        }

        if (!$_GET['page']) {
            cookie($action, null);
            $keywords = null;
        }
        return $key ? $keywords[$key] : $keywords;
    }

    /**
     * 新增行为
     *  
     */
    public function addAction()
    {
        $this->meta_title = '新增行为';

        $module = D('Module')->getAll();
		$list = M('AuthGroup')->where(array('status' => 1))->order('id asc')->select();
        $usergroups = array();
        foreach ($list as $group) {
            $usergroups[] = $group;
        }
		$this->assign('usergroups', $usergroups);
        $this->assign('module',$module);
        $this->assign('data', null);
        $this->display('editaction');
    }

    /**
     * 编辑行为
     *  
     */
    public function editAction()
    {
        $id = I('get.id');
        empty($id) && $this->error('参数不能为空！');
        $data = M('Action')->field(true)->find($id);

        $module = D('Module')->getAll();
		$list = M('AuthGroup')->where(array('status' => 1))->order('id asc')->select();
        $usergroups = array();
        foreach ($list as $group) {
            $usergroups[] = $group;
        }
		$this->assign('usergroups', $usergroups);
        $this->assign('module',$module);
        $this->assign('data', $data);
        $this->meta_title = '编辑行为';
        $this->display();
    }

    /**
     * 更新行为
     *  
     */
    public function saveAction()
    {
        $res = D('Action')->update();
        if (!$res) {
            $this->error(D('Action')->getError());
        } else {
            $this->success($res['id'] ? '更新成功！' : '新增成功！', Cookie('__forward__'));
        }
    }

    /**
     * 会员状态修改
     */
    public function changeStatus($method = null)
    {
        $id = array_unique((array)I('ids', 0));
        if (in_array(C('USER_ADMINISTRATOR'), $id)) {
            $this->error("不允许对超级管理员执行该操作!");
        }
        $id = is_array($id) ? implode(',', $id) : $id;
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }
        $umap['id'] = array('in', $id);
        switch (strtolower($method)) {
            case 'forbiduser':
            	$this->forbid('UcenterMember', array($umap));
                break;
            case 'resumeuser':
            	$this->resume('UcenterMember', array($umap));
                break;
            case 'deleteuser':
            	$this->delete('UcenterMember', array($umap));
                break;
            default:
                $this->error('参数非法');

        }
    }


    /**
     * 获取用户注册错误信息
     * @param  integer $code 错误编码
     * @return string        错误信息
     */
    private function showRegError($code = 0)
    {
        switch ($code) {
            case -1:
                $error = '用户名长度必须在'.modC('USERNAME_MIN_LENGTH',2,'USERCONFIG').'-'.modC('USERNAME_MAX_LENGTH',32,'USERCONFIG').'个字符之间！';
                break;
            case -2:
                $error = '用户名被禁止注册！';
                break;
            case -3:
                $error = '用户名被占用！';
                break;
            case -4:
                $error = '密码长度必须在6-30个字符之间！';
                break;
            case -5:
                $error = '邮箱格式不正确！';
                break;
            case -6:
                $error = '邮箱长度必须在1-32个字符之间！';
                break;
            case -7:
                $error = '邮箱被禁止注册！';
                break;
            case -8:
                $error = '邮箱被占用！';
                break;
            case -9:
                $error = '手机格式不正确！';
                break;
            case -10:
                $error = '手机被禁止注册！';
                break;
            case -11:
                $error = '手机号被占用！';
                break;
            case -12:
                $error = '用户名必须以中文或字母开始，只能包含拼音数字，字母，汉字！';
                break;
            default:
                $error = '未知错误';
        }
        return $error;
    }



    public function scoreList()
    {
        //读取数据
        $map = array('status' => array('GT', -1));
        $model = D('User/Score');
        $list = $model->getTypeList($map);
		foreach($list as &$v){
			switch($v['cash']){
				case 0:
					$v['cash'] = '不可提现';
					break;
				case 1:
					$v['cash'] = '可提现';
					break;
			}
			$v['exchange'] = $v['exchange'].":1";
			if($v['minimum'] == 0){
				$v['minimum'] = "不限";
			}else{
				$v['minimum'] = $v['minimum']." 元";
			}
			if($v['maxmum'] == 0){
				$v['maxmum'] = "不限";
			}else{
				$v['maxmum'] = $v['maxmum']." 元";
			}
			
		}
		unset($v);
        //显示页面
        $builder = new AdminListBuilder();
        $builder
            ->title('积分类型')
            ->suggest('id<=5的不能删除')
            ->buttonNew(U('editScoreType'))
            ->setStatusUrl(U('setTypeStatus'))->buttonEnable()->buttonDisable()->button('删除',array('class' => 'layui-btn layui-btn-xs fbutton ajax-post wzb-confirm', 'data-confirm' => '您确实要删除积分分类吗？（删除后对应的积分将会清空，不可恢复，请谨慎删除！）', 'url' => U('delType'), 'target-form' => 'ids'))
            ->button('充值',array('href' => U('recharge'),'class'=>'layui-btn layui-btn-xs fbutton'))

            ->keyId()->keyText('title', '名称')
           ->keyText('unit', '单位')->keyText('mark','标识')->keyText('cash','是否提现')->keyText('minimum','最低额度')->keyText('maxmum','最高额度')->keyText('exchange','人民币兑换比例')->keyStatus()->keyDoActionEdit('editScoreType?id=###')
           ->keyDoActionRealDel('delType?ids=###')
            ->data($list)
            ->display();
    }

    public function setTypeStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        $builder->doSetStatus('user_score_type', $ids, $status);

    }

    public function delType($ids){
        $model = D('User/Score');
        $res = $model->delType($ids);
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
    public function editScoreType(){
            $aId = I('id',0,'intval');
            $model = D('User/Score');
            if (IS_POST) {
                $data['title'] = I('post.title','','op_t');
                $data['status'] = I('post.status',1,'intval');
                $data['unit'] = I('post.unit','','op_t');
                $data['mark'] = I('post.mark','','op_t');
                $data['icon'] = I('post.icon','','op_t');
				$data['cash'] = I('post.cash','','intval');
				$data['minimum'] = I('post.minimum','','intval');
				$data['maxmum'] =  I('post.maxmum','','intval');
				$data['exchange'] = I('post.exchange','','intval');
                if ($aId != 0) {
                    $data['id'] = $aId;
                    $res = $model->editType($data);
                } else {
                    $res = $model->addType($data);
                }
                if ($res) {
                    $this->success(($aId == 0 ? '添加' : '编辑') . '成功');
                } else {
                    $this->error(($aId == 0 ? '添加' : '编辑') . '失败');
                }
            } else {
                $builder = new AdminConfigBuilder();
                if ($aId != 0) {
                    $type = $model->getType(array('id'=>$aId));
                } else {
                    $type = array('status' => 1, 'sort' => 0);
                }
                $builder->title(($aId == 0 ? '新增' : '编辑').'货币分类')->keyId()->keyText('title', '名称')
                    ->keyText('unit', '单位')
                    ->keyText('mark', '货币标识','必须为英文字母或者数字组合,货币标识为字段名称')
                    ->keySingleImage('icon', '货币图标')
					->keyRadio('cash','是否可提现','',array(1=> '可提现', 0=> '不可提现'))
					->keyText('minimum','最低提现额度','单位：元，最低必须提现多少元人民币起，注：请填写数字')
					->keyText('maxmum','最高提现额度','单位：元，最高可提现多少元人民币，注：请填写数字')
					->keyText('exchange','人民币兑换比例','多少货币兑换 1元人民币，注：请填写数字')
                    ->keySelect('status', '状态', null,  array(-1 => '删除', 0 => '禁用', 1 => '启用'))
                    ->data($type)
                    ->buttonSubmit(U('editScoreType'))->buttonBack()->display();
            }
    }
    
}
