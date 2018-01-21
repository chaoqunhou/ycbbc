<?php

namespace app\api\controller\v1;

use app\api\api\StandardInterface;
use app\api\controller\baseApi;
use app\api\service\Promotion as PromotionService;
use think\Exception;
use app\api\service\Cart as CartService;
use app\api\service\Address as AddressService;
use app\api\service\Order as OrderService;

class Cart extends baseApi implements StandardInterface
{

    public function index()
    {

    }

    public function create()
    {

    }

    /**
     * @param $id  用户ID  uid
     */
    public function read($id)
    {


    }

    public function edit($id)
    {

    }

    /**
     * 加入购物车
     */
    public function save()
    {
        $res = [1];
        try {

            if (empty($_SERVER['uid'])) throw new Exception('需要登陆', 1100);
            $param = input('param.');
            //加入购物车
            #sku 是否存在
            //uid  sku_id  num
            $uid = $_SERVER['uid'];
            $num = $param['num'];
            $sku_id = $param['sku_id'];
            $cartS = new CartService();
            $res = $cartS->save($sku_id,$num,$uid);
        } catch (Exception $e) {
            returnJson($e->getMessage(), $e->getCode());
        }
        returnJson($res);
    }

    /**
     * @param $id 购物车ID
     * 购物车购买数量的修改
     */
    public function update($id)
    {
        try {
            if (!($uid = $_SERVER['uid'])) throw new Exception('请先登录', '1100');
            $num = input('param.num');
            $cartS = new CartService();
            $res = $cartS->update($num, $id);
        } catch (Exception $e) {
            returnJson($e->getMessage(), $e->getCode());
        }
        returnJson($res);
    }

	/**
	 * @param $id 购物车ID
	 * 购物车货品删除
	 */
    public function delete($id)
    {
        try {
            if (!($uid = $_SERVER['uid'])) throw new Exception('请先登录', '1100');
            $cartS = new CartService();
            $res = $cartS->delete($id);
        } catch (Exception $e) {
            returnJson($e->getMessage(), $e->getCode());
        }
        returnJson($res);
    }


	/**
	 * 从APP端购物车传入的结算勾选状态
	 */
    public function chooseAction()
    {
        try {
            if (!($uid  = $_SERVER['uid'])) throw new Exception('请先登录', '1100');
            $cartS      = new CartService();
            $param      = input('param.');
            $cartS      -> chooseStatus($param);
            $orderS     = new OrderService();
            //$moneyInfo是关于总价格和优惠的多少元等相关信息
            $moneyInfo = $orderS -> orderPrice();
        } catch (Exception $e) {
            returnJson($e->getMessage(), $e->getCode());
        }
        returnJson($moneyInfo);
    }

    /**
     * @return  购物车列表
     */
    public function readCartAction()
    {
        try {
            if (!($uid = $_SERVER['uid'])) throw new Exception('请先登录', '1100');
            $cartS = new CartService();
            $cartData = $cartS->read($uid,0);
            //根据购物车数据,查找每个商品优惠
            $PromotionS = new PromotionService;
            $cartData   = $PromotionS->getGoodsPromotion($cartData);

        } catch (Exception $e) {
            returnJson($e->getMessage(), $e->getCode());
        }
        returnJson($cartData);
    }

    /**
     * 订单确认页面接口
     */
    public function readOrderAction($is_fast_buy = 0)
    {
        try{
            if (!($uid = $_SERVER['uid'])) throw new Exception('请先登录', '1100');
            $orderS     = new OrderService();
            $orderData = $orderS -> orderPrice();

            //$moneyInfo是关于总价格和优惠的多少元等相关信息
//            $orderData['orderPromotion'] = $orderData;
            $addressS = new AddressService();
            $addressData = $addressS->readDefault();
            $orderData['address'] = $addressData;
        }catch (Exception $e) {
            returnJson($e->getMessage(), $e->getCode());
        }
        returnJson($orderData);
    }
    public function buyNowAction(){
        try{
            if (!($uid = $_SERVER['uid'])) throw new Exception('请先登录', '1100');
            $cartS = new CartService();
            $param = input('param.');
            $uid = $_SERVER['uid'];
            $sku_id = $param['sku_id'];
            $is_fast_buy=1;
            $cartS->save($sku_id, $num=1 ,$uid,$is_fast_buy);
            $res = $this -> readOrderAction($is_fast_buy);
        }catch(Exception $e){
            returnJson($e -> getMessage(),$e ->getCode());
        }
        $res;
    }
    /**
     * 获取订单优惠信息接口,供订单确认页面进行使用,当前台进行点击优惠相关按钮时,就会触发该接口
     */
    public function promotionAction(){
        $promotionS = new PromotionService();
        $uid = $_SERVER['uid'];
       $promotionData = $promotionS -> getPromotion($uid);
       if (empty($promotionData)){
           $data = ['没有优惠信息'];
           returnJson($data);
       }
       returnJson($promotionData);
    }

    /**
     * 选择优惠,将优惠信息ID存入购物车
     */
    public function promotionChooseAction(){

        try {
            if (!($uid = $_SERVER['uid'])) throw new Exception('请先登录', '1100');
            $cartS = new CartService();
            $param = input('param.');
            $cartS->choosePromotionInfo($param);

            $orderS = new OrderService;
            $priceArr = $orderS ->orderPrice();

        } catch (Exception $e) {
            returnJson($e->getMessage(), $e->getCode());
        }
        returnJson($priceArr);

    }

}