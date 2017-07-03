<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;

class Film extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $data = Db::table('address')->select();
        $this->assign('data', $data);
        return $this->fetch('index/film');
    }

}
