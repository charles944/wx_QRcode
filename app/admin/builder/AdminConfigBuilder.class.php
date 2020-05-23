<?php
namespace admin\builder;

class AdminConfigBuilder extends AdminBuilder
{
    private $_title;
    private $_suggest;
    private $_keyList = array();
    private $_data = array();
    private $_buttonList = array();
    private $_savePostUrl = array();
    private $_group = array();
    private $_callback = null;

    public function title($title)
    {
        $this->_title = $title;
        $this->meta_title = $title;
        return $this;
    }
    
    /**
     * suggest  页面标题边上的提示信息
     * @param $suggest
     * @return $this
     */
    public function suggest($suggest)
    {
    	$this->_suggest = $suggest;
    	return $this;
    }
    
    public function callback($callback)
    {
    	$this->_callback = $callback;
    	return $this;
    }
    
    /**键，一般用于内部调用
     * @param      $name
     * @param      $title
     * @param null $subtitle
     * @param      $type
     * @param null $opt
     * @return $this
     * @auth
     */
    public function key($name, $title, $subtitle = null, $type, $opt = null)
    {
        $key = array('name' => $name, 'title' => $title, 'subtitle' => $subtitle, 'type' => $type, 'opt' => $opt);
        $this->_keyList[] = $key;
        return $this;
    }

    /**只读文本
     * @param      $name
     * @param      $title
     * @param null $subtitle
     * @return AdminConfigBuilder
     * @auth
     */
    public function keyHidden($name, $title, $subtitle = null)
    {
        return $this->key($name, $title, $subtitle, 'hidden');
    }
    
    public function keyLevel_gold_grow($name, $title, $subtitle)
    {
    	return $this->key($name, $title, $subtitle,'level_gold_grow');
    }
    
    public function keyMatch_level_gold($name, $title, $subtitle)
    {
    	return $this->key($name, $title, $subtitle,'match_level_gold');
    }
    
    public function keyTage($name, $title, $subtitle = null, $options)
    {
        return $this->key($name, $title, $subtitle, 'tage', $options);
    }

    /**只读文本
     * @param      $name
     * @param      $title
     * @param null $subtitle
     * @return AdminConfigBuilder
     * @auth
     */
    public function keyReadOnly($name, $title, $subtitle = null)
    {
        return $this->key($name, $title, $subtitle, 'readonly');
    }
    /**文本输入框
     * @param      $name
     * @param      $title
     * @param null $subtitle
     * @return AdminConfigBuilder
     * @auth
     */
    public function keyText($name, $title, $subtitle = null)
    {
        return $this->key($name, $title, $subtitle, 'text');
    }
    
    public function keyIcon($name, $title, $subtitle = null)
    {
    	return $this->key($name, $title, $subtitle, 'icon');
    }
    
    public function keyLabel($name, $title, $subtitle = null)
    {
    	return $this->key($name, $title, $subtitle, 'label');
    }
    
    public function keyColor($name, $title, $subtitle = null)
    {
        return $this->key($name, $title, $subtitle, 'color');
    }

    public function keyTextArea($name, $title, $subtitle = null)
    {
        return $this->key($name, $title, $subtitle, 'textarea');
    }

    public function keyInteger($name, $title, $subtitle = null)
    {
        return $this->key($name, $title, $subtitle, 'integer');
    }

    public function keyUid($name, $title, $subtitle = null)
    {
        return $this->key($name, $title, $subtitle, 'uid');
    }

    public function keyStatus($name = 'status', $title = '状态', $subtitle = null)
    {
        $map = array(-1 => '已删除', 0 => '已禁用', 1 => '使用中', 2 => '未审核');
        return $this->keyRadio($name, $title, $subtitle, $map);
    }

    public function keySelect($name, $title, $subtitle = null, $options)
    {
        return $this->key($name, $title, $subtitle, 'select', $options);
    }

    public function keyRadio($name, $title, $subtitle = null, $options)
    {
        return $this->key($name, $title, $subtitle, 'radio', $options);
    }

    public function keyCheckBox($name, $title, $subtitle = null, $options)
    {
        return $this->key($name, $title, $subtitle, 'checkbox', $options);
    }

