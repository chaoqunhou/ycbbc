<?php
namespace app\api\service;

use data\model\NsGoodsSkuModel as dataGoodsSku;
use think\Exception;
use data\model\NsGoodsSpecValueModel as dataGoodsSpecValue;
use data\model\NsGoodsSpecModel as dataGoodsSpec;
use data\model\NsGoodsSkuPictureModel as dataGoodsSkuPicture;
use app\api\service\Picture as Picture;


class Sku extends BaseService
{
    //根据sku_id 获取sku信息 [] ['']
    //根据sku_id查询goods_sku表，查出goods_id，再查goods表，取出goods_name，shop_id，picture，根据shop_id查询shop表，查出shop_name，shop_id
    public function readSku($skuId)
    {
        $IN = is_in_str($skuId);
        $goodsSkuM	= new dataGoodsSku();
        if (!$IN){
            $goodsSkuData	= $goodsSkuM -> where(['sku_id' => $skuId]) -> select();
            $goodsSkuData	= collection($goodsSkuData)->toArray();
        }else{
            $goodsSkuData   = $goodsSkuM -> where('sku_id','in',$skuId )->select();
            $goodsSkuData   = collection($goodsSkuData)->toArray();
        }
        if (empty($goodsSkuData)){
            throw new Exception('sku id 找不到对应',400);
        }
        return $goodsSkuData;
    }

    //根据sku_id 获取sku单条信息 [] ['']
    //根据sku_id查询goods_sku表，查出goods_id，再查goods表，取出goods_name，shop_id，picture，根据shop_id查询shop表，查出shop_name，shop_id
    public function readOneSku($skuId)
    {
        $goodsSkuM	= new dataGoodsSku();
        $goodsSkuData   = $goodsSkuM -> where('sku_id',$skuId )->find()->toArray();
//        $goodsSkuData   = collection($goodsSkuData)->toArray();
        if (empty($goodsSkuData)){
            throw new Exception('sku id 找不到对应',400);
        }
        return $goodsSkuData;
    }
    //根据goods_id 获取货品一系列信息
    public function read($id)
    {
        $data = [];
        $field  = 'sku_id,goods_id,sku_name,attr_value_items,attr_value_items_format,market_price,price,promote_price,cost_price,
		stock,picture,code,QRcode,create_date,update_date';

        $skuM	= new dataGoodsSku;
        $skuObj = $skuM
            ->where(['goods_id'=>$id])
            ->field($field)
            ->select();

        if (empty($skuObj)){
            throw new Exception('sku  miss','500');
        }
        $skuArr = collection($skuObj)->toArray();

        $tempItem = [];
        if (count($skuArr) > 1){
            foreach ($skuArr as $k => $v){
                $tempItem[$v['attr_value_items_format']] = $v;
            }
            $skuArr = $tempItem;
        }
        //如果仅有1个sku可以说是没有属性的情况
        if (count($skuArr) == 1){
            foreach ($skuArr as $k => $v){
                $tempItem['noattr'] = $v;
            }
            $skuArr = $tempItem;
        }
        $data['item'] = $skuArr;

        //是否有属性
        if (!(count($skuArr)==1 && empty($skuArr[0]['attr_value_items']))){
            $spec_v_arr = [];
            $spec_arr = [];
            foreach ($skuArr as $sku){
                if (strpos($sku['attr_value_items'],';') > 0 ){
                    $tempArr = explode(';',$sku['attr_value_items']);
                    foreach ($tempArr as $item){
                        if(! strpos($sku['attr_value_items'],':') > 0){
                            throw new Exception('数据异常'.__CLASS__,'500');
                        }
                        $itemArr = explode(':',$item);
                        array_push($spec_v_arr,$itemArr[1]);
                        array_push($spec_arr,$itemArr[0]);
                    }
                }elseif (strpos($sku['attr_value_items'],':') > 0){
                    $itemArr = explode(':',$sku['attr_value_items']);
                    array_push($spec_v_arr,$itemArr[1]);
                    array_push($spec_arr,$itemArr[0]);
                }

            }
            $spec_v_str = implode(array_unique($spec_v_arr),',');
            $spec_str 	= implode(array_unique($spec_arr),',');
            if (empty($spec_v_str) || empty($spec_str)){
                throw new Exception('数据异常'.__CLASS__,'500');
            }
            //根据$spec_v_arr	查询 goods_spec_value
            $spec_v	= $this->getGoodsSpecValue($spec_v_str);
            //根据$spec_arr		查询 goods_spec
            $spec		= $this->getGoodsSpec($spec_str);
            //组织一下属性的数据
            if(!empty($spec_v) && !empty($spec)){
                foreach ($spec as $key => $spec_item){
                    foreach ($spec_v as $spec_v_item){
                        if ($spec_item['spec_id'] == $spec_v_item['spec_id'] ){
                            $spec[$key]['v_item'][] = $spec_v_item;
                        }
                    }
                }
                $data['spec'] = $spec;
            }
            //根据$goods_id获取goods_sku_picture
            $data['skuPicture'] = $this->getGoodsSkuPicture($id);
        }
        return $data;

    }

    //根据$spec_v_arr	查询 goods_spec_value
    private function getGoodsSpecValue($ids)
    {
        $dataGoodsSpecValueM	= new dataGoodsSpecValue;
        $dataGoodsSpecValue		= $dataGoodsSpecValueM::all($ids);
        $arr = collection($dataGoodsSpecValue)->toArray();
        return $arr;
    }
    //根据$spec_arr		查询 goods_spec
    private function getGoodsSpec($ids)
    {
        $dataGoodsSpecM = new dataGoodsSpec;
        $dataGoodsSpec	= $dataGoodsSpecM::all($ids);
        $arr = collection($dataGoodsSpec)->toArray();
        return $arr;
    }
    //根据goods_id		查询 GoodsSkuPicture
    private function getGoodsSkuPicture($id)
    {
        $data = [];
        $dataGoodsSpecM = new dataGoodsSkuPicture;
        $dataGoodsSpec	= $dataGoodsSpecM::all(['goods_id'=>$id]);
        $arr = collection($dataGoodsSpec)->toArray();
        if (!empty($arr)){
            $data['item'] = $arr;
            $idstr = '';
            foreach ($arr as $skuPic){
                if (!empty($skuPic['sku_img_array'])){
                    $idstr = str_append($idstr,$skuPic['sku_img_array']);
                }
            }
            if (!empty($idstr)){
                $picture = new Picture;
                $pictures = $picture->transformAlbum($idstr);
                $data['img_src'] = $pictures;
            }
        }
        return $data;
    }
}