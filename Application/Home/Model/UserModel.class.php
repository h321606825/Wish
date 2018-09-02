<?php
namespace Home\Model;
use Think\Model;
class UserModel extends Model {

	private $errmsg; //错误信息

	public function inByOpenid($openid)
	{
		$sql = '
			SELECT `id`
			FROM `user`
			WHERE `openid` = "%s";
		';
		$user = $this->query($sql, $openid);
		if(empty($user))
			return false;
		else
			return $user[0]['id'];
	}

	public function register($account,$phone)
	{
		
		//var_dump($account);die;
		$sql ='UPDATE user
			set `phone` = "%s"
			WHERE `id` = %d;
		';
		$res = $this->execute($sql,$phone,$account);
		if($res != false)
			return true;
		else
			return false;
	}

	public function stuname($au_id)
	{
		$sql = 'SELECT `nickname`
			from `student`
			WHERE `account` = "%s";
		';
		$res = $this->query($sql,$au_id);
		if( ! empty($res))
			return $res[0]['nickname'];
		else
			{
				$this->errmsg = '找不到该同学';
				return false;
			}
	}

	public function stu_register($account,$phone,$nickname)
	{
		$sql = 'insert into `student` ( `account`,`nickname`,`phone`)
		values ( "%s", "%s", "%s");
		';
		$res = $this->execute($sql,$account,$nickname,$phone);

		if($res == false)
			return false;
		else
			return true;

	}

	public function alter($account,$phone){
		$sql = 'UPDATE user
			set `phone` = "%s"
			WHERE `id` = %d;
		';
		$res = $this->execute($sql,$phone,$account);

		if($res != false)
			return true;
		else
			return false;
	}


	public function tea_info($id){
		
		$sql = 'SELECT `phone`,`account`,`nickname`
		FROM user
		where `id` = %d;
		';

		$res = $this->query($sql,$id);
		
		if(empty($res)){
			return false;
		}else{
			return $res['0'];
		}
	}

	public function loginadmin($account, $password)
	{
		$sql = '
			SELECT `id`, `openid`, `password`,`admin`
			FROM `user`
			WHERE `account` = "%s";
		';
		$user = $this->query($sql, $account);
		if(empty($user))
		{
			$this->errmsg = '该账号不存在';
			return false;
		}
		if($user[0]['password'] !== md5($password))
		{
			$this->errmsg = '密码错误';
			return false;
		}

		session('adm', $user[0]['id']);
		if( ! empty(session('openid')) && session('openid') !== $user[0]['openid'])
		{
			$sql = '
				UPDATE `user`
				SET `openid` = "%s"
				WHERE `id` = %d;
			';
			$this->execute($sql, sesison('openid'), $user[0]['id']);
		}
		if($user[0]['admin'] == 1)
		{
			session('adm',$user[0]['id']);
			return 1;
		}
		else 
			return false;
	}
	public function loginteacher($account, $password)
	{
		$sql = '
			SELECT `id`, `openid`, `password`
			FROM `user`
			WHERE `account` = "%s";
		';
		$user = $this->query($sql, $account);
		if(empty($user))
		{
			$this->errmsg = '该账号不存在';
			//echo $errmsg;
			return false;
		}
		if($user[0]['password'] !== md5($password))
		{
			$this->errmsg = '密码错误';
			//echo $errmsg;
			return false;
		}
		session('user', $user[0]['id']);
		if( ! empty(session('openid')) && session('openid') !== $user[0]['openid'])
		{
			$sql = '
				UPDATE `user`
				SET `openid` = "%s"
				WHERE `id` = %d;
			';
			$this->execute($sql, sesison('openid'), $user[0]['id']);
		}

		return 1;
	}

	public function loginstudent($account, $password)
	{
		Vendor('Nefu');
		$this->nefu = new \Nefu("",$account, $password);

		$password = strtoupper(md5($password));
		$result = $this->nefu->getInstance($account, $password);
		
		if(empty($result))
		{
			$this->errmsg = '登录失败';
			return false;
		}
		session('acc', $account);//存学生学号
		return 1;
		
	}

	public function userInfo($id)
	{
		$sql = '
			SELECT `nickname` `guy`, `phone`
			FROM `user`
			WHERE `id` = %d;
		';
		$user = $this->query($sql, $id);
		if(empty($user))
		{
			$this->errmsg = '用户不存在';
			return false;
		}
		$user[0]['deadline_d'] = date('Y-m-d', time() + 86400);
		$user[0]['deadline_t'] = date('H:i', time() + 86400);
		return $user[0];
	}

	public function stuphone($au_id){
		$sql = 'SELECT `phone`
			from `student`
			WHERE `account` = %d;
		';
		$res = $this->query($sql,$au_id);
		if( ! empty($res))
			return $res[0]['phone'];
		else
			{
				$this->errmsg = '电话号码为空';
				return false;
			}
	}

	public function studentInfo($id) {
		$sql = '
			SELECT `work_time`, `quality`
			FROM `wish`
			WHERE `done` = 1
				AND `angel_id` = %d
		';
		$user = $this->query($sql, $id);
		if(empty($user))
			return 0;
		$time = 0;
		foreach ($user as $value) {
			$time += $value['work_time'] * $value['quality'] / 100;
		}
		$time = (int)$time;
		if ($time < 60) {
			$time .= 'min';
		} else {
			$time = (int)($time / 60) . 'h' . ($time % 60) . 'min';
		}
		return $time;
	}
	
	public function getError()
	{
		return $this->errmsg;
	}
}