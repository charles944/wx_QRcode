<?php
namespace admin\builder;

class AdminListBuilder extends AdminBuilder 
{
    private $_title;
    private $_suggest;
    private $_keyList = array();
    private $_buttonList = array();
    private $_pagination = array();
    private $_data = array();
    private $_setStatusUrl;
    private $_searchPostUrl;
    private $_selectPostUrl;
    private $_setClearUrl;

    private $_search = array();
    private $_select = array();

	/**设置页面标题
     * @param $title 标题文本
     * @return $this
     * @auth
     */
    public function title($title)
    {
        $this->_title = $title;
        $this->meta_title=$title;
        return $this;
    }
    /**
     * suggest  页面标题边上的提示信息
     * @param $suggest
     * @return $this
     */
    public function suggest($suggest){
    	$this->_suggest = $suggest;
    	return $this;
    }
    
    /**
     * @param $url string 已被U函数解析的地址
     * @return $this
     */
    public function setStatusUrl($url)
    {
    	$this->_setStatusUrl = $url;
    	return $this;
    }
    
    /**设置回收站根据ids彻底删除的URL
     * @param $url
     * @return $this
     */
    public function setClearUrl($url)
    {
    	$this->_setClearUrl = $url;
    	return $this;
    }
    /**更新筛选搜索功能 ，修正连续提交多出N+个GET参数的BUG
     * @param $url   提交的getURL
     */
    public function setSearchPostUrl($url)
    {
    	$this->_searchPostUrl = $url;
    	return $this;
    }
    
    /**
     * 筛选下拉选择url
     * @param $url string 已被U函数解析的地址
     * @return $this
     * @author
     */
    public function setSelectPostUrl($url)
    {
        $this->_selectPostUrl = $url;
        return $this;
    }
    
    /**加入一个按钮
     * @param $title
     * @param $attr
     * @return $this
     * @auth 
     */
    public function button($title, $attr) {
        $this->_buttonList[] = array('title'=>$title, 'text'=>$title, 'attr'=>$attr);
        return $this;
    }
    /**加入新增按钮
     * @param        $href
     * @param string $title
     * @param array  $attr
     * @return AdminListBuilder
     * @auth
     */
    public function buttonNew($href, $title='新增', $attr=array()) {
        $attr['href'] = $href;
        $attr['class'] = 'layui-btn layui-btn-xs fbutton';
    	if(empty($title)){
    		$title = '<i class="layui-icon">&#xe654;</i> 新增';
    	}else{
    		$title = '<i class="layui-icon">&#xe654;</i> '.$title;
    	}
        return $this->button($title, $attr);
    }
    
    public function buttonMade($href, $title='输入按钮标题', $attr=array()) {
        $attr['href'] = $href;
        return $this->button($title, $attr);
    }
    
    public function buttonRecycle($href, $title='回收站', $attr=array()) {
    	$attr['href'] = $href;
    	if(empty($title)){
    		$title = '<i class="layui-icon">&#xe640;</i> 回收站';
    	}else{
    		$title = '<i class="layui-icon">&#xe640;</i> '.$title;
    	}
    	$attr['class'] = 'layui-btn layui-btn-xs fbutton';
    	return $this->button($title, $attr);
    }
    
    
    public function buttonEdit($href, $title='编辑', $attr=array()) {
    	$attr['href'] = $href;
    	if(empty($title)){
    		$title = '<i class="layui-icon">&#xe642;</i> 编辑';
    	}else{
    		$title = '<i class="layui-icon">&#xe642;</i> '.$title;
    	}
    	$attr['class'] = 'layui-btn layui-btn-xs fbutton';
    	return $this->button($title, $attr);
    }

