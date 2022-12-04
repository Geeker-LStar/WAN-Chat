<!--新建群聊-->

<!DOCTYPE html>
<?php
    session_start();
?>
<?php require "../wan-config.php";?>
<?php require "../wan-userinfo.php";?>    <!--用户个人信息-->

<html>
    <head>
        <title>新建群聊</title>
        <meta charset="utf-8">
        <style>
            .must {
                color: red;
            }
        </style>
    </head>
    
    <body>
        <h1>新建群聊</h1>
        <a href="../wan-myself/myself.php">返回个人中心</a><br><br>
        <p class="must">以下均为必填字段</p>
        <form method="post" action="create.php">
            群聊名称：<input type="text" name="gname"><span class="must"> <?php echo $nErr;?></span><br><br>
            群聊介绍：<textarea type="text" name="gintrod" style="height: 200px; width: 300px; line-height: 1.5;"></textarea><span class="must"> <?php echo $iErr;?></span><br><br>
            <input type="submit" name="确认新建" value="确认新建">
        </form>
        
        <!--检测是否登录，未登录则要求登录-->
        <?php
            if (!isset($_SESSION["wuid"]))
                header("location: ../wan-login/signin.php");
        ?>
        
        <!--连接数据库-->
        <?php
            // 创建连接
            $conn = new mysqli($servername, $username, $password, $dbname);
            // 检测连接
            if ($conn->connect_error)
                die("连接失败：" . $conn->connect_error);
        ?>
        
        <!--函数-->
        <?php
            // 数据过滤函数
            function test_input($data)
            {
                $data = trim($data);
                $data = stripslashes($data);
                $data = htmlspecialchars($data);
                return $data;
            }
        ?>
        
        <!--检测输入数据并写入数据库-->
        <?php
            $flag = 0;
            $gname = $gintrod = "";
            $nErr = $iErr = "";
            
            if (isset($_POST["确认新建"]))
            {
                // 群聊名称
                if (empty($_POST["gname"]))
                    $nErr = "群聊名称不能为空哦~";
                else
                {
                    $gname = test_input($_POST["gname"]);
                    if (strlen($gname) > 30)
                        $nErr = "群聊名称不能多于 30 字符哦~";
                    else
                    {
                        // if (!preg_match("[@_!#$%^&*()<>?/|}{~:]", $gname))    // 此处不能加 ^ 和 $
                        //     $nErr = "群聊名称不能包含特殊字符哦~";
                        // else
                            $flag++;
                    }
                }
                
                // 群聊介绍
                if (empty($_POST["gintrod"]))
                    $iErr = "群聊介绍是必填项哦~";
                else
                {
                    $gintrod = test_input($_POST["gintrod"]);
                    $flag++;
                }
                    
                    
                // 所有数据验证成功
                if ($flag == 2)
                {
                    $gid = mt_rand(100000, 1000000000);
                    $sql = "SELECT * FROM All_Groups_Info WHERE wan_gid='{$gid}'";
                    $result = $conn->query($sql);
                    // 如果 gid 重复了，则换一个新的
                    if ($result->fetch_assoc() > 0)
                        $gid = mt_rand(100000, 1000000000);
                    $time = date("Y-m-d H:i:s");
                    // 将该群聊的信息加入 “所有群聊信息” 这个数据表
                    $sql = "INSERT INTO All_Groups_Info (wan_gid, g_name, g_members, g_managers, g_introd, g_state, g_time) VALUES ('{$gid}','{$gname}','{$wid}//','{$wid}//', '{$gintrod}','1','{$time}')";
                    // 为该群聊新建一个数据表
                    $sql2 = "CREATE TABLE group_{$gid} ( ".
                            "msgid INT NOT NULL AUTO_INCREMENT, ".
                            "who VARCHAR(20) NOT NULL, ".
                            "time VARCHAR(30) NOT NULL, ".
                            "msg VARCHAR(1320725) NOT NULL, ".
                            "PRIMARY KEY (msgid))ENGINE=InnoDB DEFAULT CHARSET=utf8; ";
                    $conn->query($sql2);
                    // 在群聊创建者的【个人信息——加入的群聊】中加入（追加）该群聊
                        // 此处逻辑：【个人信息——加入的群聊】用 # 分割每个群聊
                    $new_g = $mygrp . $gid . "//" ;
                    $sql3 = "UPDATE Users SET my_groups='{$new_g}' WHERE wan_uid='{$wid}'";
                    $conn->query($sql3);
                    
                    if ($conn->query($sql)==TRUE)
                    {
                        $conn->close();
                        sleep(0.5);
                        header("location: ../wan-gchat/mine.php");
                    }
                    
                    else
                    {
                        $conn->close();
                        header("location: ../wan-static/html/failed.html");
                    }
                }
            }
        ?>
    </body>
</html>
