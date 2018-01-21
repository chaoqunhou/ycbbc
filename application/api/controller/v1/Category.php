<?php

namespace app\api\controller\v1;

use app\api\api\StandardInterface;
use app\api\controller\baseApi;
use app\api\service\Search;
use think\Exception;
use app\api\service\Picture as PictureService;


class Category extends baseApi implements StandardInterface
{

    public function getCategoryGoodsAction()
    {
        $res = [];
        try {
            $SearchS = new Search;
            $SearchS->auth(input('param.'));
            $res = $SearchS->select();
            //处理图片
            $picSer = new PictureService;
            $res = $picSer->transPic($res);
        } catch (Exception $e) {
            returnJson($e->getMessage(), $e->getCode());
        }
        returnJson($res);
    }

}