<?php
function parse_at_users($content, $disabel_hight = false)
{
    $content = $content . ' ';
    //找出被AT的用户
    $at_users = get_at_users($content);

    //将@用户替换成链接
    foreach ($at_users as $e) {
        $user = D('User/Member')->where(array('uid' => $e))->find();
        if ($user) {
            $query_user = query_user(array('space_url', 'avatar32', 'nickname'), $user['uid']);
			$content = str_replace("[at:$e]", " <a ucard=\"$user[uid]\" href=\"$query_user[space_url]\">@$query_user[nickname] </a> ", $content);
        }
    }

    //返回替换的文本
    return $content;
}

/**
 * get_at_usernames  获取@用户的用户名
 * @param $content
 * @return array
 */
function get_at_users($content)
{
    //正则表达式匹配@用户名，匹配话题用#话题#：/#[^#]+#/
    $user_pattern = '/@([^\s|\/|:|@]+)/';
    preg_match_all($user_pattern, $content, $users);
    //返回用户名列表
    return array_unique($users[1]);
}

/**
 * get_at_uids  获取@的用户的uid
 * @param $content
 * @return array
 */
function get_at_uids($content)
{
    $uids = get_at_users($content);
    return $uids;
}

function parse_comment_content($content)
{
    return parse_main_content($content);
}

function parse_main_content($content)
{
    $content = shorten_white_space($content);

	$content=str_replace('/br','',$content);
	$content=str_replace('/nb','',$content);

    $content = parse_url_link($content);
    return $content;
}

function shorten_white_space($content)
{
    $content = preg_replace('/\s+/', ' ', $content);
    return $content;
}

function parse_url_link($content)
{
    $content = preg_replace("#((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie",
        "'<a class=\"label label-badge\" href=\"$1\" target=\"_blank\"><i class=\"icon-link\" title=\"$1\"></i></a>$4'", $content
    );
    return $content;
}

function parse_content($content)
{
    return $content;
}