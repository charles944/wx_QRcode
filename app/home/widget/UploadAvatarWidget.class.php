<?php
namespace home\widget;

require_once('./app/framework/library/vendor/phpimageworkshop/ImageWorkshop.php');
use think\Controller;
use phpimageWorkshop\core\ImageWorkshopLayer;
use phpimageWorkshop\ImageWorkshop;

class UploadAvatarWidget extends Controller
{

    public function render($uid = 0)
    {
        $this->assign('user', query_user(array('avatar256', 'avatar128', 'avatar64','avatar32','avatar512'), $uid));
        $this->assign('uid', $uid);
		$avatar_list = D('avatar_list')->where(array('status'=>1))->select();
		$this->assign('avatar_list', $avatar_list);
        $this->display(T('home@widget/uploadavatar'));
    }


    public function getAvatar($uid = 0, $size = 256)
    {
        $avatar = M('avatar')->where(array('uid' => $uid, 'status' => 1, 'is_temp' => 0))->find();
        if ($avatar) {
            if($avatar['driver'] == 'local'){
                $avatar_path = $avatar['path'];
                return $this->getImageUrlByPath($avatar_path, $size);
            }else{
                $new_img = $avatar['path'];
                $name = get_addon_class($avatar['driver']);
                if (class_exists($name)) {
                    $class = new $name();
                    if (method_exists($class, 'thumb')) {
                        $new_img =  $class->thumb($avatar['path'],$size,$size);
                    }
                }
                return $new_img;
            }
        } else {
            //如果没有头像，返回默认头像
            $avatar_list = D('avatar_list')->where(array('status'=>1))->order('rand()')->find();
            if(!empty($avatar_list)){
                $path=get_cover($avatar_list['thumb'], 'path');
            }else{
                if ($size != 0) {
                    $default_avatar = "/public/images/default_avatar.gif";
                    $path=$this->getImageUrlByPath($default_avatar, $size, false);
                } else {
                    $path= get_pic_src("/public/images/default_avatar.gif");
                }
            }
            return $path;
        }
    }


    private function getImageUrlByPath($path, $size,$isReplace = true)
    {
        $thumb = getThumbImage($path, $size, $size, 0, $isReplace);
        return get_pic_src($thumb['src']);
    }

    public function cropPicture($crop = null,$path)
    {
        //如果不裁剪，则发生错误
        if (!$crop) {
            $this->error('必须裁剪');
        }
        $driver = modC('PICTURE_UPLOAD_DRIVER','local','config');
        if (strtolower($driver) == 'local') {
            //解析crop参数
            $crop = explode(',', $crop);
            $x = $crop[0];
            $y = $crop[1];
            $width = $crop[2];
            $height = $crop[3];
            //本地环境
            $image = ImageWorkshop::initFromPath($path);
            //生成将单位换算成为像素
            $x = $x * $image->getWidth();
            $y = $y * $image->getHeight();
            $width = $width * $image->getWidth();
            $height = $height * $image->getHeight();
            //如果宽度和高度近似相等，则令宽和高一样
            if (abs($height - $width) < $height * 0.01) {
                $height = min($height, $width);
                $width = $height;
            }
            //调用组件裁剪头像
            $image = ImageWorkshop::initFromPath($path);
            $image->crop(ImageWorkshopLayer::UNIT_PIXEL, $width, $height, $x, $y);
            $image->save(dirname($path), basename($path));
            //返回新文件的路径
            return  cut_str('/uploads/avatar',$path,'l');
        }else{
            $name = get_addon_class($driver);
            $class = new $name();
            $new_img = $class->crop($path,$crop);
            return $new_img;
        }


    }


} 