<?php
namespace app\api\service;

use data\model\NsGoodsModel;
use data\model\NsOrderGoodsModel as dataOrderGoods;
use data\model\NsOrderModel as dataOrder;
use data\model\NsCartModel as dataCart;
use data\model\NsOrderActionModel as dataOrderAction;
use data\model\NsOrderPromotionDetailsModel as dataOrderPromotionDetail;
use data\model\NsOrderGoodsPromotionDetailsModel as dataGoodsPromotionDetail;
use data\model\NsPromotionMansongRuleModel as dataMansongRule;
use Symfony\Component\DomCrawler\Field\InputFormField;
use think\Exception;
use app\api\service\Cart as CartService;
use app\api\service\Promotion as PromotionService;
use app\api\service\Shipping as ShippingService;

class Order extends BaseService{
    /**
     * @param bool $shop_id
     * @return array
     * @throws Exception
     * @todo完成订单后可删除本方法
     */
    public function orderPrice($shop_id = false)
    {
        if (!($uid = $_SERVER['uid'])) throw new Exception('请先登录', '1100');

        $cartS = new CartService();
        $cartData   = $cartS -> getCartData($uid,1);
        //如果传了shop_id 则是计算某个店铺下
        if ($shop_id !== false){
            $tem = [];
            foreach ($cartData as $data){
                if ($data['shop_id'] == $shop_id){
                    $tem[] = $data;
                }
            }
            $cartData = $tem;
        }

        $promotionS = new PromotionService();
        //这里计算了单品折扣

        $cartRes  = $promotionS -> getGoodsPromotion($cartData);

        $totalPrice = 0;
        $realPrice  = 0;
        foreach ($cartRes as $goods){
            $totalPrice += $goods['price'] * $goods['num'];
            $realPrice  += $goods['real_price']  * $goods['num'];
        }
        $totalPrice = round($totalPrice,2);
        $realPrice  = round($realPrice,2);

        //----------------------计算优惠折扣----------------------------------
        $msRulesP = [];
        $msRules  = [];
        //遍历规则
        foreach ($cartRes as $goodsK => $goodsV ){
            if (empty($goodsV['promotion'])){
                continue;
            }
            if ($goodsV['promotion_rule_id'] != 0 && !empty($goodsV['promotion']['mansong'])){
                $msRulesP[$goodsV['promotion_rule_id']] += $goodsV['real_price'];
                foreach ($goodsV['promotion']['mansong'] as $mansong){
                    foreach ($mansong['manSongRule'] as $rule){
                        if (!empty($rule['rule_id'])){
                            $msRules[$rule['rule_id']] = $rule;
                        }
                    }
                }
            }
        }

        $discont = 0;
        $useRule = [];
        $unUseRule = [];
        foreach ($msRulesP as $rk => $p){
            if (!isset($msRules[$rk])){
                //  选择了优惠但优惠数组中没有
                continue;
            }
            $msRules[$rk]['rule_real_price'] = $p;
            if ($p >= $msRules[$rk]['price']){
                $useRule[]=  $msRules[$rk];
                if(!empty($msRules[$rk]['discount'])){
                    $discont += $msRules[$rk]['discount'];
                }
            }else{
                $unUseRule[] = $msRules[$rk];
            }
        }
        //=========---------------计算优惠折扣-------------=========================
        return [
                'total_price'   => $totalPrice, //原总价
                'price'         => $realPrice,  //折后价
                'discount'      => $discont,    //优惠的额价格
                'real_price'    => $realPrice - $discont, //实际价格
                'useRule'       => $useRule,    //使用上的优惠
                'unUseRule'     => $unUseRule,    //使用上的优惠
                'promoted_goods_list' => $cartRes,
        ];
    }


    protected function orderNo()
    {
        $orderNo = date('YmdHis',time());
        $orderNo = $orderNo . mt_rand(10000,99999);
        return $orderNo;
    }
    protected function outOrderTradeNo(){
        $outOrderTradeNo = time() . mt_rand(10000,99999);
        return $outOrderTradeNo;
    }

