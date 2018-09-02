<?php
namespace Home\Controller;
use Home\Common\ApiController;
class UserController extends ApiController {
	
	private $user;

	public function __construct(){
		parent::__construct();
		$this->user = D('user');
	}

	public function stu_info() {
			$params = json_decode(file_get_contents('php://input'), true);
			$au_id = $params['account'];
			//var_dump($au_id);die;
			if (empty($au_id))
			{
				$u_id = session('acc');
				//var_dump($u_id);die;
				if(empty($u_id))
					$this->goLogin();
				else
				{
					//echo($this->user->stuname($au_id));die;
					$res = array(
						'name' => $this->user->stuname($u_id),
						'acc' => $u_id,
						'phone' =>$this->user->stuphone($u_id),
						'time' => $this->user->studentInfo($u_id)
						);
					// if(empty($res['name']))
					// {
					// 	var_dump($res['name']);die;
					// 	$this->apiReturn($this->user->getError(),false);

					// }
					// else if(empty($res['phone']))
					// {
					// 	var_dump($res['phone']);die;
					// 	$this->apiReturn($this->user->getError(),false);
						
					// }
					// else
					//{
						$this->apiReturn($res);
					//}
				}
				
			}
			else
			{
				$res = array(
					'name' => $this->user->stuname($au_id),
					'acc' => $au_id,
					'phone' =>$this->user->stuphone($au_id),
					'time' => $this->user->studentInfo($au_id)
				);
				//var_dump($res['name']);die;
				if(empty($res['name']))
				{
					$this->apiReturn($this->user->getError(),false);
				}
				else if(empty($res['phone']))
				{
					$this->apiReturn($this->user->getError(),false);
				}
				else
				{
					$this->apiReturn($res);
				}
				
			}

		

	}

	public function stu_register(){
		
		$params = json_decode(file_get_contents('php://input'), true);
		$account = session('acc');//$params('account');
		$phone = $params['phone'];
		$nickname = $params['nickname'];

		$res = $this->user->stu_register($account,$phone,$nickname);
		if($res === false)
			$this->apiReturn('注册失败',false);
		else
			$this->apiReturn();
	}

	public function tea_register(){
		$params = json_decode(file_get_contents('php://input'), true);

		$account = session('user');//$params['account'];
		$phone =  $params['phone'];
		$res = $this->user->register($account,$phone);

		if($res === false)
			$this->apireturn('电话号码已存在',false);
		else
			$this->apireturn();
	}

	public function alter(){
		//session('user','stu');
		$user = session('user');
		$params = json_decode(file_get_contents('php://input'), true);
		//$phone = I('post.phone');
		$phone = $params['phone'];
		//$this->ajaxReturn("dsfd");
		if(empty($user))
			$this->goLogin();

		$res = $this->user->alter($user,$phone);

		if($res === false)
			$this->apireturn('修改失败',false);
		else
			$this->apireturn();
	}

	public function tea_info(){
		
		$user = session('user');
		//var_dump(session('user'));die;
		if(empty($user))
			$this->goLogin();
		$res = $this->user->tea_info($user);
		if($res === false)
			$this->apireturn('',false);
		else
		{
			$this->apireturn($res);
		}

	}

	public function login()
	{
		$params = json_decode(file_get_contents('php://input'), true);
		
		$account = $params['account'];
		$password = $params['password'];
		
		if($this->user->loginadmin($account, $password) == 1)//管理员
		{
			$arr = array('admin' =>1);
			$this->apiReturn($arr);
		}
		else if($this->user->loginteacher($account, $password) == 1)//老师
		{
			$arr = array('admin' =>2);
			$this->apireturn($arr);
		}
		else if($this->user->loginstudent($account, $password) == 1)//学生
		{
			$arr = array('admin' =>3);
			$this->apireturn($arr);
		}
		else
		{
			$this->apiReturn($this->user->getError(),false);
		}
	}

	public function loginPage()
	{
		if(empty(session('openid')))
		{
			if( ! empty(session('user')) || ! empty(session('acc')))
			{
				header('location:/');die;//跳到根目录
			}
			if( ! isset($_GET['code']))//收集method="get"的表单的值
			{
				$redirect_uri = 'http://nefuer.jblog.info/home/user/wx_in?redirect=' . urlencode($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . U('/V1/user/loginPage'));
				$this->wx->userCode($redirect_uri, false, 'redirect');
			}
			$openid = $this->wx->userOpenid(I('get.code'));
			if( ! empty($openid['errcode']))
				$this->apiReturn($openid['errmsg'], false);
			$openid = $openid['openid'];
			session('openid', $openid);
		}
		else
		{
			$openid = session('openid');
		}
		$u_id = $this->user->inByOpenid($openid);
		if( ! $u_id)
		{
			//加载登录页面
			die;
		}
		session('user', $u_id);
		header('location:/');die;
	}


}