<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/11
 * Time: 09:51
 */

namespace app\common\validate;


use think\Validate;

class Common extends Validate
{
	protected $rule = [
		'id'	=> 'require|number|>=:0',
		'client_id'			=> 'require|between:1,10',
		'device_open_id'	=> 'require|alphaDash',

		'access_token'		=> 'require|alphaDash',
	];

	protected $scene = [
		'id'  	=> ['id'],
		'system'=> ['client_id','device_open_id'],
		'transformUser' =>['access_token'],
	];
}