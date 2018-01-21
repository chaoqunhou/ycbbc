<?php
namespace app\api\service;

use app\api\service\Promotion as PromotionService;
use data\model\AlbumPictureModel;
use data\model\NsGoodsModel as dataGoods;
use data\model\NsGoodsSkuModel as dataGoodsSku;
use data\model\NsMemberFavoritesModel;
use think\Exception;

class Goods extends BaseService
{
	/**
	 * 查询商品 仅goods表 以及pic的转换
	 * @param $id - goods_id
	 * @return array|false|\PDOStatement|string|\think\Collection
	 */
	public function read($id)
	{
		$goodsM = new dataGoods;
		$field  = 'goods_id,goods_name,shop_id,category_id,category_id_1,category_id_2,category_id_3,
		brand_id,group_id_array,promotion_type,promote_id,goods_type,market_price,price,promotion_price,cost_price,
		point_exchange_type,point_exchange,give_point,is_member_discount,shipping_fee,shipping_fee_id,
		stock,max_buy,clicks,min_stock_alarm,sales,collects,star,evaluates,shares,province_id,city_id,
		picture,keywords,introduction,description,QRcode,code,is_stock_visible,is_hot,is_recommend,is_new,
		is_pre_sale,is_bill,state,sort,img_id_array,sku_img_array,match_point,match_ratio,real_sales,
		goods_attribute_id,goods_spec_format,goods_weight,goods_volume,shipping_fee_type,extend_category_id,
		extend_category_id_1,extend_category_id_2,extend_category_id_3,supplier_id,sale_date,create_time,update_time,min_buy
		';
		$res = $goodsM
			->where(['goods_id'=>$id])
			->field($field)
			->find();
			if(empty($res)){
			    throw new Exception('没有该商品','500');
            }
        $res    = $res->toArray();

		$picture = new Picture();
		if (!empty($res['picture'])){
			$res['picture_url'] = $picture ->transformAlbum($res['picture']);
		}
		if (!empty($res['img_id_array'])){
			$res['pictures_url'] = $picture ->transformAlbum($res['img_id_array']);
		}

		return $res;
	}

    public function readSubData($id)
    {
        $goodsM = new dataGoods;
        $field  = 'goods_id,goods_name,shop_id,price,picture';
        $res = $goodsM
            ->where(['goods_id'=>$id])
            ->field($field)
            ->find();
        $PromotionS = new PromotionService;
        $discount = $PromotionS->realDiscountPrice($id);
        $res['realPrice'] = $discount;
        if(empty($res)){
            throw new Exception('没有该商品','500');
        }
        $res = $res->toArray();
        $picture = new Picture();
        if (!empty($res['picture'])){
            $res['picture_url'] = $picture ->transformAlbum($res['picture']);
        }
        if (!empty($res['img_id_array'])){
            $res['pictures_url'] = $picture ->transformAlbum($res['img_id_array']);
        }

        return $res;
    }

	/**
	 * 查询商品详情 sku 商品的ID
	 * @param $id
	 * @return array|false|\PDOStatement|string|\think\Collection
	 */
	public function readItem($id)
	{
        $goodsArr	= $this->read($id);
		$skuS		= new Sku();
		$skuArr		= $skuS ->read($id);
        $promotion     = new PromotionService();
        $promotionArr  = $promotion -> read($id);
        $goodsArr['promotion'] = $promotionArr;
        //获取到折后价
        $realPriceArr   = $promotion -> realDiscountPrice($id,0,1);
        $realPrice      = $realPriceArr['realPrice'];
        $disNum         = $realPriceArr['disNum'];
        if (!empty($skuArr['item'])){
            foreach ($skuArr['item'] as $k => $item){
                $skuArr['item'][$k]['real_price'] = round($skuArr['item'][$k]['price'] * $disNum / 10, 2);
            }
        }
        $getFav     = $this -> getCollectedInfo($id);
        $fav     = $getFav -> find();
        if (empty($fav)){
            $goodsArr['is_collected']   = '0';
        }else{
            $goodsArr['is_collected']   =1 ;
        }

        $goodsArr['sku'] = $skuArr;
        $goodsArr['promotion_price'] = $realPrice;
        return $goodsArr;
	}

	public function categoryRead($category_id,$shop_id)
	{
		$tPic = Config('table.sys_album_picture');
		$goodsM = new dataGoods;
		$res = $goodsM
			->where( [ 'category_id_1'=>$category_id,'goods.shop_id'=>$shop_id ] )
			->alias('goods')
			->join($tPic.' pic','goods.picture = pic.pic_id','left')
			->select();
		$res = collection($res)->toArray();
		return $res;
	}

