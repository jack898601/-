<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;

class orders extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        // 查询所有用户数据 并且每页显示10条数据
        $order = Db::table(['orders,address,film,schedule,user'])->field('orders.id,schedule.playtime,schedule.date,schedule.hall_num,orders.ticket_number,user.username,user.phone,orders.seatnum,orders.status,film.filmname,address.name,address.address,schedule.price')->where('user_id=user.id and schedule_id = schedule.id and address_id = address.id and film_id = film.id')->order('id')->paginate(10,false);
        $page = $order->render();
        // 把分页数据赋值给模板变量order
        $this->assign('order', $order);
        $this->assign('page', $page);
        return $this->fetch('admin/orders');
    }


    /**
     * 删除订单
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $result = Db::table('orders')->where('id', $id)->delete();
        if ($result > 0 ) {
            $info['status'] = true;
            $info['id'] = $id;
            $info['info'] = 'ID为: ' . $id . ' 的用户已删除';
        } else {
            $info['status'] = false;
            $info['id'] = $id;
            $info['info'] = 'ID为: ' . $id . ' 的用户删除失败,请重试!';
        }
        return json($info);
    }
    
   
}
