<?php
namespace ucenter\controller;
use think\Controller;
class PublicController extends Controller
{
    //获取个人资料，用以支持小名片
	public function getProfile()
    {
        $uid = intval($_REQUEST['uid']);
        $userProfile = query_user(array('uid', 'nickname', 'avatar64', 'space_url', 'signature', 'rank_link'), $uid);
        $html = '';
        if (count($userProfile['rank_link'])) {
            foreach ($userProfile['rank_link'] as $val) {
                if ($val['is_show']) {
                    if(empty($val['label_content'])){
                        $html = $html . '<img class="img-responsive" src="' . $val['logo_url'] . '" title="' . $val['title'] . '" alt="' . $val['title'] . '" style="width: 18px;height: 18px;vertical-align: middle;margin-left: 3px;display: inline;"/>';
                    }else{
                        $html = $html .'<span class="label label-badge rank-label" title="'.$val['title'].'" style="background:'.$val['label_bg'].' !important;color:'.$val['label_color'].' !important;vertical-align: middle;margin-left: 3px;">'.$val['label_content'].'</span>';
                    }
                }
            }
            unset($val);
        }
        $userProfile['rank_link']=$html;
        //获取用户封面path
        $map=getUserConfigMap('user_cover','',$uid);
        $map['role_id']=0;
        $model=D('User/UserConfig');
        $cover=$model->findData($map);
        if($cover){
            $userProfile['cover_path']=getThumbImageById($cover['value'],344,100);
        }else{
            $userProfile['cover_path']=__ROOT__.'/public/images/qtip_bg.png';
        }
        echo json_encode($userProfile);
    }
	
    public function card()
    {
        $aUID = I('get.uid', 0, 'intval');
        $user = $this->getProfile($aUID);
        // $follow=D('Common/Follow')->isFollow(is_login(),$aUID);
        // $this->assign('follow',$follow);
        $this->assign('uid', $aUID);
        $this->assign('user', $user);
        $not_self = get_uid() != $aUID;
        $this->assign('not_self',$not_self);
        $this->display();
    }

    //用户修改封面
    public function changeCover()
    {
        if (!is_login()) {
            $this->error('需要登录!');
        }
        if(IS_POST){
            $aCoverId=I('post.cover_id',0,'intval');
            $result['status']=0;
            if($aCoverId<=0){
                $result['info']='非法操作！';
                $this->ajaxReturn($result);
            }
            $data=getUserConfigMap('user_cover');
            $data['role_id']=0;
            $model=D('User/UserConfig');
            $already_data=$model->findData($data);
            if(!$already_data){
                $data['value']=$aCoverId;
                $res=$model->addData($data);
            }else{
                if($already_data['value']==$aCoverId){
                    $result['info']='没有修改！';
                    $this->ajaxReturn($result);
                }else{
                    $res=$model->saveValue($data,$aCoverId);
                }
            }
            if($res){
                $result['status']=1;
                $result['path_1140']=getThumbImageById($aCoverId,1140,130);
                $result['path_273']=getThumbImageById($aCoverId,273,70);
            }else{
                $result['info']='操作失败！';
            }
            $this->ajaxReturn($result);
        }else{
            //获取用户封面id
            $map=getUserConfigMap('user_cover');
            $map['role_id']=0;
            $model=D('User/UserConfig');
            $cover=$model->findData($map);
            $my_cover['cover_id']=$cover['value'];
            $my_cover['cover_path']=getThumbImageById($cover['value'],348,70);
            $this->assign('my_cover',$my_cover);
            $this->display('_change_cover');
        }
    }
}