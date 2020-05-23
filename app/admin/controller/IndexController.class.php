<?php
namespace admin\controller;

/**
 * 后台首页控制器
 * @author 乾坤网络有限公司
 */
class IndexController extends AdminController 
{
    /**
     * 后台首页
     * @author 乾坤网络有限公司
     */
    public function index(){

        if(UID){
            $this->meta_title = '首页';
			$map['uid'] = UID;
			/* $today_stime = strtotime(date('Y-m-d'));
			$today_etime = $today_stime+24*3600;
			$map['start'] = array('between',array($today_stime,$today_etime));
			$calendarcount = M('Calendar')->where($map)->count();
			$this->assign('_calendarcount',$calendarcount); */
			
			/* $inboxmap['to_uid'] = UID;
			$inboxmap['is_read'] = 0;
			$inboxcount = M('Message')->where($inboxmap)->count();
			$this->assign('_inboxcount',$inboxcount); */
			
			//总会员
			$user_total = M('UcenterMember')->count();
			$user_total = empty($user_total) ? 0 : $user_total;
			$this->assign('_user_total', $user_total);
			
			//月会员
			$beginThismonth=mktime(0,0,0,date('m'),1,date('Y'));
		    $endThismonth=mktime(23,59,59,date('m'),date('t'),date('Y'));
			$map_month['reg_time'] = array('between',array($beginThismonth,$endThismonth));
			$user_month_new = M('UcenterMember')->where($map_month)->count();
			$user_month_new = empty($user_month_new) ? 0 : $user_month_new;
			$this->assign('_user_month_new', $user_month_new);
			
			//昨日会员量
			$time = strtotime('-1 day', time());
			$beginTime = strtotime(date('Y-m-d 00:00:00', $time));
			$endTime = strtotime(date('Y-m-d 23:59:59', $time));
			$map_yest['reg_time'] = array('between',array($beginTime,$endTime));
			$user_yest_new = M('UcenterMember')->where($map_yest)->count();
			$user_yest_new = empty($user_yest_new) ? 0 : $user_yest_new;
			$this->assign('_user_yest_new', $user_yest_new);
			
			//今日会员量
			$today=strtotime(date('Y-m-d 00:00:00'));
			$today_end = strtotime(date('Y-m-d 23:59:59'));
			$map_today['reg_time'] = array('between',array($today,$today_end));
			$user_today_new = M('UcenterMember')->where($map_today)->count();
			$user_today_new = empty($user_today_new) ? 0 : $user_today_new;
			$this->assign('_user_today_new', $user_today_new);
			
			/* $payofferCount = M('user_pay_offer')->where(array('status' => 1))->SUM('pay_offer');
			$this->assign('_payoffercount', $payofferCount); */

            $this->display();
        } else {
            $this->redirect(MODULE_ALIAS.'/public/login');
        }
    }

    public function tj_member(){
    	$tmpdata = D('User/Member')->where(array('tj_province'=>array('EXP','is NULL')))->limit(100)->select();
        foreach($tmpdata as &$val){
    		if(empty($val['tj_province']) || empty($val['tj_broadband'])){
    			$tmp_address = ipfrom(long2ip($val['reg_ip']));
    			$tmp_address_arr = explode(" ",$tmp_address);
    			if(count($tmp_address_arr)>1){
    				$tmp_address_arr_0 = explode("省", $tmp_address_arr[0]);
                    if(strpos($tmp_address_arr_0[0],'市') !== false){
                        $tmp_address_arr_0[0] = str_replace('市','',$tmp_address_arr_0[0]);
                    }
    				$data['tj_province'] = $tmp_address_arr_0[0];
    				$data['tj_broadband'] = $tmp_address_arr[1];
    			}else{
    				$tmp_address_arr_0 = explode("省", $tmp_address_arr[0]);
                    if(strpos($tmp_address_arr_0[0],'市') !== false){
                        $tmp_address_arr_0[0] = str_replace('市','',$tmp_address_arr_0[0]);
                    }
    				$data['tj_province'] = $tmp_address_arr_0[0];
    			}
    			D('User/Member')->where('uid='.$val['uid'])->save($data);
    		}
    	}
    	$list = D('User/Member')->field('count(*) as value,tj_province as name')->group('tj_province')->order('value desc')->select();
    	echo json_encode($list);
    }

    public function tj_member_week(){
    	$num = 6;
    	for($i=$num; $i>=0; $i--){
            $daystr = date('Y-m-d', strtotime("-$i day"));
            $todaystr = date('m-d', strtotime("-$i day"));
            $searchstr = "TO_DAYS(from_unixtime(reg_time,'%Y-%m-%d'))=TO_DAYS('".$daystr."')";
            $memberCount = D('User/UcenterMember')->where($searchstr)->count();
            $memberDayCountArray[] = $memberCount > 0 ? $memberCount : 0;
            $memberDayArray[] = $todaystr;
    	}
    	$result['category'] = $memberDayArray;
    	$result['line'] = $memberDayCountArray;
    	echo json_encode($result);
    }

    private function my_json_decode($str) {
        $str = preg_replace('/"(\w+)"(\s*:\s*)/is', '$1$2', $str);   //去掉key的双引号
        return $str;
	}

}
