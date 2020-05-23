<?php
/**
 * 控制器
 * Created by 朝兮夕兮.
 */
namespace admin\controller;

use admin\builder\AdminConfigBuilder;
use admin\builder\AdminListBuilder;
use admin\builder\AdminTreeListBuilder;
use vendor\PHPExcel;

class PayController extends AdminController
{

    protected $memberModel;
    protected $ucentermemberModel;
    protected $paylogModel;
    protected $payofferModel;

    function _initialize()
    {
        $this->memberModel = D('User/Member');
        $this->ucentermemberModel = D('User/UcenterMember');
        $this->paylogModel = D('User/UserPayLog');
        $this->payofferModel = D('User/UserPayOffer');
        parent::_initialize();
    }
    
    public function cashconfig()
    {
    	$builder = new AdminConfigBuilder;
    	$data = $builder->handleConfig();
    	
    	$cash_list = D('MemberPurse')->where(array('status'=>1))->select();
    	$cashoptions = array_combine(array_column($cash_list, 'id'), array_column($cash_list, 'pursename'));
    	
    	$builder->title('提现基础配置')
        ->data($data)
		->keyRadio('CASH_ON', '开启提现', '开启前台才可提现', array(0=>'关闭',1=>'开启'))
    	->keyCheckBox('CASH_TAKE_MODE[]','固定提现方式','勾选可以用来提现的方式，可选一种或者多种，多种会员将可以选择其中一个账号来提现',$cashoptions)->keyDefault('CASH_TAKE_MODE[]',$data['CASH_TAKE_MODE'])
    	->keyText('CASH_LOWEST_LIMIT','最低提现额度','单位：元')
    	->keyRadio('CASH_SMS_ON', '提现资料填写步骤是否绑定手机','提现绑定资料时是否需要绑定手机，选择否将跳过不绑定手机', array(0=>'否',1=>'是'))
    	->keyRadio('CASH_EMAIL_ON', '提现资料填写步骤是否绑定邮箱','提现绑定资料时是否需要绑定邮箱，选择否将跳过不绑定邮箱', array(0=>'否',1=>'是'))
    	->keyRadio('CASH_TAKE_VERIFY_ON', '提现时是否再次验证手机或者邮箱','提现时在输入交易密码之后是否再次验证手机或者邮箱', array(0=>'不验证',1=>'验证'))
    	->keyRadio('CASH_TAKE_SMS_ON', '提现时验证手机或者邮箱','提现时验证手机验证码，或者验证短信验证码', array(0=>'手机验证',1=>'邮箱验证'))
    	->keyEditor('CASH_INFO','提现规则说明')
    	->group('基础配置','CASH_ON,CASH_TAKE_MODE,CASH_LOWEST_LIMIT,CASH_SMS_ON,CASH_EMAIL_ON,CASH_TAKE_VERIFY_ON,CASH_TAKE_SMS_ON,CASH_INFO')
    	->buttonSubmit()
    	->buttonBack()
    	->display();
    }
	
	public function rechargeconfig()
    {
    	$builder = new AdminConfigBuilder;
    	$data = $builder->handleConfig();
    	
		$addons = \think\Hook::get('payapi');
        $opt = array('none' => '无');
        foreach ($addons as $name) {
            if (class_exists($name)) {
                $class = new $name();
                $config = $class->getConfig();
                if ($config['switch']) {
                    $opt[$class->info['name']] = $class->info['title'];
                }
            }
        }
		
		$scoreModel = D('User/Score');
		$scores = $scoreModel->getTypeList(array('status'=>1));
		foreach ($scores as &$v) {
			$scoret[$v['mark']] = $v['title'];
		}
		unset($v);
    	//$cash_list = D('MemberPurse')->where(array('status'=>1))->select();
    	//$cashoptions = array_combine(array_column($cash_list, 'id'), array_column($cash_list, 'pursename'));
    	
    	$builder->title('充值基础配置')
		->keyRadio('CHARGE_ON', '开启充值', '开启前台才可充值', array(0=>'关闭',1=>'开启'))
		->keySelect('CHARGE_ADDONS_HOOK','调用充值插件','插件设置中开启插件，才可以在此选择',$opt)
		->keySelect('CHARGE_SCORE_TYPE','充值使用货币类型','会员充值到此货币中，注意设置货币与人民币比值',$scoret)

    	->group('基础配置','CHARGE_ON,CHARGE_ADDONS_HOOK,CHARGE_SCORE_TYPE')
    	->data($data)
    	->buttonSubmit()
    	->buttonBack()
    	->display();
    }
    
