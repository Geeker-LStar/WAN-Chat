<!--显示该用户的好友-->

<!DOCTYPE html>
<html>
    <head>
        <?php
            session_start();
        ?>
        <?php require_once "../wan-config.php";?>
        <?php require_once "../wan-userinfo.php";?>    <!--用户个人信息-->

        <title><?php echo $shown;?>的好友 - WAN</title>
        <meta charset="utf-8">
    </head>
    
    <body>
        <div class="container p-5"><center>
            <h1><?php echo $shown;?>的好友</h1></center>
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
        
        <!--检索并显示该用户的好友-->
        <?php
            if (empty($myfrd))
                echo "<span style='padding-left: 30px;'>您暂无好友。</span>";
            else
            {   
                echo "<br>";
                $f_arr = explode("//", $myfrd);    // 拆分字符串 $myfrd，获取用户的好友列表
                $f_arr_lens = count($f_arr);    // 数组长度（元素个数，包含最后那个空元素）
                for ($i = $f_arr_lens-2; $i >= 1; $i--)    // -2 的原因：一是因为数组索引比元素个数小 1，二是因为要排除数组最后那个空元素
                {
                    $sql = "SELECT * FROM Users WHERE wan_uid='{$f_arr[$i]}'";
                    $result = $conn->query($sql);
                    $arr = $result->fetch_assoc();
                    $fn = $arr["showname"];    // 好友显示名
                    // 表单设置为行内块，和其他按钮在同一行显示，且点击其他按钮时不会跳转到 chat.php
                    echo "<span style='padding-left: 30px;'></span>
                        <form method='get' action='chat.php' style='display: inline-block;' target='_blank'>
                            <button type='submit' class='btn btn-outline-info' name='ta_wid' value='$f_arr[$i]'>进入聊天</button>
                        </form>
                        <span style='padding-left: 30px;'>$fn</span>
                        <span style='padding-left: 30px;'></span>
                        <button type='button' class='btn btn-warning' onclick='window.open(\"withta.php?ta_wid=$f_arr[$i]\")'>和 TA 的专属页面</button><br><br>";
                }
                // 自己和自己不需要专属页面，所以单独写，而不用循环
                echo "<span style='padding-left: 30px;'></span>
                        <form method='get' action='chat.php' style='display: inline-block;' target='_blank'>
                            <button type='submit' class='btn btn-outline-info' name='ta_wid' value='$wid'>进入聊天</button>
                        </form><span style='padding-left: 30px;'>$shown</span>";
            }
        ?>
        <?php require "../wan-footer.php";?>
        
    </body>
</html>
