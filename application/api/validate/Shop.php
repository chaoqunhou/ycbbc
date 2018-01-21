<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/10
 * Time: 15:51
 */

namespace app\common\validate;


use think\Validate;

class Shop extends Validate
{
	protected $rule = [
		'id'	=> 'require|number|>:0',
		'shop_id'	=> 'require|number',
	];

	protected $scene = [
        'readShopAdPicAction' => ['shop_id'],
        'readAction' => ['access_token']
	];
}