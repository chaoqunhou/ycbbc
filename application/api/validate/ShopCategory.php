<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/10
 * Time: 15:51
 */

namespace app\common\validate;


use think\Validate;

class ShopCategory extends Validate
{
	protected $rule = [
		'id'	=> 'require|number|>:0',
		'goods_id'	=> 'require|number|>:0',
		'shop_id'	=> 'require',
        'category_id' =>'require|number|>=:0',
        'is_index'  =>'require|number|>:0',
        ''          =>''
	];

	protected $scene = [
        'getChildAction' => ['category_id'],
        'index' => ['is_index'],
        'getCategoryAction' => [''],
	];
}