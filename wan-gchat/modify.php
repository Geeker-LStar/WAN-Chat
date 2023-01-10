<!--修改群信息（群主/管理员）-->

<!DOCTYPE html>
<html>
    <head>
        <?php
            $time_limit = 60*30;    // 过期时间为三十分钟
            session_set_cookie_params($time_limit);
            session_start();
        ?>
        <?php require_once "../wan-config.php";?>
        <?php require_once "../wan-userinfo.php";?>    <!--用户个人信息-->
        
        <title>修改群信息 - WAN</title>
        <meta charset="utf-8">
    </head>
    
    <body>
        <!--连接数据库-->
        <?php
            // 创建连接
            $conn = new mysqli($servername, $username, $password, $dbname);
            // 检测连接
            if ($conn->connect_error)
                die("连接失败：" . $conn->connect_error);
        ?>
        
        <!--检测是否登录，未登录则要求登录-->
        <?php
            if (!isset($_SESSION["wuid"]))
                header("location: ../wan-login/signin.php");
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
        
        <!--访问限制-->
        <?php
            if (isset($_POST["modify"]))
                $_SESSION["gid"] = $_POST["gid"];
            if (!isset($_SESSION["gid"]))
                header("location: mine.php");
            else {
                $sql = "SELECT * FROM All_Groups_Info WHERE wan_gid='{$_SESSION['gid']}'";
                $result = $conn->query($sql);
                $arr = $result->fetch_assoc();
                $gn = $arr["g_name"];
                $gitd = $arr["g_introd"];
            }
        ?>
        
        <?php
            if (isset($_GET["modify"]))
            {
                $newgn = $_GET["newgn"];
                $newgitd = $_GET["newgitd"];
                $flag = 0;
                $nErr = $iErr = "";
            
                // 群聊名称
                if (empty($newgn))
                    $nErr = "群聊名称不能为空哦~";
                else
                {
                    $newgn = test_input($newgn);
                    if (strlen($newgn) > 30)
                        $nErr = "群聊名称不能多于 30 字符哦~";
                    else
                    {
                        // if (!preg_match("[@_!#$%^&*()<>?/|}{~:]", $newgn))    // 此处不能加 ^ 和 $
                        //     $nErr = "群聊名称不能包含特殊字符哦~";
                        // else
                            $flag++;
                    }
                }
                
                // 群聊介绍
                if (empty($newgitd))
                    $iErr = "群聊介绍是必填项哦~";
                else
                {
                    $newgitd = test_input($newgitd);
                    $flag++;
                }
  
                // 所有数据验证成功
                if ($flag == 2)
                {
                    $sql = "UPDATE All_Groups_Info SET g_name='{$newgn}', g_introd='{$newgitd}' WHERE wan_gid='{$_SESSION['gid']}'";
                    $conn->query($sql);
                    echo "<script>alert('更新成功！');parent.location.href='modify.php'</script>";
                }
            }
        ?>

        <div class="container p-5">
            <center><h1>修改群信息</h1><br>
            <p>修改群聊 <?php echo $gn;?> 的信息</p></center>
        </div>
        <nav class="navbar navbar-expand-sm bg-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="mine.php" style="padding-left: 30px;">&larr;返回群聊列表</a>
                </li>
            </ul>
        </nav>
        <br>
        <center><form method="get" action="modify.php">
            群名称：<input type="text" name="newgn" value="<?php echo $gn;?>"><br><br>
            群介绍：<textarea type="text" name="newgitd" class="form-control" style="height: 200px; width: 300px; line-height: 1.5;"><?php echo $gitd;?></textarea><br><br>
            <button type="submit" class="btn btn-primary" name="modify" value="1">确认修改</button></center>
        </form></center>
    </body>
</html>
