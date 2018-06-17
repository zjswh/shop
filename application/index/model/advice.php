<?php
/**
 * Created by PhpStorm.
 * User: cmdn
 * Date: 2018/4/18
 * Time: 17:07
 */

namespace app\index\model;
use think\Model;

class advice extends Model{
    protected $table = 'advice';
    protected $type = [
        'createtime' => 'timestamp:Y/m/d H:i:s',
        'updatetime' => 'timestamp:Y/m/d H:i:s'
    ];
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $autoWriteTimestamp = true;
}