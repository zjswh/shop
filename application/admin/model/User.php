<?php
/**
 * Created by PhpStorm.
 * User: cmdn
 * Date: 2018/4/17
 * Time: 19:52
 */

namespace app\admin\model;
use think\Model;

class User extends Model{
    protected $table = 'user';
    protected $type       = [
        'updatetime' => 'timestamp:Y/m/d H:i:s',
        'createtime'  => 'timestamp:Y/m/d H:i:s'
    ];
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $autoWriteTimestamp = true;

    protected function getHomeAttr($value,$data){
        return $data['province'].'省'.$data['city'].'市'.$data['address'];
    }
}