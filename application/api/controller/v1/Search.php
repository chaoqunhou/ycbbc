<?php
namespace app\api\controller\v1;

use app\api\controller\baseApi;
use app\api\service\Search as SearchS;
use app\api\service\Picture as PictureService;
use think\Exception;

class Search extends baseApi {

	public function searchAction()
	{
		$res = [];
		try{
			$searchS	= new SearchS;
			$res = $searchS->auth(input('param.'));
			$res = $searchS->select();
            $picSer = new PictureService;
            $res = $picSer->transPic($res);
		}catch (Exception $e){
			returnJson($e->getMessage(),$e->getCode());
		}
		returnJson($res);
	}
}