<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;

class Seatchoose extends Controller
{
    public function index($scheduleid)
    {
        $rst = Db::query('select s.id, f.filmname, a.name, s.price, s.hall_num, s.date, s.playtime from address a, schedule s, film f where s.id='.$scheduleid.' and s.film_id=f.id and s.address_id=a.id');

        // 已经被购买的座位,从orders表里查出的信息
        $arr = Db::query('select o.seatnum from orders o, schedule s where s.id='.$scheduleid.' and o.schedule_id = s.id');
        $choseseat = json_encode(array_column($arr, 'seatnum'));

        return view('index/seatchoose',['rst'=>$rst[0],'choseseat'=>$choseseat]);
    }

    public function orderbuild(Request $request)
    {
        $data = Request::instance()->post();
        
        // 统计买了几张票
        $count = count($data['seatnum']);

        for ($i=0; $i < $count; $i++) { 
            Db::name('orders')
            ->insert([
                'user_id' => $data['userid'],
                'schedule_id' => $data['scheduleid'],
                'seatnum' => $data['seatnum'][$i],
                'ticket_number' => $this->rand_char(),
            ]);
        }

        $info['info'] = '成功';
        return json($info);
    }

    private function rand_char()
    {
        $base = 62;
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return $chars[mt_rand(1, $base) - 1];
    }

}