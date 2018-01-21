<?php
/**
 *
 * =========================================================
 *
 * ----------------------------------------------
 * 官方网址: http://www.youshengxian.com
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 优惠券表
 * @author Administrator
 *
 */
class NsCouponModel extends BaseModel {

    protected $table = 'ns_coupon';
    protected $rule = [
        'coupon_id'  =>  '',
    ];
    protected $msg = [
        'coupon_id'  =>  '',
    ];

}