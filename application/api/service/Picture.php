<?php
// app\api\service\Picture
namespace app\api\service;

use data\model\AlbumPictureModel;
use think\Request;

class Picture extends BaseService
{
	private $albumObj = null;
	public function __construct(Request $request = null)
	{
		parent::__construct($request);
		if (is_null($this->albumObj)){
			$this->albumObj = new AlbumPictureModel();
		}
	}

	public function transformAlbum($ids)
	{
		$albumObj = new AlbumPictureModel();
		$field = '*';
		$res = collection($albumObj::all($ids))->toArray();
		return ($res);
	}

	public function transPic($res,$name='picture')
	{
		//处理图片
		if (!empty($res) && isset(current($res)[$name])){
			$pics	= array_column($res,$name);
			$pics 	= $this->transformAlbum($pics);
			foreach ($res as $k => $row){
				foreach ($pics as $pk => $pic){
					if ($row[$name] == $pic['pic_id']){
						$res[$k]['pic_res'] = $pic;
					}
				}
			}
		}
		return $res;
	}

}