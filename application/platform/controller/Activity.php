<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/24
 * Time: 9:46
 */

namespace app\platform\controller;


use data\service\Activity as ActivityService;
/**
 * Class Activity活动管理
 * @package app\platform\controller
 */
class Activity extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    //活动列表
    public function activityList()
    {
        if(request()->isAjax()){
            $activity = new ActivityService();
            $activityList = $activity->getPlatformActivityList();
            return $activityList;
        }
        return view($this->style . "Activity/activityList");
    }
    //添加活动
    public function addActivity()
    {
        if(request()->isAjax()){
            $activity_title = request()->post('activity_title','');
            $activity_banner = request()->post('activity_banner','');
            $activity_desc = request()->post('activity_desc','');
            $activity_start_date = request()->post('activity_start_date','');
            $activity_end_date = request()->post('activity_end_date','');
            $activity_sort = request()->post('activity_sort','');
            $activity_state = request()->post('activity_state','');
            $activity_id ='';
            $activity = new ActivityService();

            $res = $activity->addUpdatePlatformActivity($activity_id,$activity_title,$activity_banner,$activity_desc,$activity_start_date,$activity_end_date,$activity_sort,$activity_state);
            return AjaxReturn($res);
        }
        return view($this->style . "Activity/addActivity");
    }

    //修改活动信息
    public function updateActivityInfo()
    {
        if(request()->isAjax()){
            $activity_title = request()->post('activity_title','');
            $activity_banner = request()->post('activity_banner','');
            $activity_desc = request()->post('activity_desc','');
            $activity_start_date = request()->post('activity_start_date','');
            $activity_end_date = request()->post('activity_end_date','');
            $activity_sort = request()->post('activity_sort','');
            $activity_state = request()->post('activity_state','');
            $activity_id = request()->post('activity_id','');
            $activity = new ActivityService();
            $res = $activity->addUpdatePlatformActivity($activity_id,$activity_title,$activity_banner,$activity_desc,$activity_start_date,$activity_end_date,$activity_sort,$activity_state);
            return AjaxReturn($res);
        }
        $activity_id = request()->get('activity_id','');
        $activity = new ActivityService();
        $activityInfo = $activity->getPlatformActivityInfo($activity_id);
        $this->assign('activityInfo',$activityInfo);
        return view($this->style . "Activity/updateActivityInfo");

    }

    //删除活动
    public function deleteActivity()
    {
        $activity_id = request()->post('activity_id','');
        $activity = new ActivityService();
        $res = $activity->delPlatformActivity($activity_id);
        return AjaxReturn($res);
    }

    //普通活动处理申请
    public function activityDetail()
    {
        if(request()->isAjax()){
            $pageIndex =request()->post('pageIndex',1);
            $shop_name = request()->post('shop_name','');
            $item_name = request()->post('item_name','');
            $activity_id = request()->post('activity_id','');
            $activity_detail_state = request()->post('activity_detail_state','');
            $activity = new ActivityService();
            if (! empty($shop_name))
            {
                $condition["shop_name"] = array("like","%" . $shop_name . "%");
            }

            if (! empty($item_name))
            {
                $condition["item_name"] = array("like","%" . $item_name . "%");
            }

            if (! empty($activity_detail_state) || $activity_detail_state === '0')
            {
                $condition["activity_detail_state"] = $activity_detail_state;
            }
            $condition["activity_id"] = $activity_id;
            $activity_detail_list = $activity->getPlatformActivityDetailList($pageIndex, PAGESIZE, $condition);
            return $activity_detail_list;
        }
        $activity_id = request()->get('activity_id','');
        $this->assign('activity_id',$activity_id);
        return view($this->style . "Activity/activityDetail");
    }


    //普通活动审核通过
    public function passHandle()
    {
        $activity_detail_id = request()->post('activity_detail_id','');
        $activity = new ActivityService();
        $res = $activity->setPlatformActivityDetailState($activity_detail_id,'1');
        return AjaxReturn($res);
    }

    //普通活动审核拒绝通过
    public function refuseHandle()
    {
        $activity_detail_id = request()->post('activity_detail_id','');
        $activity = new ActivityService();
        $res = $activity->setPlatformActivityDetailState($activity_detail_id,'2');
        return AjaxReturn($res);
    }

    //普通活动审核删除
    public function deleteActivityDetail()
    {
        $activity_detail_id = request()->post('activity_detail_id','');
        $activity = new ActivityService();
        $res = $activity->delPlatformActivityDetail($activity_detail_id);
        return AjaxReturn($res);
    }


    //普通活动详情排序
    public function setActivityDetailSort()
    {
        if(request()->isAjax()){
            $activity_detail_id = request()->post('activity_detail_id','');
            $activity_detail_sort = request()->post('activity_detail_sort','');
            $activity = new ActivityService();
            $res = $activity->setPlatformActivityDetailSort($activity_detail_id,$activity_detail_sort);
            return AjaxReturn($res);
        }
    }

    //拼团活动列表
    public function fightgroupList()
    {
        if(request()->isAjax()){
            $activity = new ActivityService();
            $condition['group_state'] = array('in',[0,1,2,3]);
            $fightgroupList = $activity->getPlatformFightgroupList(1,PAGESIZE,$condition);
            return $fightgroupList;
        }
        return view($this->style . "Activity/fightgroupList");
    }
    //添加拼团活动
    public function addFightgroup()
    {
        if(request()->isAjax()){
            $group_name = request()->post('group_name','');
            $group_banner = request()->post('group_banner','');
            $group_content = request()->post('group_content','');
            $start_date = request()->post('start_date','');
            $end_date = request()->post('end_date','');
            $group_sort = request()->post('group_sort','');
            $group_state = request()->post('group_state','');
            $deposit_money_rate = request()->post('deposit_money_rate','');
            $group_discount = request()->post('group_discount','');
            $max_number = request()->post('max_number','');
            $activity = new ActivityService();
            $res = $activity->addPlatformFightgroup($group_name,$group_content,$start_date,$end_date,$group_banner,$group_sort,$group_state,$deposit_money_rate,$group_discount,$max_number);
            return AjaxReturn($res);
        }
        return view($this->style . "Activity/addFightgroup");
    }
    //删除拼团活动
    public function delFightgroup()
    {
        $group_id = request()->post('group_id','');
        $activity = new ActivityService();
        $res = $activity->delPlatformFightgroup($group_id);
        return AjaxReturn($res);
    }

    //拼单活动处理申请
    public function fightgroupDetail()
    {
        if(request()->isAjax()){
            $pageIndex =request()->post('pageIndex',1);
            $shop_name = request()->post('shop_name','');
            $goods_name = request()->post('goods_name','');
            $group_id = request()->post('group_id','');
            $group_detail_state = request()->post('group_detail_state','');
            $activity = new ActivityService();
            if (! empty($shop_name))
            {
                $condition["shop_name"] = array("like","%" . $shop_name . "%");
            }

            if (! empty($goods_name))
            {
                $condition["goods_name"] = array("like","%" . $goods_name . "%");
            }
            if(!empty($group_detail_state)){
                $condition["group_detail_state"] = $group_detail_state;
            }


            $condition["platform_group_id"] = $group_id;
            $fightgroup_detail_list =$activity->getPlatformFightgroupDetail($pageIndex, PAGESIZE, $condition);
            return $fightgroup_detail_list;
        }
        $group_id = request()->get('group_id','');
        $this->assign('group_id',$group_id);
        return view($this->style . "Activity/fightgroupDetail");
    }

    //拼单活动审核通过
    public function passFightgroupHandle()
    {
        $group_detail_id = request()->post('group_detail_id','');
        $activity = new ActivityService();
        $res = $activity->setPlatformFightgroupDetailState($group_detail_id,1);
        return AjaxReturn($res);
    }

    //拼单活动审核拒绝通过
    public function refuseFightgroupHandle()
    {
        $group_detail_id = request()->post('group_detail_id','');
        $activity = new ActivityService();
        $res = $activity->setPlatformFightgroupDetailState($group_detail_id,2);
        return AjaxReturn($res);
    }