    //创建订单
    public function orderCreate($uid,$orderInfo)
    {
        $orderM = new dataOrder();
        $orderM -> startTrans();

        $paymentType = isset($orderInfo['payment_type']) ? $orderInfo['payment_type'] : 4;
        if ($paymentType != 4 ) throw new Exception('只支持货到付款', 411);
        $order_from = $orderInfo['client_id'];//订单来源 1 安卓 2 ios 3 网页 4 微信
        $addressS    = new Address();
        $addressInfo = $addressS -> readOne($orderInfo['address_id']);//收货地址信息
        $cartS = new CartService();
        $cartDataList = $cartS ->getCartData($uid,1,$orderInfo['is_fast_buy']);//获取所有购物车中所有的商品
        if (!count($cartDataList) > 0)throw new Exception('cartData为空',500);
        $userS      = new User();
        $userInfo   = $userS ->read($uid);//买家信息

        $orderInfo['buyer_id'] = $uid;
        $orderNo = $this ->orderNo();//订单编号
        $outTradeNo = $this -> outOrderTradeNo();

        $createTime = time();
        $orderM = new dataOrder();

        $shopS      = new Shop();
        //---------------------------通过购物车内商品信息获取shop_id---------------------------------
        $cartDataShop   = $this->sdfCartGoodsBYShop($cartDataList);//根据商铺id将数据分组，用于创建不同商铺的订单
        if (count($cartDataShop) != 1)throw new Exception('只能单店支付',500);
        foreach ($cartDataShop as $shop_id => $cartData){
            //获取商铺的信息
            $shopInfo   = $shopS -> read($shop_id);
            //========================================================================================
//          当买家选择自提时,将不用支付物流费用 shipping_type(物流方式 0:商家配送 1:自提) //
//          if ($orderInfo['shipping_type'] == 1){
//              //暂时定为默认商家配送
//          }elseif ($orderInfo['shipping_type'] == 0){
//              //当买家选择商铺配送时,需要获取物流费用
//              $shippingS = new ShippingService();
//              $shippingInfo = $shippingS -> getShippingInfo($orderInfo['shipping_id']);
//          }
//            $priceInfo = $this ->orderPrice($shop_id);//此处需要计算当前订单价格
            $priceInfo = $this ->getOrderPriceByCartDataList($cartData);//根据查出的购物车商品计算订单金额
            $goodsMoney = $priceInfo['total_price']; //商品折扣前销售总价
            /**
             * 订单总价即订单金额，不等于支付金额
             * 订单金额($orderMoney)包含=（支付金额（实际支付金额$pay_money【包含用户余额支付金额】）+折扣金额+优惠金额+积分折扣金额+代金券金额+购物币金额）
             */
            $pay_money = $priceInfo['promote_price']; //商品折后销售总价，订单支付金额，不等于订单金额，支付
            $orderMoney = $priceInfo['promote_price']+$priceInfo['discount_price']; //订单总价即订单金额
            $discount_money = $priceInfo['discount_price']; //商品实际折扣掉的金额（折扣前金额*（1-折扣）%）
            $promotionMoney = 0; //优惠金额(打折的优惠和满减优惠和)2018.1.18调整为满减优惠的金额
            $cartNewData = $priceInfo['promoted_goods_list'];//使用此处数据，是同步获取了最新的商品价格
            $orderM ->data([
                'shop_id'           => $shop_id,
                'shop_name'         => $shopInfo['shop_name'],
                'order_no'          => $orderNo,
                'out_trade_no'      => $outTradeNo ,
                'order_type'        => 1 ,//订单类型,都有什么类型？
                'payment_type'      => $paymentType ,// 支付方式  0：在线支付 1：微信支付 2：支付宝 3：银联卡 4：货到付款 5：余额支付
                'order_from'        => $order_from,
                'buyer_id'          => $uid ,
                'user_name'         => $userInfo['user_name'],
                'buyer_ip'          => $userInfo['current_login_ip'],
                'buyer_message'     => $orderInfo['buyer_message'],//买家留言
                'buyer_invoice'     => '',
                'create_time'       => $createTime ,
                'shipping_type'     => 1, //配送方式 0 买家自提 1 商家配送
                'shipping_company_id' => 1, //物流配送公司ID
                'receiver_mobile'   => $addressInfo['mobile'],
                'receiver_name'     => $addressInfo['consigner'],
                'receiver_province' => $addressInfo['province'],
                'receiver_city'     => $addressInfo['city'],
                'receiver_district' => $addressInfo['district'],
                'receiver_address'  => $addressInfo['address'],
                'receiver_zip'      => $addressInfo['district'],
                'order_money'       => $orderMoney,
                'goods_money'       => $goodsMoney,
                'promotion_money'   => $promotionMoney,//满减优惠金额
                'discount_money'   => $discount_money,//打折活动优惠金额
                'pay_money'         => $pay_money,//实际订单支付金额
                'point'             =>0,//消耗的积分
                'point_money'       =>0,//积分抵用的金额
                'coupon_money'      =>0,//优惠券抵用的价格
                'coupon_id'         =>0,//订单代金券ID
                'shipping_money'    =>0,//订单运费
                'refund_money'      =>0,//退款金额
                'coin_money'        =>0,//购物币金额
                'give_point'        =>0,//赠送的积分
                'give_coin'         =>0,//订单成功后反购物币
                'order_status'      =>0,//订单状态 0 未完成 1 已完成
                'pay_status'        =>0,//付款状态 0 未付款  1 已付款
                'shipping_status'   =>0,//0 未发货 1 已发货
                'review_status'     =>0,//订单评价状态 0: 未评价 1:已评价 2:已追评
                'feedback_status'   =>0,//维权状态 0:无维权 1 正在维权
            ]);
            $res = $orderM -> save();
            if ($res < 0){
                $orderM ->rollback();
                throw new \Exception('创建订单失败[1]',500);
            }
            //订单id
            $order_id = $res;
            //================================订单商品明细表================================
            $cart_pro = $priceInfo['promoted_goods_list'];
            $this ->orderGoods($order_id,$cart_pro);
            //================================打折优惠================================
            $total_discount_price = $this -> orderGoodsPromotion($order_id,$cartNewData);//存储折扣信息 ns_order_goods_promotion_details
            //===============================满减优惠================================
            //根据选择的优惠信息，存储数据到 ns_order_promotion_details
            $total_promotion_price = $this -> orderPromotionDetails($order_id,$cartNewData);
            //获取优惠总额度
            //================================订单操作日志记录================================
            $action = '创建订单';//记录订单操作日志
            $res_orderAction = $this ->orderAction($order_id,$action,$userInfo);//修复$res为$res_orderAction
            if ($res_orderAction < 0){
                $orderM ->rollback();
                throw new \Exception('创建订单失败[2]',500);
            }
            //================================订单金额更新================================
                $orderM->save([
                    'promotion_money'=>$total_promotion_price,
                    'pay_money'=>$pay_money-$total_promotion_price,
                ],['id'=>$order_id]);
            //================================支付状态表================================
            //完成订单写入后，执行购物车清空操作
            $orderM ->commit();
        }
    }