    /**
     * 删除到回收站
     */
    public function buttonDelete($url=null, $title='删除', $attr=array()) {
    	if(!$url) $url = $this->_setStatusUrl;
    	if(empty($title)){
    		$title = '<i class="layui-icon">&#xe640;</i> 删除';
    	}else{
    		$title = '<i class="layui-icon">&#xe640;</i> '.$title;
    	}
    	$attr['class'] = 'layui-btn layui-btn-xs ajax-post fbutton confirm';
    	return $this->buttonSetStatus($url, -1, $title, $attr);
    }
    
    public function buttonRestore($url=null, $title='还原', $attr=array()) {
    	if(!$url) $url = $this->_setStatusUrl;
    	if(empty($title)){
    		$title = '<i class="layui-icon">&#xe60e;</i> 还原';
    	}else{
    		$title = '<i class="layui-icon">&#xe60e;</i> '.$title;
    	}
    	$attr['class'] = 'layui-btn layui-btn-xs ajax-post fbutton';
    	return $this->buttonSetStatus($url, 1, $title, $attr);
    }
    /**清空回收站
     * @param null $model 要清空回收站的模型
     * @return $this
     */

    public function buttonClear($url=null)
    {
    	if (!$url) $url = $this->_setClearUrl;
    	$attr['class'] = 'layui-btn layui-btn-xs ajax-post fbutton';
    	$attr['url'] = $url;
    	$attr['target-form'] = 'ids';
    	return $this->button('<i class="layui-icon">&#x1006;</i> 彻底删除', $attr);
    }

    public function ajaxButton($url,$params,$title,$attr=array()){
        $attr['class'] = 'layui-btn ajax-post fbutton';
        $attr['url'] = $this->addUrlParam($url, $params);
        $attr['target-form'] = 'ids';
        return $this->button($title, $attr);
    }
    /**加入模态弹窗按钮
     * @param $url
     * @param $params
     * @param $title
     * @param array $attr
     * @return $this
     * @author 
     */
    public function buttonModal($url, $params, $title, $attr = array())
    {
    	$attr['data-url'] = $this->addUrlParam($url, $params);
    	if(empty($title)){
    		$title = '<i class="layui-icon">&#xe609;</i> 发送';
    	}else{
    		$title = '<i class="layui-icon" >&#xe609;</i> '.$title;
    	}
    	$attr['data-role']='modal';
    	return $this->button($title, $attr);
    }
    
    public function buttonSetStatus($url, $status, $title, $attr) {
        $attr['url'] = $this->addUrlParam($url, array('status'=>$status));
        $attr['target-form'] = 'ids';
        //$attr['class'] = $attr['class'].' ajax-post';
        return $this->button($title, $attr);
    }

    public function buttonDisable($url=null, $title='禁用', $attr=array()) {
        if(!$url) $url = $this->_setStatusUrl;
        if(empty($title)){
        	$title = '<i class="layui-icon">&#x1006;</i> 禁用';
        }else{
        	$title = '<i class="layui-icon">&#x1006;</i> '.$title;
        }
        $attr['class'] = 'layui-btn layui-btn-xs ajax-post fbutton';
        return $this->buttonSetStatus($url, 0, $title, $attr);
    }

    public function buttonEnable($url=null, $title='启用', $attr=array()) {
        if(!$url) $url = $this->_setStatusUrl;
        $attr['class'] = 'layui-btn layui-btn-xs ajax-post fbutton';
        if(empty($title)){
        	$title = '<i class="layui-icon">&#xe605;</i> 启用';
        }else{
        	$title = '<i class="layui-icon">&#xe605;</i> '.$title;
        }
        return $this->buttonSetStatus($url, 1, $title, $attr);
    }


    public function buttonSort($href, $title='排序', $attr=array()) {
        $attr['href'] = $href;
        if(empty($title)){
        	$title = '<i class="layui-icon">&#xe649;</i> 排序';
        }else{
        	$title = '<i class="layui-icon">&#xe649;</i> '.$title;
        }
        $attr['class'] = 'layui-btn layui-btn-xs fbutton';
        return $this->button($title, $attr);
    }
    
