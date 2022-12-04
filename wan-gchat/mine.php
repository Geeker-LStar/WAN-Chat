<!--显示该用户已加入的群聊-->

<!DOCTYPE html>
<?php
    session_start();
?>
<?php require "../wan-config.php";?>
<?php require "../wan-userinfo.php";?>    <!--用户个人信息-->

<html>
    <head>
        <title><?php echo $usern;?> 加入的群聊</title>
        <meta charset="utf-8">
    </head>
    
    <body>
        <h1><?php echo $usern;?> 加入的群聊</h1>
        <a href="../wan-myself/myself.php">返回个人中心</a><br><br>
        
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
        
        <!--检索并显示该用户加入的群聊-->
        <?php
            $sql = "SELECT * FROM Users WHERE wan_uid='{$wid}'";
            $result = $conn->query($sql);
            $all_group = $result->fetch_assoc()["my_groups"];
            if (empty($all_group))
                echo "您暂未加入任何群聊。";
            else
            {
                $g_arr = explode("//", $all_group);    // 拆分字符串 m_g，获取用户所在的每个群的 gid
                $g_arr_lens = count($g_arr);    // 数组长度（元素个数，包含最后那个空元素）
                for ($i = $g_arr_lens-2; $i >= 0; $i--)    // -2 的原因：一是因为数组索引比元素个数小 1，二是因为要排除数组最后那个空元素
                {
                    $sql = "SELECT * FROM All_Groups_Info WHERE wan_gid={$g_arr[$i]}";
                    $result = $conn->query($sql);
                    $gn = $result->fetch_assoc()["g_name"];    // 群聊名称
                    // echo "<li><a href='chatroom.php'>$g_arr[$i]</a><span style='margin-left: 30px;'>$gn</span></li><br>";
                    echo "<form method='get' action='chatroom.php'>
                            <li><input type='submit' name='gid' value='$g_arr[$i]'><span style='margin-left: 30px;'>$gn</span></li>
                        </form><br>";
                }
            }
            
        ?>

    </body>
</html>