    /**
     * 根据查出的购物车商品计算订单金额
     * @param $cartData
     */
    public function getOrderPriceByCartDataList($cartData){
        //循环处理购物车中的数据
        $sku = new Sku();
        $totalPrice = 0;
        $promote_price  = 0;
        foreach ($cartData as $k=>$v){
            $sku_data = $sku->readOneSku($v['sku_id']);//根据skuid获取最新的价格
            $totalPrice += $sku_data['price'] * $v['num'];//根据当前价格计算总额
            $promote_price += $sku_data['promote_price'] * $v['num'];//根据当前价格计算总额
            $cartData[$k]['price'] = $sku_data['price'];
            $cartData[$k]['promote_price'] = $sku_data['promote_price'];
        }
        //=========---------------计算优惠(折扣)-------------=========================
        return [
            'total_price'   => $totalPrice, //原（销售价）总价
            'promote_price'         => $promote_price,  //(实际金额)每个单品折扣后的售价=售价*折扣率%
            'discount_price'      => $totalPrice-$promote_price,    //优惠的额价格
            'promoted_goods_list' => $cartData,
        ];
    }

    /**
     * 根据订单号获取对应的满减优惠的总金额
     * @param $order_id
     */
    public function getOrderPromotionTotalAmount($order_id){
//        $orderPromotionM = new dataOrderPromotionDetail();
//        $amount = $orderPromotionM->where('order_id',$order_id)->sum('discount_money');
//        return $amount;
    }

