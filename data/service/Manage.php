<?php
/**
 * Member.php
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
 * @date : 2017年8月10日
 * @version : v1.0.0.0
 */
namespace data\service;
use data\api\IManage;
use data\service\BaseService;
use data\model\NsInformSubjectModel;
use data\model\NsInformSubjectTypeModel;
use data\model\NsInformModel;
use data\model\NsComplainSubjectModel;
use data\model\NsComplainModel;
use data\model\NsComplainTalkModel;
use data\model\SysFeedbackModel;
use data\model\UserModel;
use data\service\MemberService;
use data\model\BaseModel;
/**
 * 管理service
 */
class Manage extends BaseService implements IManage{
    
   /**举报信息添加
    * (non-PHPdoc)
    * @see \data\api\IManage::addInform()
    */
   public function addInform($inform_uid, $inform_user_name, $inform_goods_id, $inform_goods_name, $inform_goods_image, $inform_subject_id, $inform_subject_content, $inform_content, $inform_pic1, $inform_pic2, $inform_pic3, $inform_shop_id, $inform_shop_name){
       $inform_model = new NsInformModel();
       $data = array(
           'inform_uid'             => $inform_uid,
           'inform_user_name'       => $inform_user_name,
           'inform_goods_id'        => $inform_goods_id,
           'inform_goods_name'      => $inform_goods_name,
           'inform_subject_id'      => $inform_subject_id,
           'inform_subject_content' => $inform_subject_content,
           'inform_content'         => $inform_content,
           'inform_pic1'            => $inform_pic1,
           'inform_pic2'            => $inform_pic2,
           'inform_pic3'            => $inform_pic3,
           'inform_datetime'        => time(),
           'inform_shop_id'         => $inform_shop_id,
           'inform_shop_name'       => $inform_shop_name,
           'inform_state'           => 1,
           'inform_handle_type'     => 0,
           'inform_handle_message'  => '',
           'inform_handle_datetime' => 0,
           'inform_handle_member_id'=> 0,
           'inform_goods_image'     =>  $inform_goods_image
       );
       $inform_model->save($data);
       return $inform_model->inform_id;
   }

   /**举报主题添加
    * (non-PHPdoc)举报主题添加
    * @see \data\api\IManage::addInformSubject()
    */
   public function addInformSubject($inform_subject_content, $inform_subject_type_id, $inform_subject_type_name, $inform_subject_state)
   {

       $inform_subject_model = new NsInformSubjectModel();
       $data = array(
           'inform_subject_content'     => $inform_subject_content,
           'inform_subject_type_id'     => $inform_subject_type_id,
           'inform_subject_type_name'   => $inform_subject_type_name,
           'inform_subject_state'       => $inform_subject_state
       );
      
       $inform_subject_model->save($data);
       return $inform_subject_model->inform_subject_id;
       
   }
   /**举报类型添加
    * (non-PHPdoc)举报类型添加
    * @see \data\api\IManage::addInformSubjectType()
    */
   public function addInformSubjectType($inform_type_name, $inform_type_desc, $inform_type_state){

       $inform_subject_type_model = new NsInformSubjectTypeModel();
       $data = array(
           'inform_type_name'       => $inform_type_name,
           'inform_type_desc'       => $inform_type_desc,
           'inform_type_state'      => $inform_type_state
       );

       $inform_subject_type_model->save($data);
       return $inform_subject_type_model->inform_type_id;
   }
   
   public function setSubjectType($pk_name, $pk_id, $field_name, $field_value)
   {
        $inform_model = new NsInformSubjectTypeModel();
        $inform_type = $inform_model -> ModifyTableField($pk_name, $pk_id, $field_name, $field_value);
   }
   
   
   
   
   /**举报信息列表
    * (non-PHPdoc)举报信息列表
    * @see \data\api\IManage::getInformList()
    */
   public function getInformList($page_index = 1, $page_size = 0, $condition = '', $order = '')
   {
       $inform_model = new NsInformModel();
       $inform_list = $inform_model->pageQuery($page_index, $page_size, $condition, $order, '*');
       
       foreach ($inform_list['data'] as $k=>$v)
       {   
           $inform_subject_model = new NsInformSubjectModel();
           $inform_type_name = $inform_subject_model ->getInfo(['inform_subject_id'=>$v['inform_subject_id']],'inform_subject_type_name')['inform_subject_type_name'];
           $inform_list['data'][$k]['inform_type_name'] = $inform_type_name;
       }
       
       return $inform_list;
   }
   
