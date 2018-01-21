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
namespace app\admin\controller;

use data\service\Upgrade as UpgradeService;
use data\extend\upgrade\Upgrade as UpgradeExtend;
use data\service\DbQuery;

/**
 * 升级
 * 
 * @author Administrator
 */
class Upgrade extends BaseController
{

    public $backup_code = 0;

    public $backup_message = "数据库备份成功!";

    public function __construct()
    {
        parent::__construct();
    }

}
