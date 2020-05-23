<?php

function getImageUrlByPath($path, $size)
{
    //TODO 重新开启缩略
    $thumb = getThumbImage($path, $size, $size);
    // $thumb['src']=$path;
    $thumb = $thumb['src'];
    if (!is_sae()) {
        $thumb = getRootUrl() . $thumb;
    }
    return $thumb;
}

/**兼容SAE
 * @param        $filename
 * @param int $width
 * @param string $height
 * @param int $type
 * @param bool $replace
 * @return mixed|string
 * @auth
 */
function getThumbImage($filename, $width = 100, $height = 'auto', $type = 0, $replace = false)
{
    $UPLOAD_URL = '';
    $UPLOAD_PATH = '';
    $filename = str_ireplace($UPLOAD_URL, '', $filename); //将URL转化为本地地址
    $info = pathinfo($filename);
    $oldFile = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '.' . $info['extension'];
    $thumbFile = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '_' . $width . '_' . $height . '.' . $info['extension'];

    $oldFile = str_replace('\\', '/', $oldFile);
    $thumbFile = str_replace('\\', '/', $thumbFile);


    $filename = ltrim($filename, '/');
    $oldFile = ltrim($oldFile, '/');
    $thumbFile = ltrim($thumbFile, '/');


    //兼容SAE的中心裁剪缩略
    if (strtolower(C('PICTURE_UPLOAD_DRIVER')) == 'sae') {
        $storage = new SaeStorage();
        $thumbFilePath = str_replace(C('UPLOAD_SAE_CONFIG.rootPath'), '', $thumbFile);
        if(!$storage->fileExists(C('UPLOAD_SAE_CONFIG.domain'),$thumbFilePath)){
            $f = new SaeFetchurl();
            $img_data = $f->fetch($oldFile);
            $img = new SaeImage();
            $img->setData($img_data);
            $info_img = $img->getImageAttr();
            if ($height == "auto") $height = $info_img[1] * $height / $info_img[0];

            $w = $info_img[0];
            $h = $info_img[1];

            /* 居中裁剪 */
            //计算缩放比例
            $w_scale = $width / $w;
            if ($w_scale > 1) {
                $w_scale = 1 / $w_scale;
            }
            $h_scale = $height / $h;

            if ($h_scale > $w_scale) {
                //按照高来放缩
                $x1 = (1 - 1.0 * $width * $h / $w / $height) / 2;
                $x2 = (1 - $x1);
                $img->crop($x1, $x2, 0, 1);
                $img_temp = $img->exec();
                $img1 = new SaeImage();
                $img1->setData($img_temp);
                $img1->resizeRatio($h_scale);
            } else {
                $y1 = (1 - 1 * 1.0 / ($width * $h / $w / $height)) / 2;
                $y2 = (1 - $y1);
                $img->crop(0, 1, $y1, $y2);
                $img_temp = $img->exec();
                $img1 = new SaeImage();
                $img1->setData($img_temp);
                $img1->resizeRatio($w_scale);
            }

            $img1->improve();
            $new_data = $img1->exec(); // 执行处理并返回处理后的二进制数据
            if ($new_data === false)
                return $oldFile;
            // 或者可以直接输出
            $thumbed = $storage->write(C('UPLOAD_SAE_CONFIG.domain'), $thumbFilePath, $new_data);
            $info['width'] = $width;
            $info['height'] = $height;
            $info['src'] = $thumbed;
            //图片处理失败时输出错误码和错误信息
        }else{
            $info['width'] = $width;
            $info['height'] = $height;
            $info['src'] =$storage->getUrl(C('UPLOAD_SAE_CONFIG.domain'),$thumbFilePath);
        }
        return $info;
    }


    //原图不存在直接返回
    if (!file_exists($UPLOAD_PATH . $oldFile)) {
        @unlink($UPLOAD_PATH . $thumbFile);
        $info['src'] = $oldFile;
        $info['width'] = intval($width);
        $info['height'] = intval($height);
        return $info;
        //缩图已存在并且  replace替换为false
    } elseif (file_exists($UPLOAD_PATH . $thumbFile) && !$replace) {
        $imageinfo = getimagesize($UPLOAD_PATH . $thumbFile);
        $info['src'] = $thumbFile;
        $info['width'] = intval($imageinfo[0]);
        $info['height'] = intval($imageinfo[1]);
        return $info;
        //执行缩图操作
    } else {
        $oldimageinfo = getimagesize($UPLOAD_PATH . $oldFile);
        $old_image_width = intval($oldimageinfo[0]);
        $old_image_height = intval($oldimageinfo[1]);
        if ($old_image_width <= $width && $old_image_height <= $height) {
            @unlink($UPLOAD_PATH . $thumbFile);
            @copy($UPLOAD_PATH . $oldFile, $UPLOAD_PATH . $thumbFile);
            $info['src'] = $thumbFile;
            $info['width'] = $old_image_width;
            $info['height'] = $old_image_height;
            return $info;
        } else {
            if ($height == "auto") $height = $old_image_height * $width / $old_image_width;
            if ($width == "auto") $width = $old_image_width * $width / $old_image_height;
            if (intval($height) == 0 || intval($width) == 0) {
                return 0;
            }
            require_once(APP_PATH . 'framework/library/vendor/phpthumb/PhpThumbFactory.class.php');
            $thumb = PhpThumbFactory::create($UPLOAD_PATH . $filename);
            if ($type == 0) {
                $thumb->adaptiveResize($width, $height);
            } else {
                $thumb->resize($width, $height);
            }
            $res = $thumb->save($UPLOAD_PATH . $thumbFile);

            $info['src'] = $UPLOAD_PATH . $thumbFile;
            $info['width'] = $old_image_width;
            $info['height'] = $old_image_height;
            return $info;

            //内置库缩略
            /*  $image = new \think\Image();
              $image->open($UPLOAD_PATH . $filename);
              //dump($image);exit;
              $image->thumb($width, $height, $type);
              $image->save($UPLOAD_PATH . $thumbFile);
              //缩图失败
              if (!$image) {
                  $thumbFile = $oldFile;
              }
              $info['width'] = $width;
              $info['height'] = $height;
              $info['src'] = $thumbFile;
              return $info;*/


        }
    }
}

