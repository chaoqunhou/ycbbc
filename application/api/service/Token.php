<?php
namespace app\api\service;

class Token extends BaseService
{
	// generate uniquely  token
	public static function genToken($num = 1,$pc = '1')
	{
		if ($num > 1){
			for ($i = 0  ; $i < $num ; $i ++){
				$res[] = self::uniqueToken($pc);
			}
		}else{
			$res = self::uniqueToken($pc);
		}
		return $res;
	}

	private static function uniqueToken($pc = '1')
	{
		$token = uniqid($pc,true);
		$token = md5($token);
		$token = $token.rand(100,999);
		return $token;
	}

}