   /**举报主题列表
    * (non-PHPdoc)举报主题列表
    * @see \data\api\IManage::getInformSubjectList()
    */
   public function getInformSubjectList($page_index = 1, $page_size = 0, $condition = '', $order = '')
   {
       $inform_subject_model = new NsInformSubjectModel();
       $inform_subject_list = $inform_subject_model->pageQuery($page_index, $page_size, $condition, $order, '*');      
      
       return $inform_subject_list; 
   }
   
   /**举报类型列表
    * (non-PHPdoc)举报类型列表
    * @see \data\api\IManage::getInformSubjectTypeList()
    */
   public function getInformSubjectTypeList($page_index = 1, $page_size = 0, $condition = '', $order = '')
   {
       
       $inform_subject_type_model = new NsInformSubjectTypeModel();
       $inform_subject_type_list = $inform_subject_type_model->pageQuery($page_index, $page_size, $condition, $order, '*');           
       
       return $inform_subject_type_list;
   }
   
   /**举报信息详情
    * (non-PHPdoc)举报信息详情
    * @see \data\api\IManage::getInformInfo()
    */
   public function getInformInfo($inform_id, $condition = '')
   {
       $inform_model = new NsInformModel();
      
       if(!empty($inform_id))
       {
           $condition['inform_id'] = $inform_id;
       }
       
       $inform_info = $inform_model->getInfo($condition, '*');
       
       if(!empty($inform_info))
       {
           $inform_subject_model = new NsInformSubjectModel();
           $inform_info['inform_subject_type_name'] = $inform_subject_model->getInfo(['inform_subject_id' => $inform_info['inform_subject_id']],'')['inform_subject_type_name'];   
       }

       return $inform_info;
   }
   
   
   
   /**举报主题详情
    * (non-PHPdoc)举报主题详情
    * @see \data\api\IManage::getInformSubjectInfo()
    */
   public function getInformSubjectInfo($inform_subject_id, $condition = '')
   {
       $inform_subject_model = new NsInformSubjectModel();
       
       if(!empty($inform_subject_id))$condition['inform_subject_id'] = $inform_subject_id;
       $inform_subject_info = $inform_subject_model->getInfo($condition, '*');
       return $inform_subject_info; 
   }
   
   /**举报类型详情
    * (non-PHPdoc)举报类型详情
    * @see \data\api\IManage::getInformSubjectTypeInfo()
    */
   public function getInformSubjectTypeInfo($inform_type_id, $condition = '')
   {
       $inform_subject_type_model = new NsInformSubjectTypeModel();
       if(!empty($inform_type_id))$condition['inform_type_id'] = $inform_type_id;
       $inform_subject_type_info = $inform_subject_type_model->getInfo($condition, '*');
       return $inform_subject_type_info;
   }
   
   /**举报信息删除
    * (non-PHPdoc)
    * @see \data\api\IManage::delInformSubjectType()
    */
   public function delInform($inform_id)
   {
       $inform_model = new NsInformModel();
       $res = $inform_model->destroy(['inform_id'=>$inform_id]);
       return $res;
   }
      

   /**举报主题删除
    * (non-PHPdoc)举报主题删除
    * @see \data\api\IManage::delInformSubject()
    */
   public function delInformSubject($inform_subject_ids)
   {  
       $inform_subject_model = new NsInformSubjectModel();
       $res = $inform_subject_model->destroy(['inform_subject_id'=>array('in',$inform_subject_ids)]);
       return $res;
   }
   
   /**举报类型删除
    * (non-PHPdoc)举报类型删除
    * @see \data\api\IManage::delInformSubjectType()
    */
   public function delInformSubjectType($inform_type_id)
   {
       $inform_subject_type_model = new NsInformSubjectTypeModel();
       $inform_subject_count = $this->getInformSubjectSubjectByType($inform_type_id);
       if(empty($inform_subject_count)){
           $res = $inform_subject_type_model->destroy(['inform_type_id'=>$inform_type_id]);
       }
       else
       {
           $res = -1;
       }
       return $res;
   }
   
