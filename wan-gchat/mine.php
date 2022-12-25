<!--显示该用户已加入的群聊-->

<!DOCTYPE html>
<html>
    <head>
        <?php
            session_start();
        ?>
        <?php require_once "../wan-config.php";?>
        <?php require_once "../wan-userinfo.php";?>    <!--用户个人信息-->

        <title><?php echo $usern;?> 加入的群聊 - WAN</title>
        <meta charset="utf-8">
    </head>
    
    <body>
        <div class="container p-5"><center>
            <h1><?php echo $usern;?> 加入的群聊</h1></center>
        </div>
        
       <nav class="navbar navbar-expand-sm bg-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" id="reload_trigger" href="../wan-myself/myself.php" style="padding-left: 30px;">&larr;返回个人中心</a>
                </li>
            </ul>
        </nav>
        
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
            else
            {
                $sql = "SELECT * FROM Users WHERE wan_uid='{$wid}'";
                $result = $conn->query($sql);
                if ($result->num_rows == 0)
                {   
                    unset($_SESSION["wuid"]);
                    header("location: ../wan-login/signin.php");
                }
            }
        ?>
    
        <!--检索并显示该用户加入的群聊-->
        <?php
            $sql = "SELECT * FROM Users WHERE wan_uid='{$wid}'";
            $result = $conn->query($sql);
            $all_group = $result->fetch_assoc()["my_groups"];
            if (empty($all_group))
                echo "您暂未加入任何群聊。";
            else
            {   
                echo "<br>";
                $g_arr = explode("//", $all_group);    // 拆分字符串 m_g，获取用户所在的每个群的 gid
                $g_arr_lens = count($g_arr);    // 数组长度（元素个数，包含最后那个空元素）
                for ($i = $g_arr_lens-2; $i >= 0; $i--)    // -2 的原因：一是因为数组索引比元素个数小 1，二是因为要排除数组最后那个空元素
                {
                    $sql = "SELECT * FROM All_Groups_Info WHERE wan_gid='{$g_arr[$i]}'";
                    $result = $conn->query($sql);
                    $arr = $result->fetch_assoc();
                    $gid = $arr["wan_gid"];    // 群聊 id
                    $gn = $arr["g_name"];    // 群聊名称
                    // 表单设置为行内块，和退出按钮在同一行显示，且点击退出按钮时不会跳转到 chatroom.php
                    echo "<span style='padding-left: 30px;'></span>
                        <form method='post' action='chatroom.php' style='display: inline-block;'>
                            <button type='submit' class='btn btn-outline-info' name='gid' value='$g_arr[$i]'>进入群聊</button>
                        </form>
                        <span style='padding-left: 30px;'>$gn</span>
                        <span style='padding-left: 30px;'></span>
                        <form method='post' action='detail.php' style='display: inline-block'>
                            <input type='hidden' name='gid' value='$gid'>
                            <input type='submit' name='详细信息' value='详细信息' class='btn btn-warning'>
                        </form><br><br>";
                }
            }
        ?>
        <?php require "../wan-footer.php";?>

    </body>
</html>