    /**
     * @param $order_id
     * @param $action
     * @param string $userInfo
     * @return bool
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 订单操作表添加
     */
    public function orderAction($order_id,$action,$userInfo =''){
        $orderActionM = new dataOrderAction();
        $orderActionM -> startTrans();
        //如果没有用户信息则到用户表内进行取出用户信息
        if (empty($userInfo)){
            $uid =$_SERVER['uid'];
            $userS      = new User();
            $userInfo   = $userS ->read($uid);//买家信息
        }
        $data['user_name'] = $userInfo['user_name'];
        $orderM         = new dataOrder();
        $orderInfo      = $orderM -> where(['order_id' => $order_id]) ->find();
        $order_status   = $orderInfo -> order_status;
        $order_status_text          = $this ->getOrderStatus($order_id,$order_status);
        $data['order_status']       = $order_status;
        $data['order_id']       = $order_id;
        $data['action']       = $action;
        $data['order_status_text']  = $order_status_text;
        $data['action_time']        = time();
        $res = $orderActionM -> save($data);
        $orderActionM ->commit();
        if ($res < 0){
            $orderActionM -> rollback();
            throw new Exception('异常','500');
        }


        return $res;
    }

    /**
     * 订单商品明细表
     * @param $order_id
     * @param $cartData
     * @throws \Exception
     */
    public function orderGoods($order_id,$cartData)
    {
        $orderGoodsM =new dataOrderGoods();
        $orderGoodsM -> startTrans();
        foreach($cartData as $promotion_key => $promotion_value){
            $orderGoods['order_id'] = $order_id;
            $orderGoods['goods_id'] = $promotion_value['goods_id'];
            $orderGoods['goods_name'] = $promotion_value['goods_name'];
            $orderGoods['sku_id'] = $promotion_value['sku_id'];
            $orderGoods['sku_name'] = $promotion_value['sku_name'];
            $orderGoods['price'] = $promotion_value['price'];//折后价
            $orderGoods['cost_price'] = $promotion_value['price'];//商品成本@todo 获取数据
            $orderGoods['num'] = $promotion_value['num'];
            $orderGoods['goods_money'] = $promotion_value['price'] * $promotion_value['num'];//折后总价
            $orderGoods['goods_picture'] = $promotion_value['goods_picture'];//商品图片@todo 获取数据
            $orderGoods['shop_id'] = $promotion_value['shop_id'];
            $orderGoods['promotion_id'] = $promotion_value['promotion_rule_id'];//这个是在购物车选择的满减优惠规则ID
            $orderGoods['goods_type'] = '';//商品类型@todo 获取数据
            $orderGoods['order_type'] = 1;//订单类型
            $orderGoods['order_status'] = 0;//订单状态
            $orderGoodsData[] = $orderGoods;
        }
        $orderGoodsM ->saveAll($orderGoodsData);
        $orderGoodsM ->commit();

//
//
//
//
//
//
//
//
//        $promotionS = new PromotionService;
//
//        //$promotionData = $promotionS -> getGoodsPromotion($cartData);
//        $promotionData = $cartData;
//        $goodsId    = implode(',',array_column($cartData,'goods_id'));
//        $goodsM     = new NsGoodsModel();
//        $goodsData  = $goodsM ->where('goods_id','IN',$goodsId) -> select();
//        foreach($promotionData as $promotion_key => $promotion_value){
//            $orderGoods['order_id'] = $order_id;
//            $orderGoods['goods_id'] = $promotion_value['goods_id'];
//            $orderGoods['goods_name'] = $promotion_value['goods_name'];
//            $orderGoods['sku_name'] = $promotion_value['sku_name'];
//            $orderGoods['price'] = $promotion_value['real_price'];//折后价
//            $orderGoods['cost_price'] = $promotion_value['price'];
//            $orderGoods['num'] = $promotion_value['num'];
//            $orderGoods['goods_money'] = $promotion_value['real_price'] * $promotion_value['num'];//折后总价
//            $orderGoods['goods_picture'] = $promotion_value['goods_picture'];
//            $orderGoods['shop_id'] = $promotion_value['shop_id'];
//            if ($promotion_value['promotion']['mansong']){
//                $orderGoods['promotion_id'] = $promotion_value['promotion_rule_id'];
//            }elseif ($promotion_value['promotion']['discount']){
//                $orderGoods['promotion_id'] = $promotion_value['promotion']['discount'][0]['discount_id'];
//            }
//            foreach($goodsData as $key => $value){
//                if ($value['goods_id'] == $promotion_value['goods_id']){
//                    $orderGoods['goods_type'] = $promotion_value['goods_type'];
//                }
//            }
//            $orderGoodsData[] = $orderGoods;
//        }
//        $res = $orderGoodsM ->saveAll($orderGoodsData);
//        $orderGoodsM ->commit();
////        dump($res);die;
    }

