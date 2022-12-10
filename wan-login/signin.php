<!-- 登录页面 -->

<!DOCTYPE html>
<?php
    $time_limit = 60*60*24;    // 过期时间为一天
    session_set_cookie_params($time_limit);
    session_start();
?>
<?php require "../wan-config.php";?>

<html>
    <head>
        <title>登录</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../wan-static/css/footer.css">
         <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>

    
    <!-- Custom styles for this template -->
    <link href="../wan-static/css/s.css" rel="stylesheet">
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
        
        <!--函数-->
        <?php
            // 数据过滤函数
            function test_input($data)
            {
                $data = trim($data);
                $data = stripslashes($data);
                $data = htmlspecialchars($data);
                return $data;
            }
        ?>
        
        <!--检测输入数据并跳转-->
        <?php
            if (!isset($_SESSION["wuid"]))
            {
                $flag = 0;
                $usern = $pwd = $verification = "";
                $none_user = 0;    // 存储用户是否存在的变量
                $nErr = $pwdErr = $vErr = "";

                // 数据验证
                if (isset($_POST["登录"]))
                {   
                    // 用户名
                    if (empty($_POST["usern"]))
                        $nErr = "用户名是必填项哦~";
                    else
                    {   
                        $usern = test_input($_POST["usern"]);
                        // 检索数据库
                        $sql = "SELECT username FROM Users WHERE username = '{$usern}'";
                        $result = $conn->query($sql);
                        // 用户名存在
                        if ($result->num_rows > 0)
                            $flag++;
                        // 用户名不存在
                        else
                        {
                            $nErr = "该用户不存在！";
                            $none_user = 1;
                        }
                    }
                    
                    // 密码
                    // 如果用户名不存在，则无需检测密码
                    if ($none_user != 1)
                    {
                        if (empty($_POST["pwd"]))
                            $pwdErr = "密码是必填项哦~";
                        else
                        {   
                            $pwd = test_input($_POST["pwd"]);
                            // 检索数据库
                            $sql2 = "SELECT password FROM Users WHERE password='{$pwd}' and username='{$usern}'";
                            $result = $conn->query($sql2);
                            // 如果不相等
                            if ($result->num_rows == 0)
                                $pwdErr = "emmm...密码错误！";
                            else
                                $flag++;
                        }
                    }
                    /*
                    // 验证码（JS 生成）（暂未开发）
                    if (empty($_POST["verification"]))
                        $vErr = "验证码是必填项哦~";
                    else
                    {    
                        // 111 仅为测试数据
                        $verification = test_input($_POST["verification"]);
                        if ($verification != "111")    
                            $vErr = "啊哦...验证码错误...";
                        else
                            $flag++;
                    }
                    */
                    // $flag++;
                    // 登录成功，跳转，关闭数据库连接
                    if ($flag == 2)
                    {
                        $sql3 = "SELECT wan_uid FROM Users WHERE username='{$usern}'";
                        $result = $conn->query($sql3);
                        $wid = $result->fetch_assoc()["wan_uid"];
                        $_SESSION["wuid"] = $wid;
                        $conn->close();
                        header("location: signin_success.php");
                    }
                }
            }
            else
                echo "<script>
                        alert('您已登录！');
                        location.href = '../wan-myself/myself.php';
                    </script>"
        ?>
        
      <main class="form-signin">
  <form action="signin.php" method="post">
    <h1 class="h3 mb-3 fw-normal"><center>登录WAN</center></h1>

    <div class="form-floating">
      <input type="text" class="form-control" id="floatingInput" placeholder="name@example.com" name="usern">
      <label for="floatingInput">用户名</label>
    </div>
    <br>
    <div class="form-floating">
      <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="pwd">
      <label for="floatingPassword">密码</label>
    </div>

    <button class="w-100 btn btn-lg btn-primary" type="submit" name="登录">登录</button>
    <p class="mt-5 mb-3 text-muted">&copy; 2022 WAN 聊天室</p>
  </form>
</main>
    </body>
</html>
