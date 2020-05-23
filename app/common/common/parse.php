<?php
/**
 * 限制字符串长度
 * @param        $str
 * @param int $length
 * @param string $ext
 * @return string
 */
function getShort($str, $length = 40, $ext = '')
{
    $str = htmlspecialchars($str);
    $str = strip_tags($str);
    $str = htmlspecialchars_decode($str);
    $strlenth = 0;
    $out = '';
    preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/", $str, $match);
    foreach ($match[0] as $v) {
        preg_match("/[\xe0-\xef][\x80-\xbf]{2}/", $v, $matchs);
        if (!empty($matchs[0])) {
            $strlenth += 1;
        } elseif (is_numeric($v)) {
            //$strlenth +=  0.545;  // 字符像素宽度比例 汉字为1
            $strlenth += 0.5; // 字符字节长度比例 汉字为1
        } else {
            //$strlenth +=  0.475;  // 字符像素宽度比例 汉字为1
            $strlenth += 0.5; // 字符字节长度比例 汉字为1
        }

        if ($strlenth > $length) {
            $output .= $ext;
            break;
        }

        $output .= $v;
    }
    return $output;
}


/**带省略号的限制字符串长
 * @param $str
 * @param $num
 * @return string
 */
function getShortSp($str, $num)
{
    if (utf8_strlen($str) > $num) {
        $tag = '...';
    }
    $str = getShort($str, $num) . $tag;
    return $str;
}

function utf8_strlen($string = null)
{
// 将字符串分解为单元
    preg_match_all("/./us", $string, $match);
// 返回单元个数
    return count($match[0]);
}


/**
 * @param $content
 * @return mixed|string
 */
function parse_popup($content)
{
	$face = '{"[微笑]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/5c/huanglianwx_thumb.gif","[嘻嘻]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/0b/tootha_thumb.gif","[哈哈]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6a/laugh.gif","[可爱]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/14/tza_thumb.gif","[可怜]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/af/kl_thumb.gif","[挖鼻]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/0b/wabi_thumb.gif","[吃惊]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/f4/cj_thumb.gif","[害羞]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6e/shamea_thumb.gif","[挤眼]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/c3/zy_thumb.gif","[闭嘴]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/29/bz_thumb.gif","[鄙视]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/71/bs2_thumb.gif","[爱你]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6d/lovea_thumb.gif","[泪]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/9d/sada_thumb.gif","[偷笑]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/19/heia_thumb.gif","[亲亲]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/8f/qq_thumb.gif","[生病]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/b6/sb_thumb.gif","[太开心]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/58/mb_thumb.gif","[白眼]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d9/landeln_thumb.gif","[右哼哼]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/98/yhh_thumb.gif","[左哼哼]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6d/zhh_thumb.gif","[嘘]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/a6/x_thumb.gif","[衰]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/af/cry.gif","[委屈]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/73/wq_thumb.gif","[吐]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/9e/t_thumb.gif","[哈欠]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/cc/haqianv2_thumb.gif","[抱抱]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/27/bba_thumb.gif","[怒]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/7c/angrya_thumb.gif","[疑问]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/5c/yw_thumb.gif","[馋嘴]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/a5/cza_thumb.gif","[拜拜]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/70/88_thumb.gif","[思考]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/e9/sk_thumb.gif","[汗]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/24/sweata_thumb.gif","[困]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/40/kunv2_thumb.gif","[睡]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/96/huangliansj_thumb.gif","[钱]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/90/money_thumb.gif","[失望]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/0c/sw_thumb.gif","[酷]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/40/cool_thumb.gif","[色]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/20/huanglianse_thumb.gif","[哼]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/49/hatea_thumb.gif","[鼓掌]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/36/gza_thumb.gif","[晕]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d9/dizzya_thumb.gif","[悲伤]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/1a/bs_thumb.gif","[抓狂]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/62/crazya_thumb.gif","[黑线]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/91/h_thumb.gif","[阴险]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6d/yx_thumb.gif","[怒骂]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/60/numav2_thumb.gif","[互粉]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/89/hufen_thumb.gif","[心]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/40/hearta_thumb.gif","[伤心]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/ea/unheart.gif","[猪头]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/58/pig.gif","[熊猫]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6e/panda_thumb.gif","[兔子]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/81/rabbit_thumb.gif","[ok]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d6/ok_thumb.gif","[耶]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d9/ye_thumb.gif","[good]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d8/good_thumb.gif","[NO]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/ae/buyao_org.gif","[赞]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d0/z2_thumb.gif","[来]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/40/come_thumb.gif","[弱]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d8/sad_thumb.gif","[草泥马]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/7a/shenshou_thumb.gif","[神马]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/60/horse2_thumb.gif","[囧]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/15/j_thumb.gif","[浮云]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/bc/fuyun_thumb.gif","[给力]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/1e/geiliv2_thumb.gif","[围观]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/f2/wg_thumb.gif","[威武]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/70/vw_thumb.gif","[奥特曼]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/bc/otm_thumb.gif","[礼物]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/c4/liwu_thumb.gif","[钟]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d3/clock_thumb.gif","[话筒]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/9f/huatongv2_thumb.gif","[蜡烛]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d9/lazhuv2_thumb.gif","[蛋糕]":"http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/3a/cakev2_thumb.gif"}';
	$face_arr = json_decode($face,true);
    $content = replace_attr($content);
    preg_match_all('/<img src=\"(.*?)\"/', $content, $img_src);
    preg_match_all('/<img src=\".*?\/>/', $content, $img_tag);
	if(!empty($img_tag[0])){
		foreach ($img_tag[0] as $k => &$v) {
			$content = str_replace($v, '<a class="popup" href="' . $img_src[1][$k] . '" title="点击查看大图">' . $v . '</a>', $content);
		}
		$content = '  <div class="popup-gallery">' . $content . '</div>';
	}
	//编译  img 和 face
	preg_match_all('/face\[(.*?)\]/', $content, $face_cont);
	preg_match_all('/face\[.*?\]/', $content, $face_tag);
	if(!empty($face_tag[0])){
		foreach($face_tag[0] as $key => $v){
			$content = str_replace($v, '<img class="face" src="' . $face_arr["[".$face_cont[1][$key]."]"] . '" />', $content);
		}
	}
	preg_match_all('/img\[(.*?)\]/', $content, $img_cont);
	preg_match_all('/img\[.*?\]/', $content, $img_tag);
	if(!empty($img_tag[0])){
		foreach($img_tag[0] as $key => $v){
			$content = str_replace($v, '<img src="' . $img_cont[1][$key] . '" />', $content);
		}
	}
    return $content;
}

