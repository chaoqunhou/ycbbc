<?php
namespace app\api\service;

use data\model\NsGoodsModel as GoodsModel;
use think\Request;

class Search extends BaseService {
	private $goodsM		= '';

	private $pageNum	= 10;
	private $sort_type	= ['time_asc','time_desc','sale_asc','sale_desc','price_asc','price_desc'];
	public function __construct(Request $request = null)
	{
		parent::__construct($request);
		$this->goodsM = new GoodsModel();
		$this->limit(0,$this->pageNum);
	}

	public function auth($data)
	{
		foreach ($data as $type => $v){
			if (is_null($v)){
				continue;
			}
			switch ($type){
				case 'shop_id':
					$this->shop_id($v);
					break;
				case 'sort_type':
					$this->sort($v);
					break;
				case 'page':
					$this->page($v);
					break;
				case 'category_id':
					$this->category($v);
					break;
				case 'keyword':
					$this->keyword($v);
					break;
				case 'price':
//					echo 'price';
					break;
				//configuredFilters:[{"bodyValues":"1","bodyKey":"cod"}]
				//expressionKey:[{"value":"黑色","key":"颜色"}]
				//expandName:[{"value":"76036","key":"3751"},{"value":"173","key":"3753"}]
				default:
					break;
			}
		}
	}

	public function category($v)
	{

        $categoryS = new Category();
        $cat_id = $categoryS -> getChildTree($v);
        //判断是否是最低一级的分类
        if (empty($cat_id)){
            $cat_id = $v;
        }else{
            $cat_id = implode(array_column($cat_id,'category_id'));
        }

        if (is_in_str($v)){
            $this->where('category_id','IN',$cat_id);
        }else{
            $this->where('category_id',$cat_id);
        }
        return $this->goodsM;
	}

	public function shop_id($v)
	{
		$goodsM = $this->goodsM;
		$this->where('shop_id',$v);

		return $this->goodsM;
	}
	public function keyword($v)
	{
		$goodsM = $this->goodsM;
		$this->where('goods_name','LIKE','%'.$v.'%');

		return $this->goodsM;
	}

	public function select()
	{
		$goodsM = $this->goodsM;

		$res = $goodsM::all();

		return $res;
	}

	public function where($clumn,$condition,$value = false)
	{
		$goodsM = $this->goodsM;
		if ($value === false ){
			$goodsM::where($clumn,$condition);
		}else{
			$goodsM::where($clumn,$condition,$value);
		}
		return $this->goodsM;
	}

	public function page($page)
	{
		$page = (int)$page;
		$this->limit(($page-1)*$this->pageNum,$this->pageNum);
	}
	private function limit($offset, $length = null)
	{
		$goodsM = $this->goodsM;
		$goodsM->limit($offset,$length);
		return $this->goodsM;
	}

	// ['time_asc','time_desc','sale_asc','sale_desc','price_asc','price_desc'];
	public function sort($sort)
	{
		if (in_array($sort,$this->sort_type)){
			$goodsM = $this->goodsM;
			switch ($sort){
				case 'time_asc':
					$goodsM->order('goods_id');
					break;
				case 'time_desc':
					$goodsM->order('goods_id DESC');
					break;
				case 'sale_asc':
					$goodsM->order('sales');
					break;
				case 'sale_desc':
					$goodsM->order('sales DESC');
					break;
				case 'price_asc':
					$goodsM->order('price');
					break;
				case 'price_desc':
					$goodsM->order('price DESC');
					break;
				default:
					//sort desc
					break;
			}
		}
	}
}