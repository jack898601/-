<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;

class Movie extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index($filmid, $cid=null)
    {
        // 查询该电影信息
        $filmlist = Db::table('film')->find($filmid);
        $filmimg = Db::table('face')->field('imgurl')->where('id_film', $filmid)->find();
        // 查询该电影的区
        $rst = Db::query('select a.cid, a.area from address a, schedule s where address_id=a.id and film_id='.$filmid);
        $areainfo = array_unique(array_column($rst, 'area','cid'));
        
        // 首次进movie页面 默认影院名
        $first = array_keys($areainfo)[0];

        // 查询该电影的排片地址
        if ($cid == null) {
            $info = Db::query('select a.name, a.id from address a, schedule s where address_id=a.id and film_id='.$filmid.' and a.cid='.$first);
        } else {
            $info = Db::query('select a.name, a.id from address a, schedule s where address_id=a.id and film_id='.$filmid.' and a.cid='.$cid);
        }
        $info = array_column($info, 'name','id');

        return $this->fetch('index/movie',[
            'areainfo' => $areainfo,
            'info'=>$info,
            'filmlist' => $filmlist,
            'filmimg' => $filmimg,
            ]);
    }


    public function datechoose($filmid, $addressid)
    {
        $rst = Db::query('select s.id, s.hall_num, f.filmname, a.address, a.name, s.date, s.playtime, s.price from schedule s, film f, address a where s.film_id='.$filmid.' and s.address_id='.$addressid.' and s.film_id = f.id and s.address_id = a.id');
        return $this->fetch('index/datechoose',['rst'=> $rst]);
    }


    public function seatchoose($scheduleid)
    {
        return view('index/seatchoose');
    }
}
