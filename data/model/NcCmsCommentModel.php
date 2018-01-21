<?php
/**
 * NcCmsCommentModel.php
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
 * CMS评论表
 *  comment_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '评论编号',
  text varchar(2000) NOT NULL COMMENT '评论内容',
  uid int(10) UNSIGNED NOT NULL COMMENT '评论人编号',
  quote_comment_id int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '评论引用',
  up int(10) UNSIGNED NOT NULL COMMENT '点赞数量',
  comment_time int(10) UNSIGNED NOT NULL COMMENT '评论时间',
 *
 */
class NcCmsCommentModel extends BaseModel{
    protected $table = 'nc_cms_comment';
    protected $rule = [
        'comment_id'  =>  '',
    ];
    protected $msg = [
        'comment_id'  =>  '',
    ];
}