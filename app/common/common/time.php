<?php
/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 * @author 朝兮夕兮，那你自己想想
 */
function time_format($time = NULL, $format = 'Y-m-d H:i')
{
	$time = $time === NULL ? NOW_TIME : intval($time);
	return date($format, $time);
}

/**
 * 友好的时间显示
 *
 * @param int    $sTime 待显示的时间
 * @param string $type  类型. normal | mohu | full | ymd | other
 * @param string $alt   已失效
 * @return string
 * @author 朝兮夕兮，那你自己想想
 */
function friendlyDate($sTime,$type = 'normal',$alt = 'false') {
    if (!$sTime)
        return '';
    //sTime=源时间，cTime=当前时间，dTime=时间差
    $cTime      =   time();
    $dTime      =   $cTime - $sTime;
    $dDay       =   intval(date("z",$cTime)) - intval(date("z",$sTime));
    //$dDay     =   intval($dTime/3600/24);
    $dYear      =   intval(date("Y",$cTime)) - intval(date("Y",$sTime));
    //normal：n秒前，n分钟前，n小时前，日期
    if($type=='normal'){
        if( $dTime < 60 ){
            if($dTime < 10){
                return '刚刚';    //by yangjs
            }else{
                return intval(floor($dTime / 10) * 10)."秒前";
            }
        }elseif( $dTime < 3600 ){
            return intval($dTime/60)."分钟前";
            //今天的数据.年份相同.日期相同.
        }elseif( $dYear==0 && $dDay == 0  ){
            //return intval($dTime/3600)."小时前";
            return '今天'.date('H:i',$sTime);
        }elseif($dYear==0){
            return date("m月d日 H:i",$sTime);
        }else{
            return date("Y-m-d H:i",$sTime);
        }
    }elseif($type=='mohu'){
        if( $dTime < 60 ){
            return $dTime."秒前";
        }elseif( $dTime < 3600 ){
            return intval($dTime/60)."分钟前";
        }elseif( $dTime >= 3600 && $dDay == 0  ){
            return intval($dTime/3600)."小时前";
        }elseif( $dDay > 0 && $dDay<=7 ){
            return intval($dDay)."天前";
        }elseif( $dDay > 7 &&  $dDay <= 30 ){
            return intval($dDay/7) . '周前';
        }elseif( $dDay > 30 ){
            return intval($dDay/30) . '个月前';
        }
        //full: Y-m-d , H:i:s
    }elseif($type=='full'){
        return date("Y-m-d , H:i:s",$sTime);
    }elseif($type=='ymd'){
        return date("Y-m-d",$sTime);
    }else{
        if( $dTime < 60 ){
            return $dTime."秒前";
        }elseif( $dTime < 3600 ){
            return intval($dTime/60)."分钟前";
        }elseif( $dTime >= 3600 && $dDay == 0  ){
            return intval($dTime/3600)."小时前";
        }elseif($dYear==0){
            return date("Y-m-d H:i:s",$sTime);
        }else{
            return date("Y-m-d H:i:s",$sTime);
        }
    }
}


/**
 * 友好时间显示开始
 * @author 朝兮夕兮，那你自己想想
 * @Return: array
 */
function fdate($time) {
	if (!$time)
		return false;
	$fdate = '';
	$d = time() - intval($time);
	$ld = $time - mktime(0, 0, 0, 0, 0, date('Y')); //得出年
	$md = $time - mktime(0, 0, 0, date('m'), 0, date('Y')); //得出月
	$byd = $time - mktime(0, 0, 0, date('m'), date('d') - 2, date('Y')); //前天
	$yd = $time - mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')); //昨天
	$dd = $time - mktime(0, 0, 0, date('m'), date('d'), date('Y')); //今天
	$td = $time - mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')); //明天
	$atd = $time - mktime(0, 0, 0, date('m'), date('d') + 2, date('Y')); //后天
	if ($d == 0) {
		$fdate = '刚刚';
	} else {
		switch ($d) {
			case $d < $atd:
				$fdate = date('Y年m月d日', $time);
				break;
			case $d < $td:
				$fdate = '后天' . date('H:i', $time);
				break;
			case $d < 0:
				$fdate = '明天' . date('H:i', $time);
				break;
			case $d < 60:
				$fdate = $d . '秒前';
				break;
			case $d < 3600:
				$fdate = floor($d / 60) . '分钟前';
				break;
			case $d < $dd:
				$fdate = floor($d / 3600) . '小时前';
				break;
			case $d < $yd:
				$fdate = '昨天' . date('H:i', $time);
				break;
			case $d < $byd:
				$fdate = '前天' . date('H:i', $time);
				break;
			case $d < $md:
				$fdate = date('m月d日 H:i', $time);
				break;
			case $d < $ld:
				$fdate = date('m月d日', $time);
				break;
			default:
				$fdate = date('Y年m月d日', $time);
				break;
		}
	}
	return $fdate;
}


function object2array(&$object) {
	$object =  json_decode( json_encode( $object),true);
	return  $object;
}
//友好时间显示结束


/**
 * get_some_day  获取n天前0点的时间戳
 * @param int $some  n天
 * @param null $day   当前时间
 * @return int|null
 * @author:
 */
function get_some_day($some=30,$day=null){
	$time = $day?$day:time();
	$some_day = $time-60*60*24*$some;
	$btime = date('Y-m-d'.' 00:00:00',$some_day);
	$some_day = strtotime($btime);
	return $some_day;
}

