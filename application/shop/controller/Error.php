<?php
/**
 * Index.php
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
namespace app\shop\controller;

/**
 * 首页控制器
 * 创建人：王永杰
 * 创建时间：2017年2月6日 11:01:19
 */
class Error extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function _empty($name)
    {
        $this->redirect(__URL(__URL__));
    }
}