    /**更新筛选搜索功能 ，修正连续提交多出N+个GET参数的BUG
     * @param string $title 标题
     * @param string $name  键名
     * @param string $type  类型，默认文本
     * @param string $des   描述
     * @param        $attr  标签文本
     * @param string $arrdb 择筛选项数据来源
     * @param string $arrvalue 筛选数据（包含ID 和value的数组:array(array('id'=>1,'value'=>'系统'),array('id'=>2,'value'=>'项目'),array('id'=>3,'value'=>'机构'));）
     * @return $this
     */
    public function search($title = '搜索', $name = 'key', $type = 'text', $des = '', $attr , $arrdb = '',$arrvalue = null)
    {
    
    	if(empty($type) && $type = 'text'){
    		$this->_search[] = array('title' => $title, 'name' => $name, 'type' => $type, 'des' => $des, 'attr' => $attr);
    		$this->setSearchPostUrl('');
    	} else {
    		if (empty($arrdb)) {
    			$this->_search[] = array('title' => $title, 'name' => $name, 'type' => $type, 'des' => $des, 'attr' => $attr,'field'=>$field,'table'=>$table,'arrvalue'=>$arrvalue);
    			$this->setSearchPostUrl('');
    		} else {
    			//TODO:呆完善如果$arrdb存在的就把当前数据表的$name字段的信息全部查询出来供筛选。
    		}
    	}
    	return $this;
    }
    
    /**
     * 添加筛选功能
     * @param string $title 标题
     * @param string $name  键名
     * @param string $type  类型，默认文本
     * @param string $des   描述
     * @param        $attr  标签文本
     * @param string $arrdb 择筛选项数据来源
     * @param string $arrvalue 筛选数据（包含ID 和value的数组:array(array('id'=>1,'value'=>'系统'),array('id'=>2,'value'=>'项目'),array('id'=>3,'value'=>'机构'));）
     * @return $this
     * @author 
     */
    public function select($title='筛选',$name = 'key', $type = 'select', $des = '', $attr ,$arrdb = '',$arrvalue = null)
    {
    	if (empty($arrdb)) {
    		$this->_select[] = array('title' => $title, 'name' => $name, 'type' => $type, 'des' => $des, 'attr' => $attr,'arrvalue'=>$arrvalue);
    	} else {
    		//TODO:呆完善如果$arrdb存在的就把当前数据表的$name字段的信息全部查询出来供筛选。
    	}
    	return $this;
    }

    
    public function key($name, $title, $type, $opt=null, $attr=null ) {
        $key = array('name'=>$name, 'title'=>$title, 'type'=>$type, 'opt'=>$opt, 'attr'=>$attr );
        $this->_keyList[] = $key;
        return $this;
    }
    /**显示纯文本
     * @param $name 键名
     * @param $title 标题
     * @return AdminListBuilder
     * @auth 
     */
    public function keyText($name, $title ,$map = null) {
        return $this->key($name, text($title), 'text', $map);
    }
    /**显示html
     * @param $name 键名
     * @param $title 标题
     * @return AdminListBuilder
     * @auth
     */
    public function keyHtml($name, $title) {
        return $this->key($name, op_h($title), 'html');
    }

    public function keyMap($name, $title, $map) {
        return $this->key($name, $title, 'map', $map);
    }

    public function keyId($name='id', $title='ID') {
        return $this->keyText($name, $title);
    }
    /**
     * 图标展示
     * @param string $name
     * @param string $title
     * @return $this
     */
    public function keyIcon($name='icon',$title='图标')
    {
    	return $this->key($name, $title, 'icon');
    }

    /**
     * @param $name
     * @param $title
     * @param $getUrl Closure|string
     * 可以是函数或U函数解析的字符串。如果是字符串，该函数将附带一个id参数
     * @author 乾坤网络有限公司
     * @return $this
     */
    public function keyLink($name, $title, $getUrl, $attr=null) {
        //如果getUrl是一个字符串，则表示getUrl是一个U函数解析的字符串
        if (is_string($getUrl)) {
            $getUrl = $this->createDefaultGetUrlFunction($getUrl);
        }

        //修整添加多个空字段时显示不正常的BUG@mingyangliu
        if (empty($name)) {
            $name = $title;
        }

        //添加key
        return $this->key($name, $title, 'link', $getUrl, $attr);        
    }

