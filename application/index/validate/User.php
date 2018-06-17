<?php
/**
 * Created by PhpStorm.
 * User: cmdn
 * Date: 2018/2/2
 * Time: 16:17
 */

namespace app\index\validate;
use think\Validate;


class User extends validate{
    protected $rule = [
        'nickname' => ['require', 'min'=>5, 'token'],
        'email'    => ['require', 'email'],
        'birthday' => ['dateFormat' => 'Y|m|d'],

        //自定义错误提示
//        ['nickname', 'require|min:5', '昵称必须|昵称不能短于5个字符'],
//        ['email', 'email', '邮箱格式错误'],
//        ['birthday', 'dateFormat:Y-m-d', '生日格式错误'],

    //自定义验证
//        ['email', 'checkMail:thinkphp.cn', '邮箱格式错误'],
    ];

    // 验证邮箱格式 是否符合指定的域名
    protected function checkMail($value, $rule)
    {
        return 1 === preg_match('/^\w+([-+.]\w+)*@' . $rule . '$/', $value);

        //动态返回错误信息
//        $result = preg_match('/^\w+([-+.]\w+)*@' . $rule . '$/', $value);
//        if (!$result) {
//            return '邮箱只能是' . $rule . '域名';
//        } else {
//            return true;
//        }
    }

}