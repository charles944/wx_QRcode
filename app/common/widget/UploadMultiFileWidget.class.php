<?php
namespace common\widget;
use think\Controller;


class UploadMultiFileWidget extends Controller {

    public function render($attributes = array()){

        $attributes['id'] = $attributes['id']?$attributes['id']:$attributes['name'];
        $config = $attributes['config'];
        $class = $attributes['class'];
        $value = $attributes['value'];
        $name = $attributes['name'];
        $width = $attributes['width'] ? $attributes['width'] : 100;
        $height = $attributes['height'] ? $attributes['height'] : 100;
        $isLoadScript = $attributes['isLoadScript']?1:0;
        //$filetype = $this->rules['filetype'];

        $config = $config['config'];

        $attributes['config'] = array('text' => '选择文件'
        );

        $files_ids=explode(',',$value);
        if($files_ids){
            foreach ($files_ids as $v) {
               $files[]=M('File')->find($v);
            }
            unset($v);

        }
        $this->assign('isLoadScript',$isLoadScript);
        $this->assign('files',$files);
        $this->assign($attributes);
        $this->display(T('App://Common@Widget/uploadmultifile'));

    }
}