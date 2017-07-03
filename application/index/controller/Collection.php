<?php

namespace app\index\Controller;

use think\Controller;
use think\Db;
use think\Request;

class Collection extends Controller
{
    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $user = session('user_id', '','index');
        if ($user == null){
            return view('index/login');
        }
        $film_id = $request->param()['id'];
        Db::table('collection')->insert(['film_id'=>$film_id, 'user_id'=>$user]);
    }
    

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delcollection(Request $request)
    {
        //获取收藏电影的id
        $film_id = $request->param()['id'];
        Db::table('collection')->where('id',$film_id)->delete();
        return redirect('index/index/plans');
    }
}