function parse_pan($content){
	preg_match_all('/pan\[(.*?)\]/', $content, $pan_cont);
	if(!empty($pan_cont[0])){
		foreach($pan_cont[0] as $key => $v){
			$content_new = '';
			$tmp_data = explode(',',$pan_cont[1][$key]);
			$content_new .= '<div class="attachbox">';
			$content_new .= '<div style="clear:both; width:100%;text-decoration:none;" class="tab_button">';    
			$content_new .= '<div class="button">';
			$content_new .= '<a href="javascript:void(0);" title="'.$tmp_data[2].'" class="paybox" data-fid="'.$tmp_data[1].'" rel="nofollow" style="text-decoration:none;"><img src="/public/images/zip.gif" class="vm" alt="" border="0"><font style="color:#fff !important;">请点击此处下载</font></a>';
			$content_new .= '<p class="top">请先注册会员后在进行下载</p>';
			$content_new .= '<p class="bottom">已注册会员，请先登录后下载</p>';
			$content_new .= '</div>';
			
			$content_new .= '<div class="buttonright"><span style="white-space: nowrap;"><em>提取码：</em>&nbsp;'.$tmp_data[3].'</span><br>';
			$content_new .= '<em>下载次数：</em>'.$tmp_data[6].' &nbsp;&nbsp; <em>文件名：</em>'.$tmp_data[2].'&nbsp;&nbsp;<em>售价：</em>'.$tmp_data[4].' '.$tmp_data[7].' <br>';
			$content_new .= '<em>下载权限：</em><a target="_blank" href="" rel="nofollow" class="xi2" style="text-decoration:none;"><font style="font-size:16px;color:blue;font-weight:bold">'.$tmp_data[8].'&nbsp;</font></a> 以上或 <a href="#" target="_blank" rel="nofollow" style="text-decoration:none;"><font style="color:#CC0000 !important; font-size:16px;font-weight:bold">'.$tmp_data[9].'</font></a> &nbsp;'.$tmp_data[10].'</div>';
			$content_new .= '</div>';
			$content_new .= '</div>';
			$content_new .= '<br>';
			$content = str_replace($v, $content_new, $content);
		}
	}
	return $content;
}

function replace_attr($content)
{
    // 阻止代码部分被过滤 过滤前
    preg_match_all('/\<pre .*?\<\/pre\>/si',$content,$matches);
    $pattens=array();
    foreach($matches[0] as $key=>$val){
        $pattens[$key]='{$pre}_'.$key;
        $content=str_replace($val,$pattens[$key],$content);
    }
    //阻止代码部分被过滤 过滤前end

    $content = preg_replace("/class=\".*?\"/si", "", $content);
    $content = preg_replace("/id=\".*?\"/si", "", $content);
    $content = closetags($content);

    //阻止代码部分被过滤 过滤后
    $content=str_replace($pattens,$matches[0],$content);
    //阻止代码部分被过滤 过滤后end
    return $content;

}

function closetags($html)
{
    preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
    $openedtags = $result[1];

    preg_match_all('#</([a-z]+)>#iU', $html, $result);
    $closedtags = $result[1];
    $len_opened = count($openedtags);

    if (count($closedtags) == $len_opened) {
        return $html;
    }
    $openedtags = array_reverse($openedtags);
    $openedtags=array_diff($openedtags,array('br'));
    for ($i = 0; $i < $len_opened; $i++) {
        if (!in_array($openedtags[$i], $closedtags)) {
            $html .= '</' . $openedtags[$i] . '>';
        } else {
            unset($closedtags[array_search($openedtags[$i], $closedtags)]);
        }
    }
    return $html;
}