   /**添加投诉
    * (non-PHPdoc)添加投诉主题
    * @see \data\api\IManage::getInformSubjectSubjectByType()
    */
   public function getInformSubjectSubjectByType($inform_type_id){
       $inform_subject_model = new NsInformSubjectModel();
       $inform_subject_list = $inform_subject_model->getQuery(['inform_subject_type_id'=>$inform_type_id], '*', '');
       return $inform_subject_list;
   }
   
   /**
    * (non-PHPdoc)
    * @see \data\api\IManage::addComplainSubject()
    */
   public function addComplainSubject($complain_subject_content, $complain_subject_desc, $complain_subject_state){
       $complain_subject_model = new NsComplainSubjectModel();
       $data = array(
           'complain_subject_content'   => $complain_subject_content,
           'complain_subject_desc'      => $complain_subject_desc,
           'complain_subject_state'     => $complain_subject_state
       );
       $complain_subject_model->save($data);
       return $complain_subject_model->complain_subject_id;
   }
   
   /**处理举报信息
    * (non-PHPdoc)
    * @see \data\api\IManage::setInformHandle()
    */
   public function setInformHandle($inform_id, $inform_handle_type, $inform_handle_message, $inform_handle_member_id){
       $inform_model = new NsInformModel();
       $inform_model->startTrans();
       try {
           $data = array(
               'inform_state'           => 2,
               'inform_handle_type'     => $inform_handle_type,
               'inform_handle_message'  => $inform_handle_message,
               'inform_handle_datetime' => time(),
               'inform_handle_member_id'=> $inform_handle_member_id
           );
           $res = $inform_model->save($data,['inform_id'=>$inform_id]);
           $inform_info = $this->getInformInfo($inform_id);
           if($inform_handle_type == 2)
           {  //恶意举报
                $member_service = new Member();
                $member_service->setUserLlegralInform($inform_info['inform_uid'], 1);
           }else if($inform_handle_type == 3)
           {  //有效举报
                 $goods_service = new Goods();
                 $goods_service->ModifyGoodsState($inform_info['inform_goods_id'], 1);
           }
           $inform_model->commit();
       } catch (\Exception $e) {
           $inform_model->rollback();
           return $e->getMessage();
       }
     
       return res;
   }

   
   
   /**
    * (non-PHPdoc)获取投诉主题列表
    * @see \data\api\IManage::getComplainSubject()
    */
   public function getComplainSubjectList($page_index = 1, $page_size = 0, $condition = '', $order = ''){
       $complain_subject_model = new NsComplainSubjectModel();
       $complain_subject_list = $complain_subject_model->pageQuery($page_index, $page_size, $condition, $order, '*');
       return $complain_subject_list; 
   }
   
   /**
    * (non-PHPdoc)删除主题列表
    * @see \data\api\IManage::delComplainSubject()
    */
   public function delComplainSubject($complain_subject_id){
       $complain_subject_model = new NsComplainSubjectModel();
       $res = $complain_subject_model->destroy(['complain_subject_id'=>$complain_subject_id]);
       return $res;
   }
   
   /**
    * (non-PHPdoc)添加投诉
    * @see \data\api\IManage::addComplainApply()
    */
   public function addComplainApply($order_id, $order_goods_id, $accuser_uid, $accuser_user_name, $accused_shop_id, $accused_shop_name, $complain_subject_content, $complain_subject_id, $complain_content, $complain_pic1, $complain_pic2, $complain_pic3){
       $complain_model = new NsComplainModel();
       $data = array(
           'order_id'               => $order_id,
           'order_goods_id'         => $order_goods_id,
           'accuser_uid'             => $accuser_uid,
           'accuser_user_name'           => $accuser_user_name,
           'accused_shop_id'             => $accused_shop_id,
           'accused_shop_name'           => $accused_shop_name,
           'complain_subject_content'   => $complain_subject_content,
           'complain_subject_id'    => $complain_subject_id,
           'complain_content'       => $complain_content,
           'complain_pic1'          => $complain_pic1,
           'complain_pic2'          => $complain_pic2,
           'complain_pic3'          => $complain_pic3,
           'complain_datetime'      => time(),
           'complain_handle_datetime'   => 0,
           'complain_handle_member_id'  => 0,
           'appeal_message'         => '',
           'appeal_datetime'        => 0,
           'appeal_pic1'            => '',
           'appeal_pic2'            => '',
           'appeal_pic3'            => '',
           'final_handle_message'   => '',
           'final_handle_datetime'  => 0,
           'final_handle_member_id' => 0,
           'complain_state'         => 10,
           'complain_active'        => 1
       );
       $complain_model->save($data);
       return $complain_model->complain_id;
   }
   
