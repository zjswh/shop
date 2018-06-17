<?php
/**
 * Created by PhpStorm.
 * User: cmdn
 * Date: 2018/3/26
 * Time: 16:43
 */

namespace app\index\model;
use think\Model;

class banner extends Model{
    protected $table = 'banner';

    protected function getZkAttr($zk){
        return $zk*10;
    }
}