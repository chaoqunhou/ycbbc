<?php
/**
 * Complain.php
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
use data\service\Manage as ManageService;
use data\service\Order as OrderService;
use data\service\User as UserService;
/**
 * 投诉控制器
 */
class Complain extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }
    
    //新投诉列表
    public  function  ajaxComplainList()
    {        
        if (request()->isAjax()) {
            $complain = new ManageService();
            $pageIndex = request()->post('pageIndex','');
            $accuser_name = request()->post('accuser_name','');
            $accused_shop_name = request()->post('accused_shop_name','');
            $complain_subject = request()->post('complain_subject','');            
            
            $start_date = request()->post('start_date') == '' ? getTimeTurnTimeStamp('2010-1-1') : getTimeTurnTimeStamp(request()->post('start_date'));
            $end_date = request()->post('end_date') == '' ? getTimeTurnTimeStamp('2030-1-1') : getTimeTurnTimeStamp(request()->post('end_date'));
            $complain_state = request()->post('complain_state',10);
            $condition["complain_datetime"] = [[">",$start_date],["<",$end_date]];
            $condition["complain_state"] = $complain_state;
            if (! empty($accuser_name)) 
            {
            $condition["accuser_user_name"] = array("like","%" . $accuser_name . "%");
            }
            
            if (! empty($accused_shop_name))
            {
                $condition["accused_shop_name"] = array("like","%" . $accused_shop_name . "%");
            }
            
            if (! empty($complain_subject))
            {
                $condition["complain_subject_id"] =$complain_subject;
            }

//            var_dump($condition);die;
            $complainNewList = $complain->getComplainList($pageIndex, PAGESIZE, $condition, "complain_datetime desc");

            return $complainNewList;
        } 
      
    }
    //新投诉
    public  function  complainNewList()
    {
        $complain = new ManageService();
        $complainSubjectList = $complain->getComplainSubjectList();
        $this->assign('complainSubjectList',$complainSubjectList['data']);
        $this->assign('complain_state',10);
        return view($this->style . "Complain/complainList");
    }
    //待申诉
    public function  complainAppealList()
    {
        $complain = new ManageService();
        $complainSubjectList = $complain->getComplainSubjectList();
        $this->assign('complainSubjectList',$complainSubjectList['data']);
        $this->assign('complain_state',20);
        return view($this->style . "Complain/complainList");
    } 

    //对话中
    public function  complainTalkList()
    {
        $complain = new ManageService();
        $complainSubjectList = $complain->getComplainSubjectList();
        $this->assign('complainSubjectList',$complainSubjectList['data']);
        $this->assign('complain_state',30);
        return view($this->style . "Complain/complainList");
    } 

    //待仲裁
    public function  complainHandleList()
    {
        $complain = new ManageService();
        $complainSubjectList = $complain->getComplainSubjectList();
        $this->assign('complainSubjectList',$complainSubjectList['data']);
        $this->assign('complain_state',40);
        return view($this->style . "Complain/complainList");
    } 

    //已关闭
    public function  complainFinishList()
    {
        $complain = new ManageService();
        $complainSubjectList = $complain->getComplainSubjectList();
        $this->assign('complainSubjectList',$complainSubjectList['data']);
        $this->assign('complain_state',99);
        return view($this->style . "Complain/complainList");
    } 

    
    //投诉进程详情
    public function complainProgress()
    {
      $complain_id = request()->get('complain_id',0);
      
      if(request()->isPost())
      {
          $final_handle_message=request()->post('final_handle_message','');

          $final_handle_member_id=$this->uid;
          $manage=new ManageService();
          $Handlecomplain=$manage->setComplainClose($complain_id, $final_handle_message, $final_handle_member_id);
          $this->success( '处理成功！', 'complainFinishList');
      }
      else 
      {
          if ($complain_id == 0 || $complain_id == '')
          {
              $this->error("没有获取到投诉信息");
          }
          $manage=new ManageService();
          $complainInfo = $manage->getComplainInfo($complain_id);
          
          if (empty($complainInfo))
          {
              $this->error("没有获取到投诉信息");
          }
          $this->assign("complainInfo", $complainInfo);
          $order_id = $complainInfo['order_id'];
          $order = new OrderService();
          $orderDetail = $order->getOrderDetail($order_id);
          $this->assign('order',$orderDetail);
          return view($this->style . "Complain/complainProgress");
      }
      
    }    
    
    
    //审核投诉
    public function  compalinVerify()
    {
        if(request()->isAjax())
        {
            $complain_id = request()->post('complain_id','');
            $complain_handle_member_id=$this->uid;
            $complain = new ManageService();
            $compalinVerify = $complain->setComplainExamine($complain_id,$complain_handle_member_id);
            
            return AjaxReturn($compalinVerify);
        }
    }   

    //投诉主题列表
    public function  complainSubjectList()
    {
        if (request()->isAjax()) {
            $complain=new ManageService();
            $complainSubject=$complain->getComplainSubjectList();
            return $complainSubject;
        } else {
            return view($this->style . "Complain/complainSubjectList");
        }
    } 

    //添加投诉主题
    public function  complainSubjectAdd()
    {

        if (request()->isAjax()) {
           $complain=new ManageService();
           $inform_type_name=request()->post('inform_type_name','');
           $inform_type_state=request()->post('inform_type_state','');
           $inform_type_desc=request()->post('inform_type_desc',''); 
           $complainSubject=$complain->addComplainSubject($inform_type_name,$inform_type_desc,$inform_type_state);
           return AjaxReturn($complainSubject);
        } else {
            return view($this->style . "Complain/complainSubjectAdd");
        }
    } 

    //删除投诉主题
    public function delComplainSubject(){
        if(request()->isAjax()){
            $complain = new ManageService();
            $complain_subject_id = request()->post('complain_subject_id','');
            $delComplainSubject = $complain->delComplainSubject($complain_subject_id);
            return AjaxReturn($delComplainSubject);
        }
    
        
    }
    //投诉设置
    public function  complainSetting()
    {
        if (request()->isAjax()) {
            $complain=new ManageService();
            $subjectSetting=request()->post('subjectSetting_name','');
            $complainSetting=$complain->setComplainConfig($subjectSetting);
            return AjaxReturn($complainSetting);
        } else {
            return view($this->style . "Complain/complainSetting");
        }
    }
    //获取投诉设置
    public function getComplainSetting()
    {
        if(request()->isAjax()){
            $complain = new ManageService();
            $getComplainSetting = $complain->getComplainConfigInfo();
            return $getComplainSetting;
        }
    }


    //获取投诉对话
    public function getComplainTalk()
    {

        if (request()->isAjax()) {
            $complain = new ManageService();
            $complain_id = request()->post('complain_id', '');
            $complainTalk = $complain->getComplainTalkList($complain_id);
            return $complainTalk;
        }
    }

    //发布投诉对话
    public function publishComplainTalk()
    {
        $complain = new ManageService();
        $user = new UserService();
        $talk_uid = $this->uid;
        $useInfo = $user->getUserInfoByUid($talk_uid);
        $userName = $useInfo['user_name'];
        $complain_id = request()->post('complain_id','');
        $complain_talk = request()->post('complain_talk','');
        $res = $complain->addComplainTalk($complain_id,$talk_uid,$userName,3,$complain_talk);
        return AjaxReturn($res);
    }
    //屏蔽投诉对话
    public function setTalkState()
    {
        $talk_id = request()->post('talk_id','');
        $talk_admin = $this->uid;
        $complainTalk = new ManageService();
        $res = $complainTalk->setComplainTalkState($talk_id,2,$talk_admin);
        return AjaxReturn($res);
    }
    //取消对话屏蔽
    public function cancelTalkState()
    {
        $talk_id = request()->post('talk_id','');
        $talk_admin = $this->uid;
        $complainTalk = new ManageService();
        $res = $complainTalk->setComplainTalkState($talk_id,1,$talk_admin);
        return AjaxReturn($res);
    }
}

