<?php
/**
 * Member.php
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
namespace app\admin\controller;

use data\service\Member as MemberService;
use data\service\User;
use data\service\Weixin;
use data\service\Supplier;

/**
 * 会员管理
 *
 * @author Administrator
 *        
 */
class Member extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }
    /**
     * 供货商列表
     */
    public function supplierList(){
        $supplier = new Supplier();
        if(request()->isAjax()){
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = request()->post('search_text', '');
            $condition['supplier_name'] = array('like', '%'.$search_text.'%');
            $list = $supplier->getSupplierList($page_index, $page_size, $condition);
            return $list;
        }
        return view($this->style.'Member/supplierList');
    }
    /**
     * 添加供货商
     * @return multitype:unknown
     */
    public function addSupplier(){
        $supplier = new Supplier();
        if(request()->isAjax()){
            $uid = request()->post('uid', 0);
            $supplier_name = request()->post('supplier_name', '');
            $linkman_name = request()->post('linkman_name', '');
            $linkman_tel = request()->post('linkman_tel', '');
            $linkman_address = request()->post('linkman_address', '');
            $desc = request()->post('desc', '');
            $res = $supplier->addSupplier($uid, $supplier_name, $linkman_name, $linkman_tel, $linkman_address, $desc);
            return AjaxReturn($res);
        }
        return view($this->style.'Member/addSupplier');
    }
    /**
     * 修改代理商
     * @return multitype:unknown
     */
    public function updateSupplier(){
        $supplier = new Supplier();
        if(request()->isAjax()){
            $supplier_id = request()->post('supplier_id', 0);
            $supplier_name = request()->post('supplier_name', '');
            $linkman_name = request()->post('linkman_name', '');
            $linkman_tel = request()->post('linkman_tel', '');
            $linkman_address = request()->post('linkman_address', '');
            $desc = request()->post('desc', '');
            $res = $supplier->updateSupplier($supplier_id, $supplier_name, $linkman_name, $linkman_tel, $linkman_address,  $desc);
            return AjaxReturn($res);
        }
        $supplier_id = request()->get('supplier_id', 0);
        $info = $supplier->getSupplierInfo($supplier_id);
        $this->assign('supplier_id', $supplier_id);
        $this->assign('info', $info);
        return view($this->style.'Member/updateSupplier');
    }
    /**
     * 删除代理商
     * @return multitype:unknown
     */
    public function deleteSupplier(){
        $supplier = new Supplier();
        $supplier_id_array = request()->post('supplier_id', 0);
        $res = $supplier->deleteSupplier($supplier_id_array);
        return AjaxReturn($res);
    }
    
}