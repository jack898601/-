<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;

class Cinema extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $isfilm = Db::table('schedule')->field('film_id')->select();
        // 处理数据格式,得到电影id们
        $ids = implode(",", array_unique(array_column($isfilm, 'film_id')));

        $data = Db::table('film m,face e')->field('m.filmname, m.id, m.duration, m.actor, m.type, m.release_time, m.regions, m.language, e.imgurl')->where('m.id in ('.$ids.') and e.id_film = m.id')->paginate(10);
        // var_dump($data);die;
        $page = $data->render();
        $this->assign('data', $data);
        $this->assign('page', $page);
        return $this->fetch('index/cinema');
    }

}
