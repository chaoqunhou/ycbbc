<?php
/**
 * Goods.php
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
namespace app\platform\controller;
use data\service\Manage;
use data\service\Platform;
use think\Config;
use think\Request;


/**
 * 意见反馈控制器
 */
class Feedback extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }
    
/**
 * 意见反馈列表
 * @return multitype:number unknown |\think\response\View
 */    
    public function FeedbackList()
    {
        if (request()->isAjax()) {
            $manage = new Manage();
            $page_index = request()->post('pageIndex',1);
            $inform_state=request()->post('inform_state',1);
            $result = $manage->getFeedbackList($page_index, 20, '','');
            return $result;
        }       
        
         return view($this->style . "Feedback/feedbackList");           
    }
    

    /**
     * 意见反馈详情页
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
  public function feedbackinfo()
  {
      $id = request()->post('id',0);
      
      if ($id == 0) 
      {
          $this->error("没有获取到意见反馈信息");
      }     
      $manage=new Manage();
      $condition['id'] = $id;
      $feedbackinfo = $manage->getFeedbackInfo($condition);     
      if (empty($feedbackinfo)) 
      {
          $this->error("没有获取到意见反馈信息");
      }
      return $feedbackinfo;

  }

  
  /* 删除 举报信息   */
  public function deleteReport()
  {
      $inform_id=request()->post('inform_id','');
      $manage=new Manage();
      $res=$manage->delInform($inform_id);
      return AjaxReturn($res);
  } 
  

}