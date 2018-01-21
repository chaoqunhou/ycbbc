<?php
namespace app\api\service;
use  data\model\NsCartModel as dataCart;
use data\model\NsPromotionMansongRuleModel as dataPromotionMansongRule;
use data\model\NsPromotionMansongGoodsModel as dataPromotionMansongGoods;
use Symfony\Component\DomCrawler\Tests\Field\InputFormFieldTest;
use think\Exception;


class Cart extends BaseService
{
    static public $cartM = null;
    static public function getInstance()
    {
        if (self::$cartM==null){
            self::$cartM    = new dataCart();
        }
        return self::$cartM;
    }
    public function __construct(){
        parent::__construct();
        self::getInstance();
    }
	public function save($sku_id,$num,$uid,$is_fast_buy=0)
	{
		//判断 $num  是否超过库存
		$skuS = new Sku;
		$skuRes = $skuS->readSku($sku_id);
		$skuRes = $skuRes[0];
		//需要 goods_sku goods shop cart[buyer_id sku_id]
		//根据sku_id查询goods_sku表，查出goods_id，再查goods表，取出goods_name，shop_id，picture，根据shop_id查询shop表，查出shop_name，shop_id
		$cartSkuDetail	= $this->readOne($sku_id,$uid,$is_fast_buy);
		if(empty($cartSkuDetail) || $is_fast_buy==1){
			if ($skuRes['stock'] < $num){
				throw new Exception('数量不足',400);
			}

			//开启事务  if is_fast_buy==1  where buyer_id  is_fast_buy  delete
            self::$cartM -> startTrans();
			if ($is_fast_buy==1 ){
			    self::$cartM->where(['buyer_id'=>$uid,'is_fast_buy'=>1])->delete();
            }
			$goodsId	= $skuRes['goods_id'];
			$goodsM		= new Goods();
			$goodsData	= $goodsM -> read($goodsId);
//
			$shopData	= new Shop();
			$shopData	= $shopData->read($goodsData['shop_id']);
			$data		=[
				'buyer_id'		=> $uid,
				'shop_id'		=> $shopData['shop_id'],
				'shop_name'		=> $shopData['shop_name'],
				'goods_id'		=> $goodsData['goods_id'],
				'goods_name'	=> $goodsData['goods_name'],
				'sku_id'		=> $sku_id,
				'sku_name'		=> $skuRes['sku_name'],
				'price'			=> $skuRes['price'],
				'goods_picture'	=> $goodsData['picture'],
				'num'			=> $num,
			];
			//if is_fast_buy==1  $data['is_fast_buy']=1; 提交事务
            if ($is_fast_buy==1){
                $data['is_fast_buy']=1;
            }

			$res	= self::$cartM->save($data);
            if (!$res > 0) {
                self::$cartM -> rollback();
                throw new Exception('插入失败',500);
            }
            self::$cartM -> commit();

        }else{
			$num = $num + $cartSkuDetail['num'];
			if ($skuRes['stock'] < $num){
				throw new Exception('数量不足',400);
			}
			$res	= self::$cartM->save(['num'=>$num],['cart_id'=>$cartSkuDetail['cart_id']]);
		}
		return $res;

	}

	/**
	 * 取出购物车内的货品
	 * @param $uid
	 * @param int $is_check
	 * @return array
	 * @throws Exception
	 */
    public function read($uid,$is_check=0,$is_fast_buy = 0){
        $res = $this ->getCartData($uid,$is_check,$is_fast_buy);
        //根据商品确认,将这些商品的图片信息查询出来并返回
        $pictures       = array_column($res,'goods_picture');
        $pictures       =array_unique($pictures);
        $pictureS       = new Picture();
        $picturesId     = implode(',',$pictures);
        $picturesData   = $pictureS -> transformAlbum($picturesId);
        $skuData = $this -> price($res);
        //待优化
        foreach($res as $key => $cart_data){
            foreach ($picturesData as $key2 => $picture){
                if ($cart_data['goods_picture'] == $picture['pic_id']){
                    $res[$key]['goods_picture'] = $picture;
                }
            }
            foreach($skuData as $sku_key => $sku_value){
                if (isset($sku_value['sku_id']) && $cart_data['sku_id']==$sku_value['sku_id']){
                    $res[$key]['real_price'] = $sku_value['price'] ;
                }
            }
        }
        return $res;
    }
	public function readOne($sku_id,$uid,$is_fast_buy)
	{
		$res	= self::$cartM
			->where(['sku_id'=>$sku_id,'buyer_id'=>$uid,'is_fast_buy'=>$is_fast_buy])
			->find();
		return $res;
	}

