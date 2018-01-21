<?php
namespace app\api\service;

use think\Controller;
use data\model\NsGoodsCategoryModel as dataGoodsCategory;
use data\model\NsGoodsModel as dataGoods;
use think\Exception;

class Category extends Controller
{
	/**
	 * @param string $level 一级分类
	 * @return array
	 * @throws \think\Exception\DbException
	 */
	public function read($level = '1')
	{
		//

		$categoryM = new dataGoodsCategory();

		$categoryM->where('level', $level);
		//
		$res = $categoryM::all();
		if (empty($res)){
		    throw new Exception('没有找到分类商品','404');
        }
        $arr = collection($res)->toArray();
		return $arr;

	}


	//获取所有子分类
    /**
     * @param int $category_id
     * @return array 对商品分类数据进行树状处理
     * @throws Exception
     */
	public function getChildTree($category_id = 0)
    {
		//获取所有分类
            //根据goods表内的category_id在goods_category中进行查询级别
        $categoryData = $this -> getGoodsCategory();
        $tree =[];
        foreach($categoryData as $category_key => $category_value){
            if ($category_value['pid']==$category_id){
                $tree[] = $category_value;
                $tree = array_merge($tree,$this -> getChildTree($category_value['category_id']));
            }
        }

        return $tree;
		//转变为树的形式
	}

    /**
     * @return array 取出所有的商品分类数据
     */
	public function getGoodsCategory()
    {
        $goodsCategoryM = new dataGoodsCategory();
        $categoryData   = $goodsCategoryM::select();
        if (empty($categoryData)){
            throw new Exception('未找到分类','404');
        }
        $categoryData = collection($categoryData) -> toArray();
        return $categoryData;
    }
}