    public function recharge(){
    	$scoreTypes = D('User/Score')->getTypeList(array('status'=>1));
    	if(IS_POST){
    		$aUids = I('post.uid','','text');
    		if($aUids == '' || $aUids == NULL){
    			$this->error('请先选择账号');
    		}
    		$content = I('post.remark','','op_t');
    		$title = I('post.title','','op_t');
    		foreach($scoreTypes as $v){
    			$aAction = I('post.action_score'.$v['id'],'','op_t');
    			$aValue = I('post.value_score'.$v['id'],0,'intval');
    			if(abs($aValue) > 0){
	    			//D('User/Score')->setUserScore($aUids,$aValue,$v['mark'],$aAction);
	    			api('User/sendReward', array($aUids,0, 'pay', $aAction, $v['mark'], $aValue, $title, $content));
	    			api('User/sendPayLog', array($aUids,0, $aAction, $v['mark'], $aValue, $title, $content));
					
    			}
    		}
    		unset($v);
    		$this->success('充值成功');
    	}else{
    		$this->assign('scoreTypes',$scoreTypes);
    		$this->display();
    	}
    }
    
    public function getNickname(){
    	$uid = I('get.uid',0,'intval');
    	$username = I('get.username','','op_t');
    	$email = I('get.email','','text');
    	$mobile = I('get.mobile','','op_t');
    	if($uid){
    		$uid = $uid;
    	}elseif($username){
    		$uid = M('UcenterMember')->where(array('username'=>$username))->getField('id');
    	}elseif($email){
    		$uid = M('UcenterMember')->where(array('email'=>$email))->getField('id');
    	}elseif($mobile){
    		$uid = M('UcenterMember')->where(array('mobile'=>$mobile))->getField('id');
    	}
    
    	if($uid){
    		$user = query_user(array('nickname','uid'),$uid);
    		$this->ajaxReturn($user);
    	}else{
    		$this->ajaxReturn(null);
    	}
    
    }

    public function import(){
    	$scoreTypes = D('User/Score')->getTypeList(array('status'=>1));
    	$this->assign('scoreTypes',$scoreTypes);
        $this->display();
    }
    
    //上传方法
    public function upload()
    {
        header("Content-Type:text/html;charset=utf-8");
        $config = array(
            'maxSize'    =>    3145728,
            'rootPath'   =>    './uploads/',
            'savePath'   =>    'pay/',
            'exts'       =>    array('xls', 'xlsx', 'csv'),
        );
		//调用thinkphp上传类
        //$upload = new \think\Upload($config);// 实例化上传类
        //$info   =   $upload->upload($_FILES);
		//调用file模型上传类
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
    public function pay_import($filename, $exts='xls', $usertype='uid', $gold_type='gold_coin', $action_score = 'inc')
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
         
        for($coderow = 2; $coderow <= count($data); $coderow++){
            foreach($data[$coderow] as $k=>$v){
                //exit($k);
                switch($k){
                    case 'A':
                        if($usertype =='uid'){
                            $ColumnKey = 'uid';
                        }else{
                            $ColumnKey = 'username';
                        }
                        break;
                    case 'B':
                        $ColumnKey = $gold_type;
                        break;
                    case 'C':
                        $ColumnKey = 'remark';
                        break;
                    default:
                        break;
                }
                $arr[$coderow-1][$ColumnKey]=strtolower($v);
    
            }
        }
        $count = 0;
        foreach($arr as &$val){
            if($usertype =='uid'){
                $userinfos = $this->memberModel->where(array('uid'=> $val['uid']))->find();
            }else{
                $u = $this->ucentermemberModel->where(array('username'=> $val['username'], 'status'=>1))->find();
                $userinfos = $this->memberModel->where(array('uid'=> $u['id']))->find();
            }
            if($userinfos){
                if($val['remark'] == ''){
                    $content = getGoldOptionName($gold_type).($val[$gold_type] > 0 ? '加&nbsp;' . $val[$gold_type] : '减&nbsp;' . $val[$gold_type]);
                    $title = getGoldOptionName($gold_type).($val[$gold_type] > 0 ? '加&nbsp;' . $val[$gold_type] : '减&nbsp;' . $val[$gold_type]);
                    
                }else{
                    $content = $val['remark'];
                    $title = $val['remark'];
                }
	            //D('User/Score')->setUserScore($aUids,$aValue,$v['mark'],$aAction);
	            api('User/sendReward', array($userinfos['uid'],0, 'pay', $action_score, $gold_type, $val[$gold_type], $title, $content));
	            api('User/sendPayLog', array($userinfos['uid'],0, $action_score, $gold_type, $val[$gold_type], $title, $content));
                $count = $count+1;
            }
        }
    
        $this->success('共'.count($arr).'条需要导入的数据，成功导入'.$count.'条会员充值记录！',$_SERVER['HTTP_REFERER']);
    
    }
    
