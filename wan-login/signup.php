<!DOCTYPE html>

<?php
    // 如何实现“提交表单后（页面刷新后）不新建数据表？”
    // ——用 session 呀！
    $time_limit = 60*60*24;
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
            // window.scrollTo(document.body.scrollLeft, document.body.scrollHeight);
            self.scroll(0, 65000);
        </script>
    </head>
    
    <body>
        <div class="container p-5">
            <center><h1>注册 WAN</h1></center>
        </div>
        <!--注册顺序：手机号/用户名/显示名/密码/确认密码/验证码/性别/邮箱/个人简介-->
        <!--连接数据库-->
        <?php
            // 创建连接
            $conn = new mysqli($servername, $username, $password, $dbname);
            // 检测连接
            if ($conn->connect_error)
                die("连接失败：" . $conn->connect_error);
        ?>
        
        <!--变量-->
        <?php
            $phone = $usern = $shown = $pwd = $pwd2 = $verification = $email = $introd = $gender = "";
        ?>
        
        <!--创建表（存储系统和用户的聊天）-->
        <?php
            if (isset($_SESSION["tid"]))
            {
                $sql = "SELECT * FROM information_schema.TABLES WHERE TABLE_NAME='signup_{$_SESSION['tid']}'";
                $result = $conn->query($sql);
                if ($result->num_rows == 0)    // 设置了 tid，但表不存在，说明表被意外删除
                {
                    unset($_SESSION["tid"]);
                    unset($_SESSION["flag"]);
                }
            }
            
            if (!isset($_SESSION["tid"]))
            {
                // 创建表
                $temp_id = mt_rand(100000, 1000000000);
                $_SESSION["tid"] = $temp_id;
                $sql = "CREATE TABLE signup_{$_SESSION['tid']} ( " .
                        "id INT NOT NULL AUTO_INCREMENT, " .
                        "who VARCHAR(20) NOT NULL, " .
                        "msg VARCHAR(1320725) NOT NULL, " .
                        "PRIMARY KEY (id))ENGINE=InnoDB DEFAULT CHARSET=utf8; ";
                $conn->query($sql);
                $_SESSION['flag'] = -1;
            }
        ?>
        
        <!--函数-->
        <?php
            // 从 “注册问题” 数据库中检索要显示的问题消息，并插入临时表中
            function qst($qstid)    // $conn 和 $_SESSION['tid' 必须要传进来，否则会提示 NULL！
            {   
                global $conn;
                $sql = "SELECT * FROM signup_qsts WHERE id='{$qstid}'";
                $result = $conn->query($sql);
                $qst = $result->fetch_assoc()["qst"];
                $sql = "INSERT INTO signup_{$_SESSION['tid']} (who, msg) VALUES ('WAN 机器人', '$qst')";
                $conn->query($sql);
                echo(mysqli_error($conn));
            }
            
            // 查找已有用户的信息（判断手机号/用户名/显示名是否重复
            function slct($kind, $data)
            {
                global $conn;
                $sql = "SELECT * FROM Users WHERE $kind='{$data}'";
                $result = $conn->query($sql);
                $exist = $result->num_rows;    // 大于 0 说明已存在，否则说明不存在
                return $exist;
            }
            
            // 获取用户输入表单
            function input($hint, $type, $name, $value="")
            {
                echo "<br><center><form method='post' action='register.php' style='padding-bottom: 40px;'>
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
        if ($_SESSION['flag'] == -1)
        {
            qst(1);
            qst(2);
            qst(3);
            $_SESSION['flag']++;
        }
            
        ?>
        
        <!--手机号-->
        <?php
            if ($_SESSION['flag'] == 0)
            {
                qst(4);
                $_SESSION['flag'] += 0.5;    // 避免重复输出 id 4 的消息（下同）
            }
            if ($_SESSION['flag'] == 0.5)
            {
                if (isset($_POST["发送"]))
                {
                    if (isset($_POST["phone"]))
                    {   
                        $phone = $_POST["phone"];
                        usermsg($phone);
                        // 检测手机号
                        if (empty($phone))    // 手机号为空
                        {
                            qst(5);
                            qst(33);
                        }
                            
                        else
                        {
                            if (!preg_match("/^1[3456789]\d{9}$/", $phone))    // 手机号不合法
                            {
                                qst(6);
                                qst(33);
                            }
                            else
                            {
                                if (slct("phone", $phone))    // 如果函数返回值大于 0
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
                        $usern = $_POST["usern"];
                        usermsg($usern);
                        // 检测用户名
                        if (empty($usern))    // 用户名为空
                        {
                            qst(5);
                            qst(33);
                        }
                        else
                        {
                            if (strlen($usern) > 20)    // 用户名过长
                            {
                                qst(10);
                                qst(33);
                            }
                            else
                            {
                                if (!preg_match("/[A-Za-z0-9_]/", $usern))    // 用户名不符合规范
                                {
                                    qst(11);
                                    qst(33);
                                }
                                else
                                {
                                    if (slct("username", $usern))    // 如果函数返回值大 0
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
                        $shown = $_POST["shown"];
                        usermsg($shown);
                        // 检测显示名
                        if (empty($shown))    // 显示名为空
                        {
                            qst(5);
                            qst(33);
                        }
                        else
                        {
                            if (strlen($shown) > 32)    // 显示名过长
                            {
                                qst(15);
                                qst(33);
                            }
                            else
                            {
                                if (slct("showname", $shown))    // 如果函数返回值大 0
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
                        $pwd = $_POST["pwd"];
                        usermsg($pwd);
                        // 检测密码
                        if (empty($pwd))    // 密码为空
                        {
                            qst(5);
                            qst(33);
                        }
                        else
                        {
                            if (strlen($pwd) > 24 || strlen($pwd) < 8)    // 密码过长/过短
                            {
                                qst(19);
                                qst(33);
                            }
                            else
                            {
                                if ((!preg_match("/[A-Z]/", $pwd)) || (!preg_match("/[a-z]/", $pwd)) || (!preg_match("/[0-9]/", $pwd)))    // 密码不符合规范
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
                        $pwd2 = $_POST["pwd2"];
                        usermsg($pwd2);
                        // 检测确认密码
                        if (empty($pwd2))    // 验证密码为空
                        {
                            qst(5);
                            qst(33);
                        }
                        else
                        {
                            // if ($pwd2 != $pwd)    // 两次输入不一致
                            //     qst(22);
                            // else
                                    qst(34);
                                    $_SESSION['flag'] += 0.5;
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
                        $verification = $_POST["verification"];
                        usermsg($verification);
                        // 检测验证码
                        if (empty($verification))    // 验证码为空
                        {
                            qst(5);
                            qst(33);
                        }
                        else
                        {
                            if ($verification != 111)
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
                        $gender = $_POST["gender"];
                        usermsg($gender);
                        qst(34);
                        $_SESSION['flag'] += 0.5;
                    }
                }
            }
        ?>
