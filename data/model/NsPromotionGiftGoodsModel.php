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
 * 商品赠品表
 * id int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id ',
      gift_id int(10) UNSIGNED NOT NULL COMMENT '赠品id ',
      goods_id int(10) UNSIGNED NOT NULL COMMENT '商品id',
      goods_name varchar(50) NOT NULL COMMENT '商品名称',
      goods_picture varchar(100) NOT NULL COMMENT '商品图片',
 */
class NsPromotionGiftGoodsModel extends BaseModel {

    protected $table = 'ns_promotion_gift_goods';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];
    

}