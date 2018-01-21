<?php
namespace app\api\controller\v1;

use app\api\api\RESTInterface;
use app\api\controller\baseApi;

class Shipping extends baseApi implements RESTInterface{
    public function index()
    {
        // TODO: Implement index() method.
    }

    public function create()
    {
        // TODO: Implement create() method.
    }

    public function read($id)
    {
        // TODO: Implement read() method.
    }

    /**
     * @param $shop_id  商家的ID
     * 获取商家可用的物流信息
     */
    public function getShippingInfoAction($shop_id){

    }
    public function editAction($id)
    {
        // TODO: Implement editAction() method.
    }

    public function save()
    {
        // TODO: Implement save() method.
    }

    public function update($id)
    {
        // TODO: Implement update() method.
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

}
