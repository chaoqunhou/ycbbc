<?php
/**
 * Config.php
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

use app\api\controller\User;
use data\extend\Send;
use data\service\Address as DataAddress;
use data\service\Config as WebConfig;
use data\service\GoodsCategory;
use data\service\Platform;
use data\service\Promotion;
use data\service\Shop as Shop;
use data\service\Upgrade;
use Qiniu\json_decode;

/**
 * 网站设置模块控制器
 *
 * @author Administrator
 *        
 */
class Config extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 广告列表
     */
    public function shopAdList()
    {
        if (request()->isAjax()) {
            $shop_ad = new Shop();
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post("page_size", PAGESIZE);
            $list = $shop_ad->getShopAdList($page_index, $page_size, [
                'shop_id' => $this->instance_id
            ], 'sort');
            return $list;
        }
        return view($this->style . "Config/shopAdList");
    }

    /**
     * 添加店铺广告
     *
     * @return \think\response\View
     */
    public function addShopAd()
    {
        if (request()->isAjax()) {
            $ad_image = request()->post('ad_image', '');
            $link_url = request()->post('link_url', '');
            $sort = request()->post('sort', 0);
            $type = request()->post('type', 0);
            $background = request()->post('background', '#FFFFFF');
            $shop_ad = new Shop();
            $res = $shop_ad->addShopAd($ad_image, $link_url, $sort, $type, $background);
            return AjaxReturn($res);
        }
        return view($this->style . "Config/addShopAd");
    }

    /**
     * 修改店铺广告
     */
    public function updateShopAd()
    {
        if (request()->isAjax()) {
            $id = request()->post('id', '');
            $ad_image = request()->post('ad_image', '');
            $link_url = request()->post('link_url', '');
            $sort = request()->post('sort', 0);
            $type = request()->post('type', 0);
            $background = request()->post('background', '#FFFFFF');
            $shop_ad = new Shop();
            $res = $shop_ad->updateShopAd($id, $ad_image, $link_url, $sort, $type, $background);
            return AjaxReturn($res);
        }
        $shop_ad = new Shop();
        $id = request()->get('id', '');
        if (! is_numeric($id)) {
            $this->error('未获取到信息');
        }
        $info = $shop_ad->getShopAdDetail($id);
        if(empty($info))
        {
            $this->error('未获取到信息');
        }
        $this->assign('info', $info);
        return view($this->style . "Config/updateShopAd");
    }

    public function delShopAd()
    {
        $id = request()->post('id', '');
        $res = 0;
        if (! empty($id)) {
            $shop_ad = new Shop();
            $res = $shop_ad->delShopAd($id);
        }
        return AjaxReturn($res);
    }

    /**
     * 店铺导航列表
     */
    public function shopNavigationList()
    {
        if (request()->isAjax()) {
            $shop = new Shop();
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $list = $shop->ShopNavigationList($page_index, $page_size, '', 'sort');
            return $list;
        } else {
            $this->pcConfigChildMenuList(1);
            return view($this->style . "Config/shopNavigationList");
        }
    }
   
 
    /**
     * 根据路径查询配置文件集合
     * 创建时间：2017年9月4日 09:52:21
     * 修改时间：2017年9月4日 14:47:47
     * 王永杰
     *
     * @param unknown $path            
     */
    function getfiles($path)
    {
        try {
            
            $config_list = array();
            
            $k = 0;
            if ($dh = opendir($path)) {
                while (($file = readdir($dh)) !== false) {
                    if ((is_dir($path . "/" . $file)) && $file != "." && $file != "..") {
                        // 当前目录问文件夹
                        $xml_path = $path . '/' . $file . '/config.xml';
                        $xml_path = str_replace("\\", "/", $xml_path);
                        $config_list[$k]['xml_path'] = $xml_path; // XML文件路径
                        $config_list[$k]['is_readable'] = is_readable($xml_path); // 是否可读
                                                                                  
                        // $config_list[$k]['is_writable'] = is_writable($xml_path); // 是否可写
                        $k ++;
                    }
                }
                closedir($dh);
            }
            $config_list = array_merge($config_list);
        } catch (\Exception $e) {
            echo $e;
        }
        return $config_list;
    }


    /**
     * 编辑促销版块
     */
    public function updateGoodsRecommendClass()
    {
        $class_id = request()->post('class_id', 0);
        $class_name = request()->post('class_name', '');
        $goods_id_array = request()->post('goods_id_array', '');
        $sort = request()->post('sort', '');
        $show_type = request()->post('show_type', '');
        $platform = new Platform();
        $res = $platform->updatePlatformGoodsRecommendClass($class_id, $class_name, $sort, $goods_id_array, $show_type);
        return AjaxReturn($res);
    }

    /**
     * 删除 促销版块
     *
     * @return unknown[]
     */
    public function delGoodsRecommendClass()
    {
        $class_id = request()->post('class_id', 0);
        if ($class_id > 0) {
            $platform = new Platform();
            $res = $platform->deletePlatformGoodsRecommendClass($class_id);
            return AjaxReturn($res);
        } else {
            return AjaxReturn(0);
        }
    }

    /**
     * 首页公告 设置
     *
     * @return \think\response\View
     */
    public function userNotice()
    {
        $platform = new Platform();
        if (request()->isAjax()) {
            $page_index = request()->post("page_index",1);
            $page_size = request()->post("page_size", PAGESIZE);
            $list = $platform -> getNoticeList($page_index, $page_size, "", "create_time desc");
            return $list;
        }
        $this->pcConfigChildMenuList(4);
        return view($this->style . 'Config/userNotice');
    }

   
    /**
     * 修改公告
     *
     * @return Ambigous <multitype:unknown, multitype:unknown unknown string >|Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function updateNotice()
    {
        $web_config = new WebConfig();
        $shopid = $this->instance_id;
        if (request()->isAjax()) {
            $notice_message = request()->post('notice_message', '');
            $is_enable = request()->post('is_enable', '');
            $res = $web_config->setNotice($shopid, $notice_message, $is_enable);
            return AjaxReturn($res);
        }
    }

    public function selectCityListAjax()
    {
        if (request()->isAjax()) {
            $province_id = request()->post('province_id', '');
            $dataAddress = new DataAddress();
            $list = $dataAddress->getCityList($province_id);
            foreach ($list as $v) {
                if ($dataAddress->getDistrictCountByCityId($v['city_id']) > 0) {
                    $v['issetLowerLevel'] = 1;
                } else {
                    $v['issetLowerLevel'] = 0;
                }
            }
            return $list;
        }
    }

    public function selectDistrictListAjax()
    {
        if (request()->isAjax()) {
            $city_id = request()->post('city_id', '');
            $dataAddress = new DataAddress();
            $list = $dataAddress->getDistrictList($city_id);
            return $list;
        }
    }
    /*
     * 首页商品促销板块是否开启设置
     */
    public function isrecommend()
    {
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $key = 'IS_RECOMMEND';
            $value = array(
                'is_recommend' => request()->post('is_recommend', '0')
            );
            $config_service = new WebConfig();
            $retval = $config_service->setisrecommendConfig($shop_id, $key, $value);
            return AjaxReturn($retval);
        }
    }
    /**
     * 添加首页公告
     */
    public function addHomeNotice(){
        return view($this->style . "Config/addHomeNotice");
    }
    
    /**
     * 编辑公告
     */
    public function updateHomeNotice(){
        $id = request()->get("id",0);
        $platform = new Platform();
        $info = $platform -> getNoticeDetail($id);
        if(empty($info)){
            $this->error("没有获取到公告信息");
        }else{
            $this->assign("info", $info); 
        }
        return view($this->style . "Config/updateHomeNotice");
    }
    
    /**
     * 删除公告
     */
    public function deleteNotice(){
        if (request()->isAjax()) {
            $platform =new Platform();
            $id = request()->post('id', '');
            if (empty($id)) {
                $this->error('未获取到信息');
            }
            $retval = $platform ->deleteNotice($id);
            return AjaxReturn($retval);
        }
    }
    
    /**
     * 添加或修改首页公告
     */
    public function addOrModifyHomeNotice(){
        if(request()->isAjax()){
            $id = request() -> post ("id", 0);
            $title = request() -> post ("title", "");
            $content = request() -> post ("content", "");
            $sort = request() -> post ("sort", 0);
            $platform = new Platform();
            $res = $platform -> addOrModifyNotice($title, $content, $this->instance_id, $sort, $id);
            return AjaxReturn($res);
        }
    }
    
    /**
     * 修改公告排序
     *
     * @return multitype:unknown
     */
    public function modifyNoticeSort()
    {
        if (request()->isAjax()) {
            $platform = new Platform();
            $id = request()->post('id', '');
            $sort = request()->post('sort', '');
            $retval = $platform->updateNoticeSort($sort, $id);
            return AjaxReturn($retval);
        }
    }
}