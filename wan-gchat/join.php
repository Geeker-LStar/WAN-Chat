<!--加入群聊-->

<!DOCTYPE html>
<html>
    <head>
        <?php 
            session_start();
        ?>
        <?php require_once "../wan-config.php";?>
        <?php require_once "../wan-userinfo.php";?>    <!--用户个人信息-->

        <title>加入群聊 - WAN</title>
        <meta charset="utf-8">
        <style>
            .must {
                color: red;
            }
        </style>
    </head>
    
    <body>
        <div class="container p-5">
        <center><h1>加入群聊</h1></center></div>
        <nav class="navbar navbar-expand-sm bg-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" id="reload_trigger" href="../wan-myself/myself.php" style="padding-left: 30px;">&larr;返回个人中心</a>
                </li>
            </ul>
        </nav><br>
        <form method="post" action="join.php">
            群 id：<input class="form-control" type="text" name="gid"><br><br>
            <button type="submit" name="搜索" value="搜索">搜索</button>
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
        
        <!--检索并显示群信息，询问用户是否申请加入-->
        <?php
            $gid = "";
            $iErr = "";
            
            if (isset($_POST["gid"]))
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
                                echo "<br><div class='alert alert-info'>您已经加入该群聊啦~";
                                echo "<br><br><a href='mine.php'>查看我已经加入的群聊</a></div>";
                                $already = 1;
                                break;
                            }
                        }
                        // 用户未在群聊中
                        if (!$already)
                        {
                            echo "<div class=<br>群信息如下：<br><br>";
                            echo "<li>群 id：" . $gid . "</li>";
                            echo "<br><li>群名称：" . $gn . "</li><br>";
                            // 隐藏传值（传 $gid）
                            echo "<form method='post' action='join.php'>
                                    <input type='hidden' name='gid' value='$gid'>    
                                    <input type='submit' name='申请加入' value='申请加入'>
                                </form>";
                        }
                    } 
                    // 群聊不存在
                    else
                        echo "<br>未找到 id 号为 " . $gid . " 的群聊！请检查输入是否正确！";
                }
            }
        ?>
        
        <!--申请加入（未完成）-->
        <?php
            if (isset($_POST["申请加入"]))
            {
                $gid = $_POST["gid"];    // 获取隐藏传来的值
                $sql = "SELECT * FROM All_Groups_Info WHERE wan_gid='{$gid}'";
                $result = $conn->query($sql);
                $g_mngs = $result->fetch_assoc()["g_managers"];    // 群管理
                $mngs_lst = explode("//", $g_mngs);    // 群管理列表
                $time = date("Y-m-d H:i:s");
                for ($i = 0; $i <= count($mngs_lst)-2; $i++)
                {
                    // 在 Group_Verify 中插入该消息的详细信息
                    $sql = "INSERT INTO Group_Verify (sender, receiver, vrfmsg, state, kind, isread, time, global) VALUES ('{$wid}', '{$mngs_lst[$i]}', '{$gid}', '待确认', 'join_group', '0', '{$time}', '0')";
                    $conn->query($sql);
                }
                echo "<script>
                        alert('申请成功，等待群管理者确认！');
                    </script>";
            }
        ?>
        </div>
    </body>
</html>