    public function keyStatus($name='status', $title='状态' , $map = array(-1=>'被删除', 0=>'已禁用', 1=>'使用中', 2=>'未审核')) {
        $attr['class'] = array(-1=>'layui-btn layui-btn-xs layui-btn-danger', 0=>'layui-btn layui-btn-xs layui-btn-danger', 1=>'layui-btn layui-btn-xs', 2=>'layui-btn layui-btn-xs layui-btn-warm');
        return $this->key($name, $title, 'status', $map, $attr);
    }

    public function keyYesNo($name, $title) {
        $map = array(0=>'不是',1=>'是');
        return $this->keymap($name, $title, $map);
    }

    public function keyBool($name, $title) {
        return $this->keyYesNo($name, $title);
    }

    public function keyTime($name, $title) {
        return $this->key($name, $title, 'time');
    }
    
    public function keyImage($name, $title)
    {
    	return $this->key($name, $title, 'image');
    }

    public function keyCreateTime($name='create_time', $title='创建时间') {
        return $this->keyTime($name, $title);
    }

    public function keyUpdateTime($name='update_time', $title='更新时间') {
        return $this->keyTime($name, $title);
    }

    public function keyUid($name='uid', $title='用户', $getUrl=null, $attr=null) {
        //如果getUrl是一个字符串，则表示getUrl是一个U函数解析的字符串
        if (is_string($getUrl)) {
            $getUrl = $this->createDefaultGetUrlFunction($getUrl);
        }
        //修整添加多个空字段时显示不正常的BUG@
        if (empty($name)) {
            $name = $title;
        }
        //添加key
        return $this->key($name, $title, 'uid', $getUrl, $attr);  
    }
    public function keyNickname($name='uid', $title, $subtitle = null)
    {
    	return $this->key($name, $title, $subtitle, 'nickname');
    }

    public function keyTitle($name='title', $title='标题') {
        return $this->keyText($name, $title);
    }
    
    //关联表字段显示+URL连接
    public function keyJoin($name, $title , $mate , $return , $model, $url ='')
    {
    	$map = array('mate'=>$mate ,'return'=>$return,'model'=>$model,'url'=>$url);
    	return $this->key($name, $title , 'Join' , $map);
    }
    
    /**
     * 模态弹窗
     * @param $getUrl
     * @param $text
     * @param $title
     * @param array $attr
     * @return $this
     */
    public function keyDoActionModalPopup($getUrl,$text,$title = '操作',$attr=array())
    {
		//获取默认getUrl函数
    	if (is_string($getUrl)) {
    		$getUrl = $this->createDefaultGetUrlFunction($getUrl);
    	}
		$attr['data-url'] = $getUrl;
		
    	if(empty($text)){
    		$text = '<i class="layui-icon">&#xe609;</i> 发送';
    	}else{
    		$text = '<i class="layui-icon" >&#xe609;</i> '.$text;
    	}
    	$attr['data-role']='modal';

    	
    	//确认已经创建了DOACTIONS字段
    	$doActionKey = null;
    	foreach ($this->_keyList as $key) {
    		if ($key['name'] === 'DOACTIONS') {
    			$doActionKey = $key;
    			break;
    		}
    	}
    	if (!$doActionKey) {
    		$this->key('DOACTIONS', $title, 'doaction', $attr);
    	}
    
    	//找出第一个DOACTIONS字段
    	$doActionKey = null;
    	foreach ($this->_keyList as &$key) {
    		if ($key['name'] == 'DOACTIONS') {
    			$doActionKey = & $key;
    			break;
    		}
    	}
    
    	//在DOACTIONS中增加action
    	$doActionKey['opt']['actions'][] = array('text' => $text, 'get_url' => $getUrl,'opt'=>$attr);
    	return $this;
    }
    
