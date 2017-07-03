<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;


class quicklinks extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {

        $links = Db::table('quicklinks')->order('id')->paginate(3,false);
        $page = $links->render();
        // 把分页数据赋值给模板变量links
        $this->assign('links', $links);
        $this->assign('page', $page);
        return $this->fetch('admin/links');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        Db::table('quicklinks')->where('id',$id)->delete();
    }
    
    //更改链接 数据
    public function editlinks(Request $request)
    {
        $info = $request->get();
        Db::table('quicklinks')->where('id', $info['id'])->update(
            ['linkname'=>$info['linkname'], 'linkurl'=>$info['linkurl'], 'contact'=>$info['contact'], 'status'=>$info['status']]
        );
        return redirect('admin/quicklinks/index');
    }
    
    //添加链接到数据库
    public function addlinks($linkname, $linkurl)
    {
        if ($linkname == null && $linkurl == null)
        {
            return "不能为空";
        }
        Db::table('quicklinks')->insert(['linkname'=>$linkname, 'linkurl'=>$linkurl]);
    }

}
