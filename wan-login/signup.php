<!DOCTYPE html>

<?php
    // 如何实现“提交表单后（页面刷新后）不重置数据的值？”
    // ——用 session 呀！
    // 我爱 session！！！
    $time_limit = 60*20;    // 过期时间二十分钟
    session_set_cookie_params($time_limit);
    session_start();
?>
        
<html>
    <head>
        <title>注册 WAN</title>
        <?php require "../wan-config.php"?>
        <link rel="stylesheet" href="../wan-static/css/me.css">
        <link rel="stylesheet" href="../wan-static/css/others.css">
        <style>
            body {
                background-image: url('img/wan-logo-opacity.png');
                background-attachment: fixed;   
                background-position: center;    
                background-size: 100%;
            }
            /* 注：此处的 inphp 仅仅是为了和外部样式的文件名称区分开，没有其他作用和意思 */
            .me-inphp {
                margin-top: 10px;
                padding: 20px;
            }
            
            .others-inphp {
                margin-top: 10px;
                padding: 20px;
            }
            
        </style>
        
        <script>
            window.location.href="#here";    // 自动滑动到锚点处
        </script>
    </head>
    
    <body>
        <center><img src="img/wan-logo.png" style="margin-top: 25px; width: 80px; height: 80px"></center>
        <center><h1 style="margin-top: 27px; margin-bottom: 20px">注册 WAN</h1></center>
        <center><h3>——WAN，让您像聊天一样注册——</h3></center>
        
        <!--注册顺序：手机号/用户名/显示名/密码/确认密码/验证码/性别/邮箱/个人简介-->
        <!--连接数据库-->
        <?php
            // 创建连接
            $conn = new mysqli($servername, $username, $password, $dbname);
            // 检测连接
            if ($conn->connect_error)
                die("连接失败：" . $conn->connect_error);
        ?>
        
        <!--检测注册是否超时，超时则删除表-->
        <?php
            if (isset($_SESSION["time"]))
            {
                $time = date("U");
                if ($time - $_SESSION["time"] >= 1200)
                {
                    $sql = "DROP TABLE signup_{$_SESSION['tid']}";
                    $conn->query($sql);
                    unset($_SESSION["time"]);
                }
            }
        ?>
        
        <!--创建表（存储系统和用户的聊天）-->
        <?php
            // 设置了 tid（说明注册超时，表被删除）
            if (isset($_SESSION["tid"]))
            {
                $sql = "SELECT * FROM information_schema.TABLES WHERE TABLE_NAME='signup_{$_SESSION['tid']}'";
                $result = $conn->query($sql);
                if ($result->num_rows == 0)    // 设置了 tid，但表不存在，说明注册时间超过了二十分钟，表被自动删除
                {   
                    // 提示用户注册超时
                    echo "<script>
                            alert('注册超时！需重新注册！');
                        </script>";
                    unset($_SESSION["tid"]);    // 释放两个 session 变量，执行下一个 if，新建表
                    unset($_SESSION["flag"]);
                    // 释放 session 变量（即：删除之前的注册数据）
                    unset($_SESSION["phone"]);
                    unset($_SESSION["usern"]);
                    unset($_SESSION["shown"]);
                    unset($_SESSION["pwd"]);
                    unset($_SESSION["pwd2"]);
                    unset($_SESSION["gender"]);
                    unset($_SESSION["email"]);
                    unset($_SESSION["introd"]);
                    unset($_POST["发送"]);    // 避免显示之前的消息
                }
            }
            // 未设置 tid
            if (!isset($_SESSION["tid"]))
            {
                // 创建表
                // （注：通常情况下，该表将在用户注册成功后自动被删除，但如果用户二十分钟仍未注册成功，该表将自动被删除）
                $temp_id = mt_rand(100000, 1000000000);
                $_SESSION["tid"] = $temp_id;
                $sql = "CREATE TABLE signup_{$_SESSION['tid']} ( " .
                        "id INT NOT NULL AUTO_INCREMENT, " .
                        "who VARCHAR(20) NOT NULL, " .
                        "msg VARCHAR(1320725) NOT NULL, " .
                        "PRIMARY KEY (id))ENGINE=InnoDB DEFAULT CHARSET=utf8; ";
                $conn->query($sql);
                $_SESSION['flag'] = -1;    // 如果直接用变量 $flag，每次用户提交表单后都会刷新，故用 session
                $_SESSION["time"] = date("U");    // 创建时的时间（后续刷新并不会改变该变量的值！），用于实现定时删除表
            }
        ?>
        
        <!--函数-->
        <!--我爱封装！！-->
        <?php
            // 从 “注册问题” 数据库中查询要显示的问题消息，并插入新建的表中
            // $qstid：需要插入新表（即：显示在页面上）的消息的 id
            function qst($qstid)    
            {   
                global $conn;
                $sql = "SELECT * FROM signup_qsts WHERE id='{$qstid}'";
                $result = $conn->query($sql);
                $qst = $result->fetch_assoc()["qst"];
                $sql = "INSERT INTO signup_{$_SESSION['tid']} (who, msg) VALUES ('WAN 机器人', '$qst')";
                $conn->query($sql);
            }
            
            // 查找已有的用户的信息（判断手机号/用户名/显示名是否重复）
            // $kind：键的名称；$data：需查询的数据
            function slct($kind, $data)
            {
                global $conn;
                $sql = "SELECT * FROM Users WHERE $kind='{$data}'";
                $result = $conn->query($sql);
                $exist = $result->num_rows;    // 大于 0 说明已存在，否则说明不存在
                return $exist;    // 返回值（可直接用），大于 0 说明已存在
            }
            
            // 用户输入表单（可通过该函数直接创建一个表单，不需要每次都重复写）
            // $hint：提示词；$type：input 框类型；$name：input 框的 name 属性；$value：input 框的 value 属性，默认为空
            function input($hint, $type, $name, $value="")
            {
                echo "<br><center><form method='post' action='signup.php' style='padding-bottom: 40px;'>
                        $hint ：<br><br><input type='$type' name='$name'><br><br>
                        <input type='submit' name='发送' value='发送'>
                    </form></center>";
                echo "\n";
            }
            
            // 将用户消息插入临时表
            function usermsg($var)
            {
                global $conn;
                $sql = "INSERT INTO signup_{$_SESSION['tid']} (who, msg) VALUES ('用户', '$var')";
                $conn->query($sql);
            }
        ?>
        
        <!--欢迎用户-->
        <?php
            if ($_SESSION['flag'] == -1)    // 通过 session 的值决定是否输出，做到不重复输出数据（刷新也不会）
            {
                qst(1);
                qst(2);
                qst(3);
                qst(35);
                $_SESSION['flag']++;
            }
        ?>
        
        <!--手机号-->
        <?php
            if ($_SESSION['flag'] == 0)
            {
                qst(4);
                $_SESSION['flag'] += 0.5;    // 改变 session 的值，避免重复输出 id 4 的消息（下同）
            }
            if ($_SESSION['flag'] == 0.5)
            {
                if (isset($_POST["发送"]))
                {
                    if (isset($_POST["phone"]))
                    {   
                        $_SESSION['phone'] = $_POST["phone"];
                        usermsg($_SESSION['phone']);
                        // 检测手机号
                        if (empty($_SESSION['phone']))    // 手机号为空
                        {
                            qst(5);
                            qst(33);
                        }
                        else
                        {
                            if (!preg_match("/^1[3456789]\d{9}$/", $_SESSION['phone']))    // 手机号不合法
                            {
                                qst(6);
                                qst(33);
                            }
                            else
                            {
                                if (slct("phone", $_SESSION['phone']))    // 如果函数返回值大于 0
                                {
                                    qst(7);    // 说明该手机号已存在（已注册）
                                    qst(33);
                                }
                                else
                                {
                                    qst(34);
                                    $_SESSION['flag'] += 0.5;
                                }
                                    
                            }
                        }
                    }
                }
            }
        ?>
        
        <!--用户名-->
        <?php
            if ($_SESSION['flag'] == 1)
            {
                qst(8);
                qst(9);
                $_SESSION['flag'] += 0.5;
            }
            if ($_SESSION['flag'] == 1.5)
            {
                if (isset($_POST["发送"]))
                {
                    if (isset($_POST["usern"]))
                    {   
                        $_SESSION['usern'] = $_POST["usern"];
                        usermsg($_SESSION['usern']);
                        // 检测用户名
                        if (empty($_SESSION['usern']))    // 用户名为空
                        {
                            qst(5);
                            qst(33);
                        }
                        else
                        {
                            if (strlen($_SESSION['usern']) > 20)    // 用户名过长
                            {
                                qst(10);
                                qst(33);
                            }
                            else
                            {
                                if (!preg_match("/[A-Za-z0-9_]/", $_SESSION['usern']))    // 用户名不符合规范
                                {
                                    qst(11);
                                    qst(33);
                                }
                                else
                                {
                                    if (slct("username", $_SESSION['usern']))    // 如果函数返回值大 0
                                    {
                                        qst(12);    // 说明该用户名已存在（被占用）
                                        qst(33);
                                    }    
                                    else
                                    {
                                        qst(34);
                                        $_SESSION['flag'] += 0.5;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        ?>
        
        <!--显示名-->
        <?php
            if ($_SESSION['flag'] == 2)
            {
                qst(13);
                qst(14);
                $_SESSION['flag'] += 0.5;
            }
            if ($_SESSION['flag'] == 2.5)
            {
                if (isset($_POST["发送"]))
                {
                    if (isset($_POST["shown"]))
                    {   
                        $_SESSION['shown'] = $_POST["shown"];
                        usermsg($_SESSION['shown']);
                        // 检测显示名
                        if (empty($_SESSION['shown']))    // 显示名为空
                        {
                            qst(5);
                            qst(33);
                        }
                        else
                        {
                            if (strlen($_SESSION['shown']) > 32)    // 显示名过长
                            {
                                qst(15);
                                qst(33);
                            }
                            else
                            {
                                if (slct("showname", $_SESSION['shown']))    // 如果函数返回值大于 0
                                {
                                    qst(16);    // 说明该显示名已存在（被占用）
                                    qst(33);
                                }    
                                else
                                {
                                    qst(34);
                                    $_SESSION['flag'] += 0.5;
                                }
                            }
                        }
                    }
                }
            }
        ?>
        
        <!--密码-->
        <?php
            if ($_SESSION['flag'] == 3)
            {
                qst(17);
                qst(18);
                $_SESSION['flag'] += 0.5;
            }
            if ($_SESSION['flag'] == 3.5)
            {
                if (isset($_POST["发送"]))
                {
                    if (isset($_POST["pwd"]))
                    {   
                        $_SESSION['pwd'] = $_POST["pwd"];
                        usermsg($_SESSION['pwd']);
                        // 检测密码
                        if (empty($_SESSION['pwd']))    // 密码为空
                        {
                            qst(5);
                            qst(33);
                        }
                        else
                        {
                            if (strlen($_SESSION['pwd']) > 24 || strlen($_SESSION['pwd']) < 8)    // 密码过长/过短
                            {
                                qst(19);
                                qst(33);
                            }
                            else
                            {
                                if ((!preg_match("/[A-Z]/", $_SESSION['pwd'])) || (!preg_match("/[a-z]/", $_SESSION['pwd'])) || (!preg_match("/[0-9]/", $_SESSION['pwd'])))    // 密码不符合规范
                                {
                                    qst(20);
                                    qst(33);
                                }
                                else
                                {
                                    qst(34);
                                    $_SESSION['flag'] += 0.5;
                                }
                            }
                        }
                    }
                }
            }
        ?>
        
        <!--确认密码-->
        <?php
            if ($_SESSION['flag'] == 4)
            {
                qst(21);
                $_SESSION['flag'] += 0.5;
            }
            if ($_SESSION['flag'] == 4.5)
            {
                if (isset($_POST["发送"]))
                {
                    if (isset($_POST["pwd2"]))
                    {   
                        $_SESSION['pwd2'] = $_POST["pwd2"];
                        usermsg($_SESSION['pwd2']);
                        // 检测确认密码
                        if (empty($_SESSION['pwd2']))    // 验证密码为空
                        {
                            qst(5);
                            qst(33);
                        }
                        else
                        {
                            if ($_SESSION['pwd2'] != $_SESSION['pwd'])    // 两次输入不一致
                                qst(22);
                            else
                            {
                                qst(34);
                                $_SESSION['flag'] += 0.5;
                            }
                        }
                    }
                }
            }
        ?>
        
        <!--验证码-->
        <?php
            if ($_SESSION['flag'] == 5)
            {
                qst(23);
                $_SESSION['flag'] += 0.5;
            }
            if ($_SESSION['flag'] == 5.5)
            {
                if (isset($_POST["发送"]))
                {
                    if (isset($_POST["verification"]))
                    {   
                        $_SESSION['verification'] = $_POST["verification"];
                        usermsg($_SESSION['verification']);
                        // 检测验证码
                        if (empty($_SESSION['verification']))    // 验证码为空
                        {
                            qst(5);
                            qst(33);
                        }
                        else
                        {
                            if ($_SESSION['verification'] != 111)
                            {
                                qst(24);
                                qst(33);
                            }
                            else
                            {
                                qst(34);
                                $_SESSION['flag'] += 0.5;
                            }
                        }
                    }
                }
            }
        ?>
        
        <!--性别-->
        <?php
            if ($_SESSION['flag'] == 6)
            {
                qst(28);
                $_SESSION['flag'] += 0.5;
            }
            if ($_SESSION['flag'] == 6.5)
            {
                if (isset($_POST["发送"]))
                {
                    if (isset($_POST["gender"]))
                    {
                        $_SESSION['gender'] = $_POST["gender"];
                        usermsg($_SESSION['gender']);
                        qst(34);
                        $_SESSION['flag'] += 0.5;
                    }
                }
            }
        ?>
        
        <!--邮箱-->
        <?php
            if ($_SESSION['flag'] == 7)
            {
                qst(25);
                $_SESSION['flag'] += 0.5;
            }
            if ($_SESSION['flag'] == 7.5)
            {
                if (isset($_POST["发送"]))
                {
                    if (isset($_POST["email"]))
                    {   
                        $_SESSION['email'] = $_POST["email"];
                        usermsg($_SESSION['email']);
                        // 检测邮箱
                        if (empty($_SESSION['email']))    // 邮箱为空
                        {
                            qst(5);
                            qst(33);
                        }
                        else
                        {
                            if ($_SESSION['email'] == "无")    // 无邮箱
                                $_SESSION['flag']+=0.5;
                            else
                            {
                                if (!filter_var($_SESSION['email'], FILTER_VALIDATE_EMAIL))
                                {
                                    qst(26);
                                    qst(33);
                                }
                                else
                                {
                                    qst(34);
                                    $_SESSION['flag'] += 0.5;
                                }
                            }
                        }
                    }
                }
            }
        ?>
        
        <!--个人简介-->
        <?php
            if ($_SESSION['flag'] == 8)
            {
                qst(27);
                $_SESSION['flag'] += 0.5;
            }
            if ($_SESSION['flag'] == 8.5)
            {
                if (isset($_POST["发送"]))
                {
                    if (isset($_POST["introd"]))
                    {   
                        $_SESSION['introd'] = $_POST["introd"];
                        usermsg($_SESSION['introd']);
                        // 检测个人简介
                        if (empty($_SESSION['introd']))    // 个人简介为空
                        {
                            qst(5);
                            qst(33);
                        }
                        else
                        {
                            qst(34);
                            $_SESSION['flag'] += 0.5;
                        }
                    }
                }
            }
        ?>
        
        <?php
            // if ($_SESSION["flag"] == 9)
            // {
            //     qst(29);
            //     qst(30);
            //     qst(31);
            //     qst(32);
            //     $_SESSION["flag"]++;
            // }
        ?>
        
        <?php
            if ($_SESSION["flag"] == 9)
            {
                $wid = mt_rand(100000, 1000000000);    // 随机生成 wan_uid
                $time = date("Y-m-d H:i:s");    // 获取注册时间
                // 将所有数据写入数据库
                $sql = "INSERT INTO Users (wan_uid, phone, username, showname, password, gender, email, self_introd, signup_time) VALUES ('{$wid}', '{$_SESSION['phone']}', '{$_SESSION['usern']}', '{$_SESSION['shown']}', '{$_SESSION['pwd']}', '{$_SESSION['gender']}', '{$_SESSION['email']}', '{$_SESSION['introd']}', '{$time}')";
                // 判断是否写入成功、删除数据库、释放 session、关闭连接、跳转
                if ($conn->query($sql) === TRUE)
                {
                    $sql = "DROP TABLE signup_{$_SESSION['tid']}";
                    $conn->query($sql);
                    // 释放所有 session 变量
                    unset($_SESSION["tid"]);
                    unset($_SESSION["flag"]);
                    unset($_SESSION["time"]);
                    unset($_SESSION["phone"]);
                    unset($_SESSION["usern"]);
                    unset($_SESSION["shown"]);
                    unset($_SESSION["pwd"]);
                    unset($_SESSION["pwd2"]);
                    unset($_SESSION["gender"]);
                    unset($_SESSION["email"]);
                    unset($_SESSION["introd"]);
                    $conn->close();    // 仅需关闭一次即可，否则会报错 Couldn't fetch mysqli
                    echo "<script>
                        window.location.href='signup_success.php';
                    </script>";
                }
                else
                {
                    $sql = "DROP TABLE signup_{$_SESSION['tid']}";
                    $conn->query($sql);
                    // 释放所有 session 变量
                    unset($_SESSION["tid"]);
                    unset($_SESSION["flag"]);
                    unset($_SESSION["time"]);
                    unset($_SESSION["phone"]);
                    unset($_SESSION["usern"]);
                    unset($_SESSION["shown"]);
                    unset($_SESSION["pwd"]);
                    unset($_SESSION["pwd2"]);
                    unset($_SESSION["gender"]);
                    unset($_SESSION["email"]);
                    unset($_SESSION["introd"]);
                    echo mysqli_error($conn);
                    $conn->close();
                }
            }
        ?>
        
        <!--输出所有消息-->
        <?php
            $sql = "SELECT * FROM signup_{$_SESSION['tid']} ORDER BY id";
            $showmsg = $conn->query($sql);
                if ($showmsg->num_rows > 0)
                {
                    while ($linemsg = $showmsg->fetch_assoc())
                    {   
                        $who = $linemsg["who"];
                        // 是自己
                        
                        if ($who == "用户")    
                        {
                            echo "<br><div class='chat_box'><div id='".$linemsg["id"]."' class='me' style='background-color: aqua;'><div class='me-inphp'>" . "<b>". "我" . "</b>" ."：". $linemsg["msg"] ."</div></div></div>";
                        }
                        // 不是自己
                        else
                        {
                            echo "<br><div class='chat_box'><div id='".$linemsg["id"]."' class='others' style='background-color: pink'><div class='others-inphp'>" . "<b>" . $linemsg["who"] . "</b>" . "：". $linemsg["msg"] ."</div></div></div>";
                        }
                    }
                }
        ?>
        
        <!--输入框-->
        <!--注册顺序：手机号/用户名/显示名/密码/确认密码/验证码/性别/邮箱/个人简介-->
        <!--通过 session flag 的值判断显示哪个输入框-->
        <?php
            if ($_SESSION['flag'] == 0 || $_SESSION['flag'] == 0.5)
                input("手机号", "text", "phone");
            if ($_SESSION['flag'] == 1 || $_SESSION['flag'] == 1.5)
                input("用户名", "text", "usern");
            if ($_SESSION['flag'] == 2 || $_SESSION['flag'] == 2.5)
                input("显示名", "text", "shown");
            if ($_SESSION['flag'] == 3 || $_SESSION['flag'] == 3.5)
                input("密码", "password", "pwd");
            if ($_SESSION['flag'] == 4 || $_SESSION['flag'] == 4.5)
                input("确认密码", "password", "pwd2");
            if ($_SESSION['flag'] == 5 || $_SESSION['flag'] == 5.5)
                input("验证码", "text", "verification");
            if ($_SESSION['flag'] == 6 || $_SESSION['flag'] == 6.5)
                echo "<br><center><form method='post' action='signup.php' style='padding-bottom: 40px;'>
                        <input type='radio' name='gender' value='XY'> 男<span style='margin-right: 20px;'></span>
                        <input type='radio' name='gender' value='XX'> 女<br><br>
                        <input type='submit' name='发送' value='发送'>
                    </form></center>";
            if ($_SESSION['flag'] == 7 || $_SESSION['flag'] == 7.5)
                input("邮箱", "text", "email");
            if ($_SESSION['flag'] == 8 || $_SESSION['flag'] == 8.5)
                echo "<br><center><form method='post' action='signup.php' style='padding-bottom: 40px;'>
                        个人简介 ：<br><br><textarea type='text' name='introd' style='height: 200px; width: 500px; line-height: 1.5;'></textarea><br><br>
                        <input type='submit' name='发送' value='发送'>
                    </form></center>"
        ?>
        
        <!--自动滑动到底部-->
        <a id="here"/>    
    </body>
</html>
