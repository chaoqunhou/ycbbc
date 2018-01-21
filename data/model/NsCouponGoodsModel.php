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
 * 优惠券商品表
 * @author Administrator
 *
 */
class NsCouponGoodsModel extends BaseModel {

    protected $table = 'ns_coupon_goods';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];
    /**
     * 获取对应优惠券类型的相关商品列表
     * @param unknown $coupon_type_id
     */
    public function getCouponTypeGoodsList($coupon_type_id)
    {
        $list = $this->alias('ncg')
                ->join('ns_goods ng','ncg.goods_id = ng.goods_id','left')
                ->field(' ncg.coupon_type_id, ncg.goods_id, ng.goods_name, ng.stock, ng.picture, ng.shop_id, ng.price')
                ->where(['coupon_type_id'=>$coupon_type_id])->select();
        return $list;    
    }

}