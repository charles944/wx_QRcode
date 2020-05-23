<?php
$now_theme=modC('NOW_THEME','default','Theme');
if(!$now_theme){
    $now_theme = cookie('TO_LOOK_THEME','',array('prefix'=>'WZBV2'));
}
if($now_theme!='default'){
    return array(
        /* 模板相关配置 */
        'TMPL_PARSE_STRING' => array(
            '__THEME__'=>__ROOT__.'/themes/'.$now_theme,
            '__THEME_COMMON_STATIC__'=>__ROOT__.'/themes/'.$now_theme.'/common/static',
            '__THEME_STATIC__'=>__ROOT__.'/themes/'.$now_theme.'/'.MODULE_NAME.'/static',
            '__THEME_CSS__'=>__ROOT__.'/themes/'.$now_theme.'/'.MODULE_NAME.'/static/css',
            '__THEME_JS__'=>__ROOT__.'/themes/'.$now_theme.'/'.MODULE_NAME.'/static/js',
            '__THEME_IMG__'=>__ROOT__.'/themes/'.$now_theme.'/'.MODULE_NAME.'/static/images',
            '__THEME_VIEW__'=>__ROOT__.'/themes/'.$now_theme.'/'.MODULE_NAME.'/view',
            '__THEME_VIEW_PUBLIC__'=>__ROOT__.'/themes/'.$now_theme.'/'.MODULE_NAME.'/view/public',
            '__THEME_PUBLIC__'=>__ROOT__.'/themes/'.$now_theme.'/public',
        ),
    );
}
