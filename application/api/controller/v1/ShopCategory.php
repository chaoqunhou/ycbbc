<?php
/**
 * 例子
 * Created by PhpStorm.
 * User: fei
 * Date: 2017/12/10
 * Time: 10:28
 */
namespace app\api\controller\v1;

use app\api\api\StandardInterface;
use app\api\controller\baseApi;
use app\api\service\Category as GoodsCategory;
use think\Exception;


class ShopCategory extends baseApi implements StandardInterface
{
	private $shop_id;

	// get	 #返回所有一级分类列表,再分类里面有默认显示一个二级分类产品,需要前台传一个一级分类i

    /**
     * @param 当点击分类时,传入一个标记为点击分类按钮进入分类的标志值,这里判断传入值是否为$param['is_index']==true (用来表示是否是刚点击分类进入分类页面)
     */
	public function index()
	{
//	    $GoodsCategoryS = new GoodsCategory;
//        $param = input('param.');
//        if ($param['is_index']) {
//            //点击分类浸入时,默认显示category_id=1的产品
//            $CategoryDefault = $GoodsCategoryS -> readChild(1);
//        }
//        $CategoryList = $GoodsCategoryS ->read(1);
//        $CategoryList['CategoryDefault'] = $CategoryDefault;
        $GoodsCategoryS = new GoodsCategory();
        try{
            $CategoryList = $GoodsCategoryS ->getChildTree();
        }catch (Exception $e){
            returnJson($e -> getMessage(),$e -> getCode());
        }
        returnJson($CategoryList);
		//echo "index";
	}

	// get controller/create
	public function create()
	{
		echo "create";
	}

	// get controller/id   根据id 查询id下的子级分类列表

    /**
     * @param $id 一级分类ID
     *
     */
	public function read($id)
	{
		$GoodsCategoryS = new GoodsCategory();
		try{
            $res = $GoodsCategoryS ->getChildTree($id);
        }catch (Exception $e){
		    returnJson($e -> getMessage(),$e -> getCode());
        }
        returnJson($res);



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



    public function getCategoryAction(){
        $GoodsCategoryS = new GoodsCategory();
        try{
            $CategoryList = $GoodsCategoryS ->getChildTree();
        }catch (Exception $e){
            returnJson($e -> getMessage(),$e -> getCode());
        }
        returnJson($CategoryList);
    }
}

