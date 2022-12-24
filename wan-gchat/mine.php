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
                    echo "<form method='post' action='chatroom.php' style='display: inline-block;'>
                            <li style='padding-left: 30px'>
                                <button type='submit' class='btn btn-primary' name='gid' value='$g_arr[$i]'>$g_arr[$i]</button>
                                <span style='padding-left: 30px;'>$gn</span>
                            </li>
                        </form>
                        <span style='padding-left: 30px;'>
                            <form method='post' action='mine.php' style='display: inline-block'>
                                <input type='hidden' name='gid' value='$g_arr[$i]'>
                                <input type='hidden' name='gn' value='$gn'>
                                <input type='submit' name='exit' value='退出该群' class='btn btn-danger'>
                            </form>
                        </span>
                        <span style='padding-left: 30px;'>
                            <form method='post' action='detail.php' style='display: inline-block'>
                                <input type='hidden' name='gid' value='$gid'>
                                <input type='submit' name='详细信息' value='详细信息' class='btn btn-success'>
                            </form>
                        </span><br><br>";
                }
            }
        ?>
        
        <!--退出群聊-->
        <?php
            // 用户点击了 “退出”
            if (isset($_POST["exit"]))
            {
                $exit_gid = $_POST["gid"];
                $exit_gn = $_POST["gn"];
                // 判断是否为群主
                $sql = "SELECT * FROM All_Groups_Info WHERE wan_gid='{$exit_gid}'";
                $result = $conn->query($sql);
                $gmstr = $result->fetch_assoc()["g_master"];    // 获取群主
                if ($wid == $gmstr)
                    echo "<script>
                            alert('您是群主，暂不支持退出群聊！敬请期待群转让功能！');
                        </script>";
                else
                    // 二次确认是否退出
                    echo "<script>
                            var cfm = window.confirm('您确定要退出该群聊吗？');
                            if (cfm)
                                window.location.href='mine.php?exit=1&gid=$exit_gid&gn=$exit_gn';    // 传值
                        </script>";

            }
            // 用户确定退出
            if (isset($_GET["exit"]))
            {
                $egid = $_GET["gid"];    // 获取退出的群聊的群号
                $egn = $_GET["gn"];    // 退出群聊的群名称
                // 在群聊信息的 “用户” 一项中删除该用户
                $sql = "SELECT * FROM All_Groups_Info WHERE wan_gid='{$egid}'";
                $result = $conn->query($sql);
                $gm = $result->fetch_assoc()["g_members"];    // 之前的用户数组
                $members = explode("//", $gm);
                $u_index = array_search($wid, $members);    // 获得该用户的 uid 在数组中的索引（用于下一步释放该索引的元素）
                unset($members[$u_index]);    // 删除该成员
                for ($i = 0; $i < count($members)-1; $i++)
                    $new_gm = $new_gm . $members[$i] . "//";    // 新的用户数组
                $sql = "UPDATE All_Groups_Info SET g_members='{$new_gm}' WHERE wan_gid='{$egid}'";    // 更新数据库
                $conn->query($sql);
                // 在该用户的【个人信息——加入的群聊】中删除该群聊
                $sql = "SELECT * FROM Users WHERE wan_uid='{$wid}'";
                $result = $conn->query($sql);
                $userg = $result->fetch_assoc()["my_groups"];
                $groups = explode("//", $userg);
                $g_index = array_search($egid, $groups);
                unset($groups[$g_index]);    // 删除该群聊
                for ($i = 0; $i < count($groups)-1; $i++)
                    $new_grps = $new_grps . $groups[$i] . "//";
                $sql = "UPDATE Users SET my_groups='{$new_grps}' WHERE wan_uid='{$wid}'";    // 更新数据库
                $conn->query($sql);
                // 通知群管理员该成员退出了
                $inform = $shown . " 退出了群聊 “" . $egn . "”。";
                $sql = "SELECT * FROM All_Groups_Info WHERE wan_gid='{$egid}'";
                $result = $conn->query($sql);
                $g_mngs = $result->fetch_assoc()["g_managers"];    // 群管理
                $mngs_lst = explode("//", $g_mngs);    // 群管理列表
                $time = date("Y-m-d H:i:s");
                for ($i = 0; $i <= count($mngs_lst)-2; $i++)
                {
                    // 在 Group_Verify 中插入该消息的详细信息
                    $sql = "INSERT INTO Group_Verify (sender, receiver, vrfmsg, state, kind, isread, time, global) VALUES ('{$wid}', '{$mngs_lst[$i]}', '{$inform}', '无需确认', 'exit_group', '0', '{$time}', '0')";
                    $conn->query($sql);
                }
                // 通知自己
                $inform2 = "您退出了群聊 “" . $egn . "”。";
                $sql = "INSERT INTO Group_Verify (sender, receiver, vrfmsg, state, kind, isread, time, global) VALUES ('{$wid}', '{$wid}', '{$inform2}', '无需确认', 'exit_group', '0', '{$time}', '0')";
                    $conn->query($sql);
                header("location: mine.php");
                echo "<script>
                        alert('退出成功！');
                    </script>";
            }
        ?>
        
        <?php require "../wan-footer.php";?>

    </body>
</html>