    public function keyDoAction($getUrl, $text,$attr, $title = '操作')
    {
    	if(!$attr['class']){
    		$attr['class'] = 'layui-btn layui-btn-xs hbutton btn_see';
    	}
    	//获取默认getUrl函数
    	if (is_string($getUrl)) {
    		$getUrl = $this->createDefaultGetUrlFunction($getUrl);
    	}
    	//确认已经创建了DOACTIONS字段
    	$doActionKey = null;
    	foreach ($this->_keyList as $key) {
    		if ($key['name'] === 'DOACTIONS') {
    			$doActionKey = $key;
    			break;
    		}
    	}
    	if (!$doActionKey) {
    		$this->key('DOACTIONS', $title, 'doaction', $attr);
    	}
    
    	//找出第一个DOACTIONS字段
    	$doActionKey = null;
    	foreach ($this->_keyList as &$key) {
    		if ($key['name'] == 'DOACTIONS') {
    			$doActionKey = & $key;
    			break;
    		}
    	}
    
    	//在DOACTIONS中增加action
    	$doActionKey['opt']['actions'][] = array('text' => $text, 'get_url' => $getUrl,'opt'=>$attr);
    	return $this;
    }

    public function keyDoActionEdit($getUrl, $text='编辑',$attr=array()) {
        $attr['class'] = 'layui-btn layui-btn-xs hbutton btn_edit';
        $text = '<i class="layui-icon">&#xe642;</i> '.$text;
        return $this->keyDoAction($getUrl, $text, $attr);
    }
    
    public function keyDoActionDel($getUrl, $text='',$attr=array()) {
        $attr['class'] = 'layui-btn layui-btn-xs hbutton btn_trash confirm ajax-get';
        if(empty($text)){
        	$text = '<i class="layui-icon">&#xe640;</i> 标记删除';
        }else{
        	$text = '<i class="layui-icon">&#xe640;</i> '.$text;
        }
        return $this->keyDoAction($getUrl, $text, $attr);
    }
    
    public function keyDoActionRealDel($getUrl, $text='彻底删除',$attr=array()) {
    	$attr['class'] = 'layui-btn layui-btn-xs hbutton btn_trash ajax-get';
    	$text = '<i class="layui-icon">&#xe640;</i> 彻底删除';
    	return $this->keyDoAction($getUrl, $text, $attr);
    }
    
    public function keyDoActionView($getUrl, $text='查看',$attr=array()) {
        $attr['class'] = 'layui-btn layui-btn-xs hbutton btn_see';
        $text = '<i class="layui-icon">&#xe615;</i> 查看';
        return $this->keyDoAction($getUrl, $text, $attr);
    }
    
    public function keyDoActionRestore($getUrl, $text='还原',$attr=array()) {
        $attr['class'] = 'layui-btn layui-btn-xs hbutton btn_trash ajax-get';
        $text = '<i class="fa fa-reply"></i> '.$text;
        return $this->keyDoAction($getUrl, $text, $attr);
    }

    public function keyTruncText($name, $title, $length) {
        return $this->key($name, $title, 'trunktext', $length);
    }

    /**
     * 不要给listRows默认值，因为开发人员很可能忘记填写listRows导致翻页不正确
     * @param $totalCount
     * @param $listRows
     * @return $this
     */
    public function pagination($totalCount, $listRows)
    {
        $this->_pagination = array('totalCount' => $totalCount, 'listRows' => $listRows);
        return $this;
    }

