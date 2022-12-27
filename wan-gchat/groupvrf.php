<!--处理验证消息-->

<!DOCTYPE html>
<html>
    <head>
        <?php 
                session_start();
        ?>
        <?php require_once "../wan-config.php";?>
        <?php require_once "../wan-userinfo.php";?>    <!--用户个人信息-->
        
        <title>群验证消息 - WAN</title>
        <meta charset="utf-8">
    </head>
    
    <body>
        <div class="container p-5">
            <center><h1>群验证消息</h1></center><br>
            <center><p>显示您最近的 132 条群验证消息</p></center>
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
        
        <!--查找并显示所有群验证消息-->
        <?php
            // 将所有消息设置为已读
            $sql = "UPDATE Group_Verify SET isread='1' WHERE receiver='$wid' and isread='0'";
            $conn->query($sql);
            // 查找最近 132 条消息
            $sql = "SELECT * FROM Group_Verify WHERE receiver='{$wid}' ORDER BY id desc LIMIT 132";
            $result = $conn->query($sql);
            $all_vrf = array();
            if ($result->num_rows > 0)
            {   
                // 获取所有验证消息的 id，便于查询 Group_Verify 数据表
                while ($vrfid = $result->fetch_assoc()["id"])
                    array_push($all_vrf, $vrfid);    // 数组末尾追加
            }
            else
                echo "<span style='padding-left: 30px;'>您暂无验证消息。</span>";
            
            // 显示最近 132 条验证消息
            for ($i = 0; $i < count($all_vrf); $i++)
            {
                $sql = "SELECT * FROM Group_Verify WHERE id='{$all_vrf[$i]}'";
                $result = $conn->query($sql);
                $msg = $result->fetch_assoc()["vrfmsg"];    // 消息
                $sql = "SELECT * FROM Group_Verify WHERE id='{$all_vrf[$i]}'";
                $result = $conn->query($sql);
                $sender = $result->fetch_assoc()["sender"];    // 发消息人
                $sql = "SELECT * FROM Group_Verify WHERE id='{$all_vrf[$i]}'";
                $result = $conn->query($sql);
                $state = $result->fetch_assoc()["state"];    // 消息状态
                $sql = "SELECT * FROM Group_Verify WHERE id='{$all_vrf[$i]}'";
                $result = $conn->query($sql);
                $kind = $result->fetch_assoc()["kind"];    // 消息种类
                $sql = "SELECT * FROM Group_Verify WHERE id='{$all_vrf[$i]}'";
                $result = $conn->query($sql);
                $glb = $result->fetch_assoc()["global"];    // 消息是否全局
                $sql = "SELECT * FROM Group_Verify WHERE id='{$all_vrf[$i]}'";
                $result = $conn->query($sql);
                $msgtime = $result->fetch_assoc()["time"];    // 消息时间

                $sql = "SELECT * FROM Users WHERE wan_uid='{$sender}'";
                $result = $conn->query($sql);
                $sendersn = $result->fetch_assoc()["showname"];    // 发消息人的显示名
                
                // 加群申请
                if ($kind == "join_group")
                {
                    $sql = "SELECT * FROM All_Groups_Info WHERE wan_gid='{$msg}'";
                    $result = $conn->query($sql);
                    $gn = $result->fetch_assoc()["g_name"];    // 群名称
                    $apply = $sendersn . " 申请加入群聊 “" . $gn . "”。";    // 显示消息
                    
                    if ($state == "待确认")
                    {
                        echo "<span style='padding-left: 30px;'></span>" . $apply . 
                            "<form method='post' action='groupvrf.php' style='display: inline-block;'>
                                <input type='hidden' name='vrfid' value='$i'>
                                <input type='hidden' name='sender' value='$sender'>
                                <input type='hidden' name='gid' value='$msg'>
                                <input type='hidden' name='gname' value='$gn'>
                                <input type='hidden' name='kind' value='$kind'>
                                <input type='submit' name='同意' value='同意' class='btn btn-success'>
                            </form>" . 
                            "    " . 
                            "<form method='post' action='groupvrf.php' style='display: inline-block;'>
                                <input type='hidden' name='vrfid' value='$i'>
                                <input type='hidden' name='sender' value='$sender'>
                                <input type='hidden' name='gid' value='$msg'>
                                <input type='hidden' name='gname' value='$gn'>
                                <input type='hidden' name='kind' value='$kind'>
                                <input type='submit' name='拒绝' value='拒绝' class='btn btn-danger'>
                            </form>" . 
                            "<span style='padding-left: 15px;'>（</span>" . $msgtime . "<span>）</span><br><br>";
                    }
                    if ($state == "已同意")
                        echo "<span style='padding-left: 30px;'></span>" . $apply . "<button class='btn btn-default'><span class='badge bg-primary'>已同意</span></button><span style='padding-left: 15px;'>（</span>" . $msgtime . "<span>）</span><br><br>";
                
                    if ($state == "已拒绝")
                        echo "<span style='padding-left: 30px;'></span>" . $apply . "<button class='btn btn-default'><span class='badge bg-secondary'>已拒绝</span></button><span style='padding-left: 15px;'>（</span>" . $msgtime . "<span>）</span><br><br>";
                }
                
                // 无需确认的消息
                if ($state == "无需确认")
                    echo "<span style='padding-left: 30px;'></span>" . $msg . "<span style='padding-left: 15px;'>（</span>" . $msgtime . "<span>）</span><br><br>";
            }
        ?>
        <?php
            if (isset($_POST["同意"]))
            {
                // 同意加群申请
                if ($_POST["kind"] == "join_group")
                {   
                    $vrfid = $_POST["vrfid"];
                    $gid = $_POST["gid"];    // 获取隐藏传来的值
                    $joinuser = $_POST["sender"];    // 需加入的用户
                    $gn = $_POST["gname"];
                    // 在该群聊信息的 “用户” 一项中加入该用户
                    $sql = "SELECT * FROM All_Groups_Info WHERE wan_gid='{$gid}'";
                    $result = $conn->query($sql);
                    $old_gm = $result->fetch_assoc()["g_members"];    // 之前的用户名单
                    $new_g_members = $old_gm . $joinuser . "//";
                    $sql = "UPDATE All_Groups_Info SET g_members='{$new_g_members}' WHERE wan_gid='{$gid}'";    // 更新
                    $conn->query($sql);
                    // 在该用户的【个人信息——加入的群聊】中加入该群聊
                    $sql = "SELECT * FROM Users WHERE wan_uid='{$joinuser}'";
                    $result = $conn->query($sql);
                    $usergrp = $result->fetch_assoc()["my_groups"];
                    $new_g = $usergrp . $gid . "//" ;
                    $sql = "UPDATE Users SET my_groups='{$new_g}' WHERE wan_uid='{$joinuser}'";
                    $conn->query($sql);
                    // 将该项（和所有与该项相同的项）设置为已同意并刷新页面，避免重复同意
                    $sql = "SELECT * FROM Group_Verify WHERE sender='{$joinuser}' and vrfmsg='{$gid}' and kind='join_group' and state='待确认'";    // 所有这样的验证消息
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0)
                    {   
                        for ($i = 0; $i < $result->num_rows; $i++)
                        {   
                            $sql = "SELECT * FROM Group_Verify WHERE sender='{$joinuser}' and vrfmsg='{$gid}' and kind='join_group' and state='待确认' ORDER BY id LIMIT 1";    // 所有这样的验证消息
                            $result1 = $conn->query($sql);
                            $each_msg = $result1->fetch_assoc()["id"];    // 某一条消息（id）
                            $sql = "SELECT * FROM Group_Verify WHERE id='{$each_msg}'";    // 所有这样的验证消息
                            $result2 = $conn->query($sql);
                            $each_msgr = $result2->fetch_assoc()["receiver"];    // 消息接收者
                            if ($each_msgr == $wid)    // 接收人是自己
                            {   
                                $sql = "UPDATE Group_Verify SET state='已同意' WHERE id='{$each_msg}'";
                                $conn->query($sql);
                            }
                            else    // 接收人不是自己（是其他管理员）
                            {
                                $sql = "UPDATE Group_Verify SET state='已被该群其他管理者同意' WHERE id='{$each_msg}'";
                                $conn->query($sql);
                            }
                        }
                    }
                    // 插入“已确认”消息
                    $time = date("Y-m-d H:i:s");
                    $back = "您已成功加入群聊 “" . $gn . "”！";
                    $sql = "INSERT INTO Group_Verify (sender, receiver, vrfmsg, state, kind, isread, time, global) VALUES ('{$wid}', '{$joinuser}', '{$back}', '无需确认', 'agree', '0', '{$time}', '0')";
                    $conn->query($sql);
                    header("location: groupvrf.php");
                    // 群聊聊天记录中插入欢迎消息
                    $sql = "SELECT * FROM Users WHERE wan_uid='{$joinuser}'";
                    $result = $conn->query($sql);
                    $new_name = $result->fetch_assoc()["showname"];
                    $greeting = "Hey! 欢迎新成员 @" . $new_name . "！";
                    $sql = "INSERT INTO group_{$gid} (who, msg, time) VALUES ('1024', '{$greeting}', '{$time}')";
                    $conn->query($sql);
                }
            }
            
            if (isset($_POST["拒绝"]))
            {
                // 拒绝加群申请
                if ($_POST["kind"] == "join_group")
                {
                    $vrfid = $_POST["vrfid"];
                    $gid = $_POST["gid"];    // 获取隐藏传来的值
                    $joinuser = $_POST["sender"];    // 需加入的用户
                    $gn = $_POST["gname"];
                    echo "<script>
                            cfm = window.confirm('真的要拒绝 TA 的加群申请吗？');
                            if (cfm)
                                window.location.href='groupvrf.php?refuse=1&gid=$gid&vrfid=$vrfid&joinuser=$joinuser&gname=$gn';    // 传值
                        </script>";
                }
            }
            
            if (isset($_GET["refuse"]))
            {
                $vrfid = $_GET["vrfid"];
                $gid = $_GET["gid"];    // 获取隐藏传来的值
                $joinuser = $_GET["joinuser"];
                $gn = $_GET["gname"];
                // 将该项（和所有与该项相同的项）设置为已拒绝并刷新页面，避免重复拒绝
                $sql = "SELECT * FROM Group_Verify WHERE sender='{$joinuser}' and vrfmsg='{$gid}' and kind='join_group' and state='待确认'";    // 所有这样的验证消息
                $result = $conn->query($sql);
                if ($result->num_rows > 0)
                {   
                    for ($i = 0; $i < $result->num_rows; $i++)
                    {   
                        $sql = "SELECT * FROM Group_Verify WHERE vrfmsg='{$gid}' and kind='join_group' and state='待确认' ORDER BY id LIMIT 1";    // 所有这样的验证消息
                        $result1 = $conn->query($sql);
                        $each_msg = $result1->fetch_assoc()["id"];    // 某一条消息（id）
                        $sql = "SELECT * FROM Group_Verify WHERE id='{$each_msg}'";    // 所有这样的验证消息
                        $result2 = $conn->query($sql);
                        $each_msgr = $result2->fetch_assoc()["receiver"];    // 消息接收者
                        if ($each_msgr == $wid)    // 接收人是自己
                        {   
                            $sql = "UPDATE Group_Verify SET state='已拒绝' WHERE id='{$each_msg}'";
                            $conn->query($sql);
                        }
                        else    // 接收人不是自己（是其他管理员）
                        {
                            $sql = "UPDATE Group_Verify SET state='已被该群其他管理者拒绝' WHERE id='{$each_msg}'";
                            $conn->query($sql);
                        }
                    }
                }
                // 插入“已确认”消息
                $time = date("Y-m-d H:i:s");
                $back = "您被拒绝加入群聊 “" . $gn . "”！";
                $sql = "INSERT INTO Group_Verify (sender, receiver, vrfmsg, state, kind, isread, time, global) VALUES ('{$wid}', '{$joinuser}', '{$back}', '无需确认', 'disagree', '0',  '{$time}', '0')";
                $conn->query($sql);
                header("location: groupvrf.php");
            }
        ?>
    </body>
</html>
