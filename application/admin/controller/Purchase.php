<?php
/**
 * Purchase.php
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

/**
 * 用户权限控制器
 */
use data\service\AuthGroup as AuthGroup;

class Purchase extends BaseController
{

    private $auth_group;

    public function __construct()
    {
        parent::__construct();
        $this->auth_group = new AuthGroup();
    }

    public function goods()
	{
		return view($this->style.'Purchase/goods');
	}
}