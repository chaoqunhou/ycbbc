<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/11
 * Time: 17:52
 */

namespace app\api\controller\v1;

use app\api\api\StandardInterface;
use app\api\controller\baseApi;

use app\api\service\Category as GoodsCategoryService;
use app\api\service\Goods as GoodsService;
use app\api\service\Shop as ShopService;
use think\Exception;

class Shop extends baseApi implements StandardInterface
{
	private $shop_id;
	// get
	public function index()
	{
		echo "index";
	}

	// get controller/create
	public function create()
	{
		echo "create";
	}

	/**
	 * @param $id --shop_id 商铺id
     * @return array All goods that the level is 1;
	 */
	public function read($id)
	{
	    $data = [];
	    try{
            $shop_id = $id;
            #获取所有level == 1 的分类
            $GoodsCategoryS = new GoodsCategoryService;
            $categoryArr = $GoodsCategoryS->read(1);
            #遍历分类下的商品
            $GoodsS = new GoodsService;
            #遍历 $categoryArr 获取其goods limit 3
            foreach ($categoryArr as $key => $category) {
                # $category['category_id']
                $goodsArr = $GoodsS->categoryRead($category['category_id'], $shop_id);
                $categoryArr[$key]['goods_list'] = $goodsArr;
            }
            $shopS = new ShopService();
            //轮播图列表
            $slideShow = $shopS -> getShopPicList($shop_id);
            $data['category_list'] = $categoryArr;
            //将轮播图数据添加到返回数据队列
            $data['slide_show'] = $slideShow;
        }catch(Exception $e){
	        returnJson($e -> getMessage(),$e ->getCode());
        }
        returnJson($data);
	}

    /**
     * 显示用户绑定的商铺
     * @return array All goods that the level is 1;
     */
    public function readAction()
    {
        $data = [];
        try{
            if (empty($_SERVER['uid'])) throw new Exception('需要登陆', 1100);
            if ($_SERVER['shop_id'] === NULL) throw new Exception('未绑定店铺，需要绑定', 301);
            $shop_id = $_SERVER['shop_id'];
            #获取所有level == 1 的分类
            $GoodsCategoryS = new GoodsCategoryService;
            $categoryArr = $GoodsCategoryS->read(1);
            #遍历分类下的商品
            $GoodsS = new GoodsService;
            #遍历 $categoryArr 获取其goods limit 3
            foreach ($categoryArr as $key => $category) {
                # $category['category_id']
                $goodsArr = $GoodsS->categoryRead($category['category_id'], $shop_id);
                $categoryArr[$key]['goods_list'] = $goodsArr;
            }
            $shopS = new ShopService();
            //轮播图列表
            $slideShow = $shopS -> getShopPicList($shop_id);
            $data['category_list'] = $categoryArr;
            //将轮播图数据添加到返回数据队列
            $data['slide_show'] = $slideShow;
        }catch(Exception $e){
            returnJson($e -> getMessage(),$e ->getCode());
        }
        returnJson($data);
    }

	// get controller/id/edit
	public function edit($id)
	{
		echo "edit $id";
	}

	// post
	public function save()
	{
		echo "save";
	}

	// post controller/update/id
	public function update($id)
	{
		echo "update $id";
	}

	// post controller/delete/id
	public function delete($id)
	{
		echo "delete $id";
	}

	//轮播图测试接口,todo 需要屏蔽
//	public function readShopAdPicAction(){
//	    try{
//            $shop_id = input('param.shop_id');
//            $shopS = new ShopService();
//            $list = $shopS -> getShopPicList($shop_id);
//        }catch(Exception $e){
//            returnJson($e -> getMessage(),$e ->getCode());
//        }
//        returnJson($list);
//    }

}