function wk($date1) {
	$datearr = explode("-",$date1);     //将传来的时间使用“-”分割成数组
	$year = $datearr[0];       //获取年份
	$month = sprintf('%02d',$datearr[1]);  //获取月份
	$day = sprintf('%02d',$datearr[2]);      //获取日期
	$hour = $minute = $second = 0;   //默认时分秒均为0
	$dayofweek = mktime($hour,$minute,$second,$month,$day,$year);    //将时间转换成时间戳
	$shuchu = date("w",$dayofweek);      //获取星期值
	$weekarray=array("星期日","星期一","星期二","星期三","星期四","星期五","星期六");
	echo $weekarray[$shuchu];
}

/**
 * 查询当天、本周、本月、本季度、本年时间
 * @param $day
 * @author 朝夕
 * @return
 */
function find_createtime($day){
	switch ($day){
		//查询当天数据
		case 1:
			$today=strtotime(date('Y-m-d 00:00:00'));
			$today_end = strtotime(date('Y-m-d 23:59:59'));
			$map['create_time'] = array('between',array($today,$today_end));
			return $map;
			break;
			
			//查询本周数据
		case 2:

		    $date=date('Y-m-d');  //当前日期
		    $first=1; //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
		    $w=date('w',strtotime($date));  //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
		    $now_start=date('Y-m-d',strtotime("$date -".($w ? $w - $first : 6).' days')); //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
		    $now_s = strtotime("$date -".($w ? $w - $first : 6).' days');
		    $now_end=strtotime("$now_start +6 days")+86399;  //本周结束日期
			$map['create_time'] = array('between',array($now_s,$now_end));
			return $map;
			break;

			//查询本月数据
		case 3:
		    $beginThismonth=mktime(0,0,0,date('m'),1,date('Y'));
		    $endThismonth=mktime(23,59,59,date('m'),date('t'),date('Y'));
			$map['create_time'] = array('between',array($beginThismonth,$endThismonth));
			return $map;
			break;

			//查询本季度数据
		case 4:
			$month=date('m');
			if($month==1 || $month==2 ||$month==3){
				$start=strtotime(date('Y-01-01 00:00:00'));
				$end=strtotime(date("Y-03-31 23:59:59"));
			}elseif($month==4 || $month==5 ||$month==6){
				$start=strtotime(date('Y-04-01 00:00:00'));
				$end=strtotime(date("Y-06-30 23:59:59"));
			}elseif($month==7 || $month==8 ||$month==9){
				$start=strtotime(date('Y-07-01 00:00:00'));
				$end=strtotime(date("Y-09-30 23:59:59"));
			}else{
				$start=strtotime(date('Y-10-01 00:00:00'));
				$end=strtotime(date("Y-12-31 23:59:59"));
			}
			$map['create_time'] = array('between',array($start,$end));
			return $map;
			break;

			//查询本年度数据
		case 5:
		    $pay = 0;
		    //求得年份
		    $year = @date("Y",time());
		    //一年有多少天
		    $days = ($year % 4 == 0 && $year % 100 != 0 || $year % 400 == 0) ? 366 : 365;
		    //今年第一天的时间戳
		    $first = strtotime("$year-01-01");
		    //今年最后一天的时间戳
		    $last = strtotime("+ $days days", $first);
			$map['create_time'] = array('between',array($first,$last));
			return $map;
			break;
		//查询昨天数据
		case 6:
			$time = strtotime('-1 day', time());
			$beginTime = strtotime(date('Y-m-d 00:00:00', $time));
			$endTime = strtotime(date('Y-m-d 23:59:59', $time));
			$map['create_time'] = array('between',array($beginTime,$endTime));
			return $map;
			break;
		
			//查询上周数据
		case 7:
			// 本周一
			$thisMonday = '1' == date('w') ? strtotime('Monday', time()) : strtotime('last Monday', time());
			// 上周一
			$lastMonday = strtotime('-7 days', $thisMonday);
			$beginTime = strtotime(date('Y-m-d 00:00:00', $lastMonday));
			$endTime = strtotime(date('Y-m-d 23:59:59', strtotime('last sunday', time())));
			$map['create_time'] = array('between',array($beginTime,$endTime));
			return $map;
			break;
		
			//查询上月数据
		case 8:
			$ts = intval(time());
			$oneMonthAgo = mktime(0, 0, 0, date('n', $ts) - 1, 1, date('Y', $ts));
			$year = date('Y', $oneMonthAgo);
			$month = date('n', $oneMonthAgo);
			$start = strtotime(date('Y-m-1 00:00:00', strtotime($year . "-{$month}-1")));
			$end = strtotime(date('Y-m-t 23:59:59', strtotime($year . "-{$month}-1")));
			$map['create_time'] = array('between',array($start,$end));
			return $map;
			break;
		
			//查询上季度数据
		case 9:
			$ts = intval(time());
 
		    $threeMonthAgo = mktime(0, 0, 0, date('n', $ts) - 3, 1, date('Y', $ts));
		    $year = date('Y', $threeMonthAgo);
		    $month = date('n', $threeMonthAgo);
		    $startMonth = intval(($month - 1)/3)*3 + 1; // 上季度开始月份
		    $endMonth = $startMonth + 2; // 上季度结束月份
		    $start = strtotime(date('Y-m-1 00:00:00', strtotime($year . "-{$startMonth}-1")));
		    $end = strtotime(date('Y-m-t 23:59:59', strtotime($year . "-{$endMonth}-1")));
			$map['create_time'] = array('between',array($start,$end));
			return $map;
			break;
		
			//查询上年度数据
		case 10:
			$beginTime = strtotime(date('Y-m-d 00:00:00', mktime(0, 0,0, 1, 1, date('Y', time()-365*24*3600))));
			$endTime = strtotime(date('Y-m-d 23:39:59', mktime(0, 0, 0, 12, 31, date('Y', time()-365*24*3600))));
			$map['create_time'] = array('between',array($beginTime,$endTime));
			return $map;
			break;
			//全部数据
		default:
			return $map;
			break;
	}
}