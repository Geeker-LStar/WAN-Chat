<?php
/*

WAN URI Check CORE - Only core & test, please don't include this file in release. (TESTING PUREPOSES ONLY)

*/

session_start();
header("Content-Type: application/json");
header("X-Powered-By: Love");
header("X-WAN: WAN v0.8@wan.ltx1102.com");
$wan_fetch_uri = $_SERVER["REQUEST_URI"];
$wan_final_result = array();
$wan_final_result["code"] = 200;
$wan_final_result["uri"] = $wan_fetch_uri;


$i_dont_want_to_output_html = "WAN_TYPE_JSON_NO_HTML";
/* This will tell the wan-config.php, No html - this is only json. */
/* Now we can safely require wan-config.php. */
require("../wan-config.php");
require("../wan-userinfo.php");

// 连接数据库
$wan_sql_handler = new mysqli($servername, $username, $password, $dbname);
if (!isset($_SESSION["wuid"]))
    header("location: ../wan-login/signin.php");

// 获取用户加入的群聊列表
$sql = "SELECT * FROM Users WHERE wan_uid='{$wid}'";
$result = $wan_sql_handler->query($sql);
$all_group = $result->fetch_assoc()["my_groups"];
$wan_final_result["all_your_groups"] = $all_group;
$wan_the_groups = explode("//",$all_group);

$wan_user_want_group_id = explode("/",$wan_fetch_uri)[2];    // 用户希望进入的群聊的 gid
/* Looks like this: /wan-gchat-uri/2023/ . */
/*
[0] -> {nothing}
[1] -> "wan-gchat-uri"
[2] -> EXACTLY WHAT WE WANT!!!!!!
*/


$wan_user_has_access = 0;
/* foreach is a good snippet for "for"!!! */
// 检查用户是否有访问权限
foreach($wan_the_groups as $wan_specific_group) {  
    if($wan_specific_group == $wan_user_want_group_id) {    // 用户在群聊中
        $wan_user_has_access = 1;    // 则有访问权限
        break;
    }
}

// 如果用户无权访问，则需要查明原因——用户不在群聊 or 群聊根本就不存在
if ($wan_user_has_access != 1)
{
    // 检查该群聊是否存在
    $wan_exists=0;
    $sql2 = "SELECT wan_gid FROM All_Groups_Info WHERE 'wan_gid'='{$wan_user_want_group_id}'";
    $result = $wan_sql_handler->query($sql2);
    $wan_such_group_exists = $result->num_rows;
    if($wan_such_group_exists > 0)    // 群聊存在
        $wan_exists = 1;  
}



switch ($wan_user_has_access) {
    case 1:
        $wan_final_result["msg"] = "WELCOME.";
        break;
    case 0:
        if($wan_exists){
        $wan_final_result["msg"] = "ERROR - NO ACCESS";
        $wan_final_result["code"] = 403;
        }
        else{
            $wan_final_result["msg"] = "ERROR - NO SUCH GROUP";
        $wan_final_result["code"] = 404;
        }
       
        break;
    default:
        $wan_final_result["msg"] = "ERROR - SERVER UNEXPECTED ERROR";
        $wan_final_result["code"] = 500;
        break;
}


$wan_final_result["powered_by"] = "LOVE";


echo json_encode($wan_final_result);
exit();

?>
