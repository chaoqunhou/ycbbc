<?php
namespace app\api\controller\v1;

use app\api\controller\baseApi;
use app\api\api\StandardInterface;
use app\api\service\SmsSend;
use app\api\service\User as UserService;
use think\Exception;
use app\api\service\Login;
use app\api\service\UserOauth;

class User extends baseApi implements StandardInterface
{
	private $returnData = [];

	public function save()
	{
		echo "save";
	}

	public function loginAction()
	{
		try {
            $postData   = input('post.');
			switch ($postData['type']){
				case 1:

					$login      = new Login();
					$this -> returnData = $login -> nameAndPassword($postData);
					break;
                case 2;
                    $login      = new Login();
                    $this -> returnData = $login -> telAndPassword($postData);
                    break;
				default:
					throw new Exception('不支持登陆类型',1101);
			}
		}catch (Exception $e){
			returnJson($e->getMessage(),$e->getCode());
		}

		returnJson($this->returnData);
	}

    public function usBundAction()
    {
        try{
            if (empty($_SERVER['uid'])) throw new Exception('需要登陆', 1100);
            $uid = $_SERVER['uid'];
            $param = input('param.');
            $userservice = new UserService();
            $shopinfo = $userservice->usershopBund($uid,$param['invitecode']);
            $res = $shopinfo->toArray();//find方法查询的结果对象直接有toArray方法可以转数组，select方法需要collection($shopinfo)->toArray
        }catch(Exception $e){
            returnJson($e -> getMessage() , $e -> getCode() );
        }
        returnJson($res);
    }

	public function refreshTokenAction()
	{
		$param          = input('param.');
		$refresh_token  = $param['refresh_token'];
		try {
			$user_Oauth = new UserOauth();
			$res        = $user_Oauth -> update($refresh_token);
		}catch (Exception $e){
			returnJson($e->getMessage(),$e->getCode());
		}
		returnJson($res);
	}


    /**
     * 发送验证码
     * @param unknown $smsParam
     * @param unknown $send_mobile
     * @param unknown $template_code
     * 需要传 mobile 还有发送短信的标记 type_id
     */
    //$appkey, $secret, $signName, $smsParam, $send_mobile, $template_code
   public function smsAction()
    {
        try{
            $smsData    = input('param.');
            $smsSendS   = new SmsSend();
            $res        = $smsSendS ->smsCode($smsData);
        }catch(Exception $e){
            returnJson($e ->getMessage(),$e ->getCode());
        }
        returnJson($res);
    }

    /**
     * 用户注册
     */

    public function registerAction(){
       try{
           $userInfo =input('param.');
           $userS   = new UserService();
           $res     = $userS -> register($userInfo);
       }catch(Exception $e){
           returnJson($e -> getMessage(),$e -> getCode());
       }
       returnJson($res);
    }
    /**
     * 修改密码
     */
    public function changePasswordAction(){
        try{
            $oldInfo =input('param.');
            $userS   = new UserService();
            $res     = $userS -> changePassword($oldInfo);
        }catch(Exception $e){
            returnJson($e -> getMessage(),$e -> getCode());
        }
        returnJson($res);
    }
    /**
     * 找回密码
     */

    public function findPasswordAction(){
        try{
            $changeData = input('param.');
            $userS      = new UserService();
            $res        = $userS -> changePassword($changeData);
        }catch(Exception $e){
            returnJson($e -> getMessage(),$e -> getCode());
        }
        returnJson($res);
    }

    /**
     * 找回密码验证手机号
     *
     */
    public function findVerifyAction(){
        try{
            $verify     = input('param.');
            $userS      = new UserService();
            $res        = $userS -> findPasswordVerify($verify);
        }catch(Exception $e){
            returnJson($e -> getMessage(),$e -> getCode());
        }
        returnJson($res);
    }


}