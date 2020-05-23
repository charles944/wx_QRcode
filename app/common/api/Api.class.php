<?php
namespace common\api;

use common\exception\ApiException;

class Api
{
    protected function apiSuccess($message, $extra = array())
    {
        return $this->apiReturn(true, $message, $extra);
    }

    protected function apiError($message)
    {
        throw new ApiException($message);
        return null; // 这句话是为了消除IDE的警告
    }

    protected function apiReturn($success, $message, $extra)
    {
        $result = array('success' => boolval($success), 'message' => strval($message));
        $result = array_merge($result, $extra);
        return $result;
    }

    /**
     * 发送不能太频繁，否则抛出异常。
     */
    protected function requireSendInterval()
    {
        //获取最后的时间
        $lastSendTime = session('last_send_time');
        if (time() - $lastSendTime < 10) {
            throw new ApiException('操作太频繁，请稍后重试');
        }
    }

    protected function updateLastSendTime()
    {
        //更新最后发送时间
        session('last_send_time', time());
    }

    public function resetLastSendTime()
    {
        session('last_send_time', 0);
    }

    protected function requireLogin()
    {
        if (!is_login()) {
            throw new ApiException('需要登录', ErrorCodeApi::REQUIRE_LOGIN);
        }
    }
}