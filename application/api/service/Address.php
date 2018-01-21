<?php

namespace app\api\service;

use data\model\NsMemberExpressAddressModel as dataAddress;
use data\model\ProvinceModel as dataProvince;
use data\model\CityModel as dataCity;
use data\model\DistrictModel as dataDistrict;
use think\Exception;

class Address extends BaseService
{
    /**
     * @param $param  前台用户传来的地址信息
     */
    public function addressSave($uid,$param)
    {

        $addressM = new dataAddress();
        if ($param['is_default']==1) {

            $addressData = $addressM -> where(['uid' => $uid]) -> select();

            if (!empty($addressData)){
                foreach ($addressData as $address_key => $address_value) {
                    if ($address_value['is_default'] == 1) {
                        $addressM->where(['id' => $address_value['id']])->update(['is_default' => 0]);
                    }
                }
            }
        }
        $province = $this->getProvince($param['province']);
        $city = $this->getCity($param['city']);
        $district = $this->getDistrict($param['district']);
        $address = [
            'uid' => $uid,
            'is_default' => $param['is_default'],
            'consigner' => $param['consigner'],
            'address' => $param['address'],
            'alias' => $param['alias'],
            'mobile' => $param['mobile'],
            'phone' => $param['phone'],
            'city' => $city['city_id'],
            'province' => $province['province_id'],
            'district' => $district['district_id'],
            'zip_code' => $city['zipcode'],
        ];

        dump($uid);die;
        $res = $addressM->save($address);
        return $res;
    }

    public function readDefault()
    {
        $uid = $_SERVER['uid'];
        $addressM = new dataAddress();
        $addressDefault = $addressM->where(['uid' => $uid, 'is_default' => '1'])->find();
        if (empty($addressDefault)) return [];
        $addressDefault = $addressDefault->toArray();

        $provinceData = $this->getProvince($addressDefault['province']);
        $cityData = $this->getCity($addressDefault['city']);
        $districtData = $this->getDistrict($addressDefault['district']);
        $addressDefault['province'] = $provinceData ['province_name'];
        $addressDefault['city'] = $cityData['city_name'];
        $addressDefault['district'] = $districtData['district_name'];
        return $addressDefault;
    }

    /**
     * @param $id  收货地址的ID
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function readOne($id)
    {

        $addressM = new dataAddress();
        $addressData = $addressM->where(['id' => $id])->find();
        if (empty($addressData)){
            throw new Exception('没有找到相应地址信息',404);
        }
        $cityData = $this->getCity($addressData['city']);
        $provinceData = $this->getProvince($addressData['province']);
        $districtData = $this->getDistrict($addressData['district']);
        $addressData['province_name'] = $provinceData['province_name'];
        $addressData['city_name'] = $cityData['city_name'];
        $addressData['district_name'] = $districtData['district_name'];
        $address = $addressData;
        $address = $addressData -> toArray();
        return $address;
    }

    public function readAddress($uid)
    {

        $addressM = new dataAddress();
        $addressData = $addressM->where(['uid' => $uid])->select();
        if (empty($addressData)) {
            throw new Exception('您还没有添加地址', '1103');
        }
        $addressData = collection($addressData)->toArray();
        $addressProvince = array_column($addressData, 'province');
        $addressCity = array_column($addressData, 'city');
        $addressDistrict = array_column($addressData, 'district');
        $cityData = $this->getCity($addressCity);
        $provinceData = $this->getProvince($addressProvince);
        $districtData = $this->getDistrict($addressDistrict);
        foreach ($addressData as $address_key => $address_detail) {
            foreach ($provinceData as $province_key => $province_detail) {
                if ($province_detail['province_id'] == $address_detail['province']) {
                    $addressData[$address_key]['province'] = $province_detail['province_name'];
                }
            }
            foreach ($cityData as $city_key => $city_detail) {
                if ($address_detail['city'] == $city_detail['city_id']) {
                    $addressData[$address_key]['city'] = $city_detail['city_name'];
                    $addressData[$address_key]['zip_code'] = $city_detail['zipcode'];
                }
            }
            foreach ($districtData as $district_key => $district_detail) {
                if ($address_detail['district'] == $district_detail['district_id']) {
                    $addressData[$address_key]['district'] = $district_detail['district_name'];
                }
            }
        }
        return $addressData;

    }

    /**
     * @param max $province string int array
     * @return  取出相应省份的信息
     */
    function getProvince($province)
    {
        $provinceM = new dataProvince();
        if (is_numeric($province)) {
            $provinceData = $provinceM->where(['province_id' => $province])->find();
            if (empty($provinceData)) {
                throw new Exception('没有该省份', '404');
            }
            $provinceData = $provinceData->toArray();
        } elseif (gettype($province) == 'string') {
            $provinceData = $provinceM->where(['province_name' => $province])->find();
            if (empty($provinceData)) {
                throw new Exception('没有该省份', '404');
            }
            $provinceData = $provinceData->toArray();
        } elseif (gettype($province) == 'array') {

            $provinceData = $provinceM->where('province_id', 'IN', $province)->select();
            if (empty($provinceData)) {
                throw new Exception('没有该省份', '404');
            }
            $provinceData = collection($provinceData)->toArray();

        }

        return $provinceData;
    }