   /**
    * (non-PHPdoc)关闭投诉
    * @see \data\api\IManage::setComplainApplyCancel()
    */
   public function setComplainApplyCancel($complain_id){
       $complain_model = new NsComplainModel();
       $complain_info = $this->getComplainInfo($complain_id);
       $res = -1;
       if(!empty($complain_info)){
           $res = $complain_model->save(['complain_state'=>99],['complain_id'=>$complain_id]);
       }
       return $res;
   }
   
   /**
    * (non-PHPdoc)获取投诉配置信息
    * @see \data\api\IManage::setComplainConfig()
    */
   public function getComplainConfigInfo(){
       $config_service = new Config();
       $key = 'COMPLAIN_CONFIG';
       $complain_config = $config_service->getConfig(0, 'COMPLAIN_CONFIG');
       if(empty($complain_config)){
           $config_service->addComplainConfig(0, $key, '30', '单位为天，订单完成后开始计算，多少天内可以发起投诉', 1);
           $complain_config = $config_service->getConfig(0, 'COMPLAIN_CONFIG');
       }
       return $complain_config['value'];
   }
   
   /**
    * (non-PHPdoc)投诉设置
    * @see \data\api\IManage::setComplainConfig()
    */
   public function setComplainConfig($complain_time){
       $config_service = new Config();
       $res = $config_service->updateComplainConfig(0, 'COMPLAIN_CONFIG', $complain_time, '单位为天，订单完成后开始计算，多少天内可以发起投诉', 1);
       return $res;
   }
   
   /**
    * (non-PHPdoc)获取投诉列表
    * @see \data\api\IManage::getComplainList()
    */
   public function getComplainList($page_index = 1, $page_size = 0, $condition = '', $order = '')
   {
       $complain_model = new NsComplainModel();

       $complain_list = $complain_model->pageQuery($page_index, $page_size, $condition, $order, '*');
       return $complain_list; 
   }
   
   /**
    * (non-PHPdoc)获取投诉信息
    * @see \data\api\IManage::getComplainInfo()
    */
   public function getComplainInfo($complain_id){

       $complain_model = new NsComplainModel();
       $condition['complain_id']=$complain_id;
    
       $complain_info = $complain_model->getInfo($condition,'*');
       return $complain_info;
   }

    /**
     * (non-PHPdoc)通过订单id获取投诉信息
     * @see \data\api\IManage::getComplainInfo()
     */
    public function getComplainInfoByOrderId($order_id){

        $complain_model = new NsComplainModel();
        $condition['order_id']=$order_id;

        $complain_info = $complain_model->getInfo($condition,'*');
        return $complain_info;
    }

   
   /**
    * (non-PHPdoc)关闭投诉
    * @see \data\api\IManage::setComplainClose()
    */
   public function setComplainClose($complain_id, $final_handle_message, $final_handle_member_id){
       $complain_model = new NsComplainModel();
       $data = array(
           'final_handle_message'   => $final_handle_message,
           'final_handle_datetime'  => time(),
           'final_handle_member_id' => $final_handle_member_id,
           'complain_state'         => 99
       );
       $res = $complain_model->save($data,['complain_id' => $complain_id]);
       return $res;
   }
   
   /**
    * (non-PHPdoc)审核新投诉
    * @see \data\api\IManage::setComplainExamine()
    */
   public function setComplainExamine($complain_id, $complain_handle_member_id){
       $complain_model = new NsComplainModel();
       $res = $complain_model->save(['complain_handle_member_id'=>$complain_handle_member_id,'complain_active'=>2,'complain_state'=>20],['complain_id'=>$complain_id]);
       return $res;
   }

