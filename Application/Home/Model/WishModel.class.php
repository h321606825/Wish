<?php
namespace Home\Model;
use Think\Model;
class WishModel extends Model {

	private $errmsg; //错误信息

	public function stuconfirm($id){
		$sql = 'UPDATE `wish`
			set `done` = 1
			WHERE `id` = %d;
		';

		$res = $this->execute($sql,$id);

		if($res == false)
		{
			$this->errmsg = '确认失败';
			return false;
		}
		else
			return true;
	}


	public function tealist($u_id)
	{
		$wish_arr = array(
			'undone' =>array(),
			'unaccepted' => array(),
			'unevaluate' => array(),
			'done' =>array()
		);

		$sql = '
			SELECT `id`, `created`, `deadline`, `content`, `angel_id`,`angel_guy`,`angel_phone`
			FROM `wish`
			WHERE `u_id` = %d AND `deadline` > %d  AND `cancel_time` = 0 AND `done` = 0
			ORDER BY `id` DESC;
		';
		$wishes = $this->query($sql, $u_id, time());
		
		for($i = 0, $iloop =count($wishes); $i < $iloop; $i++)
		{
			$wishes[$i]['time'] = date('m月d日 H:s', $wishes[$i]['created']);
			unset($wishes[$i]['created']);
			$wishes[$i]['deadline'] = date('m月d日 H:s', $wishes[$i]['deadline']);

			//unset($wishes[$i]['angel_id']);
			if( ! empty($wishes[$i]['angel_id']))
				$wish_arr['undone'][] = $wishes[$i];
		}//未完成

		$sql = '
			SELECT `id`, `created`, `deadline`, `content`, `angel_id`
			FROM `wish`
			WHERE `u_id` = %d AND `deadline` > %d  AND `cancel_time` = 0 AND `angel_id` = 0
			ORDER BY `id` DESC;
		';
		$wishes = $this->query($sql, $u_id, time());
		
		for($i = 0, $iloop =count($wishes); $i < $iloop; $i++)
		{
			$wishes[$i]['time'] = date('m月d日 H:s', $wishes[$i]['created']);
			unset($wishes[$i]['created']);
			$wishes[$i]['deadline'] = date('m月d日 H:s', $wishes[$i]['deadline']);

			unset($wishes[$i]['angel_id']);
			$wish_arr['unaccepted'][] = $wishes[$i];
			
		}//未接受

		$sql = '
			SELECT `id`, `created`, `deadline`, `content`, `angel_id`, `angel_guy`, `angel_phone`
			FROM `wish`
			WHERE `u_id` = %d AND `deadline` > %d  AND `done` = 1 AND `quality` = 0 AND `cancel_time` = 0
			ORDER BY `id` DESC;
		';
		$wishes = $this->query($sql, $u_id, time());
		
		for($i = 0, $iloop =count($wishes); $i < $iloop; $i++)
		{
			$wishes[$i]['time'] = date('m月d日 H:s', $wishes[$i]['created']);
			unset($wishes[$i]['created']);
			$wishes[$i]['deadline'] = date('m月d日 H:s', $wishes[$i]['deadline']);

			unset($wishes[$i]['angel_id']);
			$wish_arr['unevaluate'][] = $wishes[$i];
			
		}//待评价

		$sql = '
			SELECT `id`, `created`, `deadline`, `content`, `angel_id`, `angel_guy`, `angel_phone`
			FROM `wish`
			WHERE `u_id` = %d AND `deadline` > %d  AND `done` = 1 AND `quality` != 0
			ORDER BY `id` DESC;
		';
		$wishes = $this->query($sql, $u_id, time());
		
		for($i = 0, $iloop =count($wishes); $i < $iloop; $i++)
		{
			$wishes[$i]['time'] = date('m月d日 H:s', $wishes[$i]['created']);
			unset($wishes[$i]['created']);
			$wishes[$i]['deadline'] = date('m月d日 H:s', $wishes[$i]['deadline']);

			switch ($wishes[$i]['quality']) {
				case 100:
					$wishes[$i]['quality'] = 'A';
					break;
				case 80:
					$wishes[$i]['quality'] = 'B';
					break;
				case 60:
					$wishes[$i]['quality'] = 'C';
					break;
				case 40:
					$wishes[$i]['quality'] = 'D';
					break;
				default:
					$wishes[$i]['quality'] = '';
					break;
			}

			unset($wishes[$i]['angel_id']);
			$wish_arr['done'][] = $wishes[$i];
			
		}

		return $wish_arr;
	}

