<?php
return array(
	'switch'=>array(//配置在表单中的键名 ,这个会是config[title]
        'title'=>'是否开启阿里云短信：',//表单的文字
        'type'=>'radio',		 //表单的类型：text、textarea、checkbox、radio、select等
        'options'=>array(
            '1'=>'启用',
            '0'=>'禁用',
        ),
        'value'=>'1',
        'tip'=>'默认开启'
    ),
	'method'=>array(               //配置在表单中的键名 ,这个会是config[recommendUser]
        'title'=>'method:',           //表单的文字
        'type'=>'text',                   //表单的类型：text、textarea、checkbox、radio、select等
        'options'=>array(                  //select 和radion、checkbox的子选项
            'value'=>'',
        ),
        'value'=>'dysmsapi.aliyuncs.com',
        'tip'=>'默认为短信发送类型',                     //表单的默认值
    ),
	'appkey'=>array(               //配置在表单中的键名 ,这个会是config[recommendUser]
        'title'=>'App key',           //表单的文字
        'type'=>'text',                   //表单的类型：text、textarea、checkbox、radio、select等
        'options'=>array(                  //select 和radion、checkbox的子选项
            'value'=>'',
        ),
        'value'=>'',
        'tip'=>'阿里云站账号管理下的Access Key管理中的Access Key ID',                     //表单的默认值
    ),
	'appsecret'=>array(               //配置在表单中的键名 ,这个会是config[recommendUser]
        'title'=>'App secret',           //表单的文字
        'type'=>'text',                   //表单的类型：text、textarea、checkbox、radio、select等
        'options'=>array(                  //select 和radion、checkbox的子选项
            'value'=>'',
        ),
        'value'=>'',
        'tip'=>'阿里云站账号管理下的Access Key管理中的Access Key Secret',                     //表单的默认值
    ),
	'product'=>array(               //配置在表单中的键名 ,这个会是config[recommendUser]
        'title'=>'product',           //表单的文字
        'type'=>'text',                   //表单的类型：text、textarea、checkbox、radio、select等
        'options'=>array(                  //select 和radion、checkbox的子选项
            'value'=>'',
        ),
        'value'=>'',
        'tip'=>'短信内容发送商名称：如自己网站名',                     //表单的默认值
    ),
);