<?php
/**
 * Created by PhpStorm.
 * User: cmdn
 * Date: 2018/1/19
 * Time: 15:52
 */
namespace app\index\model;
use think\Model;

class User extends Model{
    protected $table = 'user';
    //protected $name = 'name'; //不需前缀

    protected $dateFormat = 'Y-m-d';
    protected $type = [
        'birthday'=>'timestamp'
        //'birthday'=>'timestamp:Y-m-d'

    ];

    //自动写入时间戳的字段
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    //自动写入其他字段
    protected $insert = ['status'=>1];

    protected function getBirthdayAttr($birthday){
        return date('Y-m-d',$birthday);
    }

    protected function getHomeAttr($value,$data){
        return $data['province'].'省'.$data['city'].'市'.$data['address'];
    }

    //自定义属性
    protected function getUserbirthdayAttr($value,$data){
        return date('Y/m/d',$data['birthday']);
    }

}