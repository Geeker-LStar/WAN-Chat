<!--新的朋友（验证）-->

<!DOCTYPE html>
<html>
    <head>
        <?php 
            session_start();
        ?>
        <?php require_once "../wan-config.php";?>
        <?php require_once "../wan-userinfo.php";?>    <!--用户个人信息-->
        
        <title>新的朋友 - WAN</title>
        <meta charset="utf-8">
    </head>
    
    <body>
        <div class="container p-5">
            <center><h1>新的朋友</h1></center><br>
            <center><p>显示您最近的 132 条新朋友验证消息</p></center>
        </div>
        <nav class="navbar navbar-expand-sm bg-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" id="reload_trigger" href="../wan-myself/myself.php" style="padding-left: 30px;">&larr;返回个人中心</a>
                </li>
            </ul>
        </nav><br>
        
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
        
        <!--查找并显示所有新朋友验证消息-->
        <?php
            // 将所有消息设置为已读
            $sql = "UPDATE Private_Verify SET isread='1' WHERE receiver='$wid' and isread='0'";
            $conn->query($sql);
            // 查找最近 132 条消息
            $sql = "SELECT * FROM Private_Verify WHERE receiver='{$wid}' ORDER BY id desc LIMIT 132";
            $result = $conn->query($sql);
            $all_vrf = array();
            if ($result->num_rows > 0)
            {   
                // 获取所有验证消息的 id，便于查询 Private_Verify 数据表
                while ($vrfid = $result->fetch_assoc()["id"])
                    array_push($all_vrf, $vrfid);    // 数组末尾追加
            }
            else
                echo "<span style='padding-left: 30px;'>您暂无新朋友验证消息。</span>";
            
            // 显示最近 132 条验证消息
            for ($i = 0; $i < count($all_vrf); $i++)
            {
                $sql = "SELECT * FROM Private_Verify WHERE id='{$all_vrf[$i]}'";
                $result = $conn->query($sql);
                $msg = $result->fetch_assoc()["vrfmsg"];    // 消息
                $sql = "SELECT * FROM Private_Verify WHERE id='{$all_vrf[$i]}'";
                $result = $conn->query($sql);
                $dtl = $result->fetch_assoc()["detail"];    // 消息
                $sql = "SELECT * FROM Private_Verify WHERE id='{$all_vrf[$i]}'";
                $result = $conn->query($sql);
                $sender = $result->fetch_assoc()["sender"];    // 发消息人
                $sql = "SELECT * FROM Private_Verify WHERE id='{$all_vrf[$i]}'";
                $result = $conn->query($sql);
                $state = $result->fetch_assoc()["state"];    // 消息状态
                $sql = "SELECT * FROM Private_Verify WHERE id='{$all_vrf[$i]}'";
                $result = $conn->query($sql);
                $kind = $result->fetch_assoc()["kind"];    // 消息种类
                $sql = "SELECT * FROM Private_Verify WHERE id='{$all_vrf[$i]}'";
                $result = $conn->query($sql);
                $msgtime = $result->fetch_assoc()["time"];    // 消息时间
                
                // 加群申请
                if ($kind == "add_friend")
                {
                    if ($state == "待确认")
                    {
                        echo "<span style='padding-left: 30px;'></span>" . $msg . "（" . $dtl . "）" . 
                            "<form method='post' action='friendsvrf.php' style='display: inline-block;'>
                                <input type='hidden' name='vrfid' value='$i'>
                                <input type='hidden' name='sender' value='$sender'>
                                <input type='hidden' name='vrfmsg' value='$msg'>
                                <input type='hidden' name='kind' value='$kind'>
                                <input type='submit' name='同意' value='同意' class='btn btn-success'>
                            </form>" . 
                            "    " . 
                            "<form method='post' action='friendsvrf.php' style='display: inline-block;'>
                                <input type='hidden' name='vrfid' value='$i'>
                                <input type='hidden' name='sender' value='$sender'>
                                <input type='hidden' name='vrfmsg' value='$msg'>
                                <input type='hidden' name='kind' value='$kind'>
                                <input type='submit' name='拒绝' value='拒绝' class='btn btn-danger'>
                            </form>" . 
                            "<span style='padding-left: 15px;'>（</span>" . $msgtime . "<span>）</span><br><br>";
                    }
                    if ($state == "已同意")
                        echo "<span style='padding-left: 30px;'></span>" . $msg . "<button class='btn btn-default'><span class='badge bg-primary'>已同意</span></button><span style='padding-left: 15px;'>（</span>" . $msgtime . "<span>）</span><br><br>";
                
                    if ($state == "已拒绝")
                        echo "<span style='padding-left: 30px;'></span>" . $msg . "<button class='btn btn-default'><span class='badge bg-secondary'>已拒绝</span></button><span style='padding-left: 15px;'>（</span>" . $msgtime . "<span>）</span><br><br>";
                }
                
                // 无需确认的消息
                if ($state == "无需确认")
                    echo "<span style='padding-left: 30px;'></span>" . $msg . "<span style='padding-left: 15px;'>（</span>" . $msgtime . "<span>）</span><br><br>";
            }
        ?>
        
        <!-- 事件处理 -->
        <?php
            if (isset($_POST["同意"]))
            {
                // 同意好友申请
                if ($_POST["kind"] == "add_friend")
                {   
                    $newfriend = $_POST["sender"];    // 申请加好友的用户
                    $vrfmsg = $_POST["vrfmsg"];
                    // 在该用户的好友列表中加入申请的用户
                    $new_frds = $myfrd . $newfriend . "//";
                    $sql = "UPDATE Users SET my_friends='{$new_frds}' WHERE wan_uid='{$wid}'";    // 更新
                    $conn->query($sql);
                    // 在申请的用户的好友列表中加入该用户
                    $sql = "SELECT * FROM Users WHERE wan_uid='{$newfriend}'";
                    $result = $conn->query($sql);
                    $ta_old_frds = $result->fetch_assoc()["my_friends"];
                    $ta_new_frds = $ta_old_frds . $wid . "//" ;
                    $sql = "UPDATE Users SET my_friends='{$ta_new_frds}' WHERE wan_uid='{$newfriend}'";
                    $conn->query($sql);
                    // 将该项（和所有与该项相同的项）设置为已同意并刷新页面，避免重复同意
                    $sql = "SELECT * FROM Private_Verify WHERE sender='{$newfriend}' and vrfmsg='{$vrfmsg}' and kind='add_friend' and state='待确认'";    // 所有这样的验证消息
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0)
                    {   
                        for ($i = 0; $i < $result->num_rows; $i++)
                        {   
                            $sql = "SELECT * FROM Private_Verify WHERE sender='{$newfriend}' and vrfmsg='{$vrfmsg}' and kind='add_friend' and state='待确认' ORDER BY id LIMIT 1";
                            $result1 = $conn->query($sql);
                            $each_msg = $result1->fetch_assoc()["id"];    // 某一条消息（id）
                            $sql = "UPDATE Private_Verify SET state='已同意' WHERE id='{$each_msg}'";
                            $conn->query($sql);
                        }
                    }
                    // 插入“已同意”消息
                    $time = date("Y-m-d H:i:s");
                    $back = "您已成为 “<a href=\"../wan-users/user.php?wid=$wid\">" . $shown . "</a>” 的好友！";
                    $sql = "INSERT INTO Private_Verify (sender, receiver, vrfmsg, state, kind, isread, time) VALUES ('{$wid}', '{$newfriend}', '{$back}', '无需确认', 'agree', '0', '{$time}')";
                    $conn->query($sql);
                    header("location: friendsvrf.php");
                    // 为两人新建私聊
                    $sql = "CREATE TABLE private_{$wid}_{$newfriend} ( ".
                            "msgid INT NOT NULL AUTO_INCREMENT, ".
                            "who VARCHAR(20) NOT NULL, ".
                            "time VARCHAR(30) NOT NULL, ".
                            "msg VARCHAR(1320725) NOT NULL, ".
                            "PRIMARY KEY (msgid))ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ";
                    $conn->query($sql);
                }
            }
            
            if (isset($_POST["拒绝"]))
            {
                // 拒绝好友申请
                if ($_POST["kind"] == "add_friend")
                {
                    $newfriend = $_POST["sender"];    // 申请加好友的用户
                    $vrfmsg = $_POST["vrfmsg"];
                    echo "<script>
                            cfm = window.confirm('真的要拒绝 TA 的好友申请吗？');
                            if (cfm)
                                window.location.href='friendsvrf.php?refuse=1&vrfid=$vrfid&newfriend=$newfriend&vrfmsg=$vrfmsg';    // 传值
                        </script>";
                }
            }
            
            if (isset($_GET["refuse"]))
            {
                $vrfid = $_GET["vrfid"];
                $newfriend = $_GET["newfriend"];
                $vrfmsg = $_GET["vrfmsg"];
                // 将该项（和所有与该项相同的项）设置为已拒绝并刷新页面，避免重复同意
                $sql = "SELECT * FROM Private_Verify WHERE sender='{$newfriend}' and vrfmsg='{$vrfmsg}' and kind='add_friend' and state='待确认'";    // 所有这样的验证消息
                $result = $conn->query($sql);
                if ($result->num_rows > 0)
                {   
                    for ($i = 0; $i < $result->num_rows; $i++)
                    {   
                        $sql = "SELECT * FROM Private_Verify WHERE sender='{$newfriend}' and vrfmsg='{$vrfmsg}' and kind='add_friend' and state='待确认' ORDER BY id LIMIT 1";
                        $result1 = $conn->query($sql);
                        $each_msg = $result1->fetch_assoc()["id"];    // 某一条消息（id）
                        $sql = "UPDATE Private_Verify SET state='已拒绝' WHERE id='{$each_msg}'";
                        $conn->query($sql);
                    }
                }
                // 插入“已拒绝”消息
                $time = date("Y-m-d H:i:s");
                $back = "“<a href=\"../wan-users/user.php?wid=$wid\">" . $shown . "</a>” 拒绝了您的好友申请。";
                $sql = "INSERT INTO Private_Verify (sender, receiver, vrfmsg, state, kind, isread, time) VALUES ('{$wid}', '{$newfriend}', '{$back}', '无需确认', 'refuse', '0', '{$time}')";
                $conn->query($sql);
                header("location: friendsvrf.php");
            }
        ?>
    </body>
</html>