    /**
     * @param $city $province
     * 获取市名
     */
    function getCity($city)
    {
        $cityM = new dataCity();
        if (is_numeric($city)) {
            $cityData = $cityM->where(['city_id' => $city,])->find();
            if (empty($cityData)) {
                throw new Exception('没有该市', '404');
            }
            $cityData = $cityData->toArray();
        } elseif (gettype($city) == 'string') {
            $cityData = $cityM->where(['city_name' => $city])->find();
            if (empty($cityData)) {
                throw new Exception('没有该市', '404');
            }
            $cityData = $cityData->toArray();
        } elseif (gettype($city) == 'array') {

            $cityData = $cityM->where('city_id', 'IN', $city)->select();
            if (empty($cityData)) {
                throw new Exception('没有该市', '404');
            }
            $cityData = collection($cityData)->toArray();

        }
        return $cityData;
    }

    /**
     * @param $district_id
     * 获取行政区名
     */
    function getDistrict($district)
    {
        $districtM = new dataDistrict();
        if (is_numeric($district)) {
            $districtData = $districtM->where(['district_id' => $district])->find();
            if (empty($districtData)) {
                throw new Exception('没有该区县', '404');
            }
            $districtData = $districtData->toArray();
        } elseif (gettype($district) == 'string') {
            $districtData = $districtM->where(['district_name' => $district])->find();
            if (empty($districtData)) {
                throw new Exception('没有该区县', '404');
            }
            $districtData = $districtData->toArray();
        } elseif (gettype($district) == 'array') {

            $districtData = $districtM->where('district_id', 'IN', $district)->select();
            if (empty($districtData)) {
                throw new Exception('没有该区县', '404');
            }
            $districtData = collection($districtData)->toArray();

        }

        return $districtData;
    }

    /**
     * @param $id
     * @param $addressData 收货地址信息
     */
    function updateAddress($addressData)
    {
        $province = $this->getProvince($addressData['province']);
        $city = $this->getCity($addressData['city']);
        $district = $this->getDistrict($addressData['district']);
        $uid = $_SERVER['uid'];
        $addressNew = [
            'uid' => $uid,
            'is_default' => $addressData['is_default'],
            'consigner' => $addressData['consigner'],
            'address' => $addressData['address'],
            'alias' => $addressData['alias'],
            'mobile' => $addressData['mobile'],
            'phone' => $addressData['phone'],
            'city' => $city['city_id'],
            'province' => $province['province_id'],
            'district' => $district['district_id'],
            'zip_code' => $city['zipcode'],
        ];
        if ($addressData['is_default'] != 0) {
            $addressNew['is_default'] == 1;
        }
        $addressM = new dataAddress();
        $res = $addressM->where(['id' => $addressData['id']])->update($addressNew);
        if ($res <= 0) {
            throw new Exception('地址修改失败', 500);
        }
        return $res;
    }

    public function deleteAddress($id)
    {
        $res = dataAddress::destroy($id);
        if ($res > 0) {
            return $res;
        } else {
            throw new Exception('删除失败', 500);
        }
    }

    public function defaultSet($default)
    {
        $uid = $_SERVER['uid'];
        $addressM = new dataAddress();
        $is_default = $default['is_default'];
        $addressData = $this->readAddress($uid);
        foreach ($addressData as $address_key => $address_value) {
            if ($address_value['is_default'] == 1) {
                if ($addressData[$address_key]['id'] == $default['id']){
                    throw new Exception('这个已经是默认地址','203');
                }
                $addressM->where(['id' => $addressData[$address_key]['id']])->update(['is_default' => 0]);
            }
        }
        $res = $addressM->where(['id' => $default['id']])->update(['is_default' => $is_default]);
        if ($res != 1){
            throw new Exception('默认地址修改失败','500');
        }
    }

    /**
     *获取所有的省
     */
    public function getProvinceList(){

        $provinceM = new dataProvince();
        $provinceData = $provinceM::all();
        if (empty($provinceData)) {
            throw new Exception('主人我累了,让我歇会', '500');
        }
        $provinceData = collection($provinceData)->toArray();
        return $provinceData;
    }

    /**
     * @param $province_id  省的id
     */
    public function getCityList($province_id = null){

        $cityM = new dataCity();
        if ($province_id != null)        $cityM -> where('province_id',$province_id);


        $cityData = $cityM::all();
        if (empty($cityData)) {
            throw new Exception('主人我累了,让我歇会', '500');
        }
        $cityData = collection($cityData)->toArray();
        return $cityData;
    }
    /**
     * @param $city_id 市级的id
     */
    public function getDistrictList($district_id = null){

        $districtM = new dataDistrict();
        if($district_id != null)           $districtM -> where('city_id',$district_id);

        $districtData = $districtM::all();
        if (empty($districtData)) {
            throw new Exception('主人我累了,让我歇会', '500');
        }
        $districtData = collection($districtData)->toArray();
        return $districtData;
    }

    public function getAll(){

        $province   = $this -> getProvinceList();
        $city       = $this -> getCityList();
        $district   = $this -> getDistrictList();

//        dump($city);die;
        foreach($city as $city_key => $city_value){
            foreach($district as $dis_key => $dis_value){
                if ($city_value['city_id'] == $dis_value['city_id']){
                    //为了方便小程序渲染
                    $city[$city_key]['name'] = $city_value['city_name'];

                    $dis_value['name'] = $dis_value['district_name'];
                    $city[$city_key]['district'][] = $dis_value;
                }
            }
        }
        foreach($province as $pro_key => $pro_value){
            foreach($city as $city_key => $city_value){
                if ($pro_value['province_id'] == $city_value['province_id']){

                    $province[$pro_key]['name'] = $pro_value['province_name'];

                    $province[$pro_key]['city'][] = $city_value;
                }
            }
        }
        $all = $province;
        return $all;

    }
}