	public function stulist($au_id)
	{
		$wish_arr = array(
			'unevaluate' =>array(),//未评价
			'unaccepted' => array(),//未认领
			'undone' => array(),//未完成
			'done' => array(),//完成
		);

		$sql = '
			SELECT `id`, `created`, `deadline`, `content`
			FROM `wish`
			WHERE `deadline` > %d  AND `angel_id` = 0 AND `cancel_time` = 0
			ORDER BY `id`;
		';
		$wishes = $this->query($sql, time());
		for($i = 0, $iloop =count($wishes); $i < $iloop; $i++)
		{
			$wishes[$i]['time'] = date('m月d日 H:s', $wishes[$i]['created']);
			unset($wishes[$i]['created']);
			$wishes[$i]['deadline'] = date('m月d日 H:s', $wishes[$i]['deadline']);

			$wish_arr['unaccepted'][] = $wishes[$i];
		}//未接受

		$sql='
			SELECT `id`, `created`, `deadline`, `content`, `angel_id`, `angel_guy`, `angel_phone`
			FROM `wish`
			WHERE `angel_id` = %d AND `deadline` > %d AND `done` = 0 AND `cancel_time` = 0 
			ORDER BY `id` DESC;
		';

		$wishes = $this->query($sql, $au_id, time());

		for($i = 0, $iloop =count($wishes); $i < $iloop; $i++)
		{
			$wishes[$i]['time'] = date('m月d日 H:s', $wishes[$i]['created']);
			unset($wishes[$i]['created']);
			$wishes[$i]['deadline'] = date('m月d日 H:s', $wishes[$i]['deadline']);

			if( !empty($wishes[$i]['angel_id']))
				$wish_arr['undone'][] = $wishes[$i];
		}//未完成

		$sql='
			SELECT `id`, `created`, `deadline`, `content`, `angel_id`, `angel_guy`, `angel_phone`
			FROM `wish`
			WHERE `angel_id` = %d AND `deadline` > %d AND `done` = 1  AND `quality` = 0 AND `cancel_time` = 0
			ORDER BY `id` DESC;
		';
		$wishes = $this->query($sql, $au_id, time());
		for($i = 0, $iloop =count($wishes); $i < $iloop; $i++)
		{
			$wishes[$i]['time'] = date('m月d日 H:s', $wishes[$i]['created']);
			unset($wishes[$i]['created']);
			$wishes[$i]['deadline'] = date('m月d日 H:s', $wishes[$i]['deadline']);

			
			$wish_arr['unevaluate'][] = $wishes[$i];

		}//未评价

		$sql='
			SELECT `id`, `created`, `deadline`, `content`, `angel_id`, `angel_guy`, `angel_phone`
			FROM `wish`
			WHERE `angel_id` = %d AND `deadline` > %d AND `done` = 1  AND `quality`!= 0
			ORDER BY `id` DESC;
		';
		$wishes = $this->query($sql, $au_id, time());
		for($i = 0, $iloop =count($wishes); $i < $iloop; $i++)
		{
			$wishes[$i]['time'] = date('m月d日 H:s', $wishes[$i]['created']);
			unset($wishes[$i]['created']);
			$wishes[$i]['deadline'] = date('m月d日 H:s', $wishes[$i]['deadline']);

			$wish_arr['done'][] = $wishes[$i];

		}//已完成

		return $wish_arr;
	}

