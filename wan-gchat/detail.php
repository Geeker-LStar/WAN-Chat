<!--群聊详细信息-->

<!DOCTYPE html>
<html>
    <head>
        <?php
            $time_limit = 60*60*24;
            session_set_cookie_params($time_limit);
            session_start();
        ?>
        <?php require_once "../wan-config.php";?>
        <?php require_once "../wan-userinfo.php";?>    <!--用户个人信息-->
        
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
        
        <!--显示详细的群信息-->
        <?php
            if (isset($_POST["详细信息"]))
                $_SESSION["gid"] = $_POST["gid"];
            
            if (!isset($_SESSION["gid"]))
                 header("location: mine.php");
        ?>
        
        <?php
            $sql = "SELECT * FROM All_Groups_Info WHERE wan_gid='{$_SESSION['gid']}'";
            $result = $conn->query($sql);
            $detail_ar = $result->fetch_assoc();
            // 详细信息
            $gn = $detail_ar["g_name"];    // 群名
            $gms = $detail_ar["g_members"];
            $gmems = explode("//", $gms);    // 群成员
            $gmst = $detail_ar["g_master"];    // 群主
            $gmngs = $detail_ar["g_managers"];
            $mngs = explode("//", $gmngs);    // 群管理
            $gitd = $detail_ar["g_introd"];    // 群介绍
            $gtime = $detail_ar["g_time"];    // 成立时间
        ?>
        
        <title>群聊 <?php echo($gn);?> 的详细信息 - WAN</title>
        <div class="container p-5">
            <center><h1><?php echo($gn);?></h1><br>
            <p>群聊详细信息</p></center>
        </div>
        
        <nav class="navbar navbar-expand-sm bg-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" id="reload_trigger" href="../wan-gchat/mine.php" style="padding-left: 30px;">&larr;返回群聊列表</a>
                </li>
            </ul>
        </nav><br>
        
        <!--转让群聊（在详细信息前边显示）-->
        <?php
            if (isset($_POST["transfer"]))
            {
                echo "<center>
                        <div class='col-sm-11 col-md-10 col-lg-8 col-xl-7 col-xxl: 6'>    <!--自适应布局-->
                            <div class='alert alert-info alert-dismissible fade show'>
                                <button class='btn-close' data-bs-dismiss='alert'></button>
                                <strong>操作：</strong>请选择新群主
                            </div>
                        </div>";
                // 选择新群主
                echo "<br><form method='get' action='detail.php'><select name='new_gmst' style='height: 50px; width: 400px; text-align: center;'>";
                echo "<option disabled selected hidden>——请选择——</option>";    // 默认文字（不显示）
                for ($i = 1; $i < count($gmems)-1; $i++)
                {
                    $sql = "SELECT * FROM Users WHERE wan_uid='{$gmems[$i]}'";
                    $result = $conn->query($sql);
                    $sn = $result->fetch_assoc()["showname"];
                    echo "<option value='{$gmems[$i]}'>$sn</option>";
                }
                echo "</select><br><br><input type='submit' name='tsf' value='确认转让' class='btn btn-info'></form></center><br><hr>";
                // echo ""
            }
        ?>
        
        <!--转让群聊选择了新群主后-->
        <?php
            if (isset($_GET["tsf"]))
            {
                if (isset($_GET["new_gmst"]))
                {
                    // 将数据表中的 g_master 改为新群主
                    $ngmst = $_GET["new_gmst"];
                    $sql = "UPDATE All_Groups_Info SET g_master='{$ngmst}' WHERE wan_gid='{$_SESSION['gid']}'";
                    $conn->query($sql);
                    // 调整 g_managers 的顺序
                    // 新群主原先不在管理员列表
                    if (array_search($ngmst, $mngs) === false)
                    {
                        // 将新群主添加到管理员列表开头
                        array_splice($mngs, 0, 0, $ngmst);    // 第一个参数为指定的数组，第二个参数规定删除元素的开始位置，第三个参数为被移除的元素个数，第四个参数为插入的元素（字符串或数组）。
                        // 拼接成新的字符串并写入数据库
                        for ($i = 0; $i < count($mngs)-1; $i++)
                            $new_mngs = $new_mngs . $mngs[$i] . "//";
                        $sql = "UPDATE All_Groups_Info SET g_managers='{$new_mngs}' WHERE wan_gid='{$_SESSION['gid']}'";
                        $conn->query($sql);
                    }
                    // 新群主原先就在
                    else
                    {
                        $old_index = array_search($ngmst, $mngs);    // 新群主原来所在的索引
                        // 三步交换
                        $temp = $mngs[0];
                        $mngs[0] = $ngmst;
                        $mngs[$old_index] = $temp;
                        // 拼接成新的字符串并写入数据库
                        for ($i = 0; $i < count($mngs)-1; $i++)
                            $new_mngs = $new_mngs . $mngs[$i] . "//";
                        $sql = "UPDATE All_Groups_Info SET g_managers='{$new_mngs}' WHERE wan_gid='{$_SESSION['gid']}'";
                        $conn->query($sql);
                    }
                    // 调整 g_members 的顺序
                    $old_index = array_search($ngmst, $gmems);    // 新群主原来所在的索引
                    // 三步交换
                    $temp = $gmems[0];
                    $gmems[0] = $ngmst;
                    $gmems[$old_index] = $temp;
                    // 拼接成新的字符串并写入数据库
                    for ($i = 0; $i < count($gmems)-1; $i++)
                        $new_mems = $new_mems . $gmems[$i] . "//";
                    $sql = "UPDATE All_Groups_Info SET g_members='{$new_mems}' WHERE wan_gid='{$_SESSION['gid']}'";
                    $conn->query($sql);
                    // 通知新群主
                    // 在 Group_Verify 中插入该消息的详细信息
                    $time = date("Y-m-d H:i:s");
                    $inform = "您已成为群聊 “" . $gn . "” 的新群主。";
                    $sql = "INSERT INTO Group_Verify (sender, receiver, vrfmsg, state, kind, isread, time, global) VALUES ('{$wid}', '{$ngmst}', '{$inform}', '无需确认', 'transfer_group', '0', '{$time}', '0')";
                    $conn->query($sql);
                    // 群聊聊天记录中插入 “xx 成为新群主” 的通知
                    $sql = "SELECT * FROM Users WHERE wan_uid='{$ngmst}'";
                    $result = $conn->query($sql);
                    $ngmst_name = $result->fetch_assoc()["showname"];
                    $ginform = "@" . $ngmst_name . " 已成为新群主。";
                    $sql = "INSERT INTO group_{$_SESSION['gid']} (who, msg, time) VALUES ('WAN-Bot', '{$ginform}', '{$time}')";
                    $conn->query($sql);
                    header("location: detail.php");
                }
                // 未选择新群主
                else
                    echo "<script>
                            alert('转让失败！失败原因：未选择新群主。');
                        </script>";
            }
        ?>
        
        <!--群聊详细信息-->
        <center>
            <p style="padding-bottom: 10px;">群 id：<?php echo($_SESSION["gid"]);?></p>
            <p style="padding-bottom: 10px;">群名称：<?php echo($gn);?></p>
            <p style="padding-bottom: 10px;">群主：
                <?php 
                    $sql = "SELECT * FROM Users WHERE wan_uid='{$gmst}'";
                    $result = $conn->query($sql);
                    $gm_shown = $result->fetch_assoc()["showname"];
                    echo $gm_shown;
                ?>
            </p>
            <p style="padding-bottom: 10px;">群管理员：
                <!-- <-2 原因：避免多 1 的索引、去掉最后一个空元素、去掉倒数第二个元素（那个元素后面没有顿号，所以单独输出），下同-->
                <?php 
                    for($i = 0; $i < count($mngs)-2; $i++)
                    {
                        $sql = "SELECT * FROM Users WHERE wan_uid='{$mngs[$i]}'";
                        $result = $conn->query($sql);
                        $gm_shown = $result->fetch_assoc()["showname"];
                        echo $gm_shown . "、"; 
                    }
                    $sql = "SELECT * FROM Users WHERE wan_uid='{$mngs[count($mngs)-2]}'";
                    $result = $conn->query($sql);
                    $gm_shown = $result->fetch_assoc()["showname"];
                    echo $gm_shown;
                ?>
            </p>
            <p style="padding-bottom: 10px;">群成员：
                <?php 
                    for($i = 0; $i < count($gmems)-2; $i++)
                    {
                        $sql = "SELECT * FROM Users WHERE wan_uid='{$gmems[$i]}'";
                        $result = $conn->query($sql);
                        $gm_shown = $result->fetch_assoc()["showname"];
                        echo $gm_shown . "、"; 
                    }
                    $sql = "SELECT * FROM Users WHERE wan_uid='{$gmems[count($gmems)-2]}'";
                    $result = $conn->query($sql);
                    $gm_shown = $result->fetch_assoc()["showname"];
                    echo $gm_shown;
                ?>
            </p>
            <p style="padding-bottom: 10px;">群介绍：<?php echo($gitd);?></p>
            <p style="padding-bottom: 10px;">群成立时间：<?php echo($gtime);?></p>
        </center>
        
        <!--操作权限（按钮）-->
        <?php
            // 群主
            if ($wid == $gmst)
                
                echo "<center><form method='post' action='chatroom.php' style='display: inline-block;'>
                        <button type='submit' class='btn btn-outline-info' name='gid' value='{$_SESSION['gid']}'>进入群聊</button>
                    </form><br><br>
                    <form method='post' action='detail.php' style='display: inline-block'>
                        <input type='submit' name='exit' value='退出该群' class='btn btn-warning' style='margin: 0 20px;'>
                    </form>
                    <form method='post' action='detail.php' style='display: inline-block'>
                        <input type='submit' name='transfer' value='转让该群' class='btn btn-primary' style='margin: 0 20px;'>
                    </form>
                    <form method='post' action='detail.php' style='display: inline-block'>
                        <input type='submit' name='dissolve' value='解散该群' class='btn btn-danger' style='margin: 0 20px;'>
                    </form>
                    </center><br><br>";

            // 其他群成员
            else
                echo "<center><form method='post' action='chatroom.php' style='display: inline-block;'>
                        <button type='submit' class='btn btn-outline-info' name='gid' value='{$_SESSION['gid']}'>进入群聊</button>
                    </form><br><br>
                    <form method='post' action='detail.php' style='display: inline-block'>
                            <input type='submit' name='exit' value='退出该群' class='btn btn-warning' style='margin: 0 20px;'>
                        </form>
                    </center><br><br>";
        ?>

        <!--退出群聊-->
        <?php
            // 用户点击了 “退出”
            if (isset($_POST["exit"]))
            {
                // 判断是否为群主
                if ($wid == $gmst)
                    echo "<script>
                            alert('您是群主，暂不支持退出群聊！敬请期待群转让功能！');
                        </script>";
                else
                    // 二次确认是否退出
                    echo "<script>
                            var cfm = window.confirm('您确定要退出该群聊吗？');
                            if (cfm)
                                window.location.href='detail.php?exit=1&gid={$_SESSION['gid']}&gn=$gn';    // 传值
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

    </body>
</html>
