<?php
namespace Home\Controller;
use Home\Common\ApiController;
class WishController extends ApiController {
	
	protected $wish;

	public function __construct()
	{
		parent::__construct();
		$this->wish = D('wish');
	}

	public function tealist()
	{
		//session('user',1);
		$u_id = session('user');
		if(empty($u_id))
			$this->goLogin();
		$list = $this->wish->tealist($u_id);
		$this->apiReturn($list);
	}

	public function stuconfirm(){
		$id = I('get.id');

		$res = $this->wish->stuconfirm($id);
		if($res === true)
			$this->apiReturn();
		else
			$this->apiReturn($this->getError(),false);
	}


	// public function nefuerPage()
	// {
	// 	header('location:/student/list.html');die;
	// }

	// public function listAll()
	// {
	// 	$au_id = session('acc');
	// 	if(empty($au_id))
	// 		$this->goLogin();
	// 	$this->apiReturn($this->wish->listAll($au_id));
	// }

	public function stulist(){
		session ('acc',2016224408);
		$au_id = session ('acc');
		//$this->ajaxReturn("nihao ");
		if(empty($au_id))
			$this->goLogin();
		$this->apiReturn($this->wish->stulist($au_id));
	}

	public function admlist(){
		session('adm','admin');
		$u_id = session('adm');
		if(empty($u_id))
			$this->goLogin();
		$this->apiReturn($this->wish->admlist());
	}

	public function pubPage()
	{
		$user = D('user');
		$userInfo = $user->userInfo(session('user'));
		if(false === $userInfo)
		{
			$this->apiReturn($user->getError(), false);
		}
		$this->apiReturn($userInfo);
	}

	public function pub()
	{
		$params = json_decode(file_get_contents('php://input'), true);

		//session('user',1);
		$u_id     = session('user');

		$content  = $params['content'];
		$guy      = $params['guy'];
		$phone    = $params['phone'];
		$deadline = $params['deadline'];

		// $content = I('post.content');
		// $guy = I ('post.guy');
		// $phone =I ('post.phone');
		// $deadline =I ('post.phone');

		$user = D('user');
		$userInfo = $user->userInfo(session('user'));
		if(false === $userInfo)
		{
			$this->apiReturn($user->getError(), false);
		}
		if(empty($content))
			$this->apiReturn('请填写心愿内容', false);
		if(empty($guy))
			$this->apiReturn('请填写联系人', false);
		if(empty($phone))
			$this->apiReturn('请填写联系方式', false);
		if(empty($deadline))
			$this->apiReturn('请填写截止时间', false);
		$deadline = strtotime($deadline);
		if($deadline - time() < 900)
			$this->apiReturn('截止时间至少为15分钟，请修改', false);

		$this->wish->pub($u_id, $content, $guy, $phone, $deadline);
		$this->apiReturn();
	}

	public function info()
	{
		$id = I('get.id');
		if(empty($id))
			$this->apiReturn('请指定心愿id', false);

		$wishInfo = $this->wish->wishInfo($id);
		if(false === $wishInfo)
		{
			$this->apiReturn($this->wish->getError(), false);
		}
		$this->apiReturn($wishInfo);
	}

	public function cancel()
	{
		$params = json_decode(file_get_contents('php://input'), true);
		
		$id = $params['id'];
		$reason = $params['reason'];
		// $id = I('post.id');
		// var_dump($id);die;
		
		if(empty($id))
			$this->apiReturn('请指定心愿id', false);
		if(empty($reason))
			$this->apiReturn('请填写取消原因', false);
		$wishCancel = $this->wish->cancel($id, $reason);
		if(false === $wishCancel)
		{
			$this->apiReturn($this->wish->getError(), false);
		}
		$this->apiReturn();
	}

	public function accept()
	{
		$params = json_decode(file_get_contents('php://input'), true);
		//session('acc',2016224408);
		$u_id = session('acc');
		if(empty($u_id))
			$this->apiReturn('请使用学生端登录',false);
		//$data = I('post.');

		$id = $params['id'];
		$guy = $params['guy'];
		$phone = $params['phone'];
		if(empty($id))
			$this->apiReturn('请指定心愿id', false);
		if(empty($guy))
			$this->apiReturn('请填写联系人', false);
		if(empty($phone))
			$this->apiReturn('请填写联系方式', false);

		$wishAccept = $this->wish->accept($id, $u_id, $guy, $phone);
		if(false === $wishAccept)
		{
			$this->apiReturn($this->wish->getError(), false);
		}
		$this->apiReturn();
	}

	// public function resend(){
	// 	$id = I('get.id');
	// 	$params = json_decode(file_get_contents('php://input'), true);
	// 	//var_dump(I('deadline'));die;
	// 	$deadline = strtotime( $params['deadline'] );
	// 	var_dump($deadline);die;
	// 	$res =  $this->wish->resend($id,$deadline);
	// 	if($res)
	// 		$this->apiReturn();
	// 	else
	// 		$this->apiReturn($this->getError(),false);
	// }

	public function confirm()
	{
		$params = json_decode(file_get_contents('php://input'), true);

		//session('user',1);
		$id = $params['id'];
		$time = $params['time'];
		$quality = $params['judge'];
		$u_id = session('user');
		if(empty($id))
			$this->apiReturn('请指定心愿id', false);
		if(empty($u_id))
			$this->goLogin();
		$wishConfirm = $this->wish->confirm($id, $u_id, $time, $quality);
		if(false === $wishConfirm)
		{
			$this->apiReturn($this->wish->getError(), false);
		}
		$this->apiReturn();
	}

	public function admassign()
	{
		// $id = I('post.id');
		// $angel_id = I('post.angel_id');
		// $angel_guy = I('post.angel_guy');
		// $angel_phone = I('post.angel_phone');

		$params = json_decode(file_get_contents('php://input'), true);
		$id = $params['id'];
		$angel_id = $params['angel_id'];
		$angel_guy = $params['angel_guy'];
		$angel_phone = $params['angel_phone'];

		if(empty($id))
			$this->apiReturn('心愿id不能为空',false);
		if(empty($angel_id))
			$this->apiReturn('输入学号',false);
		if(empty($angel_guy))
			$this->apiReturn('请输入姓名',false);
		if(empty($angel_phone))
			$this->apiReturn('请输入联系方式',false);

		$change=$this->wish->admassign($id,$angel_id,$angel_guy,$angel_phone);
		if( false === $change )
		{
			$this->apiReturn($this->wish->getError(),false);
		}
		$this->apiReturn();

	}

	public function assignlist()
	{
		//session('adm',1);//
		$adm_id = session('adm');
		if(empty($adm_id))
			$this->goLogin();
		$list = $this->wish->assignlist($adm_id);
		$this->apiReturn($list);
	}
	
}