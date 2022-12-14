<!--编辑个人信息-->

<!DOCTYPE html>
<html>
    <head>
        <?php
            session_start();
        ?>
        <?php require_once "../wan-config.php"?>
        <?php require_once "../wan-userinfo.php"?>    <!--用户个人信息-->

        <title>编辑个人信息</title>
        <meta charset="utf-8">
        <style>
            .must {
                color: red;
            }
        </style>
    </head>
    
    <body>
        <!-- 打开模态框函数 -->
        <script>
            function open_modal() {
				$(document).ready(function(){ $('#change_ok').modal('show'); })
            }
            
        </script>
        
        <!-- 模态框 -->
        <div class="modal fade" id="change_ok">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">成功！</h4>
                        <button type="button" class="btn btn-close" data-bs-dismiss="modal" onclick="window.location.href = 'change_info.php'"></button>
                    </div>
                    <div class="modal-body">
                        <p>您的个人资料已成功更新！</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="window.location.href = 'change_info.php'">关闭</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="container p-5">
            <center><h1>编辑个人信息</h1>
        </div>
        <nav class="navbar navbar-expand-sm bg-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                <a class="nav-link" id="reload_trigger" href="myself.php" style="padding-left: 30px;">&larr;返回个人中心</a>
                </li>
            </ul>
        </nav><br>
        <center><a href="password.php">修改密码</a>
        <form method="post" action="change_info.php" class="col-8 col-sm-7 col-md-6 col-lg-4 col-xl-3 col-xxl-3">
            <div class="mb-3">
                <label for="phone">手机号：</label>
                <input type="text" class="form-control" id="phone" name="phone" value=<?php echo $phone;?>>
            <!--<span class="must"> <?php echo $pErr?></span><br><br>-->
            </div>
            <div class="mb-3">
                <label for="usern">用户名：</label>
                <input type="text" class="form-control" id="usern" name="usern" value=<?php echo $usern;?>>
                <!--<span class="must"> <?php echo $nErr?></span><br><br>-->
            </div>
            <div class="mb-3">
                <label for="shown">显示名：</label>
                <input type="text" class="form-control" id="shown" name="shown" value=<?php echo $shown;?>>
                <!--<span class="must"> <?php echo $sErr?></span><br><br>-->
            </div>
            <!--密码：<input type="password" name="pwd">-->
            <!--    <span class="must"> <?php echo $pwdErr?></span><br><br>-->
            <!--确认密码：<input type="password" name="pwd2">-->
            <!--    <span class="must"> <?php echo $pwd2Err?></span><br><br>-->
            <!--验证码（填 111）：<input type="text" name="verification" >-->
            <!--    <span class="must"> <?php echo $vErr?></span><br><br>-->
            <div class="mb-3">
                <label for="email">邮箱：</label>
                <input type="email" class="form-control" id="email" name="email" value=<?php echo $email;?>>
                <!--<span class="must"><?php echo $eErr?></span><br><br>-->
            </div>
            <div class="mb-3">
                <label for="introd">个人简介：</label>
                <textarea  type="text" class="form-control" id="introd" name="introd" style="height: 200px; line-height: 1.5;"><?php echo $introd;?></textarea>
                <span class="must"><?php echo $iErr?></span><br>
            </div>

            <button type="submit" name="确认修改" class="btn btn-primary" value="确认修改">确认修改</button><br><br>
        </form></center>
        
        <!--连接数据库-->
        <?php
            date_default_timezone_set("PRC");    // 时区
            // 创建连接
            $conn = new mysqli($servername, $username, $password, $dbname);
            // 检测连接
            if ($conn->connect_error)
                die("连接失败：" . $conn->connect_error);
        ?>
        
        <!--函数-->
        <?php
        // 数据过滤函数
            function test_input($data)
            {
                $data = trim($data);
                $data = stripslashes($data);
                return $data;
            }
        ?>
        
        <!--检测输入数据并写入数据库-->
        <?php
            $flag = 0;
            $phone2 = $usern2 = $shown2 =  $email2 = $introd2 = "";
            $pErr = $nErr = $sErr = $eErr = $iErr = "";
            
            if (isset($_POST["确认修改"]))
            {
                // 手机号
                if (empty($_POST["phone"]))
                    $pErr = "手机号是必填项哦~";
                else
                {   
                    $phone2 = test_input($_POST["phone"]);
                    if (!preg_match("/^1[3456789]\d{9}$/", $phone2))   
                        $pErr = "非法的手机号...";
                    else
                    {   
                        if ($phone2 != $phone)    // 和之前的不一样，说明修改了
                        {
                            $sql2 = "SELECT phone FROM Users WHERE phone = '{$phone2}'";
                            $result = $conn->query($sql2);
                            if ($result->num_rows > 0)
                                $pErr = "该手机号已注册！";
                            else
                            {
                                $phone = $phone2;
                                $flag++;
                            }
                        }
                        else 
                            $flag++;
                    }
                }
                
                // 用户名
                if (empty($_POST["usern"]))
                    $nErr = "用户名是必填项哦~";
                else
                {   
                    $usern2 = test_input($_POST["usern"]);
                    if (strlen($usern2) > 20)
                        $nErr = "用户名不得超过 20 个字符哦~";
                    else
                    {
                        // !/[A-Za-z0-9_]/ 意为：判断【是否不包含】大小写字母和数字；而 !/^[A-Za-z0-9_]$/ 意为：判断【是否不全为】大小写字母和数字
                        if (!preg_match("/[A-Za-z0-9_]/", $usern2))    // 此处不能加 ^ 和 $
                            $nErr = "用户名不得包含中文和特殊字符！";
                        else
                        {   
                            if ($usern2 != $usern)    // 和之前的不一样，说明修改了
                            {
                                // 判断用户名是否被占用
                                $sql3 = "SELECT username FROM Users WHERE username = '{$usern2}'";    // WHERE 子句
                                $result = $conn->query($sql3);
                                if ($result->num_rows > 0)    // 大于 0，说明已存在，即已被占用
                                    $nErr = "啊哦...用户名被别人占用啦...换一个吧~";
                                else
                                {
                                    $usern = $usern2;
                                    $flag++;
                                }
                            }
                            else
                                $flag++;
                        }
                    }
                }
                
                // 显示
                if (empty($_POST["shown"]))
                    $sErr = "显示名是必填项哦~";
                else
                {   
                    $shown2 = test_input($_POST["shown"]);
                    if (strlen($shown2) > 32)
                        $sErr = "显示名不得超过 32 个字符哦~";
                    else
                    {
                        if ($shown2 != $shown)    // 和之前的不一样，说明修改了
                        {
                            // 判断显示名是否被占用
                            $sql3 = "SELECT showname FROM Users WHERE showname='{$shown2}'";    // WHERE 子句
                            $result = $conn->query($sql3);
                            if ($result->num_rows > 0)    // 大于 0，说明已存在，即已被占用
                                $sErr = "啊哦...显示名被别人占用啦...换一个吧~";
                            else
                            {
                                $shown = $shown2;
                                $flag++;
                            }
                        }
                        else
                            $flag++;
                    }
                }

                // 邮箱
                if(empty($_POST["email"]))
                    $flag++;
                else
                {   
                    $email2 = test_input($_POST["email"]);
                    if (!filter_var($email2, FILTER_VALIDATE_EMAIL))
                        $eErr = "非法的邮箱地址...";
                    else
                    {
                        $email = $email2;
                        $flag++;
                    }
                }
                
                // 个人简介
                if(empty($_POST["introd"]))
                    $flag++;
                else
                {   
                    $introd2 = test_input($_POST["introd"]);
                    $introd = $introd2;
                    $flag++;
                }
                
                // 所有数据验证成功
                if ($flag == 5)
                {
                    // 将所有数据写入数据库
                    $sql = "UPDATE Users SET phone='{$phone}', username='{$usern}', showname='{$shown}', email='{$email}', self_introd='{$introd}' WHERE wan_uid='{$wid}'";
                    // 判断是否写入成功、关闭连接、跳转
                    if ($conn->query($sql) === TRUE)
                    {   
                        // echo "<br><div class='alert alert-success' style='top: 0;'>更新成功！</div>";
                        echo "<script>open_modal();</script>";
                        $conn->close();    // 仅需关闭一次即可（前两次无需关闭），否则会报错 Couldn't fetch mysqli
                    }
                    else
                    {
                        $conn->close();    // 仅需关闭一次即可（前两次无需关闭），否则会报错 Couldn't fetch mysqli
                        header("location: ../wan-static/html/failed.html");
                    }
                }
            }
        ?>
      <?php require "../wan-footer.php";?>

    </body>
</html>
