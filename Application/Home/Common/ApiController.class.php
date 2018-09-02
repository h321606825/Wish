<?php
namespace Home\Common;
use Think\Controller;
class ApiController extends Controller {

	protected $appid = 'wx00c21901537bc5a6';
	protected $secret = '821ab731206d8993146f3d151d6217b5';
	protected $use_wx = true;
	protected $wx;

	public function __construct(){
		parent::__construct();
		Vendor('Wx');
		date_default_timezone_set('PRC');
		header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Origin:http://localhost');
        header('Access-Control-Allow-Origin:http://192.168.31.226');
        header('Access-Control-Allow-Headers:token, Origin, X-Requested-With, Content-Type, Accept');
        header('Access-Control-Allow-Methods:PUT,POST,GET,DELETE,OPTIONS');
        header('X-Powered-By: 3.2.1');

		$this->checkLogin();
		if($this->use_wx)
			$this->wx = $this->wx();
	}

	private function checkLogin(){
		$path_info = I('server.PATH_INFO', '');
		if(false !== strpos($path_info, 'user/login'))
		{
			return true;
		}
		$path_info = explode('/', $path_info);
		if(empty($path_info[0]) || empty($path_info[1]))
		{
			$this->apiReturn('url错误', false);
		}
		if(empty(session('user')) && empty(session('acc')))
		{
			if(isset($_GET['acc']))
			{
				session('acc', $_GET['acc']);
				session('name', $_GET['name']);
				return true;
			}
			$this->goLogin();
		}
	}

	protected function goLogin()
	{
		$result = array(
			'code'    => 2,
			'message' => '请登录'
		);
		$this->ajaxReturn($result);
	}

	protected function apiReturn($data = array(), bool $correct = true)
	{
		$result = array(
			'code'    => 0,
			'data'    => $data
		);
		if( ! $correct)
		{
			$result = array(
				'code'    => 1,
				'message' => $data
			);
		}
		$result = html_escape($result);
		$this->ajaxReturn($result);
	}

	private function wx()
	{
        if(empty(S('access_token')))
        {
	        $wx = new \Wx($this->appid, $this->secret);
	        S('access_token', $wx->getAccess_token, 3600);
	        return $wx;
        }
        return new \Wx($this->appid, $this->secret, S('access_token'));
	}
}