//    //拼单活动审核删除
//    public function deleteFightgroupDetail()
//    {
//        $group_detail_id = request()->post('group_detail_id','');
//        $activity = new ActivityService();
//        $res = $activity->delPlatformFightgroupDetail($group_detail_id);
//        return AjaxReturn($res);
//    }


    //拼单详情排序
    public function setDetailSort()
    {
        if(request()->isAjax()){
            $group_detail_id = request()->post('group_detail_id','');
            $group_detail_sort = request()->post('group_detail_sort','');
            $activity = new ActivityService();
            $res = $activity->setPlatformFightgroupDetailSort($group_detail_id,$group_detail_sort);
            return AjaxReturn($res);
        }
    }


    /**
     * 搜索拼单商品
     */
    public function searchGoods()
    {
        $goods_name = request()->post('goods_name', '');
        $platform_group_id = request()->post('platform_group_id', '');
        $activity = new ActivityService();
        $group_goods_list = $activity->getPlatformFightgroupGoodsList($platform_group_id,['group_detail_state'=>3]);
        return $group_goods_list;
    }

    /**
     * 设置拼团活动状态
     */
    public function setPlatformFightgroupState()
    {
        if(request()->isAjax()){
            $group_id = request()->post('group_id','');
            $activity = new ActivityService();
            $state = 0;
            $res = $activity->setPlatformFightgroupState($group_id,$state = 0);
            return AjaxReturn($res);
        }
    }

}