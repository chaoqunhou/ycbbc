<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/10
 * Time: 15:51
 */

namespace app\common\validate;


use think\Validate;

class Cart extends Validate
{
	protected $rule = [
		'id'	        => 'require|number|>:0',
		'goods_id'	    => 'require|number|>:0',
		'shop_id'	    => 'require',
		'access_token' 	=> 'require|alphaDash',
		'sku_id'		=> 'require|number',
		'num'			=> 'require|number',
		'cart_id'		=> 'require|number',
		'is_check'		=> 'require|number',
		'is_fast_buy'		=> 'require|number|=:1',
        'mansong_id'    =>'require|number',
        'promotion_rule_id'    =>'require|number',
	];

	protected $scene = [
		'save'          =>  ['access_token','sku_id','num'],
        'chooseAction'  =>  ['access_token','cart_id','is_check'],
        'update'        =>  ['access_token','num','cart_id'],
        'delete'        =>  ['access_token','cart_id'],
        'readCartAction'    =>  ['access_token'],
        'readOrderAction'   =>  ['access_token' ],
        'buyNowAction'   =>  ['access_token' ,'is_fast_buy'],
        'promotionAction'   =>  [ 'access_token' ],
        'promotionPriceAction'   =>  [ 'access_token' ,'mansong_id'],
        'promotionChooseAction'   =>  [ 'access_token' ,'promotion_rule_id','cart_id'],
	];
}