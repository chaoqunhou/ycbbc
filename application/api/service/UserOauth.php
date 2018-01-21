<?php
namespace app\api\service;
use app\api\model\UserOauthModel;
use think\Exception;

class UserOauth extends BaseService
{
	public function index()
	{
		// TODO: Implement index() method.
	}

	public function create()
	{
		// TODO: Implement create() method.
	}

	public function read($access_token)
	{
		$uOauthM = new UserOauthModel;
		$uOauth  = $uOauthM->where(['access_token'=>$access_token])->field('user_id,client_id,expires_in,update_time')->find();
		if (is_object($uOauth) && !empty($uOauth->user_id) && !empty($uOauth->expires_in) && !empty($uOauth->update_time)){
			$expiry_time =  $uOauth->expires_in + $uOauth->update_time;
			if (time() > $expiry_time) throw new Exception('已过期',400);
			return $uOauth->user_id;
		}
		throw new Exception('accessToken没有对应',400);
	}

	public function edit($id)
	{
		// TODO: Implement edit() method.
	}

	public function save($user_id,$client_id,$device_open_id,$access_token,$refresh_token,$expires_in)
	{
		$uOauthM = new UserOauthModel;
		$update_time = time();
		$uOauthM->where(['user_id'=>$user_id,'device_open_id'=>$device_open_id]);
		$uOauth = $uOauthM::get();
		if ($uOauth == null){
			$uOauthM ->save([
					'user_id' => $user_id,
					'client_id' => $client_id,
					'device_open_id' => $device_open_id,
					'access_token' => $access_token,
					'refresh_token' => $refresh_token,
					'expires_in' => $expires_in,
					'update_time' => $update_time,
				]
			);
		}else{
            $id = $uOauth->id;
			$uOauthM = $uOauthM::get($id) ;
			$uOauthM->save([
					'user_id' => $user_id,
					'client_id' => $client_id,
					'device_open_id' => $device_open_id,
					'access_token' => $access_token,
					'refresh_token' => $refresh_token,
					'expires_in' => $expires_in,
					'update_time' => $update_time,
				]
			);
		}
		return ['access_token' => $uOauthM->access_token ,'refresh_token' =>  $uOauthM->refresh_token,'expires_in' => $uOauthM->expires_in,];
	}

	public function update($refresh_token)
	{
		//new
		$uOauthM = new UserOauthModel;
		$uOauth  = $uOauthM->where(['refresh_token'=>$refresh_token])->field('id,update_time')->find();
		if(is_object($uOauth) && !empty($uOauth->id) && !empty($uOauth->update_time)){
			if (time() > $uOauth->update_time + $this->refresh_expires_in) throw new Exception('已过期',400);
			$uOauthM = $uOauthM::get($uOauth->id);

			$refresh_token	= Token::genToken();
			$access_token	= Token::genToken();
			$update_time	= time();

			$res = $uOauthM->save([
				'access_token' => $access_token,
				'refresh_token' => $refresh_token,
				'update_time' => $update_time,
			]);
			if ($res){
				return $res;
			}
		}
		throw new Exception('未找到对应refresh_token',400);
	}

	public function delete($id)
	{
		// TODO: Implement delete() method.
	}


}