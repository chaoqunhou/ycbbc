<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/10
 * Time: 15:51
 */

namespace app\common\validate;


use think\Validate;

class SmsSend extends Validate
{
	protected $rule = [
		'type_id'		        => 'require|number|>:0',
		'mobile'		    => 'require|max:11|/^1[3-8]{1}[0-9]{9}$/',
	];

	protected $scene = [
		'smsAction'			=> ['moblie','type_id'],
	];
}