<?php
/**
 * NfxUserBankAccountModel.php
 *
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
 * 会员提现账号
 *
 *    id int(11) NOT NULL AUTO_INCREMENT,
      uid int(11) NOT NULL COMMENT '会员id',
      bank_type int(11) NOT NULL DEFAULT 1 COMMENT '账号类型 1银行卡2支付宝',
      branch_bank_name varchar(50) DEFAULT NULL COMMENT '支行信息',
      realname varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
      account_number varchar(50) NOT NULL DEFAULT '' COMMENT '银行账号',
      mobile varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
      is_default bit(1) NOT NULL DEFAULT b'0' COMMENT '是否默认账号',
      create_date datetime DEFAULT NULL COMMENT '创建日期',
      modify_date datetime DEFAULT NULL COMMENT '修改日期',
      PRIMARY KEY (Id),
 */
class NsMemberBankAccountModel extends BaseModel {

    protected $table = 'ns_member_bank_account';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];

}