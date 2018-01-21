<?php
/**
 * IMember.php
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
namespace data\api;
interface  IManage{
    
    /**
     * 举报信息添加
     * @param unknown $inform_arr
     */
    public function addInform($inform_uid, $inform_user_name, $inform_goods_id, $inform_goods_name, $inform_goods_image, $inform_subject_id, $inform_subject_content, $inform_content, $inform_pic1, $inform_pic2, $inform_pic3, $inform_shop_id, $inform_shop_name);
    
    /**
     * 举报主题添加
     * @param unknown $inform_subject_content
     * @param unknown $inform_subject_type_id
     * @param unknown $inform_subject_type_name
     * @param unknown $inform_subject_state
     */
    public function addInformSubject($inform_subject_content, $inform_subject_type_id, $inform_subject_type_name, $inform_subject_state);
    
    /**
     * 举报类型添加
     * @param unknown $inform_type_name
     * @param unknown $inform_type_desc
     * @param unknown $inform_type_state
     */
    public function addInformSubjectType($inform_type_name, $inform_type_desc, $inform_type_state);
    
    /**
     * 举报信息列表
     * @param unknown $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     */
    public function getInformList($page_index = 1, $page_size = 0, $condition = '', $order = '');
    
    /**
     * 处理举报信息
     * @param unknown $inform_id
     * @param unknown $inform_handle_type
     * @param unknown $inform_handle_message
     * @param unknown $inform_handle_member_id
     */
    public function setInformHandle($inform_id, $inform_handle_type, $inform_handle_message, $inform_handle_member_id);
    
    /**
     * 举报主题列表
     * @param unknown $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     */
    public function getInformSubjectList($page_index = 1, $page_size = 0, $condition = '', $order = '');
    
    /**
     * 举报类型列表
     * @param unknown $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     */
    public function getInformSubjectTypeList($page_index = 1, $page_size = 0, $condition = '', $order = '');
    
    /**
     * 举报信息详情
     * @param unknown $inform_id
     */
    public function getInformInfo($inform_id, $codition = '');
    
    /**
     * 举报主题详情
     * @param unknown $inform_subject_id
     */
    public function getInformSubjectInfo($inform_subject_id, $condition = '');
    
    /**
     * 举报类型详情
     * @param unknown $inform_type_id
     * @param string $condition
     */
    public function getInformSubjectTypeInfo($inform_type_id, $condition = '');
    
    /**
     * 举报主题删除(多个删除用,分隔即可)
     * @param unknown $inform_subject_ids
     */
    public function delInformSubject($inform_subject_ids);
    
    /**
     * 举报类型删除
     * @param unknown $inform_type_ids
     */
    public function delInformSubjectType($inform_type_id);
    
    /**
     * 举报类型获取举报主题 
     * @param unknown $inform_type_id
     */
    public function getInformSubjectSubjectByType($inform_type_id);
    
    /**
     * 添加投诉主题
     * @param unknown $complain_subject_content
     * @param unknown $complain_subject_desc
     * @param unknown $complain_subject_state
     */
    public function addComplainSubject($complain_subject_content, $complain_subject_desc, $complain_subject_state);
    
    /**
     * 获取投诉主题列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     */
    public function getComplainSubjectList($page_index = 1, $page_size = 0, $condition = '', $order = '');
    
    
    /**
     * 删除投诉主题
     * @param unknown $complain_subject_id
     */
    public function delComplainSubject($complain_subject_id);
    
    /**
     * 申请投诉
     * @param unknown $order_id  订单id
     * @param unknown $order_goods_id  订单项商品
     * @param unknown $accuser_id  投诉人uid
     * @param unknown $accuser_name  投诉人名称
     * @param unknown $accused_id   被告人uid店铺的人
     * @param unknown $accused_name  被告人名称
     * @param unknown $complain_subject_content 投诉主题
     * @param unknown $complain_subject_id   投诉id
     * @param unknown $complain_content   投诉内容
     * @param unknown $complain_pic1  投诉上传图片
     * @param unknown $complain_pic2
     * @param unknown $complain_pic3
     */
    public function addComplainApply($order_id, $order_goods_id, $accuser_uid, $accuser_user_name, $accused_shop_id, $accused_shop_name, $complain_subject_content, $complain_subject_id, $complain_content, $complain_pic1, $complain_pic2, $complain_pic3);
    
    /**
     * 将投诉取消
     * @param unknown $complain_id
     */
    public function setComplainApplyCancel($complain_id);
    
    /**
     * 获取投诉列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     */
    public function getComplainList($page_index = 1, $page_size = 0, $condition = '', $order = '');
    
    /**
     * 获取投诉详情 
     * @param unknown $complain_id
     */
    public function getComplainInfo($complain_id);
    
    /**
     * 平台关闭投诉
     * @param unknown $complain_id
     * @param unknown $final_handle_message
     * @param unknown $final_handle_member_id
     */
    public function setComplainClose($complain_id, $final_handle_message, $final_handle_member_id);
    
    /**
     * 审核投诉
     * @param unknown $complain_id
     */
    public function setComplainExamine($complain_id, $complain_handle_member_id);
    
    /**
     * 添加店铺申诉
     * @param unknown $complain_id
     * @param unknown $appeal_message
     * @param unknown $appeal_pic1
     * @param unknown $appeal_pic2
     * @param unknown $appeal_pic3
     */
    public function addComplainAppeal($complain_id, $appeal_message, $appeal_pic1, $appeal_pic2, $appeal_pic3);
    
    /**
     * 投诉修改仲裁
     * @param unknown $complain_id
     */
    public function setComplainArbitation($complain_id);
    
    /**
     * 获取投诉配置
     */
    public function getComplainConfigInfo();
        
    /**
     * 投诉配置
     * @param unknown $complain_time
     */
    public function setComplainConfig($complain_time);
    
    /**
     * 添加评论
     * @param unknown $complain_id
     * @param unknown $talk_uid
     * @param unknown $talk_user_name
     * @param unknown $talk_user_type
     * @param unknown $talk_content
     */
    public function addComplainTalk($complain_id, $talk_uid, $talk_user_name, $talk_user_type, $talk_content);
    
    /**
     * 评论状态修改 1显示 2不显示
     * @param unknown $complain_id
     * @param unknown $talk_state
     */
    public function setComplainTalkState($talk_id, $talk_state, $talk_admin);
   
    /**
     * 根据投诉id获取对话列表
     * @param unknown $complain_id
     */
    public function getComplainTalkList($complain_id); 
    
/**
 * 意见反馈列表
 * @param number $page_index
 * @param number $page_size
 * @param string $condition
 * @param string $order
 */
    function getFeedbackList($page_index = 1, $page_size = 0, $condition = '', $order = '');
 
/**
 * 意见反馈详情页
 * @param unknown $id
 */
    public function getFeedbackInfo($id);
    
}