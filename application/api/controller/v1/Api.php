<?php
/**
 * 例子
 * Created by PhpStorm.
 * User: fei
 * Date: 2017/12/10
 * Time: 10:28
 */
namespace app\api\controller\v1;

use app\api\api\StandardInterface;
use app\api\controller\baseApi;

class Api extends baseApi implements StandardInterface
{
	// get
	public function index()
	{
		echo "index";
	}

	// get controller/create
	public function create()
	{
		echo "create";
	}

	// get controller/id
	public function read($id)
	{
		echo "read $id ";
	}

	// get controller/id/edit
	public function edit($id)
	{
		echo "edit $id";
	}

	// post
	public function save()
	{
		echo "save";
	}

	// post controller/update/id
	public function update($id)
	{
		echo "update $id";
	}

	// post controller/delete/id
	public function delete($id)
	{
		echo "delete $id";
	}
}