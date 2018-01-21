<?php
namespace app\api\service;
use data\model\NsShopModel as dataShop;
use data\model\NsShopAdModel as dataShopAd;
use think\Exception;

class Shop extends BaseService
{
    /**
     * @param $shopId  商铺ID 单个ID或者多个ID以','号隔开
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
	public function read($shopId)
	{

		$shopM		= new dataShop();
        if (strpos($shopId,',')>0){
            $shopData   = $shopM ->where('shop_id','IN',$shopId) -> select() ;
            if (empty($shopData)){
                throw new Exception('找不到相应商铺','404');
            }
            $shopData	= collection($shopData) -> toArray();
        }else{
            $shopData	=$shopM -> where(['shop_id'=>$shopId])->find();
            if (empty($shopData)){
                throw new Exception('找不到相应商铺','404');
            }
            $shopData	= $shopData->toArray();
        }
		return $shopData;
	}
    public function getShopPicList($shop_id)
    {
        $shop_ad = new dataShopAd();
        $list = $shop_ad->order('sort','asc')->where(['shop_id' => $shop_id]) -> select();
        if (empty($list)){
            throw new Exception('暂时未上传图片','404');
        }
        $list = collection($list) -> toArray();
        return $list;
    }

}