    /**
     * 将满减活动根据订单信息写入《订单优惠详情》数据表
     * @param $order_id
     * @param $orderData
     * @throws \Exception
     */
    public function orderPromotionDetails($order_id,$cartNewData){
        /**
         * 这里的$cartNewData，已根据商家进行区分
         * 根据写入的商家订单进行处理
         */
        //先处理规则ID
        $orderPromotionM = new dataOrderPromotionDetail();
        $orderPromotionM -> startTrans();
        foreach($cartNewData as $cart_key => $cart_value){
            if($cart_value['promotion_rule_id'] !=0){
                $mansongRule[]  =$cart_value['promotion_rule_id'];
                $mansongRule    = array_unique($mansongRule);//去除重复
            }
        }
        if ($mansongRule !=null) {
            $mansongRule = implode(',', $mansongRule);
            $mansongM = new dataMansongRule();
            $mansongData = $mansongM
                ->alias('msr')
                ->join('ns_promotion_mansong ms', 'msr.mansong_id = ms.mansong_id', 'LEFT')
                ->where('msr.rule_id', 'IN', $mansongRule)
                ->select();
        }
        $promotionData['order_id']  = $order_id;
        $total_promotion_price = 0;
        foreach($mansongData as $key => $value){
            $promotionData['promotion_type_id']     = $value ->mansong_id;//优惠类型规则ID（满减对应规则）
            $promotionData['promotion_id']          = $value ->rule_id;//优惠ID
            $promotionData['promotion_type']        = 'MANJIAN';//优惠类型
            $promotionData['promotion_name']        = $value ->mansong_name;//该优惠活动的名称
            $promotionData['promotion_condition']   = '满' .$value -> price .'减' . $value -> discount;//优惠使用条件说明
            $promotionData['discount_money']        = $value -> discount;//优惠的金额，单位：元，精确到小数点后两位
            $promotionData['used_time']             = time();
            $promotion[] =$promotionData;
            $total_promotion_price+=$value -> discount;//实际优惠金额
        }
        $orderPromotionM ->saveAll($promotion);
        $orderPromotionM -> commit();
        return $total_promotion_price;
//
//
//
//
//
//
//        $test = collection($mansongData) -> toArray();
//
//
//
//
//        echo 'promotion_rule';
//        print_r($test);
//        echo 'order_promotin';
//        print_r($cartNewData);
//        exit;
//
//        $orderPromotionM = new dataOrderPromotionDetail();
//        $orderPromotionM -> startTrans();
//        foreach($cartNewData as $key => $value){
//            $promotionData['promotion_type_id']     = $value ->mansong_id;//优惠类型规则ID（满减对应规则）
//            $promotionData['promotion_id']          = $value ->rule_id;//优惠ID
//            $promotionData['promotion_type']        = 'MANJIAN';//优惠类型
//            $promotionData['promotion_name']        = $value ->mansong_name;//该优惠活动的名称
//            $promotionData['promotion_condition']   = '满' .$value -> price .'减' . $value -> discount;//优惠使用条件说明
//            $promotionData['discount_money']        = $value -> discount;//优惠的金额，单位：元，精确到小数点后两位
//            $promotionData['used_time']             = time();
//            $promotion[] =$promotionData;
//        }
//        $orderPromotionM ->saveAll($promotion);
//        $orderPromotionM -> commit();
//
//
//
//
//
//        foreach($orderData as $cart_key => $cart_value){
//            if($cart_value['promotion_rule_id'] !=0){
//                $mansongRule[]  =$cart_value['promotion_rule_id'];
//                //去除重复
//                $mansongRule    = array_unique($mansongRule);
//            }
//        }
//        $promotionData['order_id']  = $order_id;
//        if ($mansongRule !=null){
//            $mansongRule    = implode(',',$mansongRule);
//            $mansongM       = new dataMansongRule();
//            $mansongData    = $mansongM
//                ->alias('msr')
//                ->join('ns_promotion_mansong ms','msr.mansong_id = ms.mansong_id','LEFT')
//                -> where('msr.rule_id','IN',$mansongRule)
//                -> select();
//
//            foreach($mansongData as $key => $value){
//                $promotionData['promotion_type_id']     = $value ->mansong_id;//优惠类型规则ID（满减对应规则）
//                $promotionData['promotion_id']          = $value ->rule_id;//优惠ID
//                $promotionData['promotion_type']        = 'MANJIAN';//优惠类型
//                $promotionData['promotion_name']        = $value ->mansong_name;//该优惠活动的名称
//                $promotionData['promotion_condition']   = '满' .$value -> price .'减' . $value -> discount;//优惠使用条件说明
//                $promotionData['discount_money']        = $value -> discount;//优惠的金额，单位：元，精确到小数点后两位
//                $promotionData['used_time']             = time();
//                $promotion[] =$promotionData;
//            }
//            $orderPromotionM ->saveAll($promotion);
//            $orderPromotionM -> commit();
//        }

    }

