<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/10
 * Time: 15:51
 */

namespace app\common\validate;


use think\Validate;

class User extends Validate
{
	protected $rule = [
		'id'		        => 'require|number|>:0',
		'goods_id'	        => 'require|number|>:0',
		'shop_id'	        => 'require',

		'type'				=> 'require|number|>:0',
		'client_id'			=> 'require|between:1,10',
		'device_open_id'	=> 'require|alphaDash',
		'key'	            => 'require|alphaDash',

		'user_name'		    => 'require',
		'user_password'	    => 'require|alphaDash',
		'new_password'	    => 'require|alphaDash',
		'old_password'	    => 'require|alphaDash',

		'refresh_token'		=> 'require|alphaDash',
		'mobile'		    => 'require|max:11|/^1[3-8]{1}[0-9]{9}$/',
        'type_id'           => 'require|number|>:0',
		'code'		        => 'require|number',
		'invitecode'		        => 'require',
	];

	protected $scene = [
//		'loginAction'		    => ['type','client_id','device_open_id'],
		'loginAction'		    => ['type'],
		'Vpwd'				    => ['user_name','user_password'],
		'Tpwd'				    => ['user_tel','user_password'],
		'refreshTokenAction'    => ['refresh_token'],
		'save'				    => ['user_name','user_password'],
        'smsAction'		    	=> ['mobile','type_id'],
		'registerAction'	    => ['mobile','user_password','code'],
		'changePasswordAction'	=> ['mobile','new_password','old_password'],
		'findPasswordAction'	=> ['new_password','key'],
		'findVerifyAction'	    => ['mobile','code'],
        'usBundAction'       => ['invitecode']
	];
}