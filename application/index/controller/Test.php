<?php
namespace app\index\controller;
use app\admin\model\Product;
use app\admin\model\User;
use think\Controller;
use think\Db;
use think\Cookie;

use think\cache\driver\Redis;
use app\index\model\Test as TestModel;
use app\index\model\banner as BannerModel;
use app\index\model\advice as AdviceModel;
use app\index\model\order as OrderModel;

class Test extends Controller
{
    public $redis;
    public $redis_name;
    public function __construct(){
        parent::__construct();
        $this->redis = new \Redis();
        $this->redis->connect('127.0.0.1',6379);
        $this->redis_name = 'shop';
    }

    public function index(){

        $new_p = TestModel::all(function($query){
            $query->where(['type'=>'0','pid'=>'0'])->order('id','desc')->limit(4);
        });
        $feature_p = TestModel::all(function($query){
            $query->where(['type'=>'1','pid'=>'0'])->order('id','desc')->limit(4);
        });

        $banner = BannerModel::all();

        $this->assign('banner',$banner);
        $this->assign('feature_p',$feature_p);
        $this->assign('new_p',$new_p);
        return $this->fetch();
    }
    public function news(){
        return $this->fetch();
    }
    public function about(){
        return $this->fetch();
    }

    public function more($type = null){
        $page = 4;
        switch($type){
            case 'new    ': $key = 0;break;
            case 'hot'     : $key = 1;break;
            case 'search' :$key = -1;break;
            default : $key = 0;break;
        }

        if($key != -1){
            $new_p = Db::name("product")->where('type',$key)->paginate($page,false,['query'=>request()->param()]);
            $all = Db::name("product")->where('type',$key)->select();
        }else{
            $arr = input('get.');
            if(isset($arr['keyword'])){
                $new_p = Db::name("product")->where('name','like','%'.$arr['keyword'].'%')->paginate($page,false,['query'=>request()->param()]);
                $all = Db::name("product")->where('name','like','%'.$arr['keyword'].'%')->select();
            }else{
                $this->error('搜索出错，返回重试');
            }

        }


        $this->assign('all',count($all));
        $this->assign('new_p',$new_p);
        return $this->fetch();
    }
    public function contact(){
        $arr = input('post.');
        if(isset($arr['submit'])){
            $list['content'] = $arr['userMsg'];
            $res = AdviceModel::create($list);
            if($res){
                $this->success('提交成功', url('index/test/contact'));
            }else{
                $this->error('提交失败');
            }
        }else{
            return $this->fetch();
        }
    }
    public function delivery(){
        return $this->fetch();
    }
    public function preview($id = null){
        if(!$id){
            $this->error('不存在该商品');
        }
        $product = TestModel::get($id);
        if($product){
            $this->assign('pro',$product);
        }else{
            $this->error('不存在该商品');
        }
        $new_p = TestModel::all(function($query){
            $query->where(['type'=>'0','pid'=>'0'])->order('id','desc')->limit(4);
        });
        $this->assign('new_p',$new_p);

        return $this->fetch();

    }

    public function saveDb(){
        while(1){
            $order = $this->redis->lPop($this->redis_name);
            if(!$order || $order == 'nil'){
                sleep(2);
                continue;
            }
            $order_arr = explode('%',$order);
            $order_mess = json_decode($order_arr[0]);

            if(!$result = OrderModel::create($order_mess)){
                $this->redis->rPush($this->redis_name,$order);
            }
        }


    }

    public function buy(){
        $arr = input('post.');
        $num = $arr['num'];
        if(isset($arr['id'])){
            $res = Product::get($arr['id']);


            if($res){
                if($res['num'] >= $num){
                    //先判断余额 扣款
                    $user = User::get(['username'=>$arr['name']]);

                    if($user){
                        $order['orderid'] =$this->orderNum($user['id']);//订单号的生成
                        $order['userid'] = $user['id'];
                        $order['productid'] = $res['id'];
                        $order['num'] = $num;
                        $order['province'] = $user['province'];
                        $order['city'] = $user['city'];
                        $order['target'] = $user['address'];
                        $order['prices'] = $res['price']*$num;

//                        if(!$result = OrderModel::create($order)){
//                            return '7';//订单创建失败
//                        }


//                        unset($order);
                        if($user['money'] >= $res['price']*$num){


                            $res['num'] = $res['num'] - $num;//更改库存量
                            $user['money'] -= $res['price']*$num; // 扣款
                            if($res->save() && $user->save()){
                                $order['iscost'] = 1;
                                //存入redis
                                $redis = $this->redis;
                                $redis->rPush($this->redis_name,json_encode($order).'%'.microtime());

                                return '1';


                            }else{
                                $redis = $this->redis;
                                $redis->rPush($this->redis_name,json_encode($order).'%'.microtime());
                                return '4';  //购买失败，请联系管理员
                            }
                        }else{
                            $redis = $this->redis;
                            $redis->rPush($this->redis_name,json_encode($order).'%'.microtime());
                            return '6';//用户余额不足
                        }
                    }else{
                        return '5';//未登录
                    }

                }else{
                    return '2'; //库存不足
                }
            }else{
                return '3'; //商品不存在
            }
        }
    }

    public function showMoney(){
        $arr = input('post.');
        if(isset($arr['name'])){
            $res = User::get(['username'=>$arr['name']]);

            if($res){
                return json(['1',$res['money']]);
            }else{
                return json(['2','不存在此用户']);
            }
        }else{
            return json(['3','请先登录']);
        }
    }

    //订单号的生成
    public function orderNum($id){

        $username = $id;
        $data['baseTime'] = date('ymdHi');
        $data['baseRandNum'] = rand(0,99);
        $data['baseUserId'] = str_pad($username%1000,3,0,STR_PAD_LEFT);
        $orderid = $data['baseTime'].str_pad($data['baseRandNum'],2,0,STR_PAD_LEFT).$data['baseUserId'];
        return $orderid;

    }


    public function orderFinish(){
        $page = 5;
        $user = User::get(['username'=> Cookie::get('username')]);
        $list = OrderModel::where(['iscost'=>'1','userid'=>$user['id']])->paginate($page,false,['query'=>request()->param()]);
        $all = OrderModel::all(['iscost'=>'1','userid'=>$user['id']]);
        $this->assign('all',count($all));
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function orderUnfinish(){
        $page = 5;
        $user = User::get(['username'=> Cookie::get('username')]);

        $list = OrderModel::where(['iscost'=>'0','userid'=>$user['id']])->paginate($page,false,['query'=>request()->param()]);
        $all = OrderModel::all(['iscost'=>'0','userid'=>$user['id']]);
        $this->assign('all',count($all));
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function pay(){
        $arr = input('post.');
        if(isset($arr['id'])){
            $order = OrderModel::get($arr['id']);

            if($order){
                if(isset($arr['user'])){
                    $user = User::get(['username'=>$arr['user']]);

                    if($user){
                        if($user['money']>$order['prices']){
                            $user['money'] -= $order['prices'];
                            $order['iscost'] = '1';
                            $user->save();
                            $order->save();
                            return '1';
                        }else{
                            return '6';//用户余额不足
                        }
                    }else{
                        return '5';//不存在该用户
                    }
                }else{
                    return '4';//请先登录
                }
            }else{
                return '3';//订单不存在
            }
        }else{
            return '2';//无效的订单
        }
    }

}