<?php

namespace app\api\service;


use data\model\NsPromotionDiscountGoodsModel as dataDiscountGoods;
use data\model\NsPromotionMansongGoodsModel as dataManjianGoods;
use data\model\NsPromotionMansongModel;
use data\model\NsPromotionMansongRuleModel as dataManjianRule;
use data\model\NsPromotionMansongRuleModel;
use think\Exception;
use app\api\service\Sku as skuService;


class Promotion extends BaseService
{
    /**
     * @param $goods_id  商品的ID
     * @param $shop_id   商铺的ID
     * @return 商品的优惠
     */
    public function read($goods_id)
    {
        $promotion['discount'] = $this->getDiscount($goods_id);
        $promotion['money_off'] = $this->getMansongData($goods_id);
        return $promotion;
    }


    /**
     * @param $cartData
     * @return mixed
     */
    public function getGoodsPromotion($cartData)
    {
        $goodsId = array_column($cartData, 'goods_id');
        //获取相关的订单满减信息
        $mansongData = $this->getMansongData($goodsId);
        $discountData = $this->getDiscount($goodsId);
        //因为是根据购物车内商品的ID进行查询的商品优惠信息,所以只进行以下打折和满送的相关判断就行了
        foreach ($cartData as $cart_key => $cart_item) {
            if (!empty($mansongData)) {
                foreach ($mansongData as $mansong_key => $mansong_value) {
                    if ($cart_item['goods_id'] == $mansong_value['goods_id'] || $mansong_value['range_type'] == 1) {
                        $cartData[$cart_key]['promotion']['mansong'][] = $mansong_value;
                    }
                }
            }
            if (!empty($discountData)) {
                //$discountData 只有一个
                $discount_value = $discountData[0];
                if ($cart_item['goods_id'] == $discount_value['goods_id']) {
                    $cartData[$cart_key]['promotion']['discount'][] = $discount_value;
                }
            }
            $cartData[$cart_key]['real_price'] = $this->realDiscountPrice($cart_item['goods_id'],$cart_item['sku_id']);
        }
//        $promotionName = array_column($orderPromotion,'mansong_name');
        return $cartData;
    }
    /**
     * 获取订单所有的优惠
     */
    public function getPromotion($uid){
        $cartS = new Cart();
        //获取购物车选中的商品
        $cartData = $cartS->getCartData($uid, 1);
        $goodsId = array_column($cartData,'goods_id');
        $promotionData =  $this -> read($goodsId);
        return $promotionData;
    }


    //计算购物车中选中的商品原价总价格

    public function goodsPrice($uid)
    {
        $cartS = new Cart();
        //获取购物车选中的商品
        $cartData = $cartS->getCartData($uid, 1);
        foreach ($cartData as $goods_key => $goods_value) {
            $idArray = [];
            $idArray['goods_id'] = $goods_value['goods_id'];
            $idArray['sku_id'] = $goods_value['sku_id'];
            $idData[] = $idArray;
        }
        $goodsS = new Goods();
        $goodsData = $goodsS->getGoodsInfo($idData);
        $goodsPrice = array_column($goodsData, price);
        $goodsPrice = array_sum($goodsPrice);
        return $goodsPrice;
    }
    /**
     * 获取所有的打折商品
     */

    /**
     * @param $goods_id  商品的ID
     * @param $shop_id   商铺的ID
     * @return 商品打折信息 涉及到的数据表有 ns_discount ns_discount_goods表
     */
    protected function getDiscount($goods_id, $shop_id = 0)
    {

        $discountM = new dataDiscountGoods();
        if (gettype($goods_id) == 'array') {
            $goods_id = implode(',', $goods_id);
        }
        $res = $discountM
            ->alias('dg')
            ->join('ns_promotion_discount d', 'dg.discount_id=d.discount_id', $type = 'LEFT')
            ->where('dg.goods_id in' . '(' . $goods_id . ')' . ' AND dg.start_time < ' . time() . ' AND dg.end_time > ' . time())
            ->order('dg.discount_goods_id desc')
            //打折只存在最新的
            ->limit(1)
            ->select();
        if (empty($res)) return [];
        $discountArr = collection($res)->toArray();
//        $discountArr = $this -> isIndate($discountArr);
        return $discountArr;
    }

