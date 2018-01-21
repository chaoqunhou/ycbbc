<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/10
 * Time: 15:51
 */

namespace app\common\validate;


use think\Validate;

class Login extends Validate
{
	protected $rule = [
		'id'		=> 'require|number|>:0',
		'goods_id'	=> 'require|number|>:0',
		'shop_id'	=> 'require',

		'type'				=> 'require|number|>:0',
		'client_id'			=> 'require|between:1,10',
		'device_open_id'	=> 'require|alphaDash',

		'user_name'		=> 'require|alphaDash',
		'user_password'	=> 'require|alphaDash',
	];

	protected $scene = [
		'nameAndPassword'	=> ['client_id','device_open_id','user_name','user_password'],
		'telAndPassword'	=> ['client_id','device_open_id','user_name','user_password'],
	];
}