    /**
     * 获取商品的相关信息   主要服务于优惠promotion
     */
    public function getGoodsInfo($idData){
        $goodsM = new dataGoodsSku();
        $goodsData = $goodsM::All();
        $goodsData = collection($goodsData) -> toArray();
        foreach($idData as $id_key => $id_value){
            foreach($goodsData as  $goods_key => $goods_value){
                if ($id_value['goods_id'] == $goods_value['goods_id'] && $id_value['sku_id'] == $goods_value['sku_id']){
                    $goodsInfo[] = $goods_value;
                }
            }
        }
        return $goodsInfo;
    }


    public function getGoodsDetail($goods_sku){
        if (gettype($goods_sku) == 'array'){
            $goodsSkuM = new dataGoodsSku();
            $goodsDetail = $goodsSkuM
                ->join('ns_goods','ns_goods.goods_id = ns_goods_sku.goods_id','left')
                ->where('sku_id ','IN',$goods_sku)
                ->select();
            if (empty($goodsDetail)){
                throw new Exception('没有相关商品','404');
            }
            $goodsDetail = collection($goodsDetail) -> toArray();
        }else{
                $goodsSkuM = new dataGoodsSku();
                $goodsDetail = $goodsSkuM
                    ->join('ns_goods','ns_goods.goods_id = ns_goods_sku.goods_id','left')
                    ->where(['sku_id ' => $goods_sku] )
                    ->find();
            if (empty($goodsDetail)){
                throw new Exception('没有相关商品','404');
            }
            $goodsDetail = $goodsDetail -> toArray();
        }

        return $goodsDetail;
    }


        /**
         * 收藏商品
         */
        public function collecte($collectInfo)
        {
            $fav_id = $collectInfo['goods_id'];
            $fav_type = $collectInfo['fav_type'];
            $log_msg = $collectInfo['tag']; //标签
            $result = $this->collectGoods($fav_id, $fav_type, $log_msg);
            return $result;
        }

    /**
     *
     */
        public function collectGoods($fav_id, $fav_type, $log_msg = ''){
            $uid        = $_SERVER['uid'];
            $goodsM     = new dataGoods();
            $goodsInfo  = $goodsM -> where('goods_id','eq',$fav_id) ->field('goods_name,shop_id,price,picture,collects') -> find();
            if (empty($goodsInfo)){
                throw new Exception('没有该商品',404);
            }
            $memberFavoritesM = new NsMemberFavoritesModel();
            $favData = $memberFavoritesM -> where([
                "fav_id" => $fav_id,
                "uid" => $uid,
                "fav_type" => $fav_type
            ]) -> find();

            // 检查数据表中，防止用户重复收藏
            if (!empty($favData)) {
                $memberFavoritesM -> destroy($favData -> log_id);
                return 0;
            }

            //收藏商品
            // 查询商品图片信息
            $album = new AlbumPictureModel();
            $picture = $album-> where(['pic_id' => $goodsInfo -> picture]) ->field('pic_cover_small') -> find();
            $shop_name = "";
            $shop_logo = "";
            $shop_id = 0;

            $data = [
                'uid' => $uid,
                'fav_id' => $fav_id,
                'fav_type' => $fav_type,
                'fav_time' => time(),
                'shop_id' => $shop_id,
                'shop_name' => $shop_name,
                'shop_logo' => $shop_logo,
                'goods_name' => $goodsInfo -> goods_name,
                'goods_image' => $picture -> pic_cover_small,
                'log_price' => $goodsInfo -> price,
                'log_msg' => $log_msg
            ];
            $res = $memberFavoritesM -> save($data);
            $goodsM->save([
                "collects" => $goodsInfo -> collects + 1
           ], [
                "goods_id" => $fav_id
            ]);
            return $res;
        }

        public function getCollectionList($uid,$fav_type = 'goods'){
            $memberFavoritesM = new NsMemberFavoritesModel();
            $favData = $memberFavoritesM -> where([
                "uid" => $uid,
                "fav_type" => $fav_type
            ]) -> select();
            $favData = collection($favData)->toArray();
            foreach ($favData as $k => $v){
                $goodsData[] = $this->readSubData($v['fav_id']);
            }
            return $goodsData;
        }
}