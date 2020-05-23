<?php
namespace home\controller;
use think\Controller;
/**
 * @author 朝兮夕兮
 */
class QueueController extends Controller
{
	public function _empty(){
		$this->redirect('Index/index');
	}

	protected function _initialize()
	{
		if (!C('WEB_SITE_CLOSE')) {
			$this->error(C('WEB_SITE_CLOSE_INFO'));
		}
	}

	public function index(){
		$this->email_auto();
		$this->mobile_auto();
	}


	private function email_auto(){
		$limit = 50;
		$mails = M('Queue')->where(array('status'=>1,'method'=>'email'))->order('id asc')->find();
		if(empty($mails)){
			$mails = M('Queue')->where(array('status'=>0,'method'=>'email'))->order('id asc')->find();
			if (empty($mails)){
				echo 'no queue mail';
			}
			$mails_data = M('QueueDetail')->where(array('qid'=>$mails['id'],'status'=>0))->order('id asc')->limit($limit)->select();
	        if(!empty($mails_data)){
	        	M('Queue')->where(array('id'=>$mails['id']))->save(array('status'=>1,'update_time'=>TIME()));
		        foreach($mails_data as &$val){
		        	$user = M('User/Member')->where(array('uid'=>$val['uid']))->field('nickname')->find();
		        	$content = str_replace('[nickname]', $user['nickname'], $val['content']);
		        	$res = send_mail($val['email'], $val['title'], $content);
		        	if($res){
		        		$tmp_data['status'] = 1;
		        		$tmp_data['status_msg'] = json_encode($res);
		        		$tmp_data['update_time'] = TIME();
		        		M('QueueDetail')->where(array('id'=>$val['id']))->save($tmp_data);
		        	}else{
		        		$tmp_data['status'] = 2;
		        		$tmp_data['status_msg'] = json_encode($res);
		        		$tmp_data['update_time'] = TIME();
		        		M('QueueDetail')->where(array('id'=>$val['id']))->save($tmp_data);
		        	}
		        	unset($tmp_data,$user,$content,$res);
		        }

		        M('Queue')->where(array('id'=>$mails['id']))->save(array(
		        	'has_queue' => array('exp', 'has_queue+'.count($mails_data)),
		        	'update_time'=>TIME()
		        ));
		        echo 'queue mail '.count($mails_data);
		    }else{
		    	M('Queue')->where(array('id'=>$mails['id']))->save(array('status'=>2,'update_time'=>TIME()));
		    	echo 'queue mail success';
		    }
		}else{
			$mails_data = M('QueueDetail')->where(array('qid'=>$mails['id'],'status'=>0))->order('id asc')->limit($limit)->select();
	        if(!empty($mails_data)){
		        foreach($mails_data as &$val){
		        	$user = M('User/Member')->where(array('uid'=>$val['uid']))->field('nickname')->find();
		        	$content = str_replace('[nickname]', $user['nickname'], $val['content']);
		        	$res = send_mail($val['email'], $val['title'], $content);
		        	if($res){
		        		$tmp_data['status'] = 1;
		        		$tmp_data['status_msg'] = json_encode($res);
		        		$tmp_data['update_time'] = TIME();
		        		M('QueueDetail')->where(array('id'=>$val['id']))->save($tmp_data);
		        	}else{
		        		$tmp_data['status'] = 2;
		        		$tmp_data['status_msg'] = json_encode($res);
		        		$tmp_data['update_time'] = TIME();
		        		M('QueueDetail')->where(array('id'=>$val['id']))->save($tmp_data);
		        	}
		        	unset($tmp_data,$user,$content,$res);
		        }

		        M('Queue')->where(array('id'=>$mails['id']))->save(array(
		        	'has_queue' => array('exp', 'has_queue+'.count($mails_data)),
		        	'update_time'=>TIME()
		        ));
		        echo 'queue mail '.count($mails_data);
		    }else{
		    	M('Queue')->where(array('id'=>$mails['id']))->save(array('status'=>2,'update_time'=>TIME()));
		    	echo 'queue mail success';
		    }
		}
	}

	private function mobile_auto(){
		$limit = 50;
		$mails = M('Queue')->where(array('status'=>1,'method'=>'mobile'))->order('id asc')->find();
		if(empty($mails)){
			$mails = M('Queue')->where(array('status'=>0,'method'=>'mobile'))->order('id asc')->find();
			if (empty($mails)){
				echo 'no queue mobile';
			}
			$mails_data = M('QueueDetail')->where(array('qid'=>$mails['id'],'status'=>0))->order('id asc')->limit($limit)->select();
	        if(!empty($mails_data)){
	        	M('Queue')->where(array('id'=>$mails['id']))->save(array('status'=>1,'update_time'=>TIME()));
		        foreach($mails_data as &$val){
		        	$user = M('User/Member')->where(array('uid'=>$val['uid']))->field('nickname')->find();
		        	$content = str_replace('[nickname]', $user['nickname'], $val['content']);
		        	$res = sendSMS($val['mobile'], $content);
		        	if($res){
		        		$tmp_data['status'] = 1;
		        		$tmp_data['status_msg'] = json_encode($res);
		        		$tmp_data['update_time'] = TIME();
		        		M('QueueDetail')->where(array('id'=>$val['id']))->save($tmp_data);
		        	}else{
		        		$tmp_data['status'] = 2;
		        		$tmp_data['status_msg'] = json_encode($res);
		        		$tmp_data['update_time'] = TIME();
		        		M('QueueDetail')->where(array('id'=>$val['id']))->save($tmp_data);
		        	}
		        	unset($tmp_data,$user,$content,$res);
		        }

		        M('Queue')->where(array('id'=>$mails['id']))->save(array(
		        	'has_queue' => array('exp', 'has_queue+'.count($mails_data)),
		        	'update_time'=>TIME()
		        ));
		        echo 'queue mobile '.count($mails_data);
		    }else{
		    	M('Queue')->where(array('id'=>$mails['id']))->save(array('status'=>2,'update_time'=>TIME()));
		    	echo 'queue mobile success';
		    }
		}else{
			$mails_data = M('QueueDetail')->where(array('qid'=>$mails['id'],'status'=>0))->order('id asc')->limit($limit)->select();
	        if(!empty($mails_data)){
		        foreach($mails_data as &$val){
		        	$user = M('User/Member')->where(array('uid'=>$val['uid']))->field('nickname')->find();
		        	$content = str_replace('[nickname]', $user['nickname'], $val['content']);
		        	$res = sendSMS($val['mobile'], $content);
		        	if($res){
		        		$tmp_data['status'] = 1;
		        		$tmp_data['status_msg'] = json_encode($res);
		        		$tmp_data['update_time'] = TIME();
		        		M('QueueDetail')->where(array('id'=>$val['id']))->save($tmp_data);
		        	}else{
		        		$tmp_data['status'] = 2;
		        		$tmp_data['status_msg'] = json_encode($res);
		        		$tmp_data['update_time'] = TIME();
		        		M('QueueDetail')->where(array('id'=>$val['id']))->save($tmp_data);
		        	}
		        	unset($tmp_data,$user,$content,$res);
		        }

		        M('Queue')->where(array('id'=>$mails['id']))->save(array(
		        	'has_queue' => array('exp', 'has_queue+'.count($mails_data)),
		        	'update_time'=>TIME()
		        ));
		        echo 'queue mobile '.count($mails_data);
		    }else{
		    	M('Queue')->where(array('id'=>$mails['id']))->save(array('status'=>2,'update_time'=>TIME()));
		    	echo 'queue mobile success';
		    }
		}
	}
}