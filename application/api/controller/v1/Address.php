<?php
namespace app\api\controller\v1;

use app\api\api\StandardInterface;
use app\api\controller\baseApi;
use app\api\service\Address as AddressService;
use think\Exception;

class Address extends baseApi implements StandardInterface{

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

    }

    /**
     * 获取所有的省
     */
    public function getProvinceAction(){
        $addressS   = new AddressService();
        try {
            $res        = $addressS -> getProvinceList();
        }catch(Exception $e){
            returnJson($e -> getMessage(),$e -> getCode());
        }
        returnJson($res);
    }

    /**
     * 获取某一省下的所有市
     */
    public function getCityAction(){
        $addressS   = new AddressService();
        try {
            $province_id = input('param.province');
            $res        = $addressS -> getCityList($province_id);
        }catch(Exception $e){
            returnJson($e -> getMessage(),$e -> getCode());
        }
        returnJson($res);
    }
    /**
     * 获取某一市下的所有县区
     */
    public function getDistrictAction(){
        $addressS   = new AddressService();
        try {
            $city_id = input('param.city');
            $res        = $addressS -> getDistrictList($city_id);
        }catch(Exception $e){
            returnJson($e -> getMessage(),$e -> getCode());
        }
        returnJson($res);
    }
    public function readAction()
    {
        $addressS   = new AddressService();
        try {
            $uid = $_SERVER['uid'];
            $res        = $addressS -> readAddress($uid);
        }catch(Exception $e){
            returnJson($e -> getMessage(),$e -> getCode());
        }
        returnJson($res);
    }
    /**
     * @param $id  收货地址ID
     */
    public function editAction($id)
    {
        try{

            $addressS = new AddressService();
            $res     = $addressS -> readOne($id);
        }catch(Exception $e){
            returnJson($e -> getMessage(),$e -> getCode());
        }
        returnJson($res);
    }

    public function save()
    {
        try{
            $uid = $_SERVER['uid'];
            if (!$uid){
                throw new Exception('请重新登录','1100');
            }
            $addressS = new AddressService();
            $param    = input('param.');
            $res = $addressS -> addressSave($uid,$param);
        }catch(Exception $e){
            returnJson($e -> getMessage(),$e -> getCode());
        }
        returnJson($res);
    }
    public function update($id)
    {

    }
    /**
     * @param $id  收货地址ID
     * 收货地址修改
     */
    public function updateAction()
    {
        try{
            $this->vData(input('param.'),'address.updateAction');
            $addressS = new AddressService();
            $addressData = input('param.');
            $res      = $addressS -> updateAddress($addressData);
        }catch(Exception $e){
            returnJson($e -> getMessage(),$e -> getCode());
        }
        returnJson($res);
    }

    public function delete($id)
    {
        try{
            $addressS = new AddressService();
            $res      = $addressS -> deleteAddress($id);
        }catch(Exception $e){
            returnJson($e -> getMessage(),$e -> getCode());
        }
        returnJson($res);
    }

    public function defaultAction($id){
        try{
            $this->vData(input('param.'),'address.defaultAction');
            $addressS = new AddressService();
            $default= input('param.');
            $res      = $addressS -> defaultSet($default);
        }catch(Exception $e){
            returnJson($e -> getMessage(),$e -> getCode());
        }
        returnJson($res);
    }

    /**
     * 获取全国省市县树状数据结构
     */
    public function getAllAction(){
        try{
            $addressS = new AddressService();
            $res      = $addressS -> getAll();

        }catch(Exception $e){
            returnJson($e ->getMessage(), $e -> getCode());
        }
        returnJson($res);
    }
}