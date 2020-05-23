<?php
namespace common\widget;
use think\Controller;

/**
 * Class UploadMultiImageWidget  上传多图组件
 * @package Common\Widget
 * @author:
 */
class UploadMultiImageWidget extends Controller
{

    public function render($attributes = array())
    {

        $attributes_id = $attributes['id'];
        $config = $attributes['config'];
        $class = $attributes['class'];
        $value = $attributes['value'];
        $name = $attributes['name'];
        $width = $attributes['width'] ? $attributes['width'] : 100;
        $height = $attributes['height'] ? $attributes['height'] : 100;
        $isLoadScript = $attributes['isLoadScript']?1:0;
        //$filetype = $this->rules['filetype'];

        $config = $config['config'];

        $id = $attributes_id;
        $attributes['config'] = array('text' => '选择文件'
        );


        if (intval($value) != 0) {
            $url = getThumbImageById($value, $width, $height);
            $img = '<img src="' . $url . '"/>';
        } else {
            $img = '';
        }

        $this->assign('isLoadScript',$isLoadScript);

        $this->assign('img',$img);
        $this->assign($attributes);
        $this->display(T('App://Common@Widget/uploadmultiimage'));
    }
} 