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
 * 商品控制器
 */
class Inform extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }
    
/**
* 创建时间：2017年8月10日
* 举报管理
* 未处理举报列表
*/
    
    public function ajaxInformList()
    {
    
        if (request()->isAjax()) {
        
            $page_index = request()->post('pageIndex',1);
            $inform_state=request()->post('inform_state',1);
            $inform_goods_name = request()->post('inform_goods_name', '');
            $inform_subject_content = request()->post('inform_subject_content', '');

            $inform_user_name = request()->post('inform_user_name', '');            
            $startDate = request()->post('startDate', '');
            $endDate = request()->post('endDate', ''); 
         
/*          $inform_member_id = request()->post('inform_member_id', ''); */
             
            $manage = new Manage();
            $condition["inform_state"] = $inform_state;
             
            if (! empty($inform_goods_name))
            {
                $condition["inform_goods_name"] = array("like","%" . $inform_goods_name . "%");
            }
    
            if (! empty($inform_subject_content))
            {
                $condition["inform_subject_content"] = array("like","%" . $inform_subject_content . "%");
            }
            
            if (! empty($inform_user_name))
            {
                $condition["inform_user_name"] = array("like","%" . $inform_user_name . "%");
            }

            if (! empty($startDate) and ! empty($endDate))
            {
                $condition["inform_datetime"] = [[">",getTimeTurnTimeStamp($startDate)],["<",getTimeTurnTimeStamp($endDate)]];
            } 
            elseif (! empty($startDate))
            {
                $condition["inform_datetime"] = [[">",getTimeTurnTimeStamp($startDate)]];
            }
            elseif (! empty($endDate))
            {
                $condition["inform_datetime"] = [["<",getTimeTurnTimeStamp($endDate)]];
            }           
          
            
            $condition["inform_state"] = $inform_state;
  
            $result = $manage->getInformList($page_index, 10, $condition,'inform_datetime desc');
            return $result;
        }
    }       
    
        
    public function untreatedInform()
    {
            $this->assign('inform_state',1);
            return view($this->style . "Inform/informList");           
    }
    
/**
 * 创建时间：2017年8月10日
 * 举报管理
 * 已处理举报列表
 */
  public function handleInform()
  {
          $this->assign('inform_state',2);
          return view($this->style."Inform/informList"); 
  }

/**
 * 举报详情页
 * @return \think\response\View
 */

  public function informInfo()
  {
      $inform_id = request()->get('inform_id',0);
      
      if ($inform_id == 0) {
          $this->error("没有获取到举报信息");
      }
      $manage=new Manage();
      $informinfo = $manage->getInformInfo($inform_id);
      
      if (empty($informinfo)) {
          $this->error("没有获取到举报信息");
      }
      // var_dump($detail);die;
      $this->assign("informinfo", $informinfo);
      return view($this->style . "Inform/informInfo");
  }
  
  /* 举报处理   */
  /**
   * 
   * @return \think\response\View
   */
 public function informHandle ()
 {
     $inform_id = request()->get('inform_id',0);
     
     if(request()->isPost())
     {
         $inform_id = request()->get('inform_id',0);
         $inform_handle_type = request()->post('inform_handle_type',0);
         $inform_handle_message = request()->post('inform_handle_message','');
         $inform_handle_member_id = $this->uid;
         
         
         $manage=new Manage();
         $res=$manage->setInformHandle($inform_id, $inform_handle_type, $inform_handle_message, $inform_handle_member_id);
         $this->success('发布成功！', 'Inform/untreatedInform',1);
     }
     else 
     {
          
         if ($inform_id == 0)
         {
             $this->error("没有获取到举报信息");
         }
         $manage=new Manage();
         $informinfo = $manage->getInformInfo($inform_id);
          
         if (empty($informinfo))
         {
             $this->error("没有获取到举报信息");
         }
         $this->assign("informinfo", $informinfo);
         return view($this->style."inform/informhandle");
     }
     
 }
  

  /* 删除 举报信息   */
  public function deleteInform()
  {
      $inform_id=request()->post('inform_id','');
      $manage=new Manage();
      $res=$manage->delInform($inform_id);
      return AjaxReturn($res);
  } 
  

  /**
   * 举报类型列表
   */
  
    public function informTypeList()
    {

        if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post("page_size", PAGESIZE);
            
            $inform_goods_name = trim(request()->post('inform_goods_name',''));
            $inform_subject_id =request()->post('$inform_subject_id','');
            
            $manage = new Manage();
            $result = $manage->getInformSubjectTypeList($page_index, $page_size);
            
            return $result;
        } else {
            return view($this->style."Inform/informTypeList");
        }        
    }
    
    /**
     * 添加举报类型
     */
    public function addInformtype()
    {
        
        if (request()->isAjax())
        {
            $inform_type_name = request()->post("inform_type_name", '');
            $inform_type_state = request()->post("inform_type_state", '');
            $inform_type_desc = request()->post('inform_type_desc', '');
            
            $manage = new Manage();
            $result = $manage->addInformSubjectType($inform_type_name, $inform_type_desc, $inform_type_state);
            return AjaxReturn($result);
        } 
        else
        {
            return view($this->style . "Inform/addInformType");
        }
    }  
    
    
    /**
     * 删除举报类型
     */    
    public function delInformType()
    {
        $inform_type_id = request()->post('inform_type_id','');
        $manage = new Manage();
        $res = $manage->delInformSubjectType($inform_type_id);
        return AjaxReturn($res);       
    }    
     
    
    /**
     * 举报主题列表
     */
    
    public function informTopicList()
    {
    
        if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post("page_size", PAGESIZE);

            $manage = new Manage();
            $result = $manage->getInformSubjectList($page_index, $page_size);
    
            return $result;
        } else {
            
            return view($this->style."Inform/InformTopicList");
        }
    }
    
    /**添加 举报主题  */    
    public function addInformtopic()
    {    
        if (request()->isAjax())
        {

            $inform_subject_content = request()->post("inform_subject_content", '');
            $inform_subject_type_id = request()->post("inform_subject_type_id", '');
            $inform_subject_type_name = request()->post("inform_subject_type_name", '');

            $manage = new Manage();
            $result = $manage->addInformSubject( $inform_subject_content,$inform_subject_type_id,$inform_subject_type_name,1);
            return AjaxReturn($result);
        }
        else
        {          
            $manage = new Manage();
            $condition["inform_type_state"] = 1;          
            $informtopiclist = $manage->getInformSubjectTypeList(1, 0,$condition);
            
            $this->assign("informtopiclist", $informtopiclist['data']);
            
            return view($this->style . "Inform/addInformTopic");
        }
    }
       
    
    
    /**
     * 删除举报类型
     */
    public function delInformTopic()
    {
        $inform_subject_id=request()->post('inform_subject_id','');
        $manage=new Manage();
        $res=$manage->delInformSubject($inform_subject_id);
        return AjaxReturn($res);
    }   
    
  
    /**
     * 修改举报类型状态
     */
    
    public function setSubjectType()
    {
        $inform_type_id = request()->post('inform_type_id','');
        $inform_type_state = request()->post('inform_type_state','');
        
        $condition['inform_type_id']=$inform_type_id;
        
        $manage = new Manage();
        $res = $manage->setSubjectType('inform_type_id',$inform_type_id,'inform_type_state',$inform_type_state);
        return AjaxReturn($res);    
    }   


}