<!--加入群聊-->

<!DOCTYPE html>
<?php 
    session_start();
?>
<?php require "../wan-config.php";?>
<?php require "../wan-userinfo.php";?>    <!--用户个人信息-->

<html>
    <head>
        <title>加入群聊</title>
        <meta charset="utf-8">
        <style>
            .must {
                color: red;
            }
        </style>
    </head>
    
    <body>
        <h1>加入群聊</h1>
        <a href="../wan-myself/myself.php">返回个人中心</a><br><br>
        <form method="post" action="join.php">
            群 id：<input type="text" name="gid"><br><br>
            <input type="submit" name="搜索" value="搜索">
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
        
        <!--检索并显示群信息，询问用户是否确认加入-->
        <?php
            $gid = "";
            $iErr = "";
            
            if (isset($_POST["搜索"]))
            {
                if (empty($_POST["gid"]))
                    $iErr = "填写群 id 才可以搜索呀~";
                else
                {
                    $gid = $_POST["gid"];
                    // 检索数据库
                    $sql = "SELECT * FROM All_Groups_Info WHERE wan_gid='{$gid}'";
                    $result = $conn->query($sql);
                    // 群聊存在
                    if ($result->num_rows > 0)
                    {
                        $gn = $result->fetch_assoc()["g_name"];    // 群名称
                        $sql = "SELECT * FROM All_Groups_Info WHERE wan_gid='{$gid}'";
                        $result = $conn->query($sql);
                        $g_mem = $result->fetch_assoc()["g_members"];
                        $members = explode("//", $g_mem);
                        $lens = count($members);
                        for ($i = 0; $i < $lens-1; $i++)
                        {
                            // 用户已在群聊中
                            if ($members[$i] == $wid)
                            {
                                echo "<br>您已经加入该群聊啦~";
                                echo "<br><br><a href='mine.php'>查看我已经加入的群聊</a>";
                                $already = 1;
                                break;
                            }
                        }
                        // 用户未在群聊中
                        if (!$already)
                        {
                            echo "<br>群信息如下：<br><br>";
                            echo "<li>群 id：" . $gid . "</li>";
                            echo "<br><li>群名称：" . $gn . "</li><br>";
                            // 隐藏传值（传 $gid）
                            echo "<form method='post' action='join.php'>
                                    <input type='hidden' name='gid' value='$gid'>    
                                    <input type='submit' name='确认加入' value='确认加入'>
                                </form>";
                        }
                    } 
                    // 群聊不存在
                    else
                        echo "<br>未找到 id 号为 " . $gid . " 的群聊！请检查输入是否正确！";
                }
            }
        ?>
        
        <!--如果用户确认加入-->
        <?php
            if (isset($_POST["确认加入"]))
            {
                $gid = $_POST["gid"];    // 获取隐藏传来的值
                // 在该群聊信息的 “用户” 一项中加入该用户
                $sql = "SELECT * FROM All_Groups_Info WHERE wan_gid='{$gid}'";
                $result = $conn->query($sql);
                $old_gm = $result->fetch_assoc()["g_members"];    // 之前的用户名单
                $new_g_members = $old_gm . $wid . "//";
                $sql = "UPDATE All_Groups_Info SET g_members='{$new_g_members}' WHERE wan_gid='{$gid}'";    // 更新
                $conn->query($sql);
                // 在该用户的【个人信息——加入的群聊】中加入该群聊
                $new_g = $mygrp . $gid . "//" ;
                $sql = "UPDATE Users SET my_groups='{$new_g}' WHERE wan_uid='{$wid}'";
                $conn->query($sql);
                header("location: mine.php");
            }
        ?>
    </body>
</html>