    /**
     * 将打折活动根据订单信息写入《订单商品折扣优惠详情》数据表
     * @param $order_id
     * @param $orderData
     * @throws \Exception
     * @todo校验打折信息
     */

    public function orderGoodsPromotion($order_id,$orderData){

        $goodsPromotionM    = new dataGoodsPromotionDetail();
        $goodsPromotionM    ->startTrans();
        $total_discount_price = 0;
        foreach ($orderData as $k=>$v){
            $discountData['order_id'] = $order_id;
            $discountData['sku_id'] = $v['sku_id'];
            $discountData['promotion_type'] = 'ZHEKOU';//折扣
            $discountData['promotion_id'] = 0;
            $discountData['discount_money'] = $v['price']-$v['promote_price'];//优惠掉的部分
            $discountData['used_time'] = time();
            $discount[] = $discountData;
            $total_discount_price += ($v['price']-$v['promote_price']);
        }
        if ($discount){
            $goodsPromotionM ->saveAll($discount);
            $goodsPromotionM ->commit();
        }
        return $total_discount_price;

//
//
//
//
//        $promotionS         = new PromotionService;
//        $promotionData      = $promotionS -> getGoodsPromotion($orderData);
//        $promotion_type_id  = array_unique(array_column($promotionData,'promotion_rule_id'));
//
//        $i = 0;
//        foreach($promotion_type_id as $promotion_rule_id) {
//            $sumMoney = 0;
//            foreach ($promotionData as $promotion_key => $promotion_value) {
//                if (!empty($promotion_value['promotion']['mansong'])) {
//                    $discountData['order_id'] = $order_id;
//                    $discountData['sku_id'] = $promotion_value['sku_id'];
//                    $discountData['promotion_type'] = 'MANJIAN';
//
//                    $discountData['promotion_id'] = $promotion_value['promotion_rule_id'];
//
//                    $discountData['used_time'] = time();
//
//                    if ($promotion_value['promotion_rule_id'] == $promotion_rule_id) {
//                        $sumMoney += $promotion_value['real_price'];
//                    }
//                    foreach ($promotionData as $promotion_key => $promotion_value) {
//                        if ($promotion_value['promotion_rule_id'] == $promotion_rule_id) {
//                            foreach ($promotion_value['promotion']['mansong'][0]['manSongRule'] as $rule_value) {
//                                $discountData['discount_money'] = round(($promotion_value['real_price'] / $sumMoney) * $rule_value['discount'],2);
//                            }
//                        }
//                    }
//                    $discount[] = $discountData;
//                }
//            }
//        }
//
//        if ($discount){
//            $res =$goodsPromotionM ->saveAll($discount);
//            $goodsPromotionM ->commit();
//        }

    }

