<?php
/* 
* @Author: anchen
* @Date:   2017-11-25 15:53:49
* @Last Modified by:   anchen
* @Last Modified time: 2017-11-25 16:53:49
*/

require_once dirname(dirname(__FILE__)) . '/lib/class.geetestlib.php';
require_once dirname(dirname(__FILE__)) . '/config/config.php';
$GtSdk = new GeetestLib(CAPTCHA_ID, PRIVATE_KEY);
session_start();

$data = array(
    "user_id" => $_POST['user_id'], # 网站用户id
    "client_type" => "web", #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
    "ip_address" =>isset($_SERVER['HTTP_CLIENT_IP'])?$_SERVER['HTTP_CLIENT_IP']:'127.0.0.1' # 请在此处传输用户请求验证时所携带的IP
);

if($_POST['a'] == 'first'){
    $status = $GtSdk->pre_process($data, 1);
    $_SESSION['gtserver'] = $status;
    $_SESSION['user_id'] = $data['user_id'];
    echo $GtSdk->get_response_str();

}else if($_POST['a'] == 'second'){
    if ($_SESSION['gtserver'] == 1) {   //服务器正常
        $result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $data);
        if ($result) {
            echo '{"status":"success"}';
        } else{
            echo '{"status":"fail"}';
        }
    }else{  //服务器宕机,走failback模式
        if ($GtSdk->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode'])) {
            echo '{"status":"success"}';
        }else{
            echo '{"status":"fail"}';
        }
    }
}




?>