    /**
     * (non-PHPdoc)将投诉状态改为对话状态
     * @see \data\api\IManage::setComplainArbitation()
     */
    public function setComplainTalk($complain_id){
        $complain_model = new NsComplainModel();
        $res = $complain_model->save(['complain_state'=>30],['complain_id'=>$complain_id]);
        return $res;
    }


   /**
    * (non-PHPdoc)提交仲裁
    * @see \data\api\IManage::setComplainArbitation()
    */
   public function setComplainArbitation($complain_id){
       $complain_model = new NsComplainModel();
       $res = $complain_model->save(['complain_state'=>40],['complain_id'=>$complain_id]);
       return $res;
   }

   /**
    * (non-PHPdoc)添加申诉
    * @see \data\api\IManage::addComplainAppeal()
    */
   public function addComplainAppeal($complain_id, $appeal_message, $appeal_pic1, $appeal_pic2, $appeal_pic3){
       $complain_model = new NsComplainModel();
       $data = array(
           'appeal_message'     => $appeal_message,
           'appeal_datetime'    => time(),
           'appeal_pic1'        => $appeal_pic1,
           'appeal_pic2'        => $appeal_pic2,
           'appeal_pic3'        => $appeal_pic3
       );
       $res = $complain_model->save($data,['complain_id'=>$complain_id]);
       return $res;
   }
   
   /**
    * (non-PHPdoc)添加投诉对话
    * @see \data\api\IManage::addComplainTalk()
    */
   public function addComplainTalk($complain_id, $talk_uid, $talk_user_name, $talk_user_type, $talk_content){

       $complain_talk_model = new NsComplainTalkModel();
       $data = array(
           'complain_id'        => $complain_id,
           'talk_uid'           => $talk_uid,
           'talk_user_name'     => $talk_user_name,
           'talk_user_type'     => $talk_user_type,
           'talk_content'       => $talk_content,
           'talk_state'         =>  1,
           'talk_admin'         => 0,
           'talk_datetime'      => time()
       );
       $complain_talk_model->save($data);
       return $complain_talk_model->talk_id;
   }
   
   /**
    * (non-PHPdoc)设置投诉对话状态
    * @see \data\api\IManage::setComplainTalkState()
    */
   public function setComplainTalkState($talk_id, $talk_state, $talk_admin){
       $complain_talk_model = new NsComplainTalkModel();
       $res = $complain_talk_model->save(['talk_state'=>$talk_state,'talk_admin'=>$talk_admin],['talk_id'=>$talk_id]);
       return $res;
   }
   
   /**
    * (non-PHPdoc)获取投诉对话列表
    * @see \data\api\IManage::getComplainTalkList()
    */
   public function getComplainTalkList($complain_id){
       $complain_talk_model = new NsComplainTalkModel();
       $complain_talk_list = $complain_talk_model->getQuery(['complain_id'=>$complain_id], '', 'talk_datetime desc');
       return $complain_talk_list;
   }
 
   /**
    * 
    * (non-PHPdoc)
    * @see \data\api\IManage::getFeedbackList()
    */
   public function getFeedbackList($page_index = 1, $page_size = 0, $condition = '', $order = '')
   {
      
       $feedback_model = new SysFeedbackModel();      
       $complain_list = $feedback_model->pageQuery($page_index, $page_size, $condition, $order, '*');
       
       foreach($complain_list['data'] as $k=>$v)
       {
           $uid = $v['uid'];
           $user = new UserModel();
           $userinfo = $user->getInfo(['uid' => $uid]);
           $complain_list['data'][$k]['nick_name'] = $userinfo["nick_name"];
       }

       return $complain_list;
   }
   
 /**
  * 意见反馈详情页
  * (non-PHPdoc)
  * @see \data\api\IManage::getFeedbackInfo()
  */  
    public function getFeedbackInfo($condition = '',$field = '*')
    {
        $feedback_model = new SysFeedbackModel();
        $feedback_info =  $feedback_model->getInfo($condition, '*');
        if (! empty($feedback_info)) 
        {
            $user = new UserModel();
            $userinfo = $user->getInfo([
                'uid' => $feedback_info['uid']
            ]);
            $feedback_info['real_name'] = $userinfo["nick_name"];
        }

        return $feedback_info;        
    }
   

    
   
   
}