    /**
     * @param $order_id 获取订单状态
     * @param string $order_status
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */

    public function getOrderStatus($order_id,$order_status = '')
    {
        if (!$order_status){
            $orderM = new dataOrder();
            $orderInfo = $orderM -> where(['order_id' => $order_id]) ->find();
        }
        $statusArray = OrderStatus::getOrderCommonStatus();
        foreach($statusArray as $status_key => $status_value){
            if ($status_value['status_id'] ==$order_status){
                return $status_value['status_name'];
            }
        }
        return false;

    }
    public function getOrderDetail($order_id){
      $detail   =  $this -> getDetail($order_id);
      $addressS = new Address();
      $province = $addressS ->getProvince($detail['receiver_province']);
      $city     = $addressS ->getCity($detail['receiver_city']);
      $district = $addressS -> getDistrict($detail['receiver_district']);
      $receiverInfo = '收件人:' . $detail['receiver_name'] . ' 联系电话:'. $detail['receiver_mobile'] . ' 收货地址:'.$province['province_name'] . $city['city_name'] . $district['district_name'] . $detail['receiver_address'];
      $detail['receiver_info']      =  $receiverInfo ;
      $detail['receiver_province']  = $province['province_name'];
      $detail['receiver_city']      = $city['city_name'];
      $detail['receiver_district']  = $district['district_name'];
      $orderGoodsDetail   = $this ->getOrderGoodsDetail($order_id);

      $detail['goods_info'] = $orderGoodsDetail;
      return $detail;
    }

    public function getDetail($order_id){

        $orderM         = new dataOrder();
        $orderDetail    = $orderM -> where( [ 'order_id'=>$order_id ,'is_deleted' => 0 ] ) -> find();
        if (empty($orderDetail)){
            throw new Exception('订单已经删除','404');
        }

        $orderDetail = $orderDetail -> toArray();
        return $orderDetail;
    }

    /**
     * @param $order_id  订单ID
     * @return 订单商品的详细信息
     */
    public function getOrderGoodsDetail($order_id){
        $orderGoodsM        = new dataOrderGoods();
        $orderGoods = [];
        //判断是否是多个订单
        if (is_in_str($order_id)){
            $orderGoodsDetail   = $orderGoodsM -> where('order_id' ,'in', $order_id) -> select();
//
        }else{
            $orderGoodsDetail   = $orderGoodsM -> where('order_id' ,'eq', $order_id) -> select();
        }
        $orderGoodsDetail       = collection($orderGoodsDetail) ->toArray();
        if (empty($orderGoodsDetail)){
            throw new Exception('没有相关商品信息','404');
        }
        //获取商品的具体规格信息
        $goods_sku              = array_column($orderGoodsDetail,'sku_id');
        $goodsS                 = new Goods();
        $goodsDetail            = $goodsS -> getGoodsDetail($goods_sku);

        $picture                = implode(',' , array_column($orderGoodsDetail,'picture'));


        //如果是多个订单,对订单数据进行重新构建

        if (is_in_str($order_id)){
            foreach($orderGoodsDetail as $order_goods_key => $order_goods_value){
                foreach($goodsDetail as $goods_detail_key => $goods_detail_value){
                    if ($order_goods_value['goods_id'] == $goods_detail_value['goods_id']){
                        $orderGoodsDetail[$order_goods_key]['sku_detail'][] = $goodsDetail;
                    }
                }
            }
        }


        return $orderGoodsDetail;
    }