	public function admlist()
	{
		$wish_arr = array(
			'undone' =>array(),//未完成
			'unaccepted' => array(),//未认领
			//'unconfirm' => array(),//未queren
			'unevaluate' => array(),//未评价
			'done' => array(),//完成
		);

		$sql='
		SELECT `id`, `created`, `deadline`, `content`, `angel_id`, `angel_guy`, `angel_phone`
		FROM `wish`
		WHERE `deadline` > %d AND `done` = 0 AND `cancel_time` = 0
		ORDER BY `id` DESC;
		';
		$wishes = $this->query($sql, time());

		for($i = 0, $iloop =count($wishes); $i < $iloop; $i++)
		{
			$wishes[$i]['time'] = date('m月d日 H:s', $wishes[$i]['created']);
			unset($wishes[$i]['created']);
			$wishes[$i]['deadline'] = date('m月d日 H:s', $wishes[$i]['deadline']);

			//unset($wishes[$i]['angel_id']);
			if( !empty($wishes[$i]['angel_id']))
				$wish_arr['undone'][] = $wishes[$i];
		}//未完成

		$sql = '
			SELECT `id`, `created`, `deadline`, `content`
			FROM `wish`
			WHERE `deadline` > %d  AND `angel_id` = 0 AND `cancel_time` = 0
			ORDER BY `id`;
		';
		$wishes = $this->query($sql, time());
		for($i = 0, $iloop =count($wishes); $i < $iloop; $i++)
		{
			$wishes[$i]['time'] = date('m月d日 H:s', $wishes[$i]['created']);
			unset($wishes[$i]['created']);
			$wishes[$i]['deadline'] = date('m月d日 H:s', $wishes[$i]['deadline']);

			$wish_arr['unaccepted'][] = $wishes[$i];
		}//未接受

		$sql='
			SELECT `id`, `created`, `deadline`, `content`, `angel_id`, `angel_guy`, `angel_phone`
			FROM `wish`
			WHERE `deadline` > %d AND `done` = 1 AND `quality` = 0 AND `cancel_time` = 0
			ORDER BY `id` DESC;
		';
		$wishes = $this->query($sql, time());
		for($i = 0, $iloop =count($wishes); $i < $iloop; $i++)
		{
			$wishes[$i]['time'] = date('m月d日 H:s', $wishes[$i]['created']);
			unset($wishes[$i]['created']);
			$wishes[$i]['deadline'] = date('m月d日 H:s', $wishes[$i]['deadline']);

			
			$wish_arr['unconfirm'][] = $wishes[$i];

		}//待确认

		$sql='
			SELECT `id`, `created`, `deadline`, `content`, `angel_id`, `angel_guy`, `angel_phone`
			FROM `wish`
			WHERE `deadline` > %d AND `done` = 1 AND `quality` = 0 AND `cancel_time` = 0
			ORDER BY `id` DESC;
		';
		$wishes = $this->query($sql, time());
		for($i = 0, $iloop =count($wishes); $i < $iloop; $i++)
		{
			$wishes[$i]['time'] = date('m月d日 H:s', $wishes[$i]['created']);
			unset($wishes[$i]['created']);
			$wishes[$i]['deadline'] = date('m月d日 H:s', $wishes[$i]['deadline']);

			
			$wish_arr['unevaluate'][] = $wishes[$i];

		}//未评价

		$sql='
			SELECT `id`, `created`, `deadline`, `content`, `angel_id`, `angel_guy`, `angel_phone`
			FROM `wish`
			WHERE `deadline` > %d AND `done` = 1   AND `quality`!= 0
			ORDER BY `id` DESC;
		';
		$wishes = $this->query($sql, time());
		for($i = 0, $iloop =count($wishes); $i < $iloop; $i++)
		{
			$wishes[$i]['time'] = date('m月d日 H:s', $wishes[$i]['created']);
			unset($wishes[$i]['created']);
			$wishes[$i]['deadline'] = date('m月d日 H:s', $wishes[$i]['deadline']);

			$wish_arr['done'][] = $wishes[$i];

		}//已完成

		return $wish_arr;
	}