 	public function keyEditor($name, $title, $subtitle = null, $config = '', $style = array('width' => '100%', 'height' => '400px'))
    {
        $toolbars = "toolbars:[[" . $config . "]]";
        if (empty($config)) {
            $toolbars = "toolbars: [['source', '|', 'undo', 'redo', '|',
            'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
            'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
            'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
            'directionalityltr', 'directionalityrtl', 'indent', '|',
            'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
            'link', 'unlink', 'anchor', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
            'simpleupload', 'insertimage', 'emotion', 'scrawl', 'insertvideo', 'attachment', 'insertframe', 'insertcode', 'pagebreak', 'template', 'background', '|',
            'horizontal', 'date', 'time', 'spechars', 'wordimage', '|',
            'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', '|',
            'searchreplace'
        ]]";
            
        }
        if ($config == 'all') {
            $toolbars = 'all';
        }
        $key = array('name' => $name, 'title' => $title, 'subtitle' => $subtitle, 'type' => 'editor', 'config' => $toolbars, 'style' => $style);
        $this->_keyList[] = $key;
        return $this;
    }
    /**
     * 日期选择器：支持三种类型
     * @param $name
     * @param $title
     * @param null $subtitle
     * @param string $type 类型：支持（time）（datetime，默认）(date)
     * @return $this
     */
	public function keyTime($name, $title, $subtitle = null,$type='datetime')
    {
        return $this->key($name, $title, $subtitle, $type);
    }

    public function keyCreateTime($name = 'create_time', $title = '创建时间', $subtitle = null)
    {
        return $this->keyTime($name, $title, $subtitle);
    }

    public function keyBool($name, $title, $subtitle = null)
    {
        $map = array(1 => '是', 0 => '否');
        return $this->keyRadio($name, $title, $subtitle, $map);
    }
    
    public function keySwitch($name, $title, $subtitle = null)
    {
    	$map = array(1 => '开启', 0 => '关闭');
    	return $this->keyRadio($name, $title, $subtitle, $map);
    }
	
	public function keyMultiJson($name, $title, $subtitle = null, $options)
    {
        return $this->key($name, $title, $subtitle, 'multijson', $options);
    }

    public function keyUpdateTime($name = 'update_time', $title = '修改时间', $subtitle = null)
    {
        return $this->keyTime($name, $title, $subtitle);
    }

    public function keyTitle($name = 'title', $title = '标题', $subtitle = null)
    {
        return $this->keyText($name, $title, $subtitle);
    }
    
    public function keyKanban($name, $title, $subtitle = null)
    {
    
    	return $this->key($name, $title, $subtitle, 'kanban');
    }

    public function keyId($name = 'id', $title = '编号', $subtitle = null)
    {
        return $this->keyReadOnly($name, $title, $subtitle);
    }

    public function keyMultiUserGroup($name, $title, $subtitle = null)
    {
        $options = $this->readUserGroups();
        return $this->keyCheckBox($name, $title, $subtitle, $options);
    }

    /**单文件上传
     * @param $name
     * @param $title
     * @param null $subtitle
     */
    public function keySingleFile($name, $title, $subtitle = null){
        return  $this->key($name,$title,$subtitle,'singleFile');
    }
    
    /**多文件上传
     * @param $name
     * @param $title
     * @param null $subtitle
     */
    public function keyMultiFile($name, $title, $subtitle = null){
        return   $this->key($name,$title,$subtitle,'multiFile');
    }

    public function keySingleImage($name, $title, $subtitle = null)
    {
        return $this->key($name, $title, $subtitle, 'singleImage');
    }
    
	public function keyMultiImage($name, $title, $subtitle = null, $limit = '')
    {
        return $this->key($name, $title, $subtitle, 'multiImage', $limit);
    }

    public function keySingleUserGroup($name, $title, $subtitle = null)
    {
        $options = $this->readUserGroups();
        return $this->keySelect($name, $title, $subtitle, $options);
    }

    /**
     * 添加城市选择（需安装城市联动插件）
     * @param  $title
     * @param  $subtitle
     * @return hook ChinaCity
     */
    public function keyCity($title, $subtitle)
    {
    	//修正在编辑信息时无法正常显示已经保存的地区信息
    	return $this->key(array('province', 'city', 'district'), $title, $subtitle, 'city');
    }
    /**
     * 增加数据时通过列表页选择相应的关联数据ID  -_-。sorry！表述不清楚..
     * @param  unknown $name 字段名
     * @param  unknown $title 标题
     * @param  string $subtitle 副标题（说明）
     * @param  unknown $url 选择数据的列表页地址，U方法地址'index/index'
     * @return $this
     */
    public function keyDataSelect($name, $title, $subtitle = null, $url)
    {
    	$urls = U($url, array('inputid' => $name));
    	return $this->key($name, $title, $subtitle, 'dataselect', $urls);
    }
    
    public function button($title, $attr = array())
    {
        $this->_buttonList[] = array('title' => $title, 'attr' => $attr);
        return $this;
    }

    public function buttonSubmit($url = '', $title = '确定')
    {
        if ($url == '') {
            $url = __SELF__;
        }
        $this->savePostUrl($url);

        $attr = array();
        $attr['class'] = "layui-btn layui-btn-sm ajax-post";
        $attr['id'] = 'submit';
        $attr['type'] = 'submit';
        $attr['target-form'] = 'layui-form';
        return $this->button($title, $attr);
    }

    public function buttonBack($title = '返回')
    {
        $attr = array();
        $attr['onclick'] = 'javascript:history.back(-1);return false;';
        $attr['class'] = 'layui-btn layui-btn-sm layui-btn-primary';
        return $this->button($title, $attr);
    }

    public function data($list)
    {
        $this->_data = $list;
        return $this;
    }

    public function savePostUrl($url)
    {
        if ($url) {
            $this->_savePostUrl = $url;
        }
    }

	public function display()
    {

        //将数据融入到key中
        foreach ($this->_keyList as &$e) {
            if ($e['type'] == 'multiInput') {
                $e['name'] = explode('|', $e['name']);
            }

            //修正在编辑信息时无法正常显示已经保存的地区信息/***修改的代码****/
            if (is_array($e['name'])) {
                $i = 0;
                $n = count($e['name']);
                while ($n > 0) {
                    $e['value'][$i] = $this->_data[$e['name'][$i]];
                    $i++;
                    $n--;
                }
            } else {
                $e['value'] = $this->_data[$e['name']];
            }
            //原代码
            /*$e['value'] = $this->_data[$e['name']];*/
        }

        //编译按钮的html属性
        foreach ($this->_buttonList as &$button) {
            $button['attr'] = $this->compileHtmlAttr($button['attr']);
        }
        //显示页面
        $this->assign('group', $this->_group);
        $this->assign('title', $this->_title);
        $this->assign('suggest', $this->_suggest);
        $this->assign('keyList', $this->_keyList);
        $this->assign('buttonList', $this->_buttonList);
        $this->assign('savePostUrl', $this->_savePostUrl);

        parent::display('admin_config');
    }
    
    /**
     * keyChosen  多选菜单
     * @param $name
     * @param $title
     * @param null $subtitle
     * @param $options
     * @return $this
     */
    public function keyChosen($name, $title, $subtitle = null, $options)
    {
    	// 解析option数组
    	if (key($options) === 0) {
    		if (!is_array(reset($options))) {
    			foreach ($options as $key => &$val) {
    				$val = array($val, $val);
    			}
    			unset($key, $val);
    		}
    	} else {
    		foreach ($options as $key => &$val) {
    			foreach ($val as $k => &$v) {
    				if (!is_array($v)) {
    					$v = array($v, $v);
    				}
    			}
    			unset($k, $v);
    		}
    		unset($key, $val);
    	}
    	return $this->key($name, $title, $subtitle, 'chosen', $options);
    }
    
    
    /**
     * keyMultiInput  输入组组件
     * @param $name
     * @param $title
     * @param $subtitle
     * @param $config
     * @param null $style
     * @return $this
     * @author
     */
    public function keyMultiInput($name, $title, $subtitle, $config, $style = null)
    {
    	empty($style) && $style = 'width:400px;';
    	$key = array('name' => $name, 'title' => $title, 'subtitle' => $subtitle, 'type' => 'multiInput', 'config' => $config, 'style' => $style);
    	$this->_keyList[] = $key;
    	return $this;
    }
    
    /**插入配置分组
     * @param       $name 组名
     * @param array $list 组内字段列表
     * @return $this
     * @auth
     */
    public function group($name, $list = array())
    {
    	!is_array($list) && $list = explode(',', $list);
    	$this->_group[$name] = $list;
    	return $this;
    }
    
    public function groups($list = array())
    {
    	foreach ($list as $key => $v) {
    		$this->_group[$key] = is_array($v) ? $v : explode(',', $v);
    	}
    	return $this;
    }

	/**自动处理配置存储事件，配置项必须全大写
     * @auth
     */
    public function handleConfig()
    {
        if (IS_POST) {
            $success = false;
            $configModel = D('Config');
            foreach (I('') as $k => $v) {
                $config['name'] = '_' . strtoupper(CONTROLLER_NAME) . '_' . strtoupper($k);
                $config['type'] = 0;
                $config['title'] = '';
                $config['group'] = 0;
                $config['extra'] = '';
                $config['remark'] = '';
                $config['create_time'] = time();
                $config['update_time'] = time();
                $config['status'] = 1;				
                $config['value'] = is_array($v) ? implode(',', $v) : $v;
                $config['sort'] = 0;
                if ($configModel->add($config, null, true)) {
                    $success = 1;
                }
                $tag = 'conf_' . strtoupper(CONTROLLER_NAME) . '_' . strtoupper($k);
                S($tag, null);
            }
            if ($success) {
                if ($this->_callback) {
                    $str = $this->_callback;
                    A(CONTROLLER_NAME)->$str(I(''));
                }
                header('Content-type: application/json');
                exit(json_encode(array('info' => '保存配置成功。', 'status' => 1, 'url' => __SELF__)));
            } else {
                header('Content-type: application/json');
                exit(json_encode(array('info' => '保存配置失败。', 'status' => 0, 'url' => __SELF__)));
            }

        } else {
            $configs = D('Config')->where(array('name' => array('like', '_' . strtoupper(CONTROLLER_NAME) . '_' . '%')))->limit(999)->select();
            $data = array();
            foreach ($configs as $k => $v) {
                $key = str_replace('_' . strtoupper(CONTROLLER_NAME) . '_', '', strtoupper($v['name']));
                $data[$key] = $v['value'];
            }
            return $data;
        }
    }

    private function readUserGroups()
    {
        $list = M('AuthGroup')->where(array('status' => 1))->order('id asc')->select();
        $result = array();
        foreach ($list as $group) {
            $result[$group['id']] = $group['title'];
        }
        return $result;
    }

    /**
     * parseKanbanArray  解析看板数组
     * @param $data
     * @param array $item
     * @param array $default
     * @return array|mixed
     */
    public function parseKanbanArray($data, $item = array(), $default = array())
    {
        if (empty($data)) {
            $head = reset($default);
            if (!array_key_exists("items", $head)) {
                $temp = array();
                foreach ($default as $k => $v) {
                    $temp[] = array('data-id' => $k, 'title' => $k, 'items' => $v);
                }
                $default = $temp;
            }
            $result = $default;
        } else {
            $data = json_decode($data, true);
            $item_d = getSubByKey($item, 'data-id');
            $all = array();
            foreach ($data as $key => $v) {
                $data_id = getSubByKey($v['items'], 'data-id');
                $data_d[$key] = $v;
                unset($data_d[$key]['items']);
                $data_d[$key]['items'] = $data_id ? $data_id : array();
                $all = array_merge($all, $data_id);
            }
            unset($v);
            foreach ($item_d as $val) {
                if (!in_array($val, $all)) {
                    $data_d[0]['items'][] = $val;
                }
            }
            unset($val);
            foreach ($all as $v) {
                if (!in_array($v, $item_d)) {
                    foreach ($data_d as $key => $val) {
                        $key_search = array_search($v, $val['items']);
                        if (!is_bool($key_search)) {
                            unset($data_d[$key]['items'][$key_search]);
                        }
                    }
                    unset($val);
                }
            }
            unset($v);
            $item_t = array();
            foreach ($item as $val) {
                $item_t[$val['data-id']] = $val['title'];
            }
            unset($v);
            foreach ($data_d as &$v) {
                foreach ($v['items'] as &$val) {
                    $t = $val;
                    $val = array();
                    $val['data-id'] = $t;
                    $val['title'] = $item_t[$t];
                }
                unset($val);
            }
            unset($v);

            $result = $data_d;
        }
        return $result;

    }

    public function setDefault($data, $key, $value)
    {
        $data[$key] = $data[$key]!=null ? $data[$key] : $value;
        return $data;
    }

    public function keyDefault($key, $value)
    {
        $data = $this->_data;
        $data[$key] = $data[$key]!=null ? $data[$key] : $value;
        $this->_data = $data;
        return $this;
    }

    /**
     * groupLocalComment
     * @param $group_name    组名
     * @param $mod    mod名。path的第二个参数。
     * @return $this
     */
    public function groupLocalComment($group_name,$mod){
        $mod = strtoupper($mod);
        $this->keyDefault($mod.'_LOCAL_COMMENT_CAN_GUEST',1);
        $this->keyDefault($mod.'_LOCAL_COMMENT_ORDER',0);
        $this->keyDefault($mod.'_LOCAL_COMMENT_COUNT',10);
        $this->keyRadio($mod.'_LOCAL_COMMENT_CAN_GUEST', '是否允许游客评论', '默认为允许',array(0=>'不允许',1=>'允许'))
            ->keyRadio($mod.'_LOCAL_COMMENT_ORDER','评论排序','默认降序',array(0=>'降序',1=>'升序'))
            ->keyText($mod.'_LOCAL_COMMENT_COUNT','每页评论显示的数量','默认为10');
        $this->group($group_name, $mod.'_LOCAL_COMMENT_CAN_GUEST,'.$mod.'_LOCAL_COMMENT_ORDER,'.$mod.'_LOCAL_COMMENT_COUNT');
        return $this;
    }
}