    public function data($list) {
        $this->_data = $list;
        return $this;
    }

/**
     * $solist 判断是否属于选择返回数据的列表页，如果是在列表页->display('admin_solist');@mingyangliu
     * */
    public function display($solist = '')
    {
        //key类型的等价转换
        //map转换成text
        $this->convertKey('map', 'text', function ($value, $key) {
            return $key['opt'][$value];
        });

        //uid转换成text
        $this->convertKey('uid', 'text', function ($value, $key, $item) {
        	if($value ==0){
        		return '-';
        	}else{
				$value = htmlspecialchars($value);
				$getUrl = $key['opt'];
				if(!empty($getUrl)){
					$url = $getUrl($item);
				}else{
					$url = "";
				}
				//允许字段为空，如果字段名为空将标题名填充到A变现里
				$values = query_user(array('nickname', 'uid', 'space_url'), $value);
				if(!$values){
					return "<a href=\"$url\" target=\"_blank\" class=\"".$key['attr']['class']."\">".$key['title']."</a>";
				} else {
					return "<a href=\"$url\" target=\"_blank\" class=\"".$key['attr']['class']."\">[{$values[uid]}]" . $values['nickname'] ."</a>";
				}
        	}
            
        });

        //nickname转换成text
        $this->convertKey('nickname', 'text', function ($value) {
        	
            $value = query_user(array('nickname', 'uid', 'space_url'), $value);exit;
            return "<a href='" . $value['space_url'] . "' target='_blank'>[{$value[uid]}]" . $value['nickname'] . '</a>';
        });

        //time转换成text
        $this->convertKey('time', 'text', function ($value) {
            if($value!=0){
                return time_format($value);
            }
            else{
                return '-';
            }
        });

        //trunctext转换成text
        $this->convertKey('trunktext', 'text', function ($value, $key) {
            $length = $key['opt'];
            return msubstr($value, 0, $length);
        });

        //text转换成html
        $this->convertKey('text', 'html', function ($value, $key) {
			if(!empty($key['opt'])){
				return $key['opt'][$value];
			}else{
				return $value;
			}
        });

        //link转换为html
        $this->convertKey('link', 'html', function ($value, $key, $item) {
            $value = htmlspecialchars($value);
            $getUrl = $key['opt'];
            $url = $getUrl($item);
            //允许字段为空，如果字段名为空将标题名填充到A变现里
            if(!$value){
                return "<a href=\"$url\" target=\"_blank\" class=\"".$key['attr']['class']."\">".$key['title']."</a>";
            } else {
                return "<a href=\"$url\" target=\"_blank\" class=\"".$key['attr']['class']."\">$value</a>";
            }
        });

        //如果icon为空
        $this->convertKey('icon', 'html', function ($value, $key, $item) {
            $value = htmlspecialchars($value);
            if($value==''){
                $html="无";
            }else{
                $html="<i class=\"$value\"></i> $value";
            }
            return $html;
        });

        //image转换为图片

        $this->convertKey('image', 'html', function ($value, $key, $item) {
            if(intval($value)){//value是图片id
                $value = htmlspecialchars($value);
                $sc_src = get_cover($value, 'path');

                $src = getThumbImageById($value, auto, auto);
                $sc_src = $sc_src == '' ? $src : $sc_src;
                $html="<div class='popup-gallery'><a title=\"查看大图\" href=\"$sc_src\"><img src=\"$sc_src\"/ style=\"max-width:25px;border-radius:5px; -moz-border-radius: 5px;-webkit-border-radius: 5px;\"></a></div>";
            }else{//value是图片路径
                $sc_src=$value;
                $html="<div class='popup-gallery'><a title=\"查看大图\" href=\"$sc_src\"><img src=\"$sc_src\"/ style=\"border-radius:100%;max-width:100px;\"></a></div>";
            }
            return $html;
        });

        //doaction转换为html
        $this->convertKey('doaction', 'html', function ($value, $key, $item) {
            $actions = $key['opt']['actions'];
            $result = array();
            foreach ($actions as $action) {
                $getUrl = $action['get_url'];
                $linkText = $action['text'];
                $url = $getUrl($item);
                if(isset($action['opt'])){
                    $content = array();
                    foreach($action['opt'] as $key=>$value) {
                        $value = htmlspecialchars($value);
                        $content[] = "$key=\"$value\"";
                    }
                    $content = implode(' ', $content);
                    if(isset($action['opt']['data-role'])&&$action['opt']['data-role']=="modal"){//模态弹窗
                        $result[] = "<a href=\" javascrapt:void(0);\" ".$content.">$linkText</a>";
                    }else{
                        $result[] = "<a href=\"$url\" ".$content.">$linkText</a>";
                    }
                }else{
                    $result[] = "<a href=\"$url\">$linkText</a>";
                }
            }
            return implode(' ', $result);
        });

        //Join转换为html
        $this->convertKey('Join', 'html', function ($value,$key) {
            if($value!=0){
                $val = get_table_field($value, $key['opt']['mate'], $key['opt']['return'] , $key['opt']['model']);
                if(!$key['opt']['url']){
                    return $val;
                } else {
                    $urld = U($key['opt']['url'],array($key['opt']['return']=>$value));
                    return "<a href=\"$urld\">$val</a>";
                }
            }
            else{
                return '-';
            }  
        });

        //status转换为html
        $setStatusUrl = $this->_setStatusUrl;
        $that = & $this;
        $this->convertKey('status', 'html', function ($value, $key, $item) use ($setStatusUrl, $that) {
            //如果没有设置修改状态的URL，则直接返回文字
            $map = $key['opt'];
            $text = $map[$value];
            if (!$setStatusUrl) {
                return $text;
            }

            //返回带链接的文字
            $switchStatus = $value == 1 ? 0 : 1;
            $url = $that->addUrlParam($setStatusUrl, array('status' => $switchStatus, 'ids' => $item['id']));
            return "<a href=\"{$url}\" class=\"ajax-get ".$key['attr']['class'][$value]."\">$text</a>";
        });

        //如果html为空
        $this->convertKey('html', 'html', function ($value) {
            if ($value === '') {
                return '<span style="color:#bbb;">（空）</span>';
            }
            return $value;
        });


        //编译buttonList中的属性
        foreach ($this->_buttonList as &$button) {
            $button['tag'] = isset($button['attr']['href']) ? 'a' : 'button';
            $this->addDefaultCssClass($button);
            $button['attr'] = $this->compileHtmlAttr($button['attr']);
        }

        //生成翻页HTML代码
        C('VAR_PAGE', 'page');
        $pager = new \think\Page($this->_pagination['totalCount'], $this->_pagination['listRows'], $_REQUEST);
        $pager->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        $paginationHtml = $pager->show();

        //显示页面
        $this->assign('title', $this->_title);
        $this->assign('suggest', $this->_suggest);
        $this->assign('keyList', $this->_keyList);
        $this->assign('buttonList', $this->_buttonList);
        $this->assign('pagination', $paginationHtml);
        $this->assign('list', $this->_data);
        /*加入搜索 */
        $this->assign('searches', $this->_search);
        $this->assign('searchPostUrl', $this->_searchPostUrl);

        /*加入筛选select*/
        $this->assign('selects', $this->_select);
        $this->assign('selectPostUrl', $this->_selectPostUrl);
        //如果是选择返回数据的列表页就调用admin_solist模板文件，否则编译原有模板
        if($solist){
            parent::display('admin_solist');
        } else {
            parent::display('admin_list');
        }
    }

