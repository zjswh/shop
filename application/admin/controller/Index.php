<?php
/**
 * Created by PhpStorm.
 * User: cmdn
 * Date: 2018/4/6
 * Time: 16:04
 */

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Cookie;
use think\Request;
use app\admin\model\Product as Product;
use app\admin\model\User as UserModel;
use app\index\model\advice as AdviceModel;
use app\index\model\order as OrderModel;

class Index extends Controller{


    public function __construct(){
        parent::__construct();
        $this->checklogin();
    }
    public function checklogin(){
        $user = Cookie::get('admin_username');

        if(isset($user) == false){
//            var_dump(url('index/user/login'));
            return $this->error('未登陆',url('index/user/login'));
        }
    }
    public function index(){
      //总注册
       $user = UserModel::all();
       $allUser = count($user);
       $this->assign('allUser',$allUser);

       //今日注册
        $result = Db::name('user')->whereTime('createtime', 'today')->select();;
        $this->assign('todayUser',count($result));

        //总销售
        $products = Db::name('orderlist')
                    ->where('iscost','1')
                    ->sum('num');
        $this->assign('products',$products);
        //今日销售
        $product = Db::name('orderlist')
                   ->whereTime('createtime', 'today')
                   ->where('iscost','1')
                     ->sum('num');
        $this->assign('product',$product);
       return  $this->fetch();
    }

    public function form(){

        $arr = input('get.');
        $page = 5;
        if(isset($arr['key'])){
            $key = $arr['key'];
            $list = Db::name("advice")->where('content','like','%'.$key.'%')->paginate($page,false,['query'=>request()->param()]);
            $all = Db::name("advice")->where('content','like','%'.$key.'%')->select();
        }else{
            $list = AdviceModel::paginate($page);
            $all = AdviceModel::all();
        }
        if($list){
            $this->assign('list',$list);
            $this->assign('all',count($all));
            return $this->fetch('Index/form');
        }else{

            return $this->fetch();
        }

    }

    public function user(){
        $arr = input('get.');
        $page = 3;
        if(isset($arr['key'])){
            $key = $arr['key'];
            $user = Db::name("user")->where('username','like','%'.$key.'%')->paginate($page,false,['query'=>request()->param()]);
            $all = Db::name("user")->where('username','like','%'.$key.'%')->select();
        }else{
            $user = UserModel::paginate($page);
            $all     = UserModel::all();
        }
        if($user){
            $this->assign('list',$user);
            $this->assign('all',count($all));
            return $this->fetch('Index/user');
        }else{

            return $this->fetch();
        }

    }



    public function deletePro(){
        $arr = input('post.');
        if($arr['id']){
            if(Product::destroy($arr['id'])){
                return 'yes';
            }else{
                return 'no';
            }
        }

    }

    public function deleteUser(){
        $arr = input('post.');
        if($arr['id']){
            if(UserModel::destroy($arr['id'])){
                return 'yes';
            }else{
                return 'no';
            }
        }

    }

    public function deleteAdvice(){
        $arr = input('post.');
        if($arr['id']){
            if(AdviceModel::destroy($arr['id'])){
                return 'yes';
            }else{
                return 'no';
            }
        }

    }

    public function table(){
        $page = 3;
        $arr = input('get.');
        if(isset($arr['key'])){
            $key = $arr['key'];
            $product = Db::name("product")->where('name','like','%'.$key.'%')->paginate($page,false,['query'=>request()->param()]);
            $all = Db::name("product")->where('name','like','%'.$key.'%')->select();
        }else{
            $product = Product::paginate($page);
            $all = Db::name("product")->select();
        }
        if($product){
            $this->assign('list',$product);
            $this->assign('all',count($all));
            return $this->fetch('Index/table');
        }else{
            return $this->fetch();
        }

    }

