<?php
namespace app\api\service;



use Aliyun\Core\DefaultAcsClient;
use data\model\NsSmsModel;
use \SendSmsRequest;

class SmsSend extends BaseService{
    protected $appkey = 'LTAI1WSzw1utrotg';
    protected $secret = 'Q0FqQk1Zyn59Np83U7FhjLt7gmCFM4';
    protected $signName = '新冻网';
    //注册验证
    public function smsCode($smsData){
        $appkey         = $this -> appkey;
        $secret         = $this -> secret;
        $signName       = $this -> signName;
        switch ($smsData['type_id']){
            case '1';
                $template_code  = 'SMS_112470558';

                break;
            case '2';
                $code  = mt_rand(1000,9999);
                $template_code  = 'SMS_112470558';
                break;
            case '3';
                $template_code  = 'SMS_112470558';
                break;
            case '4';
                $template_code  = 'SMS_112470558';
                break;
            case '5';
                $template_code  = 'SMS_112470558';
                break;
            case '6';
                $template_code  = 'SMS_112470558';
                break;
            case '7';
                $template_code  = 'SMS_112470558';
                break;
            case '8';
                $template_code  = 'SMS_112470558';
                $code  = mt_rand(1000,9999);
                break;
            case '9';
                $template_code  = 'SMS_112470558';
                $code  = mt_rand(1000,9999);
                break;
            case '10';
                $template_code  = 'SMS_112470558';
                break;
            case '11';
                $template_code  = 'SMS_112470558';
                break;
            case '12';
                $template_code  = 'SMS_112470558';
                break;
            case '13';
                $template_code  = 'SMS_112470558';
                break;
        }



        $smsParam = json_encode(Array(  // 短信模板中字段的值
            "code"=> $code,
        ));
        $time           = time();
        $res            = $this -> aliSmsSend($appkey,$secret,$signName,$smsParam,$smsData['mobile'],$template_code);
//        dump($smsData['mobile']);die;
        if($res -> Code == 'OK'){
            $smsM = new NsSmsModel();
            $find = $smsM -> where(['mobile' => $smsData['mobile']]) -> find();
            if (empty($find)){
                $smsM -> data([
                    'mobile'        => $smsData['mobile'],
                    'template_code' => $code,
                    'send_time'     => $time
                ]);
                $res = $smsM -> save();
            }else{
                $res =  $smsM -> save([
                    'mobile'        => $smsData['mobile'],
                    'template_code' => $code,
                    'send_time'     => $time
                ],['mobile' => $smsData['mobile']]);
            }

        }
        return $res;
    }

    public function aliSmsSend($appkey, $secret, $signName, $smsParam, $send_mobile, $template_code)
    {
        require_once 'data/extend/alisms_new/aliyun-php-sdk-core/Config.php';
        require_once 'data/extend/alisms_new/SendSmsRequest.php';
        // 短信API产品名
        $product = "Dysmsapi";
        // 短信API产品域名
        $domain = "dysmsapi.aliyuncs.com";
        // 暂时不支持多Region
        $region = "cn-hangzhou";
        $profile = \DefaultProfile::getProfile($region, $appkey, $secret);
        \DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);

        $acsClient = new \DefaultAcsClient($profile);
//        $acsClient = new DefaultAcsClient($profile);

        $request = new SendSmsRequest();

        // 必填-短信接收电话号码

        $request->setPhoneNumbers($send_mobile);

        // 必填-短信签名
        $request->setSignName($signName);

        // 必填-短信模板Code
        $request->setTemplateCode($template_code);

        // 选填-假如模板中存在变量需要替换则为必填(JSON格式)

        $request->setTemplateParam($smsParam);
        // 选填-发送短信流水号
        $request->setOutId("0");
        // 发起访问请求
        $acsResponse = $acsClient->getAcsResponse($request);
        return $acsResponse;
    }

}