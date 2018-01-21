<?php
namespace app\api\service;

use think\Controller;

class BaseService extends Controller
{
	//token 有效时间
	protected $expires_in = 60*60*24*7;
	//refresh_token 有效时间
	protected $refresh_expires_in = 60*60*24*60;
}