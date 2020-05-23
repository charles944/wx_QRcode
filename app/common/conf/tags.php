<?php
return array(
    'app_begin' => array('behavior\CheckLangBehavior'),
	'app_init' => array('common\behavior\InitHookBehavior'),
    'action_begin' => array('common\behavior\InitModuleInfoBehavior'),
);