    public function addPro(Request $request){
        $arr = $request->post();

        if(isset($arr['submit'])){

            $file =$request->file('file');

            if (empty($file)) {
                $this->error('请选择上传文件');
            }

            $info = $file->move(ROOT_PATH . 'public' . DS . 'static/uploads');
            $path = 'uploads/'.$info->getSaveName();
//            var_dump($path);
//            exit();
            if ($info) {
                $product = new Product;
                $list = [
                    'name' => $arr['proname'],
                    'price'=>$arr['price'],
                    'num'=>$arr['num'],
                    'type'=>$arr['gg'],
                    'attr'=>$path
                ];
                $res = $product->create($list);
                if($res){
                    $this->success('新增成功！', url('admin/index/table'));
                }else{
                    $this->error('新增失败！');
                }

            } else {
                // 上传失败获取错误信息
                $this->error($file->getError());
            }

        }else{
            return  $this->fetch();
        }

    }




    public function updatePro($id = null,Request $request){

        if($id){
            $product = Product::get($id);
            $this->assign('list',$product);
            return  $this->fetch();
        }else{
            $arr = $request->post();

            if($arr){
                $product           = Product::get($arr['proid']);
                $product['price']    = $arr['price'];
                $product['name']     = $arr['proname'];
                $product['num']     = $arr['num'];
                if($arr['ischange']){
                    $file =$request->file('files');
                    if (empty($file)) {
                        $this->error('请选择上传文件');
                    }

                    $info = $file->move(ROOT_PATH . 'public' . DS . 'static/uploads');
                    $path = 'uploads/'.$info->getSaveName();
                    $product['attr']     = $path;
                }

                $res =  $product->save();
                if($res){
                    $this->success('修改成功', url('admin/index/table'));
                }else{
                    $this->error('未修改任何数据');
                }
            }else{
                $this->error('没有数据交互');
            }

        }


    }

    public function updateUser($id = null){

        if($id){
            $user = UserModel::get($id);
            $this->assign('list',$user);
            return  $this->fetch();
        }else{
            $arr = input('post.');
            if($arr){
                $user           = UserModel::get($arr['userid']);
                $user['username']    = $arr['username'];
                $user['mobile']     = $arr['mobile'];
                $user['level']     = $arr['level'];
                $user['money']     = $arr['money'];
                $user['address']     = $arr['address'];
                $user['email']     = $arr['email'];
                $res =  $user->save();
                if($res){
                    $this->success('修改成功', url('admin/index/user'));
                }else{
                    $this->error('未修改任何数据');
                }
            }else{
                $this->error('没有数据交互');
            }

        }

    }


    public function orderList(){
        $page = 3;
        $arr = input('get.');
        if(isset($arr['key'])){
            $key = $arr['key'];
            $order = Db::name("orderlist")->where('username|orderid|product','like','%'.$key.'%')->paginate($page,false,['query'=>request()->param()]);
            $all = Db::name("orderlist")->where('username|orderid|product','like','%'.$key.'%')->select();
        }else{
            $order = OrderModel::paginate($page);
            $all = OrderModel::all();
        }
        if($order){
            foreach($order as $k=>$val){
                $order[$k]['username'] = $val->user->username;
                $order[$k]['product'] = $val->product->name;

            }

            $this->assign('list',$order);
            $this->assign('all',count($all));
            return $this->fetch('Index/orderList');
        }else{
            return $this->fetch();
        }

    }

    public function deleteOrder(){
        $arr = input('post.');
        if($arr['id']){
            if(OrderModel::destroy($arr['id'])){
                return 'yes';
            }else{
                return 'no';
            }
        }

    }


    public function updateOrder($id = null){

        if($id){
            $order = OrderModel::get($id);
            $order['username'] = $order->user->username;
            $order['productname'] = $order->product->name;
            $order['price'] = $order->product->price;
            $this->assign('list',$order);
            return  $this->fetch();
        }else{
            $arr = input('post.');
            if($arr){
                $order                 = OrderModel::get($arr['orderid']);
                $order['num']         = $arr['num'];
                $order['prices']      = $arr['price']*$arr['num'];
                $order['iscost']      = $arr['state'];
                $order['province']    = $arr['province'];
                $order['city']         = $arr['city'];
                $order['target']      = $arr['address'];
                $res =  $order->save();
                if($res){
                    $this->success('修改成功', url('admin/index/orderList'));
                }else{
                    $this->error('未修改任何数据');
                }
            }else{
                $this->error('没有数据交互');
            }

        }

    }

    public function logout(){
        Cookie::delete('admin_username');
        $this->success('退出成功',url('index/user/login'));
    }


}