    public function doSetStatus($model, $ids, $status=1)
    {
    	$id = array_unique((array)$ids);
    	$rs=M($model)->where(array('id' => array('in', $id)))->save(array('status' => $status));
    	if($rs===false)
    	{
    		$this->error('状态修改失败。');
    	}
    	$this->success('状态修改成功', $_SERVER['HTTP_REFERER']);
    }

    public function doDeleteTrue($model, $ids)
    {
        $id = array_unique((array)$ids);
        $rs=M($model)->where(array('id' => array('in', $id)))->delete();
        if($rs===false)
        {
            $this->error('删除失败。');
        }
        $this->success('删除成功', $_SERVER['HTTP_REFERER']);
    }

    private function convertKey($from, $to, $convertFunction)
    {
    	foreach ($this->_keyList as &$key) {
    		if ($key['type'] == $from) {
    			$key['type'] = $to;
    			foreach ($this->_data as &$data) {
    				$value = & $data[$key['name']];
    				$value = $convertFunction($value, $key, $data);
    				unset($value);
    			}
    			unset($data);
    		}
    	}
    	unset($key);
    }
    
    private function addDefaultCssClass(&$button)
    {
    	if (!isset($button['attr']['class'])) {
    		$button['attr']['class'] = 'layui-btn layui-btn-xs';
    	} else {
    		$button['attr']['class'] .= ' layui-btn layui-btn-xs';
    	}
    }

/**
     * @param $pattern U函数解析的URL字符串，例如 Admin/Test/index?test_id=###
     * Admin/Test/index?test_id={other_id}
     * ###将被id替换
     * {other_id}将被替换
     * @return callable
     */
    private function createDefaultGetUrlFunction($pattern)
    {
        $explode = explode('|',$pattern);
        $pattern = $explode[0];
        $fun = empty($explode[1])?'U':$explode[1];
        return function ($item) use ($pattern,$fun) {
            $pattern = str_replace('###', $item['id'], $pattern);
			$pattern = str_replace('[uid]', $item['uid'], $pattern);
            //调用ThinkPHP中的解析引擎解析变量
            $view = new \think\View();
            $view->assign($item);
            $pattern = $view->fetch('', $pattern);
            return $fun($pattern);
        };
    }

