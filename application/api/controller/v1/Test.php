<?php
namespace app\api\controller\v1;
use app\api\api\RESTInterface;
use app\api\controller\baseApi;
use app\api\service\Promotion;

class Test extends baseApi implements RESTInterface{
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
        $uid = $_SERVER['uid'];
        $promotionS = new Promotion();
      $promotion =  $promotionS -> orderRealPrice($uid);
//        dump($promotion);
        returnJson($promotion);
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