    public function paylog($page=1, $r = 20){
        $aCate=I('cate',0,'op_t');
        if($aCate){
        	$map['pay_type']=$aCate;
        }
        $aDead=I('dead',0,'intval');
        switch($aDead){
        	case 0:
        		$map['status'] = array('egt','-1');
        		break;
        	case 1:
        		$map['status'] = '1';
        		break;
        	case 2:
        		$map['status'] = '2';
        		break;
        	case 3:
        		$map['status'] = '0';
        		break;
        	case 4:
        		$map['status'] = '-1';
        		break;
        	default:
        		$map['status'] = '1';
        		break;
        }
        $aTitle=I('title','','op_t');
        if($aTitle){
        	$map['title'] = array('like','%'.$aTitle.'%');
        }
        $aPos=I('pos',0,'intval');
        switch($aPos){
        	case 0:
        		$map['type'] = array('egt','-1');
        		break;
        	case 1:
        		$map['type'] = 1;
        		break;
        	case 2:
        		$map['type'] = 2;
        		break;
        	case 3:
        		$map['type'] = 0;
        		break;
        	
        	default:
        		break;
        }
        
        list($list,$totalCount)=$this->getListByPage('UserPayLog',$map,$page,'id desc','*',$r);

        // 显示页面
        $builder = new AdminListBuilder();
        $builder->meta_title = '充值记录列表';
        
        foreach ($list as &$val) {
            switch($val['status']){
                case -1:
                    $val['status'] = '已取消';
                    break;
                case 0:
                    $val['status'] = '待付款／待确认';
                    break;
                case 1:
                    $val['status'] = '充值成功';
                    break;
                case 2:
                    $val['status'] = '充值失败';
                    break;
                default:
                    break;
            }
            switch($val['type']){
                case -1:
                    break;
                case 0:
                    $val['type'] = '系统充值';
                    break;
                case 1:
                    $val['type'] = '用户充值';
                    break;
                case 2:
                    $val['type'] = '应用充值';
                    break;
                default:
                    break;
            }
            $val['pay_type'] = D('User/Score')->getTypeName($val['pay_type']);
            $val['uid'] = $val['uid'].'|'.$val['username'];
        }
        unset($val);
        
        $categoryopt = D('User/Score')->getTypeList(array('status'=>1));
        foreach($categoryopt as $k=>$val){
        	$categoryopts[$k]['value'] = $val['title'];
        	$categoryopts[$k]['id'] = $val['mark'];
        }
        unset($val);
        $roption = array(array('id'=>20,'value'=>'显示20行'),array('id'=>50,'value'=>'显示50行'),array('id'=>100,'value'=>'显示100行'));
        
        
        $builder->title('充值记录列表')
        ->setSelectPostUrl(U('Pay/PayLog'))
        ->setSearchPostUrl(U('Pay/PayLog'))
        ->search('搜索', 'title', 'text', '请输入标题关键词')
        ->select('显示：','r','select','','','',$roption)
        ->select('货币类型：','cate','select','','','',array_merge(array(array('id'=>0,'value'=>'全部')),$categoryopts))
        ->select('状态：','dead','select','','','',array(array('id'=>0,'value'=>'全部'),array('id'=>1,'value'=>'充值成功'),array('id'=>2,'value'=>'充值失败'),array('id'=>3,'value'=>'待付款／待确认'),array('id'=>4,'value'=>'已取消')))
        ->select('类型：','pos','select','','','',array_merge(array(array('id'=>0,'value'=>'全部'),array('id'=>3,'value'=>'系统充值'),array('id'=>1,'value'=>'用户充值'),array('id'=>2,'value'=>'应用充值'))))
        ->keyId()
        ->keyText('uid', '会员')
        ->keyLink('title', '充值记录', 'Pay/payView?id=###')
        ->keyText('pay_type', '充值类型')
        ->keyText('pay_num', '充值金额')
        ->keyText('status', '状态')
        ->keyText('type', '类型')
        ->keyCreateTime()
        ->keyDoActionDel('Pay/setstatus?ids=###&status=-1')
        ->data($list)
        ->pagination($totalCount, $r)
        ->display();
    }

