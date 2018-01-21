<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/10
 * Time: 15:51
 */

namespace app\common\validate;


use think\Validate;

class Address extends Validate
{
	protected $rule = [
	    'id'            => 'require|number',
		'access_token' 	=> 'require|alphaDash',
        'consigner'     => 'require',
        'mobile'        => 'require|max:11|/^1[3-8]{1}[0-9]{9}$/',
        'province'      => 'require',
        'city'          => 'require',
        'district'      => 'require',
        'address'       => 'require',
        'is_default'       => 'require|number',
        'zipcode'       => '/^[0-9]\d{5}(?!\d)/',

	];

	protected $scene = [
        'save'          =>  ['consigner','mobile','province','city','district','address','access_token','zipcode','is_default'],
        'update'        =>  ['consigner','mobile','province','city','district','address','access_token','zipcode','is_default'],
        'updateAction'  =>  ['consigner','mobile','province','city','district','address','access_token','zipcode','id','is_default'],
        'readAction'    =>  ['access_token'],
        'editAction'          =>  ['access_token'],
        'delete'          =>  ['id'],
        'defaultAction'          =>  ['id','is_default','access_token' ],
        'getProvinceAction'          =>  [ 'access_token' ],
        'getCityAction'          =>  [ 'access_token','province' ],
        'getDistrictAction'          =>  [ 'access_token','city' ],
        'getAllAction'      => ['']
        ];
}