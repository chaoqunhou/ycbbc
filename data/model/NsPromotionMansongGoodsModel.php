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
 * 满减送活动商品表
 *  id int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  promotion_goods_mansong_id int(11) NOT NULL COMMENT '满减送ID',
  goods_id int(11) NOT NULL COMMENT '商品ID',
  goods_name varchar(50) NOT NULL DEFAULT '' COMMENT '商品名称',
  goods_picture varchar(255) NOT NULL DEFAULT '' COMMENT '商品图片',
  PRIMARY KEY (id)
 */
class NsPromotionMansongGoodsModel extends BaseModel {

    protected $table = 'ns_promotion_mansong_goods';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];

}