<?php
/**
 * Created by PhpStorm.
 * User: cmdn
 * Date: 2018/2/1
 * Time: 16:38
 */

namespace app\index\controller;
use think\Controller;
use think\Cookie;
use app\index\model\User as UserModel;

class User extends Controller
{

    public function add($user){

        if($result = UserModel::create($user)){
            return '用户[ ' . $result->username . ':' . $result->id . ' ]新增成功';
        }else{
            return '新增出错';
        }
    }

    public function addlist(){
        $user = new UserModel;
        $list = [
            ['nickname'=>'李四','email'=>'741258933@qq.com','birthday'=>strtotime('2010-05-15')],
            ['nickname'=>'王五','email'=>'741258933@163.com','birthday'=>strtotime('2001-05-15')],
        ];
        if($user->saveAll($list)){
            return '用户批量新增成功';
        }else{
            return $user->getError();
        }
    }


    public function read($id=''){
        $user = UserModel::get($id);
        echo $user->nickname . '<br/>';
        echo $user->email . '<br/>';
        echo $user->birthday. '<br/>';
        echo $user->userbirthday. '<br/>';

    }

    public function register(){
        $arr = input('post.');
        //判断是否有内容提交
        if(isset($arr['register'])){
            $user['username'] = $arr['username'];
            $user['password'] = md5($arr['password']);
            $user['email'] = $arr['email'];
            $user['money'] = 10000;
            $user['mobile'] = $arr['mobile'];
            if(UserModel::create($user)){
                $this->success('注册成功',url('index/user/login'));
            }else{
                $this->error('注册失败，返回重试');
            }
        }else{
            return $this->fetch();
        }

    }

    public function checkUser(){
        $arr = input('post.');
        if(UserModel::get(['username'=>$arr['username']])){
            return 'no';
        }else{
            return 'yes';
        }

    }
    public function login(){
        $arr = input('post.');
        if(isset($arr['username'])){
            $res = UserModel::get(['username'=>$arr['username'],'password'=>md5($arr['password'])]);
            if($res){
                return json(array('1',$res));
            }else{
                return json(array('0'));
            }
        }else{
            return $this->fetch();
        }

    }

    public function personal(){
        $username = Cookie::get('username');
        $arr = input('post.');

            if($username){
                $user = UserModel::get(['username'=>$username]);

                    if($user){
                        if(isset($arr['submit'])){
                            $user['email'] = $arr['email'];
                            $user['mobile'] = $arr['mobile'];
                            $user['province'] = $arr['province'];
                            $user['city'] = $arr['city'];
                            $user['address'] = $arr['address'];
                            if($arr['password']){
                                $user['password'] = md5($arr['password']);
                            }
                            if($user->save()){
                                if($arr['password']){
                                    $this->success('成功，由于修改了密码，请重新登录',url('index/user/login'));
                                    Cookie::delete('username');
                                }else{
                                    $this->success('修改成功');
                                }

                            }else{
                                $this->error('没有数据被修改');
                            }
                        }else{
                            $this->assign('user',$user);
                            return $this->fetch();
                        }

                    }else{
                        $this->error('请先登录',url('index/user/login'));
                    }


        }


    }
    public function old(){
        return $this->fetch();
    }

}