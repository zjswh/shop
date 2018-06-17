<?php
namespace app\index\controller;


use think\Controller;
use think\Config;
use org\util\tool;
use PHPExcel_IOFactory;
use PHPExcel;
use think\Db;
use app\index\model\User as UserModel;

class Index extends Controller
{
    public function index()
    {
        return '<style type="text/css">*{ padding: 0; margin: 0; } .think_default_text{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }
    public function test(){
        return 'asdas';
    }
    public function showtest(){
        $music = new tool();
        $list_new = $this->showlist($music,1);
        $list_hot = $this->showlist($music,2);
        $list_bb = $this->showlist($music,6);
        $list_hito = $this->showlist($music,18);
        $this->assign('list_new',  $list_new);
        $this->assign('list_hot',  $list_hot);
        $this->assign('list_bb',   $list_bb);
        $this->assign('list_hito', $list_hito);

        return $this->fetch();
    }
    private function showlist($music,$type){
        $lists = array();
        $list  = $music->getList($type);
        if(is_array($list)){
            foreach($list as $k=>$v){
                $lists[] = $v['title'];
            }
        }
        return $lists;
    }
    public function nolist(){
        $music = new tool();
        $list_new = $this->showlist($music,1);
        $list_hot = $this->showlist($music,2);
        $list_bb = $this->showlist($music,6);
        $list_hito = $this->showlist($music,18);
        $this->assign('list_new',  $list_new);
        $this->assign('list_hot',  $list_hot);
        $this->assign('list_bb',   $list_bb);
        $this->assign('list_hito', $list_hito);
        $this->view->engine->layout(false);
        $content = <<<EOT
<div id="main">

    <!--排行版-->
    <div class='Ranking'>

        <h2 class='bg_top'>排行榜</h2>

        <div class='classify'>
            <div class='new'>
                <h3>巅峰榜<br/>新歌</h3>
                {volist name="list_new" id="music"}
                {\$music}<br>
                {/volist}
            </div>

            <div class='bill'>
                <h3>巅峰榜<br/>新歌速递</h3>
                {volist name="list_bb" id="music"}
                {\$music}<br>
                {/volist}
            </div>
            <div class='hot'>
                <h3>巅峰榜<br/>热歌</h3>
                {volist name="list_hito" id="music"}
                {\$music}<br>
                {/volist}
            </div>
            <div class='ktv'>
                <h3>巅峰榜<br/>ktv热歌</h3>
                {volist name="list_hot" id="music"}
                {\$music}<br>
                {/volist}
            </div>
        </div>
    </div>


    <audio style='display:none' id="myaudio"  controls="controls" loop="false" hidden="true">

</div>
EOT;
        return $this->display($content);

        }
    public function login(){
        return $this->fetch();
    }

    public function dbs(){
//        $res = Db::query("SELECT * FROM think_user");
//        var_dump($res);
//        Db::name('user')
//            ->insert(['nickname' => '李四1','name'=>'hhh','password'=>'123456','create_time'=>time(),'update_time'=>time(), 'status' => 1]);
          Db::transaction(function(){
              Db::name('user')->delete(6);
              DB::name('data')->insert(['name'=>'gt3','status'=>'1']);
          });

    }
    public function dbview(){
        $result = Db::view('user','id,nickname,status')
            ->view('profile',['truename'=>'truenames','email'],'profile.user_id=user.id')
            ->where('status',1)
            ->order('id desc')
            ->select();
        dump($result);
    }

    public function dexcel(){
        $path = dirname(__FILE__); //找到当前脚本所在路径
        $PHPExcel = new PHPExcel(); //实例化PHPExcel类，类似于在桌面上新建一个Excel表格
        $PHPSheet = $PHPExcel->getActiveSheet(); //获得当前活动sheet的操作对象
        $PHPSheet->setTitle('demo'); //给当前活动sheet设置名称
        $PHPSheet->setCellValue('A1','姓名')->setCellValue('B1','分数');//给当前活动sheet填充数据，数据填充是按顺序一行一行填充的，假如想给A1留空，可以直接setCellValue('A1','');
        $PHPSheet->setCellValue('A2','张三')->setCellValue('B2','50');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel,'Excel2007');//按照指定格式生成Excel文件，'Excel2007'表示生成2007版本的xlsx，
        $PHPWriter->save($path.'/demo.xlsx'); //表示在$path路径下面生成demo.xlsx文件
    }
    
    public function outExcel(){
        $path = dirname(__FILE__); //找到当前脚本所在路径
        $PHPExcel = new PHPExcel(); //实例化PHPExcel类，类似于在桌面上新建一个Excel表格
        $PHPSheet = $PHPExcel->getActiveSheet(); //获得当前活动sheet的操作对象
        $PHPSheet->setTitle('demo'); //给当前活动sheet设置名称

        //
        $PHPSheet->setCellValue('A1','姓名')->setCellValue('B1','分数');//给当前活动sheet填充数据，数据填充是按顺序一行一行填充的，假如想给A1留空，可以直接setCellValue('A1','');
        $PHPSheet->setCellValue('A2','张三')->setCellValue('B2','50');


        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel,'Excel2007');//按照指定格式生成Excel文件，'Excel2007'表示生成2007版本的xlsx，'Excel5'表示生成2003版本Excel文件
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//告诉浏览器输出07Excel文件
        //header('Content-Type:application/vnd.ms-excel');//告诉浏览器将要输出Excel03版本文件
        header('Content-Disposition: attachment;filename="01simple.xlsx"');//告诉浏览器输出浏览器名称
        header('Cache-Control: max-age=0');//禁止缓存
        $PHPWriter->save("php://output");

    }
    public function chart(){
        $this->view->engine->layout(false);
        return $this->fetch();
    }
    public function getdata(){
        $res = [
            'name'=>'swh',
            'sex'=>'男'
        ];
        return json($res);
    }




}
