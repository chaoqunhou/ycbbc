<?php
namespace app\common\validate;


use think\Validate;

class Search extends Validate
{
	protected $rule = [
		'id'	=> 'require|number|>:0',
		'goods_id'	=> 'require|number|>:0',
		'shop_id'	=> 'require',
		'category_id' =>'require|number|>=:0',
	];

	protected $scene = [
		'searchAction' => [''],
	];
}