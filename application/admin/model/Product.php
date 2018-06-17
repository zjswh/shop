<?php
/**
 * Created by PhpStorm.
 * User: cmdn
 * Date: 2018/4/17
 * Time: 14:58
 */

namespace app\admin\model;
use think\Model;

class Product extends  Model{
    protected $table="product";
    protected $type       = [
        'updatetime' => 'timestamp:Y/m/d H:i:s',
        'starttime'  => 'timestamp:Y/m/d H:i:s'
    ];
    protected $createTime = 'starttime';
    protected $updateTime = 'updatetime';
    protected $autoWriteTimestamp = true;
}