<?php
/**
 * Created by PhpStorm.
 * User: fei
 * Date: 2017/12/10
 * Time: 10:28
 */

namespace app\api\controller\v1;

use app\api\api\StandardInterface;
use app\api\controller\baseApi;
use app\api\service\Promotion as PromotionService;
use app\api\service\Goods as GoodsService;
use data\service\Goods as DataGoodsService;
use data\model\NsMemberFavoritesModel;
use think\Exception;

class Goods extends baseApi implements StandardInterface
{
    protected $instance_id;

    // get
    public function index()
    {
        echo "index";
    }

    // get controller/create
    public function create()
    {
        // aaa
        echo "create";
    }

    // post
    public function save()
    {
        echo "save";
    }

    // get controller/id
    public function read($id)
    {

        $goods_id = $id ;
        try {
            $GoodsService = new GoodsService;
            $res = $GoodsService -> readItem($goods_id);
        } catch (Exception $e) {
            returnJson($e -> getMessage(), $e -> getCode());
        }
        returnJson($res);
    }

    // get controller/id/edit
    public function edit($id)
    {
        echo "edit $id";
    }

    // put controller/id
    public function update($id)
    {
        echo "update $id";
    }

    // delete controller/id
    public function delete($id)
    {
        echo "delete $id";
    }

    /**
     * 得到当前时间戳的毫秒数
     *
     * @return number
     */
    private function getCurrentTime()
    {
        $time = time();
        $time = $time * 1000;
        return $time;
    }

    /**
     * 返回商品数量和当前商品的限购
     *
     * @param unknown $goods_id
     */
    private function getCartInfo($goods_id)
    {

    }

    public function aaAction()
    {
        echo 1;
    }

    /**
     * 收藏商品/店铺
     */

    public function collectsAction(){
        try{
            if(!$uid    = $_SERVER['uid'])  throw new Exception('未登录',1100);
            $goodsInfo  = input('param.');
//            dump($goodsInfo);die;
            $goodsS     = new GoodsService();
            $res = $goodsS     -> collecte($goodsInfo);

        }catch(Exception $e){
            returnJson($e -> getMessage(),$e -> getCode());
        }
        returnJson($res);
    }

    /**
     * 收藏列表
     */
    public function collectsListAction()
    {
        try {
            if (empty($_SERVER['uid'])) throw new Exception('请先登录', '1100');
            $uid = $_SERVER['uid'];
            $goodsS = new GoodsService();
            $res = $goodsS->getCollectionList($uid);
        } catch (Exception $e) {
            returnJson($e->getMessage(), $e->getCode());
        }
        returnJson($res);
    }

}