/**
 * checkImageSrc  判断链接是否为图片
 * @param $file_path
 * @return bool
 * @author:
 */
function check_image_src($file_path)
{
    if (!is_bool(strpos($file_path, 'http://'))) {
        $header = curl_get_headers($file_path);
        $res = strpos($header['Content-Type'], 'image/');
        return is_bool($res) ? false : true;
    } else {
        return true;
    }
}

/**
 * filterImage  对图片src进行安全过滤
 * @param $content
 * @return mixed
 * @author:
 */
function filter_image($content)
{
    preg_match_all("/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/", $content, $arr); //匹配所有的图片
    if ($arr[1]) {
        foreach ($arr[1] as $v) {
            $check = check_image_src($v);
            if (!$check) {
                $content = str_replace($v, '', $content);
            }
        }
    }
    return $content;
}

function curl_get_headers($url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_NOBODY, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	$f = curl_exec($ch);
	curl_close($ch);
	$h = explode("\n", $f);
	$r = array();
	foreach ($h as $t) {
		$rr = explode(":", $t, 2);
		if (count($rr) == 2) {
			$r[$rr[0]] = trim($rr[1]);
		}
	}
	return $r;
}


/**
 * checkHtmlTags  判断是否存在指定html标签
 * @param $content
 * @param $tags
 * @return bool
 * @author:
 */
function check_html_tags($content, $tags = array())
{
    $tags = is_array($tags) ? $tags : array($tags);
    if (empty($tags)) {
        $tags = array('script', '!DOCTYPE', 'meta', 'html', 'head', 'title', 'body', 'base', 'basefont', 'noscript', 'applet', 'object', 'param', 'style', 'frame', 'frameset', 'noframes', 'iframe');
    }
    foreach ($tags as $v) {
        $res = strpos($content, '<' . $v);
        if (!is_bool($res)) {
            return true;
        }
    }
    return false;
}

/**
 * filterBase64   对内容进行base64过滤
 * @param $content
 * @return mixed
 * @author:
 */
function filter_base64($content)
{
    preg_match_all("/data:.*?,(.*?)\"/", $content, $arr); //匹配base64编码
    if ($arr[1]) {
        foreach ($arr[1] as $v) {
            $base64_decode = base64_decode($v);
            $check = check_html_tags($base64_decode);
            if ($check) {
                $content = str_replace($v, '', $content);
            }
        }
    }
    return $content;
}
/**
 * render_video  渲染视频
 * @param $content
 * @return mixed
 * @author:
 */
function render_video($content)
{
	$content = D('Common/ContentHandler')->renderVideo($content);
	return $content;
}


function render($content){
	$content =  render_video($content);
	return $content;
}
/**
 * filter_content  过滤内容，主要用于过滤视频
 * @param $content
 * @return mixed
 * @author:
 */
function filter_content($content){
	$content = D('Common/ContentHandler')->filterHtmlContent($content);
	return $content;
}
//反编译内容编辑
function reverse_content($content){
	/* $content = strtolower($content);
	preg_match_all("/\[br\]/", $content, $arr);
	
	if($arr){
		$content = str_replace("[br]", '\n', $content);
	}
	$content = html_entity_decode($content);
	*/
	return $content; 
}
function parse_br($content){
	preg_match_all("/\[br\]/", $content, $arr);
	preg_match_all("/\[pre\]/", $content, $arr2);
	if($arr){
		$content = str_replace('[br]', '<br/>', $content);
	}
	if($arr2){
		$content = str_replace('[pre]', '<pre>', $content);
		$content = str_replace('[/pre]', '</pre>', $content);
	}
	return $content;
}
//解析回复可见内容
function parse_hide($content,$id){
	preg_match_all("/\[hide\](.*?)\[\/hide\]/si", $content, $arr);
	$uid = is_login();
	if(!empty($arr[0])){
		if($uid){
			$user_reply_uid = $uid;
			$map = array('post_id' => $id, 'status' => 1, 'uid' => $user_reply_uid);
			$replyListcount = D('ForumPostReply')->where($map)->count();
			if(empty($replyListcount)){
				$user_reply = query_user(array('nickname'), $user_reply_uid);
				foreach($arr[0] as $key => $v){
					$content = str_replace($v, '<div class="locked">'.$user_reply['nickname'].'，如果您要查看本帖隐藏内容请<a href="#reply_form">回复</a></div>', $content);
				}
			}else{
				foreach($arr[0] as $key => $v){
					$content = str_replace($v, '<div class="showhide"><h4>本帖隐藏的内容</h4><div>'.$arr[1][$key].'</div></div>', $content);
				}
			}
		}else{
			foreach($arr[0] as $key => $v){
				$content = str_replace($v, '<div class="locked">如果您要查看本帖隐藏内容请<a data-login="do_login">先登录</a></div>', $content);
			}
		}
	}
	return $content;
}