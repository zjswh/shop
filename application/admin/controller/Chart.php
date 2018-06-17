<?php
/**
 * Created by PhpStorm.
 * User: cmdn
 * Date: 2018/4/22
 * Time: 10:19
 */

namespace app\admin\controller;
use think\Controller;
use think\Db;
use app\index\model\order as OrderModel;

class Chart extends Controller{
    //商品类别
    public function getPie(){
        $res = Db::query("select count(*) as value,type from product  group by type ");
        $type = [];
        foreach($res as $key=>$val){
            switch($val['type']){
                case '1':  $data[$key]['name'] = 'sss';$data[$key]['value'] = $val['value'];$type[]='sss';break;
                case '0':  $data[$key]['name'] = 'ddd';$data[$key]['value'] = $val['value'];$type[]='ddd';break;
            }

        }
        return json(['data'=>$data,'type'=>$type]);
    }

    //每月注册的用户
    public function getLine(){
        $arr = input('post.');
        $path = $_SERVER['HTTP_HOST'].'/../';
        $path = $path.'/log/data/user/'.$arr['year'].'_'.$arr['month'].'_day.json';
        if(file_exists($path)){
            $content  = json_decode(file_get_contents($path));
            return $content;
        }else{
            return '0';
        }

    }


    //每月的销售数
    public function getBar(){

        $arr = input('post.');
        $path = $_SERVER['HTTP_HOST'].'/../';
        $path = $path.'/log/data/orderlist/'.$arr['year'].'_'.$arr['month'].'_day.json';
        if(file_exists($path)){
            $content  = json_decode(file_get_contents($path));
            return $content;
        }else{
            return '0';
        }

//        $data = ['data'=>[120, 200, 150, 80, 70, 110, 130],'type'=>'bar'];
//        return json($data);
    }

    public function getMap(){
        $res = Db::query("select count(*) as value,province as name from orderlist where iscost='1' group by province ");

//        $data = [['value'=>335,'name'=>'四川'],['value'=>310,'name'=>'浙江'],['value'=>234,'name'=>'青海'],
//            ['value'=>135,'name'=>'江西'],['value'=>1548,'name'=>'福建']];
        return json($res);
    }

    public static function getMonAndDayUser(){
        $num = array();
        $day = array();
        $mon = date("m");
        for($i=date("d")-1;$i>=0;$i--){
            $beginToday= mktime(0,0,0,$mon,date("d")-$i,date('Y'));//当天的零点时间

            $endToday=mktime(0,0,0,$mon,date("d")+1-$i,date('Y'))-1; //昨天的零点时间

            $first = Db::query("SELECT count(*) as num FROM user WHERE  createtime>{$beginToday} AND  createtime<{$endToday}");

            $num[] = $first[0]['num'];
            $day[] = date("d")-$i;
        }

        $res = json_encode(array( 'categories'=>$day,'data'=> $num));

        $path = $_SERVER['HTTP_HOST'].'/../';
        file_put_contents($path.'/log/data/user/'.date('Y').'_'.intval($mon).'_day.json',$res);

    }

    public static function getMonAndDayOrder(){
        $num = array();
        $day = array();
        $mon = date("m");
        for($i=date("d")-1;$i>=0;$i--){
            $beginToday= mktime(0,0,0,$mon,date("d")-$i,date('Y'));//当天的零点时间

            $endToday=mktime(0,0,0,$mon,date("d")+1-$i,date('Y'))-1; //昨天的零点时间

            $first = Db::query("SELECT  num FROM orderlist WHERE  createtime>{$beginToday} AND  createtime<{$endToday}");
            $nums = 0;
            foreach($first as $val){
                if($val['num']){
                    $nums += $val['num'];
                }
            }
            $num[] = $nums;
            $day[] = date("d")-$i;
        }

        $res = json_encode(array( 'categories'=>$day,'data'=> $num));

        $path = $_SERVER['HTTP_HOST'].'/../';
        file_put_contents($path.'/log/data/orderlist/'.date('Y').'_'.intval($mon).'_day.json',$res);

    }
}