    public function setStatus($ids, $status)
    {
        $ids = is_array($ids) ? $ids : explode(',', $ids);
        M('UserPayLog')->where(array('id' => array('in', $ids)))->save(array('status' => $status));
        $this->success('设置成功', $_SERVER['HTTP_REFERER']);
    }
    
    public function payoffer($r = 100, $page=1){
    	$aCate=I('cate',0,'intval');
    	if($aCate){
    		$cates=$this->getOneCategoryList("MemberPurse",array('id'=>$aCate));
    		if(count($cates)){
    			$cates=array_column($cates,'id');
    			$cates=array_merge(array($aCate),$cates);
    			$map['purse_pid']=array('in',$cates);
    		}else{
    			$map['purse_pid']=$aCate;
    		}
    	}
    	$aDead=I('dead',0,'intval');
    	switch($aDead){
    		case 0:
    			$map['status'] = array('gt','-1');
    			break;
    		case 1:
    			$map['status'] = '1';
    			break;
    		case 2:
    			$map['status'] = '2';
    			break;
    		case 3:
    			$map['status'] = '3';
    			break;
    		case 4:
    			$map['status'] = '-1';
    			break;
    		case 5:
    			$map['status'] = '0';
    			break;
    		default:
    			$map['status'] = '1';
    			break;
    	}
    	$aTitle=I('title','','text');
    	if($aTitle){
    		$map['uid'] = array('eq',$aTitle);
    	}
		$aTitle=I('title1','','text');
    	if($aTitle){
    		$map['username'] = array('eq',$aTitle);
    	}
		$aTitle=I('title2','','text');
    	if($aTitle){
    		$map['pursecardno'] = array('eq',$aTitle);
    	}
		//$map['order_sel'] = 0;
    	list($list,$totalCount)=$this->getListByPage("UserPayOffer",$map,$page,'status asc,id desc','*',$r);
		$score = D('User/UserScoreType')->select();
		foreach($score as &$v){
			$tarray[$v['id']] = $v['title'];
		}
		unset($v);
    	foreach($list as &$v){
			$v['score_type'] = $tarray[$v['score_type']];
		}
		unset($v);
    	$categoryopt = D('MemberPurse')->where('status = 1')->select();
    	foreach($categoryopt as $k=>$val){
    		$categoryopts[$k]['value'] = $val['pursename'];
    		$categoryopts[$k]['id'] = $val['id'];
    	}
    	unset($val);
    	
    	$roption = array(array('id'=>100,'value'=>'显示100行'),array('id'=>20,'value'=>'显示20行'),array('id'=>50,'value'=>'显示50行'));
        
        // 显示页面
        $builder = new AdminListBuilder();
        $builder->meta_title = '会员提现列表';
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';
        
        foreach ($list as &$val) {
            switch($val['status']){
                case -1:
                    $val['status'] = '<span class="layui-btn layui-btn-xs layui-btn-danger">已删除</span>';
                    break;
                case 0:
                    $val['status'] = '<span class="layui-btn layui-btn-xs layui-btn-warm">待验证</span>';
                    break;
                case 1:
                    $val['status'] = '<span class="layui-btn layui-btn-xs layui-btn-warm">等待处理中</span>';
                    break;
                case 2:
                    $val['status'] = '<span class="layui-btn layui-btn-xs">处理成功</span>';
                    break;
                case 3:
                    $val['status'] = '<span class="layui-btn layui-btn-xs layui-btn-danger">已退回</span>';
                    break;
                default:
                    break;
            }
			switch($val['purse_pname']){
                case '支付宝':
                    $val['purse_pname'] = '<span class="layui-btn layui-btn-xs">支付宝</span>';
                    break;
                case '财付通':
                    $val['purse_pname'] = '<span class="layui-btn layui-btn-xs layui-btn-danger">财付通</span>';
                    break;
                default:
                    break;
            }
        }
        unset($val);
        
        $builder->title('会员提现列表')
        ->suggest('付款单：勾选要处理的会员提现申请，点击生成付款单，会将选中的会员申请 生成一张付款单，方便后期维护统计查看每天发放的金额数据，点击生成付款单，就会自动将会员的提现申请处理成成功提现状态')
        ->Button('查看付款单',array('class'=>'layui-btn layui-btn-xs fbutton','href'=>U('pay/paymentorder')));
		foreach($categoryopts as &$v){
			$builder->Button('生成今日【'.$v['value'].'】付款单',array('class'=>'layui-btn layui-btn-xs fbutton ajax-get','href'=>U('pay/dopayorder?t='.$v['id'])));
		}
        $builder->Button('生成付款单',array('class'=>'layui-btn layui-btn-xs fbutton ajax-post','href'=>U('pay/dopayorder'),'target-form'=>'ids'))
        ->Button('批量退回',array('class'=>'layui-btn layui-btn-xs fbutton ajax-post','href'=>U('Pay/setpaystatus?status=3'),'target-form'=>'ids'));
        if($aDead == 2){
        	$builder->buttonRestore(U('Pay/setpaystatus'),'批量还原');
        }else{
        	$builder->buttonDelete(U('Pay/setpaystatus'),'批量彻底删除');
        }
        $builder->setStatusUrl(U('setpaystatus'))
        ->setSelectPostUrl(U('Pay/payoffer'))
        ->setSearchPostUrl(U('Pay/payoffer'))
        ->search('搜索', 'title', 'text', '请输入用户ID')
		->search('搜索', 'title1', 'text', '请输入用户名')
		->search('搜索', 'title2', 'text', '请输入钱包账号')
        ->select('显示：','r','select','','','',$roption)
        ->select('钱包类型：','cate','select','','','',array_merge(array(array('id'=>0,'value'=>'全部')),$categoryopts))
        ->select('状态：','dead','select','','','',array(array('id'=>0,'value'=>'全部'),array('id'=>1,'value'=>'等待处理中'),array('id'=>2,'value'=>'已支付'),array('id'=>3,'value'=>'已退回'),array('id'=>4,'value'=>'已删除'),array('id'=>5,'value'=>'待验证')))
        ->keyId()
		->keyText('score_type','货币')
        ->keyText('purse_pname', '钱包')
        ->keyText('pursecardno', '钱包账号')
        ->keyText('pay_offer', '金额')
        ->keyUid('uid', '会员')//'user/mingxi?nickname=[uid]'
        ->keyText('name','姓名')
        ->keyText('status', '状态')
        ->keyCreateTime()
        ->keyDoAction('Pay/setpaystatus?ids=###&status=2','支付',array('class'=>'layui-btn layui-btn-xs ajax-get'))
        ->keyDoAction('Pay/setpaystatus?ids=###&status=3','退回',array('class'=>'layui-btn layui-btn-xs layui-btn-danger ajax-get'));
        if($aDead == 5){
        	$builder->keyDoActionRealDel('Dama/setpaystatus?ids=###&status=-1');
        }
        $builder->data($list)
		->pagination($totalCount, $r)
        ->display();
    }

    
    public function paymentorder($r = 100, $page=1){
		$aCate=I('cate',0,'intval');
    	if($aCate){
    		$cates=$this->getOneCategoryList("MemberPurse",array('id'=>$aCate));
    		if(count($cates)){
    			$cates=array_column($cates,'id');
    			$cates=array_merge(array($aCate),$cates);
    			$map['order_type']=array('in',$cates);
    		}else{
    			$map['order_type']=$aCate;
    		}
    	}
		
    	list($list,$totalCount)=$this->getListByPage("UserPayOrder",$map,$page,'status asc,id desc','*',$r);
		$score = D('User/UserScoreType')->select();
		foreach($score as &$v){
			$tarray[$v['id']] = $v['title'];
		}
		unset($v);
		$categoryopt = D('MemberPurse')->where('status = 1')->select();
    	foreach($categoryopt as $k=>$val){
    		$categoryopts[$k]['value'] = $val['pursename'];
    		$categoryopts[$k]['id'] = $val['id'];
			$purse[$val['id']]['value'] = $val['pursename'];
			$purse[$val['id']]['id'] = $val['id'];
    	}
    	unset($val);
		$purse[0]['id'] = 0;
		$purse[0]['value'] = '全部';
    	foreach($list as &$v){
			$v['score_type'] = $tarray[$v['score_type']];
			$v['order_type'] = $purse[$v['order_type']]['value']; 
			switch($v['status']){
                case -1:
                    $v['status'] = '<span class="layui-btn layui-btn-xs layui-btn-danger">已删除</span>';
                    break;
                case 0:
                    $v['status'] = '<span class="layui-btn layui-btn-xs layui-btn-warm">待验证</span>';
                    break;
                case 1:
                    $v['status'] = '<span class="layui-btn layui-btn-xs">处理成功</span>';
                    break;
                case 2:
                    $v['status'] = '<span class="layui-btn layui-btn-xs">处理成功</span>';
                    break;
                case 3:
                    $v['status'] = '<span class="layui-btn layui-btn-xs layui-btn-danger">已退回</span>';
                    break;
                default:
                    break;
            }
		}
		unset($v);
    	
    	
    	$roption = array(array('id'=>100,'value'=>'显示100行'),array('id'=>20,'value'=>'显示20行'),array('id'=>50,'value'=>'显示50行'));
        
        // 显示页面
        $builder = new AdminListBuilder();
        $builder->meta_title = '付款单管理';
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';
		$builder->title('付款单管理')
        ->Button('查看待处理提现',array('class'=>'layui-btn layui-btn-xs fbutton','href'=>U('pay/payoffer')));
		$builder->setStatusUrl(U('setpaystatus'))
        ->setSelectPostUrl(U('Pay/paymentorder'))
        ->select('显示：','r','select','','','',$roption)
        ->select('钱包类型：','cate','select','','','',array_merge(array(array('id'=>0,'value'=>'全部')),$categoryopts))
        ->keyId()
		->keyCreateTime()
		->keyDoAction('Pay/vieworder?ids=###','<i class="layui-icon">&#xe615;</i> 查看详情',array('class'=>'layui-btn layui-btn-xs hbutton btn_see'))
        ->keyDoAction('Pay/vieworder?ids=###','撤销付款单',array('class'=>'layui-btn layui-btn-xs hbutton btn_trash'))
		->keyText('order_type','支付类型')
		->keyText('order_money','付款单总额（元）')
        ->keyText('order_num', '订单集合笔数')
        ->keyText('status', '状态');
        
        $builder->data($list)
		->pagination($totalCount, $r)
        ->display();
    }
	