    /**
     * @param $uid 用户ID
     * @return array|false|\PDOStatement|string|\think\Collection
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOrderList($uid,$param){
        $orderM = new dataOrder();
        $all = true;
        foreach($param as $key =>$value){
            $status = substr($key,-6);
            if ($status == 'status'){
                //获取相关的订单
                $orderList = $orderM -> where(['buyer_id' => $uid ,$key => $value ,'is_deleted' => 0]) -> select();
                $all =false;//代表不是返回全部列表
            }
        }
//        dump($orderList);
//        dump($all);die;
        if ($all){
                //获取全部订单
                $orderList = $orderM -> where(['buyer_id' => $uid , 'is_deleted' => 0]) -> select();
        }

        if (empty($orderList)){
            throw new Exception('您还没有相关订单','404');
        }
        $orderList     = collection($orderList) -> toArray();
        $orderIdArr    = array_column($orderList,'order_id');
        $orderId       = implode(',',$orderIdArr);
//        $orderGoodsM = new dataOrderGoods();
//        $orderGoodsData = $orderGoodsM -> where('order_id','in',$orderIdArr) -> select();
//        $orderGoodsData = collection($orderGoodsData) ->toArray();
        $orderGoodsData     = $this -> getOrderGoodsDetail($orderId);
//        dump($orderGoodsData);die;

        //根据不同订单将相应的商品放入订单树下
            foreach($orderList as $orderList_key => $orderList_value){
                foreach($orderGoodsData as $goods_key => $goods_value){
                    if ($orderList_key == $goods_value['order_id']){
                        $orderGoodslist[$goods_key][] = $goods_value;
                    }
                }
            }
//
//        foreach($orderList as $list_key => $list_value){
//            foreach($orderGoodsData as $goods_key => $goods_value){
//                if ($list_value['order_id'] == $goods_value['order_id']){
//                    $orderList[$list_key]['goods_list'][]= $goods_value;
//                }
//            }
//        }

        return $orderGoodslist;
    }
//    public function getOrderWaitForPayList($uid){
//        $orderM = new dataOrder();
//        $orderList = $orderM -> where(['buyer_id' => $uid ,'pay_status' => 0]) -> select();
//        if (empty($orderList)){
//            throw new Exception('您没有待付款订单','404');
//        }
//        $orderList = collection($orderList) -> toArray();
//        return $orderList;
//    }
//    public function getOrderShippingList($uid,$param){
//
//        $orderM = new dataOrder();
//        $orderList = $orderM -> where(['buyer_id' => $uid ,'shipping_status' => $param['shipping_status']]) -> select();//0是待收货,1是已收货
//        if (empty($orderList)){
//            throw new Exception('您没有待付款订单','404');
//        }
//        $orderList = collection($orderList) -> toArray();
//        return $orderList;
//    }
    public function orderDelete($order_id,$uid)
    {
        $orderM = new dataOrder();
        $res = $orderM -> where('order_id','eq',$order_id) -> update(['is_deleted' => 1]);//is_delete = 1 表示已经删除
        $action = '删除订单';
        $resAction = $this -> orderAction($order_id,$action);

        if ($res < 0 && $resAction){
            throw new Exception('没有找到该订单','404');
        }
        return $res;
    }

    /**
     * @param $order_id  订单ID
     * @return int|string
     * @throws Exception
     * @throws \think\exception\PDOException
     * 关闭订单
     */
    public function orderClose($order_id)
    {
        $orderM = new dataOrder();
        $res = $orderM -> where('order_id','eq',$order_id) -> update(['order_status' => 5 ]);//order_status = 5 表示已经关闭
        $action = '关闭订单';
        $resAction = $this -> orderAction($order_id,$action);
        $orderGoodsM = new dataOrderGoods();
        $orderData = $orderGoodsM -> where('order_id','eq',$order_id)-> select();
        foreach($orderData as $key => $value){
            if($value -> order_id == $order_id){
                $orderGoodsM -> where('order_goods_id','eq',$value -> order_goods_id) -> update(['order_status' => 5]);
            }
        }

        if ($res < 0 && $resAction){
            throw new Exception('没有找到该订单','404');
        }
        return $res;
    }
    public function orderCancel(){

    }

    //将CartGoods分店铺返回
    private function sdfCartGoodsBYShop($cartData)
    {
        $res = [];
        if (!count($cartData) > 0) throw new Exception('数组为空',500);
        foreach ($cartData as $goods){
            $res[$goods['shop_id']][] = $goods;
        }
        return $res;
    }
}
