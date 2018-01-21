<?php
/**
 * Created by PhpStorm.
 * User: fei
 * Date: 2017/12/10
 * Time: 18:28
 */
namespace app\api\controller;

use app\api\api\StandardInterface;
use think\Controller;
use think\Exception;
use think\Request;
use app\api\service\UserOauth;

class baseApi extends Controller
{
	protected $assignData = [];
	protected $uid = 0;
	protected $uidMsg = '';


	public function __construct(Request $request = null)
	{
		header("Access-Control-Allow-Origin: *");
		parent::__construct($request);

		try{
			$this->checkData();
			$this->pre_validate();
			//转换用户登陆信息
			$this->checkUser();

		}catch (Exception $e){
			returnJson($e->getMessage(),$e->getCode());
		}
	}

	/**
	 * api 验参层
	 * @return bool
	 * @throws Exception
	 */
	private function pre_validate()
	{
		$v_msg = '';
		$v_fillable = ['index','create'];
		if ( in_array(request()->action(),$v_fillable) ){
			$v_msg .= 'pass';
			return true;
		}

		if( $this instanceof StandardInterface){
			$v_id_must  = ['read','edit','delete','update'];		$v_id_must_key = 'common.id';
			if (in_array(request()->action(),$v_id_must)){
				$v_msg .= 'common.id';
				$result = $this->vData( input(),$v_id_must_key );
			}else{
				$key = explode('.',request()->controller())[1].'.'.request()->action();
				$v_msg .= $key;
				$result = $this->vData(input(),$key);
			}

			if ($result !== true){
				throw new Exception($result.'  ['.$v_msg.']',300);
			}
		}
	}

	//转换用户登陆信息 todo
	private function checkUser()
	{
		$param = input('param.');
		try{
			$this->vData($param,'common.transformUser');
			$UserOauth = new UserOauth;
			$this->uid = $UserOauth->read($param['access_token']);
			$_SERVER['uid'] = $this->uid;
            $shop_id = db('sys_shop_user_bund')->field('shop_id')->order('record','desc')->where('user_id',$_SERVER['uid'])->limit(1)->find();
            if(!empty($shop_id)){
                $_SERVER['shop_id'] = $shop_id['shop_id'];
            }
            if (explode('.',request()->controller())[1] == 'user'){
                return;
            }else{
                throw new Exception('未绑定店铺，需要绑定',301);
            }
            $this->uidMsg = 'succ';

		}catch (Exception $e){
			$this->uidMsg = $e->getMessage();
		}
	}

	private function checkData()
	{
		$v_msg = 'common.system';
		$this->vData(input(),$v_msg);

		if(input('key') === null){
//			throw new Exception('key null',300);
		}

		//$par = [
		//	'id' => 1,
		//	'data' => [
		//		'ch' => 'a1',
		//		'ch2' => 'abc',
		//	],
		//];
		//$this->sign($par);
	}
	private function sign($params, $token='[token]') {
		$sign = $this->assemble($params).$token;
		//dump($sign);
		$sign = strtoupper(md5($sign));
		return $sign;
	}

	private function assemble($params) {
		if(!is_array($params))  return null;
		ksort($params, SORT_STRING);
		$sign = '';
		foreach($params AS $key=>$val){
			if(is_null($val))   continue;
			if(is_array($val))  continue;
			if(is_bool($val))   $val = ($val) ? 1 : 0;
			$sign .= $key . $val;
		}
		return $sign;
	}

	protected function assignJson($name,$data)
	{
		$this->assignData[$name] = $data;
	}
}