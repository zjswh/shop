<?php
/**
 * Created by PhpStorm.
 * User: cmdn
 * Date: 2018/4/21
 * Time: 13:36
 */

namespace app\index\model;

use think\Model;

class order extends Model{
    protected $table = 'orderlist';
    protected $type = [
        'createtime' => 'timestamp:Y-m-d H:i:s',
        'updatetime' => 'timestamp:Y-m-d H:i:s'
    ];
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $autoWriteTimestamp = true;

    protected function getStateAttr($value,$data){
        if($data['iscost'] == 0){
            return '<span style="color:red;">未付款</span>';
        }else if($data['iscost'] == 1){
            return '<span style="color:green;">已付款</span>';
        }else{
            return '<span style="color:yellow;">付款异常</span>';;
        }
    }

    public function user(){
        return $this->hasOne('User','id','userid');
    }

    public function product(){
        return $this->hasOne('\app\admin\model\Product','id','productid');
    }

    protected function getAddressAttr($value,$data){
        if($data['province']){
            return $data['province'].'省'.$data['city'].$data['target'];
        }else{
            return $data['province'].$data['city'].$data['target'];
        }

    }

}