	public function pub($u_id, $content,  $guy, $phone, $deadline)
	{
		$sql = '
			INSERT INTO `wish`(`u_id`, `content`, `guy`, `phone`, `deadline`, `created`)
			VALUES(%d, "%s", "%s", "%s", %d, %d);
		';
		$this->execute($sql, $u_id, $content, $guy, $phone, $deadline, time());
		return true;
	}

	public function wishInfo($id)
	{
		$sql = '
			SELECT `content`, `quality`, `guy`, `phone`, `deadline`, `created` `time`, `angel_guy`, `angel_phone`, `done`,  `cancel_reason`
			FROM `wish`
			WHERE `id` = %d;
		';
		$wish = $this->query($sql, $id);
		if(empty($wish))
		{
			$this->errmsg = '心愿不存在';
			return false;
		}
		$wish = $wish[0];
		if( ! empty($wish['cancel_reason']))
		{
			$this->errmsg = '心愿已被取消';
			return FALSE;
		}
		unset($wish['cancel_reason']);
		if(empty($wish['angel_guy']))
			$wish['state'] = 4;//weijieshou
		else if( !empty($wish['done']) AND !empty($wish['done1']))
				{
					if(empty($wish['quality']))
						$wish['state'] = 3;//weipingjia
					else
						$wish['state'] = 1;//= array('undone' => 1);//wancheng
				}
		else
			$wish['state'] = 2;//weiwancheng


		if( ! empty($wish['angel_guy']))
			$wish['angel'] = array(
				'guy' => $wish['angel_guy'],
				'phone' => $wish['angel_phone']//
			);
		switch ($wish['quality']) {
			case '100':
				$wish['quality'] = 'A';
				break;
			case '80':
				$wish['quality'] = 'B';
				break;
			case '60':
				$wish['quality'] = 'C';
				break;
			case '40':
				$wish['quality'] = 'D';
				break;
			default:
				$wish['quality'] = '';
				break;
		}

		$week = array('日', '一', '二', '三', '四', '五', '六');
		$wish['time'] = date('m月d日 星期', $wish['time']) . $week[date('w', $wish['time'])] . date(' H:s', $wish['time']);
		$wish['deadline'] = date('m月d日 星期', $wish['deadline']) . $week[date('w', $wish['deadline'])] . date(' H:s', $wish['deadline']);
		unset($wish['angel_guy']);
		unset($wish['angel_phone']);
		unset($wish['done']);
		unset($wish['done1']);
		return $wish;
	}

	public function cancel($id, $reason)
	{
		$u_id = session('user');
		$sql = '
			SELECT `u_id`
			FROM `wish`
			WHERE `id` = %d AND `cancel_time` = 0;
		';
		$u_id_sql = $this->query($sql, $id);
		if(empty($u_id))
		{
			$this->errmsg = '心愿不存在';
			return FALSE;
		}
		if($u_id !== $u_id_sql[0]['u_id'])
		{
			$this->errmsg = '您不是该心愿的发布者';
			return FALSE;
		}

		$sql = '
			UPDATE `wish`
			SET `cancel_reason` = "%s", `cancel_time` = %d
			WHERE `id` = %d;
		';
		$flag = $this->execute($sql, array($reason, time(), $id));
		if($flag != 0)
			return true;
		else
			return false;

	}

	public function accept($id, $u_id, $guy, $phone)
	{
		$sql = '
			SELECT `angel_guy`
			FROM `wish`
			WHERE wish.`id` = %d;
		';
		$accepted = $this->query($sql, $id);
		if(empty($accepted))
		{
			$this->errmsg = '心愿不存在';
			return FALSE;
		}
		if( ! empty($accepted[0]['angel_guy']) )
		{
			$this->errmsg = '心愿已被接受';
			return FALSE;
		}
		
		$sql = '
			UPDATE `wish`
			SET `angel_id` = %d, `angel_guy` = "%s", `angel_phone` = "%s"
			WHERE `id` = %d;
		';
		$this->execute($sql, array($u_id, $guy, $phone, $id));
		return true;
	}


