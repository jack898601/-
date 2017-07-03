<?php

namespace app\admin\controller;

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
    public function user()
    {
        $username = empty($_GET['username'])?'':$_GET['username'];
        $list = Db::table('user')->order('id')->where('username', 'like', '%'.$username.'%')->paginate(5);
        $page = $list->render();
        
        $this->assign('page', $page);
        $this->assign('list', $list);
        return $this->fetch('admin/index');
    }
  
    //电影列表查询
    public function filmsearch()
    {
        $filmname = empty($_GET['filmname'])?'':$_GET['filmname'];
        $list = Db::table('film')->order('id')->where('filmname', 'like', '%'.$filmname.'%')->paginate(4, false);
        $page = $list->render();
        $this->assign('page', $page);
        $this->assign('list', $list);
        return $this->fetch('admin/film');
    }
    
    //链接查询
    public function linkssearch()
    {
        $name = empty($_GET['linkname'])?'':$_GET['linkname'];
        $links = Db::table('quicklinks')->order('id')->where('linkname', 'like', '%'.$name.'%')->paginate(3,false);
        $page = $links->render();
        // 把分页数据赋值给模板变量links
        $this->assign('links', $links);
        $this->assign('page', $page);
        return $this->fetch('admin/links');
    }
}