    public function addUrlParam($url, $params)
    {
        if (strpos($url, '?') === false) {
            $seperator = '?';
        } else {
            $seperator = '&';
        }
        $params = http_build_query($params);
        return $url . $seperator . $params;
    }

    /**自动处理清空回收站
     * @param string $model 要清空的模型
     * @auth 
     */
    public function clearTrash($model = '')
    {
        if (IS_POST) {
            if ($model != '') {
				$aIds = I('post.ids', array());
                if (!empty($aIds)) {
                    $map['id'] = array('in', $aIds);
                } else {
                    $map['status'] = -1;
                }
                $result = D($model)->where($map)->delete();
                if ($result) {
                    $this->success('成功清空回收站，共删除 ' . $result . ' 条记录。');
                }
                $this->error('回收站是空的，未能删除任何东西。');
            } else {
                $this->error('请选择要清空的模型。');
            }
        }
    }
    /**执行彻底删除数据，只适用于无关联的数据表
     * @param $model
     * @param $ids
     */
    public function doClear($model, $ids)
    {
        $ids = is_array($ids) ? $ids : explode(',', $ids);
        M($model)->where(array('id' => array('in', $ids)))->delete();
        $this->success('彻底删除成功', $_SERVER['HTTP_REFERER']);
    }

    /**
     * keyLinkByFlag  带替换表示的链接
     * @param        $name
     * @param        $title
     * @param        $getUrl
     * @param string $flag
     * @return $this
     */
    public function keyLinkByFlag($name, $title, $getUrl, $flag = 'id')
    {

        //如果getUrl是一个字符串，则表示getUrl是一个U函数解析的字符串
        if (is_string($getUrl)) {
            $getUrl = $this->ParseUrl($getUrl, $flag);
        }

        //添加key
        return $this->key($name, $title, 'link', $getUrl);
    }

    /**解析Url
     * @param $pattern URL文本
     * @param $flag
     * @return callable
     * @auth 
     */
    private function ParseUrl($pattern, $flag)
    {
        return function ($item) use ($pattern, $flag) {

            $pattern = str_replace('###', $item[$flag], $pattern);
            //调用ThinkPHP中的解析引擎解析变量
            $view = new \think\View();
            $view->assign($item);
            $pattern = $view->fetch('', $pattern);
            return U($pattern);
        };
    }

}