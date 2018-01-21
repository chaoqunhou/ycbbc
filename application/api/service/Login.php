<?php
namespace app\api\service;
use app\api\service\User;
use think\Exception;
use app\api\service\Token;
use app\api\service\UserOauth;

class Login extends BaseService
{
	public function __construct()
	{
		parent::__construct();
	}

    /**
     * @param $data
     * @return array
     * @throws Exception
     * 用户名登录
     */
	public function nameAndPassword($data)
	{
		$this->vData($data,'Login.nameAndPassword');
		$client_id 		= $data['client_id'];
		$device_open_id = $data['device_open_id'];

		$user_name 		= $data['user_name'];
		$user_password 	= md5($data['user_password']);

		$user = new User;
		$res  = $user->Vpwd($user_name,$user_password,'uid,user_name');
		if ($res == false) 		throw new Exception('帐号信息有误',1102);
		if (empty($res['uid']))	throw new Exception('miss uid',500);
		$user_id		= $res['uid'];
		$access_token	= Token::genToken();
		$refresh_token	= Token::genToken();
		$expires_in		= $this->expires_in;

		$user_Oauth		= new UserOauth();

		$resData = $user_Oauth ->save($user_id,$client_id,$device_open_id,$access_token,$refresh_token,$expires_in);
		return $resData;
	}

    /**
     * @param $data
     * @return array
     * @throws Exception
     * 手机号登录
     */
    public function telAndPassword($data)
    {
        $this->vData($data,'Login.telAndPassword');
        $client_id 		= $data['client_id'];
        $device_open_id = $data['device_open_id'];

        $user_tel 		= $data['user_name'];
        $user_password 	= md5($data['user_password']);

        $user = new User;
        $res  = $user->Tpwd($user_tel,$user_password,'uid,user_tel');
        if ($res == false) 		throw new Exception('帐号信息有误',1102);
        if (empty($res['uid']))	throw new Exception('miss uid',500);
        $user_id		= $res['uid'];
        $access_token	= Token::genToken();
        $refresh_token	= Token::genToken();
        $expires_in		= $this->expires_in;

        $user_Oauth		= new UserOauth();

        $resData = $user_Oauth ->save($user_id,$client_id,$device_open_id,$access_token,$refresh_token,$expires_in);
        return $resData;
    }

}