/**获取网站的根Url
 * @return string
 * @auth
 */
function getRootUrl()
{
    if (__ROOT__ != '') {
        return __ROOT__ . '/';
    }
    if (C('URL_MODEL') == 2)
        return __ROOT__ . '/';
    return __ROOT__;
}

/**通过ID获取到图片的缩略图
 * @param        $cover_id 图片的ID
 * @param int $width 需要取得的宽
 * @param string $height 需要取得的高
 * @param int $type 图片的类型，qiniu 七牛，local 本地, sae SAE
 * @param bool $replace 是否强制替换
 * @return string
 */
function getThumbImageById($cover_id, $width = 100, $height = 'auto', $type = 0, $replace = false)
{

    $picture = S('picture_' . $cover_id);
    if (empty($picture)) {
        $picture = M('Picture')->where(array('status' => 1))->getById($cover_id);
        S('picture_' . $cover_id, $picture);
    }
    if (empty($picture)) {
        return get_pic_src('public/images/nopic.png');
    }
    if ($picture['type'] == 'local') {
        $attach = getThumbImage($picture['path'], $width, $height, $type, $replace);
        return get_pic_src($attach['src']);
    } else {
        $new_img = $picture['path'];
        $name = get_addon_class($picture['type']);
        if (class_exists($name)) {
            $class = new $name();
            if (method_exists($class, 'thumb')) {
                $new_img = $class->thumb($picture['path'], $width, $height, $type, $replace);
            }
        }
        return get_pic_src($new_img);
    }
}


/**简写函数，等同于getThumbImageById（）
 * @param $cover_id 图片id
 * @param int $width 宽度
 * @param string $height 高度
 * @param int $type 裁剪类型，0居中裁剪
 * @param bool $replace 裁剪
 * @return string
 */
function thumb($cover_id, $width = 100, $height = 'auto', $type = 0, $replace = false)
{
    return getThumbImageById($cover_id, $width, $height, $type, $replace);
}

/**获取第一张图
 * @param $str_img
 * @return mixed
 */
function get_pic($str_img)
{
    preg_match_all("/<img.*\>/isU", $str_img, $ereg); //正则表达式把图片的整个都获取出来了
    $img = $ereg[0][0]; //图片
	if(empty($img)){
		 preg_match_all("/img\[(.*?)\]/isU", $str_img, $ereg1); //正则表达式把图片的整个都获取出来了
		 $img = $ereg1[1][0]; //图片
		 if(!empty($img)){
			return $img;
		 }else{
			 return './public/images/nopic.png';
		 }
	}
    $p = "#src=('|\")(.*)('|\")#isU"; //正则表达式
    preg_match_all($p, $img, $img1);
    $img_path = $img1[2][0]; //获取第一张图片路径
    return $img_path;
}

/**
 * get_pic_src   渲染图片链接
 * @param $path
 * @return mixed
 */
function get_pic_src($path)
{
	if(empty($path)){
		return str_replace('//', '/', getRootUrl() . 'public/images/nopic.png'); //防止双斜杠的出现;
	}else{
		//不存在http://
		$not_http_remote=(strpos($path, 'http://') === false);
		//不存在https://
		$not_https_remote=(strpos($path, 'https://') === false);
		if ($not_http_remote && $not_https_remote) {
			//本地url
			return str_replace('//', '/', getRootUrl() . $path); //防止双斜杠的出现
		} else {
			//远端url
			return $path;
		}
	}
    
}