    /**
     * @param $param
     * @return mixed
     * @throws Exception
     * 修改选择状态
     */
	public function chooseStatus($param)
    {
        $count = self::$cartM -> where(['cart_id' => $param['cart_id'] , 'is_check' => $param['is_check']]) -> count();
        if ($count > 0){
            $res = 1 ;
        }else{
            $res    = self::$cartM
                ->where('cart_id',$param['cart_id'])
                ->update(['is_check'=> $param['is_check'] ]);
        }

        if($res > 0){
            return($res);
        }else{
            throw new Exception('选中失败,请重新选择','500');
        }
    }
    /**
     * @param $param
     * @return mixed
     * @throws Exception
     * 修改选择的优惠规则ID
     */
    public function choosePromotionInfo($param)
    {
        $cartM = new dataCart();
        $goodsInfo = $cartM -> where(['cart_id' => $param['cart_id']]) ->find();
        ####################################----判断在满减优惠信息里有没有相应的商品ID----###################################
        $promotionM = new dataPromotionMansongRule();
        $promotionInfo =  $promotionM
            ->alias('msr')
            ->join('ns_promotion_mansong ms', 'msr.mansong_id=ms.mansong_id', $type = 'LEFT')
            ->where('msr.rule_id = ' . $param['promotion_rule_id'])
            ->find();
        if (empty($promotionInfo)){
            $res =0;
        }
        if ($promotionInfo ->range_type == 1){
            $res    = $cartM
                ->where('cart_id',$param['cart_id'])
                ->update(['promotion_rule_id'=>$param['promotion_rule_id']]);
        }else{
            $mansongGoodsM = new dataPromotionMansongGoods();
            $mansongGoodsInfo =  $mansongGoodsM -> where(['mansong_id' => $promotionInfo['mansong_id']]) -> find();
            if (empty($mansongGoodsInfo)){
                throw new Exception('数据库异常',500);
            }

            if ($mansongGoodsInfo ->goods_id == $goodsInfo->goods_id){
                $res    = $cartM
                    ->where('cart_id',$param['cart_id'])
                    ->update(['promotion_rule_id'=>$param['promotion_rule_id']]);
            }
        }

        if($res > 0){
            return($res);
        }else{
            throw new Exception('选中失败,重复选择','500');
        }
    }

    /**
     * @param $uid 用户ID
     * @param $is_check_out int $is_check 是否已经选中
     * @param int $is_fast_buy 是否是立即购买
     * @return array 返回二维数组
     * @throws Exception 当购物车没有商品是抛出提示信息
     */
    public function getCartData($uid,$is_check_out = 0,$is_fast_buy = 0){
        $field      = 'cart_id,buyer_id,shop_id,shop_name,goods_id,goods_name,sku_id,sku_name,price,num,goods_picture,promotion_rule_id,is_check';
        //判断是否是快速购买且是否选中了该商品
        if ($is_fast_buy == 1 || $is_check_out == 0){
            if ($is_fast_buy == 1){
                //快速购买的订单确认页面
                $res	= self::$cartM->where(['buyer_id'=>$uid,'is_fast_buy'=>1])->field($field)->select();
            }else{
                //购物车列表
                $res	= self::$cartM->where(['buyer_id'=>$uid,'is_fast_buy'=>0])->field($field)->select();
            }
            $res    = collection($res)->toArray();
        }else{
            //普通订单确认页面
            $res    = self::$cartM->where(['buyer_id'=>$uid,'is_fast_buy'=>0,'is_check' => 1])
                ->field($field)
                ->select();
            $res    = collection($res)->toArray();
        }

        if (empty($res))  throw new Exception('您还没有添加商品到购物车,赶紧去添加吧','200');
        return $res;
    }

    /**
     * @param $id 购物车ID;
     * $param $num 客户端传来的购买数量值
     *
     */
    public function update($num,$id){

        if ($num < 1)           throw new Exception('购买数量必须大于等于1','500');

        $res = self::$cartM->where(['cart_id'=>$id]) ->update(['num'=> $num]);
        if ($res > 0){
            return $res;
        }else{
            throw new Exception('请重新输入购买数量','500');
        }
    }

    /**
     * @param $id  购物车ID;
     */
    public function delete($id){
        $res    = dataCart::destroy($id);
        if ($res > 0) {
            return $res;
        }else{
            throw new Exception('删除失败',500);
        }
    }
    public function price($res){
        $skuId  = array_column($res,'sku_id');
        $skuId  = implode(',',$skuId);
        $skuS   = new Sku();
        $skuData    = $skuS   -> readSku($skuId);
        return $skuData;
    }



}