	public function confirm($id, $u_id, $time, $quality)
	{
		$sql = '
			SELECT `u_id`, `angel_id`,  `done`
			FROM `wish`
			WHERE `id` = %d;
		';
		$u_id_sql = $this->query($sql, $id);
		if(empty($u_id_sql))
		{
			$this->errmsg = '心愿不存在';
			return FALSE;
		}
		if($u_id != $u_id_sql[0]['u_id'])
		{
			$this->errmsg = '您不是该心愿的发布者';
			return FALSE;
		}
		if(empty($u_id_sql[0]['angel_id']))
		{
			$this->errmsg = '心愿未被接受';
			return FALSE;
		}
		if( ! ($u_id_sql[0]['done']))
		{
			$this->errmsg = '心愿未完成';
			return false;
		}
		
		switch ($quality) {
			case 'A':
				$quality = 100;
				break;
			case 'B':
				$quality = 80;
				break;
			case 'C':
				$quality = 60;
				break;
			case 'D':
				$quality = 40;
				break;
			default:
				$quality = 0;
				break;
		}
		$sql = '
			UPDATE `wish`
			SET `work_time` = %d, `quality` = %d
			WHERE `id` = %d;
		';
		$this->execute($sql, $time, $quality, $id);
		return true;
	}

	public function admassign($id,$angel_id,$angel_guy,$angel_phone){

		$sql='UPDATE `wish`
			set `angel_id` = %d,
			`angel_guy` = "%s",
			`angel_phone` = "%s"
			where `id` = %d;
		';
		$wish_sql=$this->execute($sql,$angel_id,$angel_guy,$angel_phone,$id);
		
		if( 0 === $wish_sql )
		{
			$this->errmsg = '分配失败！';
			return false;
		}

		$sql='
			INSERT  into `assign`
			(`wish_id` , `angel_id`)
			VALUES (%d,%d);
		';
		$ass_sql = $this->execute($sql,$id,$angel_id);
		if( 0 == $ass_sql)
		{
			$this->errmsg = '分配失败';
			return false;
		}
		return true;
	}


	public function assignlist()
	{
		$sql = '
			SELECT `id`, `created`, `deadline`, `content`, wish.`angel_id`, `done`,`cancel_time`
			FROM `wish`
			WHERE  id in ( SELECT wish_id FROM assign) AND `deadline` > %d
			ORDER BY `done` DESC;
		';
		$wishes = $this->query($sql, time());
		$wish_arr = array(
			'assigned' => array(),
			'unassigned' => array(),
			'cancel' => array(),
		);
		for($i = 0, $iloop =count($wishes); $i < $iloop; $i++)
		{
			$wishes[$i]['time'] = date('m月d日 H:s', $wishes[$i]['created']);
			unset($wishes[$i]['created']);
			$wishes[$i]['deadline'] = date('m月d日 H:s', $wishes[$i]['deadline']);
			
			if($wishes[$i]['cancel_time'] != 0)
			{
				unset($wishes[$i]['angel_id']);
				$wish_arr['cancel'][] = $wishes[$i];
			}
			else
			{
				unset($wishes[$i]['angel_id']);
				$wish_arr['assigned'][] = $wishes[$i];
			}
		}

		$sql = '
			SELECT `id`, `created`, `deadline`, `content`, `angel_id`, `done`
			FROM `wish`
			WHERE `angel_id` = 0 AND `deadline` > %d AND `cancel_time` = 0 AND `done` = 0;
		';
		$wishes = $this->query($sql, time());

		for($i = 0, $iloop =count($wishes); $i < $iloop; $i++)
		{
			$wishes[$i]['time'] = date('m月d日 H:s', $wishes[$i]['created']);
			unset($wishes[$i]['created']);
			$wishes[$i]['deadline'] = date('m月d日 H:s', $wishes[$i]['deadline']);

			if(empty($wishes[$i]['angel_id']))
			{
				unset($wishes[$i]['angel_id']);
				$wish_arr['unassigned'][] = $wishes[$i];
			}
			
		}

		return $wish_arr;
	}

	public function getError()
	{
		return $this->errmsg;
	}

}