    /**
     * @param $goods_id 商品ID
     * @param int $shop_id 商铺ID
     * @return 满减信息 涉及到的表有 ns_mansong   ns_mansong_goods  ns_mansong_rule
     */
    protected function getMansongData($goods_id, $shop_id = 0)
    {
        $mansongM = new dataManJianGoods();
        if (is_array($goods_id)) {
            $goods_id = implode(',', $goods_id);
        }
        $res = $mansongM
            ->alias('mjg')
            ->join('ns_promotion_mansong mj', 'mjg.mansong_id=mj.mansong_id', $type = 'RIGHT')
            ->where('mjg.goods_id in'.' (' . $goods_id . ')' . ' OR mj.range_type = 1')
            ->order('mj.mansong_id','desc')
            ->select();

        if (empty($res)) return [];
        $mansongData = collection($res)->toArray();
        $mansongData = $this->isIndate($mansongData);
        foreach ($mansongData as $key => $v) {
            if ($v['promotion_status'] !== 1) {
                $mansongData[$key]['manSongRule'] = '不在有效期跳过查询';
                unset($mansongData[$key]);
                continue;
            }
            $manjianRM = new dataManJianRule();
            $ruleRes = $manjianRM
                ->where(['mansong_id' => $v['mansong_id']])
                ->select();
            $mansongData[$key]['manSongRule'] = collection($ruleRes)->toArray();
        }
        return $mansongData;
    }

    /**
     * @param$promotionData
     * @param
     * @return 优惠是否已经关闭或者结束 1 在活动期内
     * promotion_status 活动状态  0 活动未开始  1 活动中  2 活动已经结束
     */
    public function isIndate($promotionData)
    {
        $time = time();
        if (count($promotionData) == count($promotionData, 1)) {
            if ($promotionData['start_time'] > $time) {
                $promotionData['promotion_status'] = 0;
                return 0;
            } elseif ($promotionData['end_time'] < $time) {
                $promotionData['promotion_status'] = 2;//活动已经结束
            } else {
                $promotionData['promotion_status'] = 1;//活动中
            }
        } else {
            foreach ($promotionData as $promotion_key => $promotion_value) {
                if ($promotion_value['start_time'] > $time) {
                    $promotionData[$promotion_key]['promotion_status'] = 0;
                } elseif ($promotion_value['end_time'] < $time) {
                    $promotionData[$promotion_key]['promotion_status'] = 2;
                } else {
                    $promotionData[$promotion_key]['promotion_status'] = 1;
                }
            }
        }
        return $promotionData;
    }

    //计算商品打折后的真实价格
    //$returnDiscount :既返回价又返回折扣率
    public function realDiscountPrice($goods_id, $sku_id = 0 ,$returnDiscount = 0)
    {
        $discountArr = $this->getDiscount($goods_id);
        if ($sku_id != 0){
            $skuS   = new skuService;
            $skuData= $skuS->readSku($sku_id);
            $price  = $skuData[0]['price'];
        }else{
            $goodsS = new Goods();
            $goodsData = $goodsS->read($goods_id);
            $price  = $goodsData['price'];
        }

        if (!empty($discountArr)) {
            $disNum = $discountArr[0]['discount'];
            $realPrice = round($discountArr[0]['discount'] * $price / 10, 2);
        } else {
            $disNum = 10;
            $realPrice = $price;
        }
        if ($returnDiscount){
            return ['realPrice'=>$realPrice,'disNum'=>$disNum];
        }
        return $realPrice;
    }


}
