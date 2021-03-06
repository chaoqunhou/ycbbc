<?php
/**
 * AdminUserViewModel.php
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
 * 后台用户view列表
 */
class AdminUserViewModel extends BaseModel {
    protected $table = 'sys_user_admin';
    /**
     * 获取列表返回数据格式
     * @param unknown $page_index
     * @param unknown $page_size
     * @param unknown $condition
     * @param unknown $order
     * @return unknown
     */
    public function getAdminUserViewList($page_index, $page_size, $condition, $order){
        
        $queryList = $this->getAdminUserViewQuery($page_index, $page_size, $condition, $order);
        $queryCount = $this->getAdminUserViewCount($condition);
        $list = $this->setReturnList($queryList, $queryCount, $page_size);
        return $list;
    }
    /**
     * 获取列表
     * @param unknown $page_index
     * @param unknown $page_size
     * @param unknown $condition
     * @param unknown $order
     * @return \data\model\multitype:number
     */
     public function getAdminUserViewQuery($page_index, $page_size, $condition, $order)
    {
        $viewObj = $this->alias('sua')
        ->join('sys_user sur', 'sur.uid=sua.uid','inner')
        ->join('sys_user_group su','sua.group_id_array=su.group_id','left')
        ->field('sua.uid, sur.user_name as admin_name, sua.is_admin, sur.user_status, su.group_name, sur.user_headimg, sur.user_email, sur.user_tel');
        $list = $this->viewPageQuery($viewObj, $page_index, $page_size, $condition, $order);
        return $list;
    }
    /**
     * 获取列表数量
     * @param unknown $condition
     * @return \data\model\unknown
     */
    public function getAdminUserViewCount($condition)
    {
        $viewObj = $this->alias('sua')
        ->join('sys_user sur', 'sur.uid=sua.uid','inner')
        ->join('sys_user_group su','sua.group_id_array=su.group_id','left')
        ->field('sua.ua_id');
        $count = $this->viewCount($viewObj,$condition);
        return $count;
    }

}