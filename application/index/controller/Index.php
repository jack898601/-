<?php
namespace app\index\Controller;

use think\Request;
use think\Controller;
use think\Db;
use think\Session;



class Index extends Controller
{
    public function index()
    {
        // 首页天气API接口
         $cityName = '上海';
         $city = iconv('UTF-8', 'GBK', $cityName);
         $url = 'http://php.weather.sina.com.cn/xml.php?city='.$city.'&password=DJOYnieT8234jlsK&day=0';
         $curl = curl_init();
         curl_setopt($curl, CURLOPT_URL, $url);
         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
         $data = curl_exec($curl);
         curl_close($curl);
         // 处理XML数据
         $obj = simplexml_load_string($data)->Weather;
         $this->assign('obj', $obj);
    
        //新闻接口
        header("Content-Type:text/html;charset=UTF-8");
        date_default_timezone_set("PRC");
        $showapi_appid = '41521';  //替换此值,在官网的"我的应用"中找到相关值
        $showapi_secret = '4e344e3180934218b30d9b380d994e02';  //替换此值,在官网的"我的应用"中找到相关值
        $paramArr = array(
            'showapi_appid'=> $showapi_appid,
            'channelId'=> "",
            'channelName'=> "",
            'title'=> "电影",
            'page'=> "",
            'needContent'=> "",
            'needHtml'=> "",
            'needAllList'=> "",
            'maxResult'=> "5"
            //添加其他参数
        );
        //创建参数(包括签名的处理)
        function createParam ($paramArr,$showapi_secret) {
            $paraStr = "";
            $signStr = "";
            ksort($paramArr);
            foreach ($paramArr as $key => $val) {
                if ($key != '' && $val != '') {
                    $signStr .= $key.$val;
                    $paraStr .= $key.'='.urlencode($val).'&';
                }
            }
            $signStr .= $showapi_secret;//排好序的参数加上secret,进行md5
            $sign = strtolower(md5($signStr));
            $paraStr .= 'showapi_sign='.$sign;//将md5后的值作为参数,便于服务器的效验
            return $paraStr;
        }
    
        $param = createParam($paramArr,$showapi_secret);
        $url = 'http://route.showapi.com/109-35?'.$param;
        $result = file_get_contents($url);
        $result = json_decode($result);
        $result = $result->showapi_res_body->pagebean->contentlist;
        $this->assign('news', $result);

        //轮廓图获取数据库的封面是1的图片
        $img = Db::table('banner')->where('face', 'EQ', '1')->select();
        $this->assign('imgs', $img);
        //链接
        $links = Db::table('quicklinks')->where('status', '1')->select();
        $this->assign('links', $links);
        Session::set('links', $links, 'index');
        //首页图片
        $notice = Db::table('notice')->where('status', 1)->select();
        $this->assign('notice', $notice);
        return $this->fetch('index/index');
    }
    //个人中心页面
    public function PersonalCenter()
    {
        $user = session('name', '','index');
        $result = Db::table('user')->where('username', 'EQ', $user)->field('username, email, phone, nickname, id , icon')->select();
        $this->assign('result', $result[0]);
        return view('index/PersonalCenter');
    }

    //登录页面
    public function login()
    {
        return view('index/login');
    }

    //修改密码页面
    public function faq()
    {
         $user = session('name', '','index');
         $result = Db::table('user')->where('username', 'EQ', $user)->field('icon')->select();
         $this->assign('result', $result[0]);
        return view('index/faq');
    }

     //收藏页面
     public function plans()
    {
        $user = session('name', '','index');
        $result = Db::table('user')->where('username', 'EQ', $user)->field('icon')->select();
        $id = session('user_id','', 'index');
        $this->assign('result', $result[0]);
        //收藏电影
        $pp = Db::table(['collection, film,user'])->field('collection.id,film.filmname,film.regions,film.duration,film.actor')->where('collection.film_id = film.id and collection.user_id = user.id and user.id='.$id)->select();
//        var_dump($pp);die;
        $this->assign('pp', $pp);
        return view('index/plans');
    }

    //评论页面
    public function grid()
    {
        $user = session('name', '','index');
        $result = Db::table('user')->where('username', 'EQ', $user)->field('icon')->select();
        $this->assign('result', $result[0]);
        return view('index/grid');
    }

    // 订单管理页面
    public function charts()
    {
        $user = session('name', '','index');
        $result = Db::table('user')->where('username', 'EQ', $user)->field('icon')->select();
        $this->assign('result', $result[0]);
        return view('index/charts');
    }
}
