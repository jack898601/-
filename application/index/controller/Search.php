<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Db;

class Search extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
//        var_dump($_GET);die;
        $filmname = empty($_GET['contents'])?'':$_GET['contents'];
        $list = Db::table(['film','face'])->field('film.id,film.filmname,film.duration,film.actor,film.type,film.release_time,film.regions,film.language,face.imgurl')->where('filmname', 'like', '%'.$filmname.'%')->where('face.id_film = film.id')->select();
        // var_dump($list);die;
        $this->assign('data', $list);
        return $this->fetch('index/cinema');
    }
}
