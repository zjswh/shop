<?php
/**
 * Created by PhpStorm.
 * User: cmdn
 * Date: 2018/4/23
 * Time: 16:35
 */

namespace app\index\controller;
//use think\cache\Driver\Redis;

class Red{
    public $redis;

    public function __construct(){
        $this->redis =  new \Redis;
        $this->redis->connect('127.0.0.1',6379);
    }
    public function index(){
        $this->redis->set('aaa','111');
        var_dump($this->redis->get('aaa'));
    }
}