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
class Report extends BaseController
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
    
    public function ajaxReportList()
    {
    
        if (request()->isAjax()) {
        
            $page_index = request()->post('pageIndex',1);
            $inform_state=request()->post('inform_state',1);
            $inform_goods_name = request()->post('inform_goods_name', '');
            $inform_subject_content = request()->post('inform_subject_content', '');
            $infoinform_user_name = request()->post('inform_user_name', '');
            $startDate = request()->post('startDate', '2010-1-1');
            $endDate = request()->post('endDate', '2030-1-1'); 
            
            $inform_member_id = request()->post('inform_member_id', '');
             
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

            $condition["inform_datetime"] = [[">",getTimeTurnTimeStamp($startDate)],["<",getTimeTurnTimeStamp($endDate)]];

            
            
            
            $condition["inform_state"] = $inform_state;
            
    
            $result = $manage->getInformList($page_index, 20, $condition,'inform_datetime desc');
    
            /*
             $platform = new Platform();
    
             if(!empty($result['data']))
             {
             foreach ($result['data'] as $k=>$v)
             {
             $result['data'][$k]['is_platform_new'] = $platform->getGoodsIsnew($v['goods_id']);
             $result['data'][$k]['is_platform_best'] = $platform->getGoodsIsBest($v['goods_id']);
             $result['data'][$k]['is_platform_hot'] = $platform->getGoodsIshot($v['goods_id']);
             }
             }
            */
            return $result;
        }
    }       
    
    
    
    
    public function untreatedreport()
    {
            $this->assign('inform_state',1);
            return view($this->style . "Report/reportList");           
    }
    
/**
 * 创建时间：2017年8月10日
 * 举报管理
 * 已处理举报列表
 */
  public function handlereport()
  {
          $this->assign('inform_state',2);
          return view($this->style."Report/reportList"); 
  }

 
    /**
     * 举报详情页
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
  public function reportinfo()
  {
      $inform_id = request()->get('inform_id',0);
      
      if ($inform_id == 0) {
          $this->error("没有获取到举报信息");
      }
      $manage=new Manage();
      $reportinfo = $manage->getInformInfo($inform_id);
      
      if (empty($reportinfo)) {
          $this->error("没有获取到举报信息");
      }
      // var_dump($detail);die;
      $this->assign("reportinfo", $reportinfo);
      return view($this->style . "Report/reportInfo");
  }
  
  /* 举报处理   */  
 public function reporthandle ()
 {
     $inform_id = request()->get('inform_id',0);
     
     if(request()->isPost())
     {
         $data = input('post.');
         $data['inform_state']=2;
         $condition['inform_id']=$inform_id;
         
         $manage=new Manage();
         $res=$manage->addInformHandle($data,$condition);
         $this->success('发布成功！', 'report/untreatedreport',1);
     }
     else 
     {
          
         if ($inform_id == 0)
         {
             $this->error("没有获取到举报信息");
         }
         $manage=new Manage();
         $reportinfo = $manage->getInformInfo($inform_id);
          
         if (empty($reportinfo))
         {
             $this->error("没有获取到举报信息");
         }
         $this->assign("reportinfo", $reportinfo);
         return view($this->style."Report/reporthandle");
     }
     
 }
  
  
  
  
  
  
  
  /* 删除 举报信息   */
  public function deleteReport()
  {
      $inform_id=request()->post('inform_id','');

      $manage=new Manage();
      $res=$manage->delInform($inform_id);
      return AjaxReturn($res);
  } 
  
  
  
  
  /**
   * 举报类型列表
   */
  
    public function reporttypelist()
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
            return view($this->style."Report/reportTypeList");
        }        
    }
    
    /**
     * 添加举报类型
     */
    public function addreporttype()
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
            return view($this->style . "Report/addReportType");
        }
    }  
    
    
    /**
     * 删除举报类型
     */    
    public function delreporttype()
    {
        $inform_type_id = request()->post('inform_type_id','');
        $manage = new Manage();
        $res = $manage->delInformSubjectType($inform_type_id);
        return AjaxReturn($res);       
    }    
     
    
    /**
     * 举报主题列表
     */
    
    public function reporttopiclist()
    {
    
        if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post("page_size", PAGESIZE);

            $manage = new Manage();
            $result = $manage->getInformSubjectList($page_index, $page_size);
    
            return $result;
        } else {
            
            return view($this->style."Report/reportTopicList");
        }
    }
    
    /**添加 举报主题  */    
    public function addreporttopic()
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
            $reporttopiclist = $manage->getInformSubjectTypeList(1, 0,$condition);
            
            $this->assign("reporttopiclist", $reporttopiclist['data']);
            
            return view($this->style . "Report/addReportTopic");
        }
    }
       
    
    
    /**
     * 删除举报类型
     */
    public function delreporttopic()
    {
        $inform_subject_id=request()->post('inform_subject_id','');
        $manage=new Manage();
        $res=$manage->delInformSubject($inform_subject_id);
        return AjaxReturn($res);
    }   
    
  
    /**
     * 修改举报类型状态
     */
    
    public function modifyGoodsRecommend(){
        $inform_type_id = request()->post('inform_type_id','');
        $type = request()->post('type','');
        $inform_type_state = request()->post('inform_type_state','');
        $platform = new Platform();
        $res = $platform->modifyGoodsRecommend($inform_type_id, $type, $inform_type_state);
        return AjaxReturn($res);
    
    }   
    
    
    
    
    

}