	public function dopayorder(){
		$ids = I('ids',0);
		$t = I('t',0);
		$categoryopt = D('MemberPurse')->where('status = 1')->select();
    	foreach($categoryopt as $k=>$val){
    		$categoryopts[$val['id']]['value'] = $val['pursename'];
    		$categoryopts[$val['id']]['id'] = $val['id'];
    	}
    	unset($val);
		if(empty($t)){
			$ids = is_array($ids) ? $ids : explode(',', $ids);
			if(empty($ids)){
				$this->error('请先选择操作数据',$_SERVER['HTTP_REFERER']);
			}
			$total_num = 0;
			$total_money = 0;
			$data = $this->payofferModel->where(array('id' => array('in',$ids)))->select();
			if(empty($data)){
				$this->error('无提现数据可操作',$_SERVER['HTTP_REFERER']);
			}else{
				foreach($data as &$v){
					$total_num = $total_num + 1;
					$total_money = $total_money + $v['pay_offer'];
					$id_arr[] = $v['id'];
				}
				$order['order_num'] = $total_num;
				$order['order_money'] = $total_money;
				$order['order_id'] = implode(',',$id_arr);
				$order['status'] = 1;
				$order['order_type'] = 0;
				$order['create_time'] = TIME();
				$oid = D('UserPayOrder')->add($order);
				foreach($data as &$v){
					if($v['status'] == 1){
						$tmp['update_time'] = TIME();
						$tmp['order_sel'] = $oid;
						$tmp['status'] = 2;
						$this->payofferModel->where('id='.$v['id'])->save($tmp);
					}else{
						$tmp['order_sel'] = $oid;
						$this->payofferModel->where('id='.$v['id'])->save($tmp);
					}
				}
				$this->success('生成付款单【#'.$oid.'】成功', $_SERVER['HTTP_REFERER']);
			}
		}else {
				//exit("1-".$t);
				$today = strtotime(date('Y-m-d 00:00:00'));
				$today_end = strtotime(date('Y-m-d 23:59:59'));
				$map['create_time'] = array('between',array($today,$today_end));
				$map['purse_pname'] = array('like','%'.$categoryopts[$t]['value'].'%');
				$map['status'] = 1;
				$map['order_sel'] = 0;
				$data = $this->payofferModel->where($map)->select();
				if(empty($data)){
					$this->error('今日无【'.$categoryopts[$t]['value'].'】提现数据可操作',$_SERVER['HTTP_REFERER']);
				}else{
					foreach($data as &$v){
						$total_num = $total_num + 1;
						$total_money = $total_money + $v['pay_offer'];
						$id_arr[] = $v['id'];
					}
					$order['order_num'] = $total_num;
					$order['order_money'] = $total_money;
					$order['order_id'] = implode(',',$id_arr);
					$order['status'] = 1;
					$order['order_type'] = $categoryopts[$t]['id'];
					$order['create_time'] = TIME();
					$oid = D('UserPayOrder')->add($order);
					foreach($data as &$v){
						if($v['status'] == 1){
							$tmp['update_time'] = TIME();
							$tmp['order_sel'] = $oid;
							$tmp['status'] = 2;
							$this->payofferModel->where('id='.$v['id'])->save($tmp);
						}else{
							$tmp['order_sel'] = $oid;
							$this->payofferModel->where('id='.$v['id'])->save($tmp);
						}
					}
					$this->success('生成今日【'.$categoryopts[$t]['value'].'】付款单【#'.$oid.'】成功', $_SERVER['HTTP_REFERER']);
				}
		}
    	
    }
	
	public function vieworder($ids){
		$ids = is_array($ids) ? $ids : explode(',', $ids);
		$data = D('UserPayOrder')->where(array('id' => array('in',$ids)))->select();
		$this->assign('data',$data);
		$this->display();
	}
    
    /**设置字段状态：删除=-1，禁用=0，启用=1
     * @param $ids
     * @param $status
     */
    public function setpaystatus($ids, $status)
    {
    	$ids = is_array($ids) ? $ids : explode(',', $ids);
		if(!empty($ids)){
			switch((int)$status){
				case 0:
					
					break;
				case 1:
					break;
				case 2:
					$data = $this->payofferModel->where(array('id' => array('in',$ids)))->select();
					foreach($data as &$v){
						if((int)$v['status'] == 1){
							$this->payofferModel->where('id='.$v['id'])->save(array('status' => 2,'update_time'=> time()));
						}
					}
					unset($v);
					break;
				case 3:
					$data = $this->payofferModel->where(array('id' => array('in',$ids)))->select();
					foreach($data as &$v){
						if((int)$v['status'] == 2 || (int)$v['status'] == 1){
							$this->payofferModel->where('id='.$v['id'])->save(array('status'=>3,'update_time'=> time()));
							$scoretp = D('User/UserScoreType')->where('id='.$v['score_type'])->find();
							$num = $v['pay_gold_coin'];
							$gold_type = $scoretp['mark'];
							$gold_name = $scoretp['title'];
							$title= L('_BACK_DRAW_').$gold_name.$num;
							$content = L('_BACK_DRAW_').$gold_name.$num;
							$r = api('User/sendReward', array($v['uid'],0, 'cash', 'inc',$gold_type ,$num, $title, $content));
						}
					}
					unset($v);
					break;
				case -1:
					$data = $this->payofferModel->where(array('id' => array('in',$ids)))->select();
					foreach($data as &$v){
						if((int)$v['status'] == 3 || (int)$v['status'] == 0){
							$this->payofferModel->where('id='.$v['id'])->delete();
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
    
    public function deloffer($ids)
    {
    	!is_array($ids)&&$ids=explode(',',$ids);
    	$map['id'] = array('in', $ids);
    	$res=$this->payofferModel->where($map)->delete();
    	if($res){
    		//action_log('dama_delete','dama',$ids,UID);
    		$this->success('操作成功！');
    
    	}else{
    		$this->error('操作失败！'.$this->payofferModel->getError());
    	}
    }

}
?>